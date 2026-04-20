<?php
namespace App\Controllers;

use App\Views\View;
use App\Models\EnvModel;

class SettingsController {
    public function envIndex() {
        // 권한 확인 (Super Admin만 접근 가능)
        $isSuperAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Super Admin');
        if (!$isSuperAdmin) {
            header('Location: /');
            exit;
        }

        $envVars = EnvModel::getEnvVariables();

        View::render('settings/env', [
            'envVars' => $envVars
        ]);
    }

    public function envStore() {
        $isSuperAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Super Admin');
        if (!$isSuperAdmin) {
            header('Location: /');
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
        $isSuperAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Super Admin');
        if (!$isSuperAdmin) {
            header('Location: /');
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