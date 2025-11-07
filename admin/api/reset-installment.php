<?php
/**
 * Reset Installment API - TESTING ONLY
 * Resets all installments for a customer (marks all schedules as unpaid, resets order balances)
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    require_once __DIR__ . '/../config/db.php';
    
    // Get JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['customer_id'])) {
        echo json_encode(['success' => false, 'error' => 'Customer ID is required']);
        exit;
    }
    
    $customerId = intval($data['customer_id']);
    
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        // Verify customer exists
        $customerSql = "SELECT id, customer_code, full_name FROM customers WHERE id = ?";
        $customerStmt = $pdo->prepare($customerSql);
        $customerStmt->execute([$customerId]);
        $customer = $customerStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$customer) {
            throw new Exception('Customer not found');
        }
        
        // Get all orders for this customer (for reporting before deletion)
        $ordersSql = "SELECT id, order_number, total_amount FROM orders WHERE customer_id = ?";
        $ordersStmt = $pdo->prepare($ordersSql);
        $ordersStmt->execute([$customerId]);
        $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $ordersDeleted = 0;
        $schedulesDeleted = 0;
        $paymentsDeleted = 0;
        $orderItemsDeleted = 0;
        $totalAmountReset = 0;
        
        // Get total payments before deletion (for reporting)
        $allPaymentsSql = "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE customer_id = ?";
        $allPaymentsStmt = $pdo->prepare($allPaymentsSql);
        $allPaymentsStmt->execute([$customerId]);
        $allPaymentsData = $allPaymentsStmt->fetch(PDO::FETCH_ASSOC);
        $totalAmountReset = floatval($allPaymentsData['total'] || 0);
        
        // Get count of schedules before deletion (for reporting)
        $schedulesCountSql = "SELECT COUNT(*) as count FROM installment_schedules isch
                             INNER JOIN orders o ON isch.order_id = o.id
                             WHERE o.customer_id = ?";
        $schedulesCountStmt = $pdo->prepare($schedulesCountSql);
        $schedulesCountStmt->execute([$customerId]);
        $schedulesCountData = $schedulesCountStmt->fetch(PDO::FETCH_ASSOC);
        $schedulesDeleted = intval($schedulesCountData['count'] || 0);
        
        // Get count of order items before deletion (for reporting)
        $orderItemsCountSql = "SELECT COUNT(*) as count FROM order_items oi
                              INNER JOIN orders o ON oi.order_id = o.id
                              WHERE o.customer_id = ?";
        $orderItemsCountStmt = $pdo->prepare($orderItemsCountSql);
        $orderItemsCountStmt->execute([$customerId]);
        $orderItemsCountData = $orderItemsCountStmt->fetch(PDO::FETCH_ASSOC);
        $orderItemsDeleted = intval($orderItemsCountData['count'] || 0);
        
        // 1. Delete all payments for this customer first (to avoid foreign key issues)
        $deletePaymentsSql = "DELETE FROM payments WHERE customer_id = ?";
        $deletePaymentsStmt = $pdo->prepare($deletePaymentsSql);
        $deletePaymentsStmt->execute([$customerId]);
        $paymentsDeleted = $deletePaymentsStmt->rowCount();
        
        // 2. Delete all orders for this customer
        // This will CASCADE delete:
        //   - installment_schedules (ON DELETE CASCADE)
        //   - order_items (ON DELETE CASCADE)
        $deleteOrdersSql = "DELETE FROM orders WHERE customer_id = ?";
        $deleteOrdersStmt = $pdo->prepare($deleteOrdersSql);
        $deleteOrdersStmt->execute([$customerId]);
        $ordersDeleted = $deleteOrdersStmt->rowCount();
        
        // 3. Reset customer totals to exactly 0.00 (matching original database state)
        // Use DECIMAL(10,2) to ensure no rounding issues
        $resetCustomerSql = "UPDATE customers 
                            SET total_paid = 0.00,
                                total_purchased = 0.00,
                                updated_at = CURRENT_TIMESTAMP
                            WHERE id = ?";
        $resetCustomerStmt = $pdo->prepare($resetCustomerSql);
        $resetCustomerStmt->execute([$customerId]);
        
        // Verify the reset was successful
        $verifyCustomerSql = "SELECT total_paid, total_purchased FROM customers WHERE id = ?";
        $verifyCustomerStmt = $pdo->prepare($verifyCustomerSql);
        $verifyCustomerStmt->execute([$customerId]);
        $verifyCustomer = $verifyCustomerStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($verifyCustomer) {
            $verifyTotalPaid = floatval($verifyCustomer['total_paid'] || 0);
            $verifyTotalPurchased = floatval($verifyCustomer['total_purchased'] || 0);
            
            // Check if there's any rounding issue (should be exactly 0.00)
            if (abs($verifyTotalPaid) > 0.01 || abs($verifyTotalPurchased) > 0.01) {
                // Force set to exactly 0.00 if there's any discrepancy
                $forceResetSql = "UPDATE customers 
                                 SET total_paid = 0.00,
                                     total_purchased = 0.00,
                                     updated_at = CURRENT_TIMESTAMP
                                 WHERE id = ?";
                $forceResetStmt = $pdo->prepare($forceResetSql);
                $forceResetStmt->execute([$customerId]);
                error_log("Forced customer totals reset due to rounding issue. Customer ID: {$customerId}");
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Installment reset successfully - all orders, schedules, and payments deleted',
            'orders_deleted' => $ordersDeleted,
            'schedules_deleted' => $schedulesDeleted,
            'order_items_deleted' => $orderItemsDeleted,
            'payments_deleted' => $paymentsDeleted,
            'total_amount_reset' => number_format($totalAmountReset, 2, '.', ''),
            'customer_total_paid' => '0.00',
            'customer_total_purchased' => '0.00'
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log('Reset installment error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

