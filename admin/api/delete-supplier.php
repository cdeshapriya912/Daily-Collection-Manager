<?php
/**
 * Delete Supplier API
 * Deletes a supplier from the database
 */

header('Content-Type: application/json');

session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate required fields
if (empty($data['id'])) {
    echo json_encode(['success' => false, 'error' => 'Supplier ID is required']);
    exit;
}

try {
    require_once __DIR__ . '/../config/db.php';
    
    // Check if supplier exists
    $checkStmt = $pdo->prepare("SELECT id FROM suppliers WHERE id = ?");
    $checkStmt->execute([$data['id']]);
    if (!$checkStmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Supplier not found']);
        exit;
    }
    
    // Delete supplier
    $stmt = $pdo->prepare("DELETE FROM suppliers WHERE id = ?");
    $stmt->execute([$data['id']]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Supplier deleted successfully'
    ]);
    
} catch (Exception $e) {
    error_log('Delete supplier error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to delete supplier: ' . $e->getMessage()
    ]);
}
?>








