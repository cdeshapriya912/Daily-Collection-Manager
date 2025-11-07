<?php
/**
 * Add Category API
 * Creates a new category in the database
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
if (empty($data['name'])) {
    echo json_encode(['success' => false, 'error' => 'Category name is required']);
    exit;
}

try {
    require_once __DIR__ . '/../config/db.php';
    
    // Check if category already exists
    $checkStmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
    $checkStmt->execute([$data['name']]);
    if ($checkStmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Category with this name already exists']);
        exit;
    }
    
    // Insert new category
    $stmt = $pdo->prepare("
        INSERT INTO categories (name, description, created_at) 
        VALUES (?, ?, NOW())
    ");
    
    $stmt->execute([
        trim($data['name']),
        trim($data['description'] ?? '')
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Category added successfully',
        'category_id' => $pdo->lastInsertId()
    ]);
    
} catch (Exception $e) {
    error_log('Add category error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to add category: ' . $e->getMessage()
    ]);
}
?>








