<?php

namespace App\Models;

class Event {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($userId, $title, $description, $startDate, $endDate, $categoryId) {
        $sql = "INSERT INTO events (user_id, title, description, start_date, end_date, category_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $title, $description, $startDate, $endDate, $categoryId]);
    }

    public function getByUserId($userId) {
        $sql = "SELECT e.*, c.name as category_name, c.color as category_color 
                FROM events e 
                LEFT JOIN categories c ON e.category_id = c.id 
                WHERE e.user_id = ? 
                ORDER BY e.start_date";
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

    public function update($id, $userId, $title, $description, $startDate, $endDate, $categoryId) {
        $sql = "UPDATE events 
                SET title = ?, description = ?, start_date = ?, end_date = ?, category_id = ? 
                WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$title, $description, $startDate, $endDate, $categoryId, $id, $userId]);
    }

    public function delete($id, $userId) {
        $sql = "DELETE FROM events WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id, $userId]);
    }
}