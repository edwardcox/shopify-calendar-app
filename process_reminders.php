<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$db = getDBConnection();
$reminderService = new App\Services\ReminderService($db);
$reminderService->processReminders();