<?php
/**
 * Delete Category API
 * Deletes a category from the database
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
    echo json_encode(['success' => false, 'error' => 'Category ID is required']);
    exit;
}

try {
    require_once __DIR__ . '/../config/db.php';
    
    // Check if category exists
    $checkStmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
    $checkStmt->execute([$data['id']]);
    if (!$checkStmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Category not found']);
        exit;
    }
    
    // Check if category has products
    $productStmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ?");
    $productStmt->execute([$data['id']]);
    $productCount = $productStmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($productCount > 0) {
        echo json_encode([
            'success' => false, 
            'error' => "Cannot delete category. It has {$productCount} product(s) associated with it."
        ]);
        exit;
    }
    
    // Delete category
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$data['id']]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Category deleted successfully'
    ]);
    
} catch (Exception $e) {
    error_log('Delete category error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to delete category: ' . $e->getMessage()
    ]);
}
?>



