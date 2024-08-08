<?php

namespace App\Controllers;

use App\Models\User;

class AuthController {
    private $db;
    private $user;

    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($db);
    }

    public function showLoginForm() {
        include __DIR__ . '/../Views/login.php';
    }

    public function login() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->user->findByUsername($username);

        if ($user && $this->user->validatePassword($user, $password)) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: /dashboard');
            exit;
        } else {
            $error = "Invalid username or password";
            include __DIR__ . '/../Views/login.php';
        }
    }

    public function logout() {
        session_destroy();
        header('Location: /login');
        exit;
    }
}