<?php
/**
 * Get Categories API - Real-time Data from Database
 * Fetches all categories with live product counts from products table
 */

// Prevent caching - force fresh data
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    require_once __DIR__ . '/../config/db.php';
    
    // Real-time query - fetches category data with live product count from products table
    // This subquery counts products in real-time for each category
    $sql = "SELECT 
                c.id, 
                c.name, 
                c.description, 
                c.created_at,
                c.updated_at,
                (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) as product_count
            FROM categories c
            WHERE 1=1";
    
    $params = [];
    
    // Search filter - case-insensitive search
    if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
        $search = '%' . trim($_GET['search']) . '%';
        $sql .= " AND (c.name LIKE ? OR c.description LIKE ?)";
        $params[] = $search;
        $params[] = $search;
    }
    
    // Order by name for consistent display
    $sql .= " ORDER BY c.name ASC";
    
    // Execute query with fresh database connection
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format timestamps for better readability
    foreach ($categories as &$category) {
        $category['product_count'] = (int)$category['product_count'];
    }
    
    // Return real-time data
    echo json_encode([
        'success' => true,
        'categories' => $categories,
        'timestamp' => time(),
        'total_count' => count($categories)
    ]);
    
} catch (Exception $e) {
    error_log('Get categories error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch categories from database',
        'details' => $e->getMessage()
    ]);
}
?>



