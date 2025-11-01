<?php
/**
 * Add Product API
 * Handles product creation with image upload
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
    
    // Get form data
    $productName = trim($_POST['productName'] ?? '');
    $productId = trim($_POST['productId'] ?? ''); // This is the SKU
    $category = trim($_POST['category'] ?? '');
    $supplier = trim($_POST['supplier'] ?? '');
    $buyingPrice = floatval($_POST['buyingPrice'] ?? 0);
    $sellingPrice = floatval($_POST['sellingPrice'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    
    // Validate required fields
    if (empty($productName)) {
        throw new Exception('Product name is required');
    }
    if (empty($productId)) {
        throw new Exception('Product ID is required');
    }
    if (empty($category)) {
        throw new Exception('Category is required');
    }
    if (empty($supplier)) {
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
    
    // Check if product ID (SKU) already exists
    $stmt = $pdo->prepare("SELECT id FROM products WHERE sku = ?");
    $stmt->execute([$productId]);
    if ($stmt->fetch()) {
        throw new Exception('Product ID already exists. Please use a different ID.');
    }
    
    // Handle image upload
    $imagePath = null;
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['productImage'];
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG, PNG, and GIF are allowed.');
        }
        
        // Validate file size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new Exception('File size must be less than 2MB');
        }
        
        // Create upload directory if it doesn't exist
        $uploadDir = __DIR__ . '/../../upload/product/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Get file extension
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Create new filename with product ID
        $newFileName = $productId . '_' . time() . '.' . $fileExtension;
        $targetPath = $uploadDir . $newFileName;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception('Failed to upload image');
        }
        
        // Store relative path for database
        $imagePath = 'upload/product/' . $newFileName;
    }
    
    // Get user ID from session
    $userId = $_SESSION['user_id'] ?? null;
    
    // Insert product into database
    $sql = "INSERT INTO products (
        sku, name, description, category_id, supplier_id, 
        price_buying, price_selling, quantity, image_url, 
        status, created_by
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $productId,
        $productName,
        $description,
        $category,
        $supplier,
        $buyingPrice,
        $sellingPrice,
        $quantity,
        $imagePath,
        $userId
    ]);
    
    // Get the inserted product ID
    $insertedId = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Product added successfully',
        'product_id' => $insertedId,
        'image_path' => $imagePath
    ]);
    
} catch (Exception $e) {
    error_log('Add product error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

