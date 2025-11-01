<?php
/**
 * Get Product Detail API
 * Fetches a single product's details by ID
 */

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    require_once __DIR__ . '/../config/db.php';
    
    // Get product ID
    $productId = intval($_GET['id'] ?? 0);
    
    if ($productId <= 0) {
        throw new Exception('Invalid product ID');
    }
    
    // Fetch product details
    $sql = "SELECT 
                p.id,
                p.sku,
                p.name,
                p.description,
                p.category_id,
                c.name as category_name,
                p.supplier_id,
                s.company_name as supplier_name,
                p.price_buying,
                p.price_selling,
                p.quantity,
                p.low_stock_threshold,
                p.image_url,
                p.status,
                p.created_at,
                p.updated_at
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN suppliers s ON p.supplier_id = s.id
            WHERE p.id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        throw new Exception('Product not found');
    }
    
    echo json_encode([
        'success' => true,
        'product' => $product
    ]);
    
} catch (Exception $e) {
    error_log('Get product detail error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

