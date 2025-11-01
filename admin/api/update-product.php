<?php
/**
 * Update Product API
 * Handles product updates with validation
 */

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    require_once __DIR__ . '/../config/db.php';
    
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Get JSON data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }
    
    // Get form data
    $productId = intval($data['id'] ?? 0);
    $productName = trim($data['name'] ?? '');
    $categoryId = intval($data['category_id'] ?? 0);
    $supplierId = intval($data['supplier_id'] ?? 0);
    $buyingPrice = floatval($data['price_buying'] ?? 0);
    $sellingPrice = floatval($data['price_selling'] ?? 0);
    $quantity = intval($data['quantity'] ?? 0);
    $status = trim($data['status'] ?? 'active');
    $description = trim($data['description'] ?? '');
    
    // Validate required fields
    if ($productId <= 0) {
        throw new Exception('Invalid product ID');
    }
    if (empty($productName)) {
        throw new Exception('Product name is required');
    }
    if ($categoryId <= 0) {
        throw new Exception('Category is required');
    }
    if ($supplierId <= 0) {
        throw new Exception('Supplier is required');
    }
    if ($buyingPrice <= 0) {
        throw new Exception('Buying price must be greater than 0');
    }
    if ($sellingPrice <= 0) {
        throw new Exception('Selling price must be greater than 0');
    }
    
    // Validate: Selling price must be greater than buying price
    if ($sellingPrice <= $buyingPrice) {
        throw new Exception('Selling price (Rs. ' . number_format($sellingPrice, 2) . ') must be greater than buying price (Rs. ' . number_format($buyingPrice, 2) . ')');
    }
    
    // Validate status
    $validStatuses = ['active', 'inactive', 'out_of_stock'];
    if (!in_array($status, $validStatuses)) {
        $status = 'active';
    }
    
    // Check if product exists
    $checkStmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
    $checkStmt->execute([$productId]);
    if (!$checkStmt->fetch()) {
        throw new Exception('Product not found');
    }
    
    // Update product in database
    $sql = "UPDATE products SET 
                name = ?,
                description = ?,
                category_id = ?,
                supplier_id = ?,
                price_buying = ?,
                price_selling = ?,
                quantity = ?,
                status = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([
        $productName,
        $description,
        $categoryId,
        $supplierId,
        $buyingPrice,
        $sellingPrice,
        $quantity,
        $status,
        $productId
    ]);
    
    if (!$success) {
        throw new Exception('Failed to update product');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Product updated successfully',
        'product_id' => $productId
    ]);
    
} catch (Exception $e) {
    error_log('Update product error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
