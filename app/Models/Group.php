<?php

namespace App\Models;

class Group {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($name) {
        $stmt = $this->db->prepare("INSERT INTO groups (name) VALUES (?)");
        return $stmt->execute([$name]);
    }

    public function addUser($groupId, $userId) {
        $stmt = $this->db->prepare("INSERT INTO user_groups (group_id, user_id) VALUES (?, ?)");
        return $stmt->execute([$groupId, $userId]);
    }

    public function getUsers($groupId) {
        $stmt = $this->db->prepare("
            SELECT u.* FROM users u
            JOIN user_groups ug ON u.id = ug.user_id
            WHERE ug.group_id = ?
        ");
        $stmt->execute([$groupId]);
        return $stmt->fetchAll();
    }
}