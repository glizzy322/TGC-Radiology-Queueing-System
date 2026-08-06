<?php

namespace App\Controllers;

use App\Services\AuthService;

class AuthController
{
    public function showLogin()
    {
        if (AuthService::check()) {
            header('Location: /dashboard');
            exit;
        }
        require dirname(__DIR__, 2) . '/resources/views/auth/login.php';
    }

    public function processLogin()
    {
        $name = $_POST['name'] ?? '';
        $password = $_POST['password'] ?? '';

        $auth = new AuthService();
        if ($auth->login($name, $password)) {
            header('Location: /dashboard');
            exit;
        }

        $error = "Invalid username or password.";
        require dirname(__DIR__, 2) . '/resources/views/auth/login.php';
    }

    public function logout()
    {
        $auth = new AuthService();
        $auth->logout();
        header('Location: /login');
        exit;
    }
}
