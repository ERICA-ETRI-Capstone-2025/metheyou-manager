<?php
namespace App\Controllers;

use App\Views\View;
use App\Models\EnvModel;
use App\Controllers\AuthController;
use App\Controllers\ErrorController;

class SettingsController {
    public function envIndex() {
        // 권한 확인 (Super Admin만 접근 가능)
        if (!AuthController::hasRole('Super Admin')) {
            $error = new ErrorController();
            $error->forbidden();
            exit;
        }

        $envVars = EnvModel::getEnvVariables();

        View::render('settings/env', [
            'envVars' => $envVars
        ]);
    }

    public function envStore() {
        if (!AuthController::hasRole('Super Admin')) {
            $error = new ErrorController();
            $error->forbidden();
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $key = $_POST['key'] ?? '';
            $value = $_POST['value'] ?? '';

            if (!empty($key) && preg_match('/^[A-Z0-9_]+$/', $key)) {
                EnvModel::updateEnvVariable($key, $value);
            }
        }
        header('Location: /settings/env');
        exit;
    }

    public function envDelete() {
        if (!AuthController::hasRole('Super Admin')) {
            $error = new ErrorController();
            $error->forbidden();
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $key = $_POST['key'] ?? '';
            if (!empty($key)) {
                EnvModel::deleteEnvVariable($key);
            }
        }
        header('Location: /settings/env');
        exit;
    }
}