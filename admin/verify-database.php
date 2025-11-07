<?php
/**
 * Database Verification Page
 * Checks if all required columns exist in the database
 */

// Require database connection
require_once __DIR__ . '/config/db.php';

header('Content-Type: application/json');

try {
    // Check if mobile column exists in users table
    $stmt = $pdo->query("
        SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'users'
          AND COLUMN_NAME = 'mobile'
    ");
    
    $mobileColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get sample user data
    $userStmt = $pdo->query("
        SELECT id, username, full_name, email, mobile, role_id, status 
        FROM users 
        LIMIT 5
    ");
    $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all columns in users table
    $columnsStmt = $pdo->query("
        SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'users'
        ORDER BY ORDINAL_POSITION
    ");
    $allColumns = $columnsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'mobile_column_exists' => !empty($mobileColumn),
        'mobile_column_details' => $mobileColumn ?: null,
        'sample_users' => $users,
        'all_columns' => $allColumns,
        'database_name' => $pdo->query('SELECT DATABASE()')->fetchColumn()
    ], JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>








