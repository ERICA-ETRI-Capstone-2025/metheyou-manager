<?php
namespace App\Controllers;

use App\Views\View;
use App\Models\AccountModel;

class AuthController {
    public function login() {
        View::render('auth/login');
    }

    public function loginSubmit() {
        $user = $_POST['username'] ?? '';
        $pass = $_POST['password'] ?? '';

        $account = AccountModel::getAccountByUsername($user);

        if ($account && password_verify($pass, $account['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $account['id'];
            $_SESSION['user_role'] = $account['role'];
            $_SESSION['username'] = $account['username'];
            header('Location: /');
            exit;
        } else {
            $error = "Invalid credentials";
            View::render('auth/login', ['error' => $error]);
        }
    }

    public function logout() {
        session_destroy();
        header('Location: /');
        exit;
    }
}
