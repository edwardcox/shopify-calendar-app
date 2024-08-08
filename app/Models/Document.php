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
            // Log the error
            error_log($e->getMessage());
            return false;
        }
    }

    public function getByEventId($eventId) {
        $sql = "SELECT * FROM documents WHERE event_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }

    public function delete($id) {
        $sql = "SELECT file_path FROM documents WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $document = $stmt->fetch();

        if ($document && $this->filesystem->fileExists($document['file_path'])) {
            $this->filesystem->delete($document['file_path']);
        }

        $sql = "DELETE FROM documents WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function download($id) {
        $sql = "SELECT * FROM documents WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
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