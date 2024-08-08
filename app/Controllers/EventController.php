<?php

namespace App\Controllers;

use App\Models\Event;
use App\Models\Document;

class EventController {
    private $db;
    private $event;
    private $document;

    public function __construct($db) {
        $this->db = $db;
        $this->event = new Event($db);
        $this->document = new Document($db);
    }

    public function view($id) {
        $event = $this->event->getById($id);
        $documents = $this->document->getByEventId($id);
        include __DIR__ . '/../Views/event_view.php';
    }

    public function uploadDocument($eventId) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
            $file = $_FILES['document'];
            $uploadedBy = $_SESSION['user_id'];

            if ($this->document->upload($eventId, $file, $uploadedBy)) {
                header("Location: /event/view/$eventId");
                exit;
            } else {
                $error = "Failed to upload document";
            }
        }
        
        $event = $this->event->getById($eventId);
        $documents = $this->document->getByEventId($eventId);
        include __DIR__ . '/../Views/event_view.php';
    }

    public function deleteDocument($documentId) {
        $sql = "SELECT event_id FROM documents WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$documentId]);
        $document = $stmt->fetch();

        if ($this->document->delete($documentId)) {
            header("Location: /event/view/{$document['event_id']}");
            exit;
        } else {
            $error = "Failed to delete document";
            $event = $this->event->getById($document['event_id']);
            $documents = $this->document->getByEventId($document['event_id']);
            include __DIR__ . '/../Views/event_view.php';
        }
    }

    public function downloadDocument($documentId) {
        $fileData = $this->document->download($documentId);
        
        if ($fileData) {
            header("Content-Type: {$fileData['mime_type']}");
            header("Content-Disposition: attachment; filename=\"{$fileData['filename']}\"");
            echo $fileData['content'];
            exit;
        } else {
            echo "File not found";
        }
    }
    
    public function index($groupId) {
        $events = $this->event->getByGroupId($groupId);
        include __DIR__ . '/../Views/calendar.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $groupId = $_POST['group_id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $startDate = $_POST['start_date'];
            $endDate = $_POST['end_date'];
            $createdBy = $_SESSION['user_id'];

            if ($this->event->create($groupId, $title, $description, $startDate, $endDate, $createdBy)) {
                header("Location: /calendar?group_id=$groupId");
                exit;
            } else {
                $error = "Failed to create event";
                include __DIR__ . '/../Views/event_form.php';
            }
        } else {
            include __DIR__ . '/../Views/event_form.php';
        }
    }

    public function edit($id) {
        $event = $this->event->getById($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $startDate = $_POST['start_date'];
            $endDate = $_POST['end_date'];

            if ($this->event->update($id, $title, $description, $startDate, $endDate)) {
                header("Location: /calendar?group_id={$event['group_id']}");
                exit;
            } else {
                $error = "Failed to update event";
            }
        }

        include __DIR__ . '/../Views/event_form.php';
    }

    public function delete($id) {
        $event = $this->event->getById($id);
        
        if ($this->event->delete($id)) {
            header("Location: /calendar?group_id={$event['group_id']}");
            exit;
        } else {
            $error = "Failed to delete event";
            include __DIR__ . '/../Views/calendar.php';
        }
    }
}