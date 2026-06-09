<?php
namespace App\Controllers;

use App\Models\AnalysisModel;
use App\Views\View;

class AnalysisController {
    public function index() {
        $model = new AnalysisModel();
        
        $searchType = $_GET['searchType'] ?? 'title';
        $keyword = $_GET['keyword'] ?? '';
        $orderBy = $_GET['orderBy'] ?? 'created_at';
        $orderDir = $_GET['orderDir'] ?? 'DESC';
        
        $filters = [
            'searchType' => $searchType,
            'keyword' => $keyword,
            'orderBy' => $orderBy,
            'orderDir' => $orderDir
        ];
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 15;
        
        $analyses = $model->searchPaginated($filters, $page, $limit);
        $totalCount = $model->getSearchTotalCount($filters);
        
        View::render('analysis/index', [
            'analyses' => $analyses,
            'totalCount' => $totalCount,
            'currentPage' => $page,
            'limit' => $limit,
            'filters' => $filters
        ]);
    }

    public function getAnalysisData() {
        header('Content-Type: application/json');
        
        $searchType = $_GET['searchType'] ?? 'title';
        $keyword = $_GET['keyword'] ?? '';
        $orderBy = $_GET['orderBy'] ?? 'created_at';
        $orderDir = $_GET['orderDir'] ?? 'DESC';
        $idOnly = filter_var($_GET['idOnly'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $filters = [
            'searchType' => $searchType,
            'keyword' => $keyword,
            'orderBy' => $orderBy,
            'orderDir' => $orderDir
        ];
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 15;
        
        if ($page < 1) {
            $page = 1;
        }
        
        $model = new AnalysisModel();
        $totalCount = $model->getSearchTotalCount($filters);
        $totalPages = ceil($totalCount / $limit) ?: 1;
        
        if ($idOnly) {
            $analysisIds = $model->searchPaginatedIds($filters, $page, $limit);

            echo json_encode([
                'ids' => $analysisIds,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalCount' => $totalCount,
                'limit' => $limit
            ]);
            exit;
        }

        $analyses = $model->searchPaginated($filters, $page, $limit);
        
        echo json_encode([
            'data' => $analyses,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
            'limit' => $limit
        ]);
        exit;
    }

    public function detail() {
        $id = $_GET['id'] ?? null;
        $page = $_GET['page'] ?? 1;
        $searchType = $_GET['searchType'] ?? 'title';
        $keyword = $_GET['keyword'] ?? '';
        $orderBy = $_GET['orderBy'] ?? 'created_at';
        $orderDir = $_GET['orderDir'] ?? 'DESC';
        
        $queryParams = http_build_query([
            'page' => $page,
            'searchType' => $searchType,
            'keyword' => $keyword,
            'orderBy' => $orderBy,
            'orderDir' => $orderDir
        ]);

        if (!$id) {
            header('Location: /analysis?' . $queryParams);
            exit;
        }

        $model = new AnalysisModel();
        $analysis = $model->getById($id);

        if (!$analysis) {
            die("Analysis not found.");
        }

        $tasks = $model->getTasksByAnalysisId($id);
        $relatedVideos = $model->getRelatedVideos($analysis['youtube_video_id'] ?? '');
        $currentEmbedding = $model->getEmbedding($analysis['youtube_video_id'] ?? '');

        View::render('analysis/detail', [
            'analysis' => $analysis,
            'tasks' => $tasks,
            'relatedVideos' => $relatedVideos,
            'currentEmbedding' => $currentEmbedding,
            'page' => $page,
            'searchParams' => [
                'searchType' => $searchType,
                'keyword' => $keyword,
                'orderBy' => $orderBy,
                'orderDir' => $orderDir
            ],
            'queryString' => $queryParams
        ]);
    }

    public function delete() {
        $page = $_POST['page'] ?? 1;
        $searchType = $_POST['searchType'] ?? 'title';
        $keyword = $_POST['keyword'] ?? '';
        $orderBy = $_POST['orderBy'] ?? 'created_at';
        $orderDir = $_POST['orderDir'] ?? 'DESC';
        
        $queryParams = http_build_query([
            'page' => $page,
            'searchType' => $searchType,
            'keyword' => $keyword,
            'orderBy' => $orderBy,
            'orderDir' => $orderDir
        ]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /analysis?' . $queryParams);
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: /analysis?' . $queryParams);
            exit;
        }

        $model = new AnalysisModel();
        $model->deleteAnalysis($id);

        header('Location: /analysis?' . $queryParams);
        exit;
    }
}
