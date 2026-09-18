<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Support both the normal Laravel public directory and Hostinger's separate
// public_html document root used by the production deployment.
$applicationPath = is_file(__DIR__.'/../vendor/autoload.php')
    ? dirname(__DIR__)
    : dirname(__DIR__, 3).'/isetial_app';

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = $applicationPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require $applicationPath.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once $applicationPath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
