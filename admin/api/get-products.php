<?php
/**
 * Get Products API
 * Fetches products from database with optional filters
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
    
    // Build query with optional filters
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
                p.image_url,
                p.status,
                p.created_at,
                p.updated_at
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN suppliers s ON p.supplier_id = s.id
            WHERE 1=1";
    
    $params = [];
    
    // Search filter (product name or SKU)
    if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
        $search = '%' . trim($_GET['search']) . '%';
        $sql .= " AND (p.name LIKE ? OR p.sku LIKE ? OR p.description LIKE ?)";
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
    }
    
    // Category filter
    if (isset($_GET['category']) && !empty(trim($_GET['category']))) {
        $sql .= " AND p.category_id = ?";
        $params[] = trim($_GET['category']);
    }
    
    // Status filter
    if (isset($_GET['status']) && !empty(trim($_GET['status']))) {
        $sql .= " AND p.status = ?";
        $params[] = trim($_GET['status']);
    }
    
    // Order by
    $orderBy = $_GET['order_by'] ?? 'created_at';
    $orderDir = $_GET['order_dir'] ?? 'DESC';
    
    // Validate order by field
    $allowedOrderFields = ['sku', 'name', 'price_buying', 'price_selling', 'quantity', 'created_at'];
    if (!in_array($orderBy, $allowedOrderFields)) {
        $orderBy = 'created_at';
    }
    
    // Validate order direction
    $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';
    
    $sql .= " ORDER BY p.{$orderBy} {$orderDir}";
    
    // Pagination (optional)
    if (isset($_GET['limit'])) {
        $limit = intval($_GET['limit']);
        $offset = intval($_GET['offset'] ?? 0);
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total count (without pagination)
    $countSql = "SELECT COUNT(*) as total FROM products p WHERE 1=1";
    $countParams = [];
    
    if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
        $search = '%' . trim($_GET['search']) . '%';
        $countSql .= " AND (p.name LIKE ? OR p.sku LIKE ? OR p.description LIKE ?)";
        $countParams[] = $search;
        $countParams[] = $search;
        $countParams[] = $search;
    }
    
    if (isset($_GET['category']) && !empty(trim($_GET['category']))) {
        $countSql .= " AND p.category_id = ?";
        $countParams[] = trim($_GET['category']);
    }
    
    if (isset($_GET['status']) && !empty(trim($_GET['status']))) {
        $countSql .= " AND p.status = ?";
        $countParams[] = trim($_GET['status']);
    }
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $totalCount = $countStmt->fetch()['total'];
    
    echo json_encode([
        'success' => true,
        'products' => $products,
        'total' => $totalCount
    ]);
    
} catch (Exception $e) {
    error_log('Get products error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch products: ' . $e->getMessage()
    ]);
}
?>

