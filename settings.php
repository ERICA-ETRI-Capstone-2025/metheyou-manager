<?php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// MariaDB 설정
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'metheyou');
define('DB_USER', $_ENV['DB_USER'] ?? 'metheyou');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

// PostgreSQL 설정
define('POSTGRES_DSN', $_ENV['POSTGRES_DSN'] ?? 'pgsql:host=127.0.0.1;dbname=metheyou');
define('POSTGRES_USER', $_ENV['POSTGRES_USER'] ?? 'metheyou_app');
define('POSTGRES_PASS', $_ENV['POSTGRES_PASS'] ?? '');
