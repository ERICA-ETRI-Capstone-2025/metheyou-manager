<?php
namespace App\Controllers;

use App\Views\View;
use App\Models\AccountModel;
use App\Controllers\ErrorController;

class AccountController {
    public function index() {
        $accounts = AccountModel::getAllAccounts();

        View::render('accounts/index', [
            'accounts' => $accounts
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'Manager';

            if ($username && $email && $password) {
                try {
                    AccountModel::addAccount($username, $email, $password, $role);
                } catch (\PDOException $e) {
                    // Handle duplicate username or other errors
                }
            }
        }
        header('Location: /accounts');
        exit;
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            if ($id) {
                $isSelf = (isset($_SESSION['user_id']) && $id == $_SESSION['user_id']);
                $isSuperAdmin = \App\Controllers\AuthController::hasRole('Super Admin');

                // 본인이거나 Super Admin인 경우에만 삭제 허용
                if ($isSelf || $isSuperAdmin) {
                    AccountModel::deleteAccount($id);

                    // 자기 자신을 삭제했을 경우 로그아웃 후 홈으로 리다이렉트
                    if ($isSelf) {
                        session_destroy();
                        header('Location: /');
                        exit;
                    }
                } else {
                    $error = new ErrorController();
                    $error->forbidden();
                    exit;
                }
            }
        }
        header('Location: /accounts');
        exit;
    }
}
