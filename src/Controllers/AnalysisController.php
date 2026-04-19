<?php
namespace App\Controllers;

use App\Models\AnalysisModel;
use App\Views\View;

class AnalysisController {
    public function index() {
        $model = new AnalysisModel();
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $analyses = $model->getPaginated($page, 15);
        $totalCount = $model->getTotalCount();
        View::render('analysis/index', [
            'analyses' => $analyses,
            'totalCount' => $totalCount,
            'currentPage' => $page,
            'limit' => 15
        ]);
    }

    public function getAnalysisData() {
        header('Content-Type: application/json');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 15;
        
        if ($page < 1) {
            $page = 1;
        }
        
        $model = new AnalysisModel();
        $analyses = $model->getPaginated($page, $limit);
        $totalCount = $model->getTotalCount();
        $totalPages = ceil($totalCount / $limit);
        
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
        
        if (!$id) {
            header('Location: /analysis?page=' . $page);
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
            'page' => $page
        ]);
    }

    public function delete() {
        $page = $_POST['page'] ?? 1;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /analysis?page=' . $page);
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: /analysis?page=' . $page);
            exit;
        }

        $model = new AnalysisModel();
        $model->deleteAnalysis($id);

        header('Location: /analysis?page=' . $page);
        exit;
    }
}
