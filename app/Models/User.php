<?php

namespace App\Models;

class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($username, $password, $email) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $hashedPassword, $email]);
    }

    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function validatePassword($user, $password) {
        return password_verify($password, $user['password']);
    }

    public function getGroups($userId) {
        $stmt = $this->db->prepare("
            SELECT g.* FROM groups g
            JOIN user_groups ug ON g.id = ug.group_id
            WHERE ug.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}