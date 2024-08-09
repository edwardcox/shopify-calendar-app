<?php

namespace App\Models;

class Event {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($userId, $title, $description, $startDate, $endDate, $categoryId, $recurrence, $recurrenceEnd, $reminders = []) {
        $this->db->beginTransaction();

        try {
            $sql = "INSERT INTO events (user_id, title, description, start_date, end_date, category_id, recurrence, recurrence_end) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $title, $description, $startDate, $endDate, $categoryId, $recurrence, $recurrenceEnd]);
            
            $eventId = $this->db->lastInsertId();

            $reminderModel = new Reminder($this->db);
            foreach ($reminders as $reminder) {
                $reminderTime = (new \DateTime($startDate))->sub(new \DateInterval($reminder['time']));
                $reminderModel->create($eventId, $userId, $reminderTime->format('Y-m-d H:i:s'), $reminder['type']);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error creating event: " . $e->getMessage());
            return false;
        }
    }


    public function update($id, $userId, $title, $description, $startDate, $endDate, $categoryId, $recurrence, $recurrenceEnd) {
        $sql = "UPDATE events 
                SET title = ?, description = ?, start_date = ?, end_date = ?, category_id = ?, recurrence = ?, recurrence_end = ? 
                WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$title, $description, $startDate, $endDate, $categoryId, $recurrence, $recurrenceEnd, $id, $userId]);
    }

    public function delete($id, $userId, $deleteAll = false) {
        if ($deleteAll) {
            // First, get the recurrence information for the event
            $sql = "SELECT recurrence, recurrence_end FROM events WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id, $userId]);
            $event = $stmt->fetch();

            if ($event && $event['recurrence'] !== 'none') {
                // Delete all recurring instances
                $sql = "DELETE FROM events WHERE user_id = ? AND 
                        ((id = ?) OR 
                        (start_date >= (SELECT start_date FROM events WHERE id = ?) AND 
                         recurrence = ? AND recurrence_end = ?))";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$userId, $id, $id, $event['recurrence'], $event['recurrence_end']]);
            }
        }
        
        // If not deleting all or it's not a recurring event, just delete the single event
        $sql = "DELETE FROM events WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id, $userId]);
    }

    public function getByUserId($userId) {
        $sql = "SELECT e.*, c.name as category_name, c.color as category_color 
                FROM events e 
                LEFT JOIN categories c ON e.category_id = c.id 
                WHERE e.user_id = ? 
                ORDER BY e.start_date";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $events = $stmt->fetchAll();

        // Expand recurring events
        $expandedEvents = [];
        foreach ($events as $event) {
            $expandedEvents = array_merge($expandedEvents, $this->expandRecurringEvent($event));
        }

        return $expandedEvents;
    }

    private function expandRecurringEvent($event) {
        if ($event['recurrence'] === 'none') {
            return [$event];
        }

        $expandedEvents = [];
        $startDate = new \DateTime($event['start_date']);
        $endDate = new \DateTime($event['end_date']);
        $recurrenceEnd = $event['recurrence_end'] ? new \DateTime($event['recurrence_end']) : null;
        $interval = $this->getRecurrenceInterval($event['recurrence']);

        while (true) {
            $expandedEvents[] = array_merge($event, [
                'start_date' => $startDate->format('Y-m-d H:i:s'),
                'end_date' => $endDate->format('Y-m-d H:i:s')
            ]);

            $startDate->add($interval);
            $endDate->add($interval);

            if ($recurrenceEnd && $startDate > $recurrenceEnd) {
                break;
            }

            // Limit to prevent infinite loops
            if (count($expandedEvents) > 100) {
                break;
            }
        }

        return $expandedEvents;
    }

    private function getRecurrenceInterval($recurrence) {
        switch ($recurrence) {
            case 'daily':
                return new \DateInterval('P1D');
            case 'weekly':
                return new \DateInterval('P1W');
            case 'monthly':
                return new \DateInterval('P1M');
            case 'yearly':
                return new \DateInterval('P1Y');
            default:
                return new \DateInterval('P1D');
        }
    }
}