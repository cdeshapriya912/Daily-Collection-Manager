<?php
/**
 * Get Payments API
 * Fetches payments for a customer or order
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
    
    $sql = "SELECT 
                p.id,
                p.customer_id,
                p.order_id,
                p.amount,
                p.payment_date,
                p.payment_method,
                p.remaining_balance,
                p.notes,
                p.collected_by,
                o.order_number
            FROM payments p
            LEFT JOIN orders o ON p.order_id = o.id
            WHERE 1=1";
    
    $params = [];
    
    // Filter by customer_id
    if (isset($_GET['customer_id']) && !empty($_GET['customer_id'])) {
        $sql .= " AND p.customer_id = ?";
        $params[] = intval($_GET['customer_id']);
    }
    
    // Filter by order_id
    if (isset($_GET['order_id']) && !empty($_GET['order_id'])) {
        $sql .= " AND p.order_id = ?";
        $params[] = intval($_GET['order_id']);
    }
    
    // Order by payment date (newest first)
    $sql .= " ORDER BY p.payment_date DESC";
    
    // Limit results
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
    if ($limit > 0) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'payments' => $payments,
        'count' => count($payments)
    ]);
    
} catch (Exception $e) {
    error_log('Get payments error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

