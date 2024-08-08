<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

use App\Controllers\AuthController;
use App\Controllers\EventController;

session_start();

$db = getDBConnection();

$route = $_SERVER['REQUEST_URI'];

// Simple router
if ($route === '/login' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $authController = new AuthController($db);
    $authController->showLoginForm();
} elseif ($route === '/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController = new AuthController($db);
    $authController->login();
} elseif ($route === '/logout') {
    $authController = new AuthController($db);
    $authController->logout();
} elseif (strpos($route, '/calendar') === 0) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    $eventController = new EventController($db);
    $groupId = $_GET['group_id'] ?? null;
    $eventController->index($groupId);
} elseif ($route === '/event/create') {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    $eventController = new EventController($db);
    $eventController->create();
} elseif (preg_match('/^\/event\/edit\/(\d+)$/', $route, $matches)) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    $eventController = new EventController($db);
    $eventController->edit($matches[1]);
} elseif (preg_match('/^\/event\/delete\/(\d+)$/', $route, $matches)) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    $eventController = new EventController($db);
    $eventController->delete($matches[1]);
} elseif (preg_match('/^\/event\/view\/(\d+)$/', $route, $matches)) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    $eventController = new EventController($db);
    $eventController->view($matches[1]);
} elseif (preg_match('/^\/event\/upload-document\/(\d+)$/', $route, $matches)) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    $eventController = new EventController($db);
    $eventController->uploadDocument($matches[1]);
} elseif (preg_match('/^\/document\/delete\/(\d+)$/', $route, $matches)) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    $eventController = new EventController($db);
    $eventController->deleteDocument($matches[1]);
} elseif (preg_match('/^\/document\/download\/(\d+)$/', $route, $matches)) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    $eventController = new EventController($db);
    $eventController->downloadDocument($matches[1]);
} else {
    // Add more routes here as we develop the application
    echo "404 - Page not found";
}