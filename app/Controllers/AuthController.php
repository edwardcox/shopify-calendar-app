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

    public function showLoginForm($error = null) {
        include __DIR__ . '/../Views/login.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $this->showLoginForm("Username and password are required.");
                return;
            }

            $user = $this->user->findByUsername($username);

            if ($user) {
                if (password_verify($password, $user['password'])) {
                    // Password is hashed and correct
                    $this->loginUser($user);
                } elseif ($password === $user['password']) {
                    // Password is stored in plain text and correct
                    // Update the password to be hashed
                    $this->user->updatePassword($user['id'], $password);
                    $this->loginUser($user);
                } else {
                    $this->showLoginForm("Invalid username or password.");
                }
            } else {
                $this->showLoginForm("Invalid username or password.");
            }
        } else {
            $this->showLoginForm();
        }
    }

    private function loginUser($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: /shop-calendar-new/dashboard");
        exit;
    }

    public function logout() {
        session_destroy();
        header("Location: /shop-calendar-new/login");
        exit;
    }
}