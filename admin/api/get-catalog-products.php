<?php
/**
 * Get Catalog Products API - Real-time Data from Database
 * Fetches all products with category, supplier info from database
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
    
    // Real-time query - fetches products with category and supplier info
    $sql = "SELECT 
                p.id,
                p.sku,
                p.name,
                p.description,
                p.price_buying,
                p.price_selling,
                p.quantity,
                p.low_stock_threshold,
                p.image_url,
                p.status,
                p.created_at,
                p.updated_at,
                c.name as category_name,
                c.id as category_id,
                s.company_name as supplier_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN suppliers s ON p.supplier_id = s.id
            WHERE 1=1";
    
    $params = [];
    
    // Search filter - search in name, description, SKU, category
    if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
        $search = '%' . trim($_GET['search']) . '%';
        $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ? OR c.name LIKE ?)";
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
    }
    
    // Category filter
    if (isset($_GET['category']) && !empty(trim($_GET['category']))) {
        $sql .= " AND c.name = ?";
        $params[] = trim($_GET['category']);
    }
    
    // Status filter (optional - default to active products)
    if (isset($_GET['status']) && !empty(trim($_GET['status']))) {
        $sql .= " AND p.status = ?";
        $params[] = trim($_GET['status']);
    } else {
        // By default, only show active products in catalog
        $sql .= " AND p.status = 'active'";
    }
    
    // Sorting
    $sortBy = $_GET['sort'] ?? 'name';
    switch ($sortBy) {
        case 'name':
            $sql .= " ORDER BY p.name ASC";
            break;
        case 'price-low':
            $sql .= " ORDER BY p.price_selling ASC";
            break;
        case 'price-high':
            $sql .= " ORDER BY p.price_selling DESC";
            break;
        case 'newest':
            $sql .= " ORDER BY p.created_at DESC";
            break;
        case 'stock-low':
            $sql .= " ORDER BY p.quantity ASC";
            break;
        case 'stock-high':
            $sql .= " ORDER BY p.quantity DESC";
            break;
        default:
            $sql .= " ORDER BY p.name ASC";
    }
    
    // Execute query with fresh database connection
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format data for frontend
    foreach ($products as &$product) {
        $product['id'] = (int)$product['id'];
        $product['price_buying'] = (float)$product['price_buying'];
        $product['price_selling'] = (float)$product['price_selling'];
        $product['quantity'] = (int)$product['quantity'];
        $product['low_stock_threshold'] = (int)$product['low_stock_threshold'];
        $product['category_id'] = $product['category_id'] ? (int)$product['category_id'] : null;
        
        // Determine stock status
        if ($product['quantity'] <= 0) {
            $product['stock_status'] = 'out_of_stock';
        } elseif ($product['quantity'] <= $product['low_stock_threshold']) {
            $product['stock_status'] = 'low_stock';
        } else {
            $product['stock_status'] = 'in_stock';
        }
        
        // Calculate discount percentage if applicable
        if ($product['price_buying'] > 0 && $product['price_selling'] > $product['price_buying']) {
            $product['discount_percentage'] = round((($product['price_selling'] - $product['price_buying']) / $product['price_selling']) * 100);
        } else {
            $product['discount_percentage'] = 0;
        }
    }
    
    // Return real-time data
    echo json_encode([
        'success' => true,
        'products' => $products,
        'timestamp' => time(),
        'total_count' => count($products)
    ]);
    
} catch (Exception $e) {
    error_log('Get catalog products error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch products from database',
        'details' => $e->getMessage()
    ]);
}
?>

