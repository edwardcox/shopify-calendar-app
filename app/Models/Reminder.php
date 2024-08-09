<?php

namespace App\Models;

class Reminder {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($eventId, $userId, $reminderTime, $type) {
        $sql = "INSERT INTO reminders (event_id, user_id, reminder_time, type) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$eventId, $userId, $reminderTime, $type]);
    }

    public function getPendingReminders() {
        $sql = "SELECT r.*, e.title as event_title, e.start_date as event_start, u.email 
                FROM reminders r 
                JOIN events e ON r.event_id = e.id 
                JOIN users u ON r.user_id = u.id 
                WHERE r.status = 'pending' AND r.reminder_time <= NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function markAsSent($id) {
        $sql = "UPDATE reminders SET status = 'sent' WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}