<?php
namespace App\Core;

use App\Controllers\AuthController;
use App\Controllers\AnalysisController;
use App\Controllers\AccountController;
use App\Controllers\SettingsController;
use App\Controllers\ErrorController;

class Router {
    public function run() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            $controller = new AuthController();
            if ($uri === '/login' && $method === 'POST') {
                $controller->loginSubmit();
            } else {
                $controller->login();
            }
            return;
        }

        switch ($uri) {
            case '/':
            case '/analysis':
                $controller = new AnalysisController();
                $controller->index();
                break;
            case '/analysis/detail':
                $controller = new AnalysisController();
                $controller->detail();
                break;
            case '/analysis/delete':
                $controller = new AnalysisController();
                $controller->delete();
                break;
            case '/api/analysis':
                $controller = new AnalysisController();
                $controller->getAnalysisData();
                break;
            case '/accounts':
                $controller = new AccountController();
                $controller->index();
                break;
            case '/accounts/store':
                $controller = new AccountController();
                $controller->store();
                break;
            case '/accounts/delete':
                $controller = new AccountController();
                $controller->delete();
                break;
            case '/logout':
                $controller = new AuthController();
                $controller->logout();
                break;
            case '/settings/env':
                $controller = new SettingsController();
                $controller->envIndex();
                break;
            case '/settings/env/store':
                $controller = new SettingsController();
                $controller->envStore();
                break;
            case '/settings/env/delete':
                $controller = new SettingsController();
                $controller->envDelete();
                break;
            default:
                $controller = new ErrorController();
                $controller->notFound();                
                break;
        }
    }
}
