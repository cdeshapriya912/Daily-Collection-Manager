<?php
/**
 * Delete User API
 * Deletes a staff user
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $userId = isset($data['id']) ? (int)$data['id'] : 0;
    
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
        exit;
    }
    
    // Prevent deleting yourself
    if ($userId == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'error' => 'You cannot delete your own account']);
        exit;
    }
    
    // Check if user exists
    $checkStmt = $pdo->prepare("SELECT id, username FROM users WHERE id = ?");
    $checkStmt->execute([$userId]);
    $user = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit;
    }
    
    // Delete user
    $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $deleteStmt->execute([$userId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Staff member deleted successfully',
        'deleted_id' => $userId
    ]);
    
} catch (PDOException $e) {
    error_log('Delete user error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to delete user: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log('Delete user error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to delete user: ' . $e->getMessage()
    ]);
}
?>


