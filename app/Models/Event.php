<?php

namespace App\Models;

class Event {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($groupId, $title, $description, $startDate, $endDate, $createdBy) {
        $sql = "INSERT INTO events (group_id, title, description, start_date, end_date, created_by) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$groupId, $title, $description, $startDate, $endDate, $createdBy]);
    }

    public function getById($id) {
        $sql = "SELECT * FROM events WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByGroupId($groupId) {
        $sql = "SELECT * FROM events WHERE group_id = ? ORDER BY start_date";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$groupId]);
        return $stmt->fetchAll();
    }

    public function update($id, $title, $description, $startDate, $endDate) {
        $sql = "UPDATE events SET title = ?, description = ?, start_date = ?, end_date = ? 
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$title, $description, $startDate, $endDate, $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM events WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}