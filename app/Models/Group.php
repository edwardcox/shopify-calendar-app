<?php

namespace App\Models;

class Group {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($name, $createdBy) {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO groups (name) VALUES (?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$name]);
            $groupId = $this->db->lastInsertId();

            $sql = "INSERT INTO user_groups (user_id, group_id) VALUES (?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$createdBy, $groupId]);

            $this->db->commit();
            return $groupId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function getById($id) {
        $sql = "SELECT * FROM groups WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAll() {
        $sql = "SELECT * FROM groups";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function update($id, $name) {
        $sql = "UPDATE groups SET name = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name, $id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM groups WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function addUser($groupId, $userId) {
        $sql = "INSERT INTO user_groups (user_id, group_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $groupId]);
    }

    public function removeUser($groupId, $userId) {
        $sql = "DELETE FROM user_groups WHERE user_id = ? AND group_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $groupId]);
    }

    public function getUsers($groupId) {
        $sql = "SELECT u.* FROM users u
                JOIN user_groups ug ON u.id = ug.user_id
                WHERE ug.group_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$groupId]);
        return $stmt->fetchAll();
    }
}