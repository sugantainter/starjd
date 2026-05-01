<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Process Memory Limit
|--------------------------------------------------------------------------
|
| Guard production requests from low default memory ceilings. Allow env
| override via APP_MEMORY_LIMIT, otherwise use a safer baseline.
|
*/
$appMemoryLimit = $_ENV['APP_MEMORY_LIMIT'] ?? $_SERVER['APP_MEMORY_LIMIT'] ?? '256M';
if (is_string($appMemoryLimit) && $appMemoryLimit !== '') {
    @ini_set('memory_limit', $appMemoryLimit);
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
