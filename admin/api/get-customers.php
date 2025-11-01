<?php
/**
 * Get Customers API
 * Fetches customers from database with optional filters
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
    
    // Build query using the customer summary view that includes remaining balance
    $sql = "SELECT 
                c.id,
                c.customer_code,
                c.full_name,
                c.email,
                c.mobile,
                c.address,
                c.status,
                c.total_purchased,
                c.total_paid,
                (c.total_purchased - c.total_paid) as remaining_balance,
                c.created_at,
                c.updated_at
            FROM customers c
            WHERE 1=1";
    
    $params = [];
    
    // Search filter (customer name, code, mobile, or email)
    if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
        $search = '%' . trim($_GET['search']) . '%';
        $sql .= " AND (c.full_name LIKE ? OR c.customer_code LIKE ? OR c.mobile LIKE ? OR c.email LIKE ?)";
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
    }
    
    // Status filter
    if (isset($_GET['status']) && !empty(trim($_GET['status']))) {
        $sql .= " AND c.status = ?";
        $params[] = trim($_GET['status']);
    }
    
    // Order by
    $orderBy = $_GET['order_by'] ?? 'created_at';
    $orderDir = $_GET['order_dir'] ?? 'DESC';
    
    // Validate order by field
    $allowedOrderFields = ['customer_code', 'full_name', 'email', 'mobile', 'created_at', 'remaining_balance'];
    if (!in_array($orderBy, $allowedOrderFields)) {
        $orderBy = 'created_at';
    }
    
    // Validate order direction
    $orderDir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';
    
    $sql .= " ORDER BY c.{$orderBy} {$orderDir}";
    
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
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total count (without pagination)
    $countSql = "SELECT COUNT(*) as total FROM customers c WHERE 1=1";
    $countParams = [];
    
    if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
        $search = '%' . trim($_GET['search']) . '%';
        $countSql .= " AND (c.full_name LIKE ? OR c.customer_code LIKE ? OR c.mobile LIKE ? OR c.email LIKE ?)";
        $countParams[] = $search;
        $countParams[] = $search;
        $countParams[] = $search;
        $countParams[] = $search;
    }
    
    if (isset($_GET['status']) && !empty(trim($_GET['status']))) {
        $countSql .= " AND c.status = ?";
        $countParams[] = trim($_GET['status']);
    }
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $totalCount = $countStmt->fetch()['total'];
    
    echo json_encode([
        'success' => true,
        'customers' => $customers,
        'total' => $totalCount
    ]);
    
} catch (Exception $e) {
    error_log('Get customers error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch customers: ' . $e->getMessage()
    ]);
}
?>

