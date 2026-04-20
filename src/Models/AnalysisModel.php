<?php
namespace App\Models;

use App\Core\Database;

class AnalysisModel {
    public function getAll() {
        $db = Database::getMariaDb();
        $stmt = $db->query("SELECT * FROM analysis ORDER BY id DESC LIMIT 100");
        return $stmt->fetchAll();
    }

    public function getTotalCount() {
        $db = Database::getMariaDb();
        $stmt = $db->query("SELECT COUNT(*) as count FROM analysis");
        $result = $stmt->fetch();
        return $result['count'];
    }

    public function getPaginated($page = 1, $limit = 15) {
        $offset = (int)(($page - 1) * $limit);
        $limit = (int)$limit;
        $db = Database::getMariaDb();
        $query = "SELECT * FROM analysis ORDER BY id DESC LIMIT " . $limit . " OFFSET " . $offset;
        $stmt = $db->query($query);
        return $stmt->fetchAll();
    }

    private function buildSearchQuery($filters, &$params) {
        $searchType = $filters['searchType'] ?? 'title';
        $keyword = $filters['keyword'] ?? '';

        $joins = "";
        $where = "1=1";

        if (!empty($keyword)) {
            if ($searchType === 'task_id') {
                $joins = "LEFT JOIN tasks t ON a.id = t.analysis_id";
                $where .= " AND t.task_id LIKE ?";
                $params[] = "%" . $keyword . "%";
            } else {
                $allowedColumns = ['title', 'channel_name', 'youtube_video_id'];
                $column = in_array($searchType, $allowedColumns) ? $searchType : 'title';
                $where .= " AND a.$column LIKE ?";
                $params[] = "%" . $keyword . "%";
            }
        }

        return [$joins, $where];
    }

    public function getSearchTotalCount($filters) {
        $db = Database::getMariaDb();
        $params = [];
        list($joins, $where) = $this->buildSearchQuery($filters, $params);
        
        $query = "SELECT COUNT(DISTINCT a.id) as count FROM analysis a $joins WHERE $where";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'];
    }

