<?php

namespace App\Controllers;

use App\Models\Group;

class GroupController {
    private $db;
    private $group;

    public function __construct($db) {
        $this->db = $db;
        $this->group = new Group($db);
    }

    public function index() {
        $groups = $this->group->getAll();
        include __DIR__ . '/../Views/group_list.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $createdBy = $_SESSION['user_id'];

            if ($this->group->create($name, $createdBy)) {
                header("Location: /groups");
                exit;
            } else {
                $error = "Failed to create group";
            }
        }

        include __DIR__ . '/../Views/group_form.php';
    }

    public function edit($id) {
        $group = $this->group->getById($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];

            if ($this->group->update($id, $name)) {
                header("Location: /groups");
                exit;
            } else {
                $error = "Failed to update group";
            }
        }

        include __DIR__ . '/../Views/group_form.php';
    }

    public function delete($id) {
        if ($this->group->delete($id)) {
            header("Location: /groups");
            exit;
        } else {
            $error = "Failed to delete group";
            $groups = $this->group->getAll();
            include __DIR__ . '/../Views/group_list.php';
        }
    }

    public function manageUsers($id) {
        $group = $this->group->getById($id);
        $groupUsers = $this->group->getUsers($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'];
            $userId = $_POST['user_id'];

            if ($action === 'add') {
                $this->group->addUser($id, $userId);
            } elseif ($action === 'remove') {
                $this->group->removeUser($id, $userId);
            }

            header("Location: /groups/manage-users/$id");
            exit;
        }

        include __DIR__ . '/../Views/group_manage_users.php';
    }
}