<?php

namespace App\Models;

class Event {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($userId, $title, $description, $startDate, $endDate) {
        $sql = "INSERT INTO events (user_id, title, description, start_date, end_date) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $title, $description, $startDate, $endDate]);
    }

    public function getByUserId($userId) {
        $sql = "SELECT * FROM events WHERE user_id = ? ORDER BY start_date";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getById($id, $userId) {
        $sql = "SELECT * FROM events WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }

    public function update($id, $userId, $title, $description, $startDate, $endDate) {
        $sql = "UPDATE events SET title = ?, description = ?, start_date = ?, end_date = ? 
                WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$title, $description, $startDate, $endDate, $id, $userId]);
    }

    public function delete($id, $userId) {
        $sql = "DELETE FROM events WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id, $userId]);
    }
}