    public function searchPaginated($filters, $page = 1, $limit = 15) {
        $offset = (int)(($page - 1) * $limit);
        $limit = (int)$limit;
        $db = Database::getMariaDb();
        
        $params = [];
        list($joins, $where) = $this->buildSearchQuery($filters, $params);

        $orderBy = $filters['orderBy'] ?? 'created_at';
        $orderDir = strtoupper($filters['orderDir'] ?? 'DESC');

        $allowedOrderColumns = ['created_at', 'channel_name', 'title'];
        if (!in_array($orderBy, $allowedOrderColumns)) {
            $orderBy = 'created_at';
        }
        if ($orderDir !== 'ASC' && $orderDir !== 'DESC') {
            $orderDir = 'DESC';
        }

        $query = "SELECT DISTINCT a.* FROM analysis a $joins WHERE $where ORDER BY a.$orderBy $orderDir LIMIT $limit OFFSET $offset";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $db = Database::getMariaDb();
        $stmt = $db->prepare("SELECT * FROM analysis WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getTasksByAnalysisId($id) {
        $db = Database::getMariaDb();
        $stmt = $db->prepare("SELECT * FROM tasks WHERE analysis_id = ? ORDER BY created_at DESC");
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    public function deleteAnalysis($id) {
        $db = Database::getMariaDb();
        
        // youtube_video_id 가져오기 (각종 연관 데이터 삭제를 위함)
        $stmt = $db->prepare("SELECT youtube_video_id FROM analysis WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return false;
        }
        $youtubeVideoId = $row['youtube_video_id'];
        
        try {
            $db->beginTransaction();
            
            // MariaDB 내 연관 데이터 삭제
            $stmt = $db->prepare("DELETE FROM feedback WHERE youtube_video_id = ?");
            $stmt->execute([$youtubeVideoId]);
            
            $stmt = $db->prepare("DELETE FROM tasks WHERE analysis_id = ?");
            $stmt->execute([$id]);
            
            $stmt = $db->prepare("DELETE FROM analysis WHERE id = ?");
            $stmt->execute([$id]);
            
            $db->commit();
        } catch (\PDOException $e) {
            $db->rollBack();
            throw $e;
        }
        
        // PostgreSQL (Vector DB) 내 연관 데이터 삭제
        try {
            $pgDb = Database::getPgDb();
            $stmt = $pgDb->prepare("DELETE FROM video_embeddings WHERE youtube_video_id = ?");
            $stmt->execute([$youtubeVideoId]);
        } catch (\PDOException $e) {
            error_log("Failed to delete from PGDB: " . $e->getMessage());
        }
        
        return true;
    }

    public function getRelatedVideos($youtubeVideoId, $limit = 3) {
        try {
            $pgDb = Database::getPgDb();
            
            // 1. 대상 비디오의 임베딩 값을 먼저 조회
            $stmt = $pgDb->prepare("SELECT embedding FROM video_embeddings WHERE youtube_video_id = ? LIMIT 1");
            $stmt->execute([$youtubeVideoId]);
            $currentVideo = $stmt->fetch();
            
            if (!$currentVideo || empty($currentVideo['embedding'])) {
                return [];
            }
            
            $currentEmbedding = $currentVideo['embedding'];
            
            // 2. 가져온 임베딩 값을 기준으로 가장 가까운 비디오 조회
            // MariaDB에 존재하지 않는 비디오일 수도 있으므로 좀 더 넉넉하게 10개를 가져옵니다
            $stmt = $pgDb->prepare("
                SELECT youtube_video_id 
                FROM video_embeddings 
                WHERE youtube_video_id != ? 
                  AND embedding IS NOT NULL
                ORDER BY embedding <=> ?::vector 
                LIMIT 10
            ");
            $stmt->bindValue(1, $youtubeVideoId);
            $stmt->bindValue(2, $currentEmbedding);
            $stmt->execute();
            
            $pgResults = $stmt->fetchAll();
            
            if (empty($pgResults)) {
                return [];
            }
            
            $relatedYtIds = array_column($pgResults, 'youtube_video_id');
            
            // 3. MariaDB에서 해당 ID들의 세부정보 조회
            $db = Database::getMariaDb();
            $inClause = rtrim(str_repeat('?,', count($relatedYtIds)), ',');
            $stmt = $db->prepare("SELECT * FROM analysis WHERE youtube_video_id IN ($inClause)");
            $stmt->execute($relatedYtIds);
            
            $analyses = $stmt->fetchAll();
            
            // 4. 벡터 거리 오름차순으로 정렬 유지 및 최대 $limit 개까지만 반환
            $orderedAnalyses = [];
            $count = 0;
            foreach ($relatedYtIds as $id) {
                foreach ($analyses as $ana) {
                    if ($ana['youtube_video_id'] === $id) {
                        $orderedAnalyses[] = $ana;
                        $count++;
                        if ($count >= $limit) {
                            break 2;
                        }
                        break;
                    }
                }
            }
            
            return $orderedAnalyses;
        } catch (\PDOException $e) {
            error_log("Failed to get related videos: " . $e->getMessage());
            // 디버깅 목적으로, 오류 시 뷰에서 오류를 확인할 수 있게 배열에 담아 반환해봅니다.
            return [['error' => 'PDO Exception: ' . $e->getMessage()]];
        }
    }

    public function getEmbedding($youtubeVideoId) {
        try {
            $pgDb = Database::getPgDb();
            $stmt = $pgDb->prepare("SELECT embedding FROM video_embeddings WHERE youtube_video_id = ? LIMIT 1");
            $stmt->execute([$youtubeVideoId]);
            $row = $stmt->fetch();
            return $row ? $row['embedding'] : null;
        } catch (\PDOException $e) {
            error_log("Failed to get embedding: " . $e->getMessage());
            return null;
        }
    }
}
