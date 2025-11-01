<?php
/**
 * Real-time Data Verification Script
 * This demonstrates that category product counts are fetched live from the database
 */

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    require_once __DIR__ . '/../config/db.php';
    
    // Get database timestamp
    $timestampStmt = $pdo->query("SELECT NOW() as db_timestamp");
    $dbTime = $timestampStmt->fetch(PDO::FETCH_ASSOC);
    
    // Count total categories
    $categoriesStmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
    $categoriesCount = $categoriesStmt->fetch(PDO::FETCH_ASSOC);
    
    // Count total products
    $productsStmt = $pdo->query("SELECT COUNT(*) as total FROM products");
    $productsCount = $productsStmt->fetch(PDO::FETCH_ASSOC);
    
    // Get detailed category-product mapping (real-time)
    $detailStmt = $pdo->query("
        SELECT 
            c.id,
            c.name,
            COUNT(p.id) as product_count
        FROM categories c
        LEFT JOIN products p ON p.category_id = c.id
        GROUP BY c.id, c.name
        ORDER BY c.name ASC
    ");
    $categoryDetails = $detailStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get sample products with their categories
    $sampleProductsStmt = $pdo->query("
        SELECT 
            p.id,
            p.name as product_name,
            p.sku,
            c.name as category_name,
            c.id as category_id
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LIMIT 5
    ");
    $sampleProducts = $sampleProductsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Real-time data verification',
        'database_timestamp' => $dbTime['db_timestamp'],
        'server_timestamp' => date('Y-m-d H:i:s'),
        'statistics' => [
            'total_categories' => (int)$categoriesCount['total'],
            'total_products' => (int)$productsCount['total']
        ],
        'category_product_counts' => $categoryDetails,
        'sample_products' => $sampleProducts,
        'data_source' => [
            'categories_table' => 'SAHANALK.categories',
            'products_table' => 'SAHANALK.products',
            'query_method' => 'Real-time JOIN and subquery',
            'caching' => 'Disabled - Fresh data on every request'
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    error_log('Verification error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to verify real-time data',
        'details' => $e->getMessage()
    ]);
}
?>

