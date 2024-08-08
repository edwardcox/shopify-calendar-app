<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

define('DB_HOST', $_ENV['DB_HOST']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);

define('SHOPIFY_API_KEY', $_ENV['SHOPIFY_API_KEY']);
define('SHOPIFY_API_SECRET', $_ENV['SHOPIFY_API_SECRET']);
define('SHOPIFY_SCOPE', $_ENV['SHOPIFY_SCOPE']);

define('APP_URL', $_ENV['APP_URL']);