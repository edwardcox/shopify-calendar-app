<?php

// Enable error reporting
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

function debug_log($message) {
    $timestamp = date("Y-m-d H:i:s");
    $log_message = "[$timestamp] $message\n";
    file_put_contents(__DIR__ . '/debug.log', $log_message, FILE_APPEND);
}

function checkClassExists($className) {
    if (class_exists($className)) {
        debug_log("Class $className exists");
    } else {
        debug_log("Class $className does not exist");
    }
}

debug_log("Starting index.php");

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

debug_log("Required files loaded");

debug_log("Starting class imports");

use App\Controllers\AuthController;
use App\Controllers\EventController;
use App\Controllers\UserController;
use App\ShopifyIntegration;

checkClassExists('App\Controllers\AuthController');
checkClassExists('App\Controllers\EventController');
checkClassExists('App\Controllers\UserController');
checkClassExists('App\ShopifyIntegration');

debug_log("Class existence check completed");
debug_log("Class imports completed");

session_start();

debug_log("Session started");

try {
    $db = getDBConnection();
    debug_log("Database connection established");
} catch (Exception $e) {
    debug_log("Database connection failed. Error: " . $e->getMessage());
    die("Database connection failed. Please check the error log for more details.");
}

$shopifyIntegration = new ShopifyIntegration();

debug_log("ShopifyIntegration initialized");

$route = $_SERVER['REQUEST_URI'];
$route = str_replace('/shop-calendar-new', '', $route);
$route = strtok($route, '?'); // Remove query string
debug_log("Processed route: " . $route);

// Shopify OAuth routes
if ($route === '/shopify/auth') {
    debug_log("Entering Shopify auth route");
    $shop = $_GET['shop'] ?? '';
    if (empty($shop)) {
        debug_log("No shop provided for Shopify auth");
        die("Error: No shop provided");
    }
    $authUrl = $shopifyIntegration->getAuthUrl($shop, 'https://apps.shopappdev.net/shop-calendar-new/shopify/callback');
    header("Location: $authUrl");
    exit;
} elseif ($route === '/shopify/callback') {
    debug_log("Entering Shopify callback route");
    $session = $shopifyIntegration->handleCallback($_SERVER);
    $_SESSION['shopify_session'] = $session;
    header("Location: /shop-calendar-new/dashboard");
    exit;
}

// Check if user is authenticated
if (!isset($_SESSION['user_id']) && !in_array($route, ['/', '/login', '/user/add', '/shopify/auth', '/shopify/callback'])) {
    debug_log("User not authenticated, redirecting to login");
    header("Location: /shop-calendar-new/login");
    exit;
}

debug_log("Proceeding to route handling");

// Route handling
switch ($route) {
    case '/':
    case '':
    case '/dashboard':
        debug_log("Entering dashboard route");
        if (!isset($_SESSION['user_id'])) {
            debug_log("User not authenticated, redirecting to login");
            header("Location: /shop-calendar-new/login");
            exit;
        }
        include __DIR__ . '/app/Views/dashboard.php';
        break;

    case '/login':
        debug_log("Entering login route");
        $authController = new AuthController($db);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->login();
        } else {
            $authController->showLoginForm();
        }
        break;

    case '/logout':
        debug_log("Entering logout route");
        $authController = new AuthController($db);
        $authController->logout();
        break;

    case '/event/create':
            debug_log("Entering event create route");
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }
            $eventController = new EventController($db);
            $eventController->create();
            break;
    
    case '/event/get':
            debug_log("Entering event get route");
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }
            $eventController = new EventController($db);
            $eventController->getEvents();
            break;

            case '/event/update':
                debug_log("Entering event update route");
                if (!isset($_SESSION['user_id'])) {
                    http_response_code(401);
                    echo json_encode(['error' => 'Unauthorized']);
                    exit;
                }
                $eventController = new EventController($db);
                $eventController->update();
                break;
        
            case '/event/delete':
                debug_log("Entering event delete route");
                if (!isset($_SESSION['user_id'])) {
                    http_response_code(401);
                    echo json_encode(['error' => 'Unauthorized']);
                    exit;
                }
                $eventController = new EventController($db);
                $eventController->delete();
                break;
    // We'll add more routes for event management here later
    case '/event/get-categories':
        debug_log("Entering get categories route");
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        $eventController = new EventController($db);
        $eventController->getCategories();
        break;
    default:
        debug_log("No matching route found, displaying 404");
        http_response_code(404);
        echo "404 - Page not found";
        break;
}

debug_log("End of index.php");