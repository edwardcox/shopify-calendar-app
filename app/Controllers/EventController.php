<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\Category;

class EventController {
    private $db;
    private $event;
    private $category;

    public function __construct($db) {
        $this->db = $db;
        $this->event = new Event($db);
        $this->category = new Category($db);
    }

    public function create() {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }

            $userId = $_SESSION['user_id'] ?? null;
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $startDate = $_POST['start_date'] ?? '';
            $endDate = $_POST['end_date'] ?? '';
            $categoryId = $_POST['category_id'] ?? null;
            $recurrence = $_POST['recurrence'] ?? 'none';
            $recurrenceEnd = $_POST['recurrence_end'] ?? null;
            $reminders = json_decode($_POST['reminders'] ?? '[]', true);

            if (!$userId) {
                throw new \Exception('User not authenticated');
            }

            if (empty($title) || empty($startDate) || empty($endDate)) {
                throw new \Exception('Missing required fields');
            }

            // Log incoming data for debugging
            error_log("Creating event: " . json_encode($_POST));

            if ($this->event->create($userId, $title, $description, $startDate, $endDate, $categoryId, $recurrence, $recurrenceEnd, $reminders)) {
                echo json_encode(['success' => true, 'message' => 'Event created successfully']);
            } else {
                throw new \Exception('Failed to create event');
            }
        } catch (\Exception $e) {
            error_log('Error in EventController::create: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function update() {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }
    
            $userId = $_SESSION['user_id'] ?? null;
            $eventId = $_POST['id'] ?? null;
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $startDate = $_POST['start_date'] ?? '';
            $endDate = $_POST['end_date'] ?? '';
            $categoryId = $_POST['category_id'] === 'null' ? null : ($_POST['category_id'] ?? null);
            $recurrence = $_POST['recurrence'] ?? 'none';
            $recurrenceEnd = $_POST['recurrence_end'] ?? null;
    
            if (!$userId) {
                throw new \Exception('User not authenticated');
            }
    
            if (!$eventId || empty($title) || empty($startDate) || empty($endDate)) {
                throw new \Exception('Missing required fields');
            }
    
            // Log incoming data for debugging
            error_log("Updating event: " . json_encode([
                'id' => $eventId,
                'title' => $title,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'category_id' => $categoryId,
                'recurrence' => $recurrence,
                'recurrence_end' => $recurrenceEnd
            ]));
    
            if ($this->event->update($eventId, $userId, $title, $description, $startDate, $endDate, $categoryId, $recurrence, $recurrenceEnd)) {
                echo json_encode(['success' => true, 'message' => 'Event updated successfully']);
            } else {
                throw new \Exception('Failed to update event');
            }
        } catch (\Exception $e) {
            error_log('Error in EventController::update: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    
    private function convertToMySQLDateTime($isoDate) {
        $date = new \DateTime($isoDate);
        $date->setTimezone(new \DateTimeZone('UTC')); // Ensure UTC
        return $date->format('Y-m-d H:i:s');
    }
    
    private function formatDateForMySQL($dateString) {
        $date = new \DateTime($dateString);
        return $date->format('Y-m-d H:i:s');
    }

    public function getEvents() {
        header('Content-Type: application/json');
        
        try {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                throw new \Exception('User not authenticated');
            }
    
            $events = $this->event->getByUserId($userId);
            $formattedEvents = array_map(function($event) {
                return [
                    'id' => $event['id'],
                    'title' => $event['title'],
                    'start' => $event['start_date'],
                    'end' => $event['end_date'],
                    'extendedProps' => [
                        'description' => $event['description'],
                        'category_id' => $event['category_id'],
                        'category_color' => $event['category_color'] ?? null,
                        'recurrence' => $event['recurrence'],
                        'recurrence_end' => $event['recurrence_end']
                    ]
                ];
            }, $events);
            
            echo json_encode($formattedEvents);
        } catch (\Exception $e) {
            error_log('Error in EventController::getEvents: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function delete() {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }

            $userId = $_SESSION['user_id'] ?? null;
            $eventId = $_POST['id'] ?? null;
            $deleteAll = $_POST['delete_all'] ?? false;

            if (!$userId) {
                throw new \Exception('User not authenticated');
            }

            if (!$eventId) {
                throw new \Exception('Missing event ID');
            }

            if ($this->event->delete($eventId, $userId, $deleteAll)) {
                echo json_encode(['success' => true, 'message' => 'Event deleted successfully']);
            } else {
                throw new \Exception('Failed to delete event');
            }
        } catch (\Exception $e) {
            error_log('Error in EventController::delete: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getCategories() {
        header('Content-Type: application/json');
        
        try {
            $categories = $this->category->getAll();
            echo json_encode($categories);
        } catch (\Exception $e) {
            error_log('Error in EventController::getCategories: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}