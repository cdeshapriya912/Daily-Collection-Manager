<?php

declare(strict_types=1);

// Database configuration with environment variable support
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_PORT = getenv('DB_PORT') ?: '3306';
$DB_NAME = getenv('DB_NAME') ?: 'daily_m';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: 'root';

// Build DSN with port specification
$dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => false, // Disable persistent connections for better security
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]);
    
    // Verify connection by running a simple query
    $pdo->query("SELECT 1");
    
} catch (Throwable $e) {
    // Log the actual error for debugging
    error_log("Database connection failed: " . $e->getMessage());
    
    // Set appropriate HTTP response code
    http_response_code(500);
    
    // Show generic error message to user
    die('Database connection failed. Please try again later.');
}
