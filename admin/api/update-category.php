<?php
/**
 * Update Category API
 * Updates an existing category in the database
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
if (empty($data['id']) || empty($data['name'])) {
    echo json_encode(['success' => false, 'error' => 'ID and category name are required']);
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
    
    // Check for duplicate name (excluding current category)
    $dupStmt = $pdo->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
    $dupStmt->execute([$data['name'], $data['id']]);
    if ($dupStmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Another category with this name already exists']);
        exit;
    }
    
    // Update category
    $stmt = $pdo->prepare("
        UPDATE categories 
        SET name = ?, description = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        trim($data['name']),
        trim($data['description'] ?? ''),
        $data['id']
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Category updated successfully'
    ]);
    
} catch (Exception $e) {
    error_log('Update category error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to update category: ' . $e->getMessage()
    ]);
}
?>



