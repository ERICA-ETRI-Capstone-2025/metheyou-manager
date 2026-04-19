<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/settings.php';

use App\Core\Router;

$router = new Router();
$router->run();
