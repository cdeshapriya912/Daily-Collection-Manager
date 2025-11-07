<?php
/**
 * Check Customer Eligibility API
 * Checks if a customer has active installments and is eligible for new assignment
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
    
    // Get customer_id from query parameter
    if (!isset($_GET['customer_id']) || empty(trim($_GET['customer_id']))) {
        echo json_encode([
            'success' => false,
            'error' => 'Customer ID is required'
        ]);
        exit;
    }
    
    $customerId = intval($_GET['customer_id']);
    
    // Check for active orders (pending or active status)
    $sql = "SELECT 
                o.id,
                o.order_number,
                o.total_amount,
                o.paid_amount,
                o.remaining_balance,
                o.installment_period,
                o.daily_payment,
                o.status,
                o.assignment_date,
                o.order_date,
                COUNT(DISTINCT oi.id) as product_count
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.customer_id = ?
              AND o.status IN ('pending', 'active')
            GROUP BY o.id
            ORDER BY o.order_date DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$customerId]);
    $activeOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $isEligible = count($activeOrders) === 0;
    
    // Get customer info
    $customerSql = "SELECT 
                        id,
                        customer_code,
                        full_name,
                        status as customer_status
                    FROM customers
                    WHERE id = ?";
    
    $customerStmt = $pdo->prepare($customerSql);
    $customerStmt->execute([$customerId]);
    $customer = $customerStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$customer) {
        echo json_encode([
            'success' => false,
            'error' => 'Customer not found'
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'eligible' => $isEligible,
        'customer' => $customer,
        'active_orders_count' => count($activeOrders),
        'active_orders' => $activeOrders,
        'message' => $isEligible 
            ? 'Customer is eligible for new installment assignment'
            : 'Customer has active installment(s). Must complete existing installment before assigning new one.'
    ]);
    
} catch (Exception $e) {
    error_log('Check customer eligibility error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to check eligibility: ' . $e->getMessage()
    ]);
}
?>

