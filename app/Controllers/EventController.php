<?php

namespace App\Controllers;

use App\Models\Event;

class EventController {
    private $db;
    private $event;

    public function __construct($db) {
        $this->db = $db;
        $this->event = new Event($db);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'] ?? null;
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $startDate = $_POST['start_date'] ?? '';
            $endDate = $_POST['end_date'] ?? '';

            if (!$userId) {
                $this->logError("User ID not found in session");
                http_response_code(401);
                echo json_encode(['error' => 'User not authenticated']);
                return;
            }

            if (empty($title) || empty($startDate) || empty($endDate)) {
                $this->logError("Missing required fields: " . json_encode($_POST));
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                return;
            }

            try {
                if ($this->event->create($userId, $title, $description, $startDate, $endDate)) {
                    echo json_encode(['success' => true, 'message' => 'Event created successfully']);
                } else {
                    $this->logError("Failed to create event: " . json_encode($_POST));
                    http_response_code(500);
                    echo json_encode(['error' => 'Failed to create event']);
                }
            } catch (\Exception $e) {
                $this->logError("Exception occurred: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                http_response_code(500);
                echo json_encode(['error' => 'An unexpected error occurred']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'] ?? null;
            $eventId = $_POST['id'] ?? null;
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $startDate = $_POST['start_date'] ?? '';
            $endDate = $_POST['end_date'] ?? '';

            if (!$userId || !$eventId) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid request']);
                return;
            }

            if (empty($title) || empty($startDate) || empty($endDate)) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                return;
            }

            if ($this->event->update($eventId, $userId, $title, $description, $startDate, $endDate)) {
                echo json_encode(['success' => true, 'message' => 'Event updated successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update event']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
    }
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'] ?? null;
            $eventId = $_POST['id'] ?? null;

            if (!$userId || !$eventId) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid request']);
                return;
            }

            if ($this->event->delete($eventId, $userId)) {
                echo json_encode(['success' => true, 'message' => 'Event deleted successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to delete event']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
        }
    }
    private function logError($message) {
        $logFile = __DIR__ . '/../../error.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
    }

    public function getEvents() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['error' => 'User not authenticated']);
            return;
        }
    
        $events = $this->event->getByUserId($userId);
        $formattedEvents = array_map(function($event) {
            return [
                'id' => $event['id'],
                'title' => $event['title'],
                'start' => $event['start_date'],
                'end' => $event['end_date'],
                'description' => $event['description']
            ];
        }, $events);
    
        echo json_encode($formattedEvents);
    }
}