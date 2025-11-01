<?php
/**
 * Get Roles API
 * Returns list of available roles
 */

session_start();

// Prevent caching - force fresh data always
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Get database connection
require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query("SELECT id, name, description FROM roles ORDER BY id ASC");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $formattedRoles = [];
    foreach ($roles as $role) {
        $formattedRoles[] = [
            'id' => (int)$role['id'],
            'name' => $role['name'],
            'description' => $role['description'] ?? ''
        ];
    }
    
    echo json_encode([
        'success' => true,
        'roles' => $formattedRoles
    ]);
    
} catch (Exception $e) {
    error_log('Get roles error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch roles: ' . $e->getMessage()
    ]);
}
?>


