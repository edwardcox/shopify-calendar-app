<?php

namespace App\Controllers;

use App\Models\User;

class UserController {
    private $db;
    private $user;

    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($db);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $email = $_POST['email'] ?? '';

            if ($this->user->create($username, $password, $email)) {
                header('Location: /login');
                exit;
            } else {
                $error = "Failed to create user";
            }
        }

        include __DIR__ . '/../Views/user_form.php';
    }
}