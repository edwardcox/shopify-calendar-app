<?php

namespace App\Models;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

class Document {
    private $db;
    private $filesystem;

    public function __construct($db) {
        $this->db = $db;
        $adapter = new LocalFilesystemAdapter(__DIR__ . '/../../public/uploads');
        $this->filesystem = new Filesystem($adapter);
    }

    public function upload($eventId, $file, $uploadedBy) {
        $filename = $file['name'];
        $tmpName = $file['tmp_name'];
        $fileType = $file['type'];

        $newFilename = uniqid() . '_' . $filename;
        $filePath = 'documents/' . $newFilename;

        try {
            $stream = fopen($tmpName, 'r+');
            $this->filesystem->writeStream($filePath, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }

            $sql = "INSERT INTO documents (event_id, filename, file_path, file_type, uploaded_by) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$eventId, $filename, $filePath, $fileType, $uploadedBy]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getByEventId($eventId, $userId) {
        $sql = "SELECT d.* FROM documents d
                JOIN events e ON d.event_id = e.id
                JOIN user_groups ug ON e.group_id = ug.group_id
                WHERE d.event_id = ? AND ug.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$eventId, $userId]);
        return $stmt->fetchAll();
    }

    public function delete($id, $userId) {
        $sql = "SELECT d.file_path FROM documents d
                JOIN events e ON d.event_id = e.id
                JOIN user_groups ug ON e.group_id = ug.group_id
                WHERE d.id = ? AND ug.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $userId]);
        $document = $stmt->fetch();

        if ($document && $this->filesystem->fileExists($document['file_path'])) {
            $this->filesystem->delete($document['file_path']);
        }

        $sql = "DELETE d FROM documents d
                JOIN events e ON d.event_id = e.id
                JOIN user_groups ug ON e.group_id = ug.group_id
                WHERE d.id = ? AND ug.user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id, $userId]);
    }

    public function download($id, $userId) {
        $sql = "SELECT d.* FROM documents d
                JOIN events e ON d.event_id = e.id
                JOIN user_groups ug ON e.group_id = ug.group_id
                WHERE d.id = ? AND ug.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $userId]);
        $document = $stmt->fetch();

        if ($document && $this->filesystem->fileExists($document['file_path'])) {
            return [
                'content' => $this->filesystem->read($document['file_path']),
                'filename' => $document['filename'],
                'mime_type' => $document['file_type']
            ];
        }

        return null;
    }
}