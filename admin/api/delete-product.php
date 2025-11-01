<?php
/**
 * Delete Product API
 * Handles product deletion including image file
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
    
    // Get product ID from POST or GET
    $productId = 0;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $productId = intval($_POST['id'] ?? 0);
    } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $productId = intval($_GET['id'] ?? 0);
    }
    
    if ($productId <= 0) {
        throw new Exception('Invalid product ID');
    }
    
    // Get product details before deletion
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        throw new Exception('Product not found');
    }
    
    // Check if product is used in any orders (optional - prevents deletion if there are orders)
    // Uncomment if you want to prevent deletion of products that have been ordered
    /*
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM order_items WHERE product_id = ?");
    $stmt->execute([$productId]);
    $orderCount = $stmt->fetch()['count'];
    
    if ($orderCount > 0) {
        throw new Exception('Cannot delete product. It has been used in ' . $orderCount . ' order(s). Consider marking it as inactive instead.');
    }
    */
    
    // Delete the product
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    
    // Delete image file if exists
    if (!empty($product['image_url'])) {
        $imagePath = __DIR__ . '/../../' . $product['image_url'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Product deleted successfully'
    ]);
    
} catch (Exception $e) {
    error_log('Delete product error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

