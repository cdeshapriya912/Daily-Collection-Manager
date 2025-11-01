<?php
/**
 * Delete Customer API
 * Deletes a customer from the database
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
    // Get POST data
    parse_str(file_get_contents('php://input'), $_POST);
    
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        echo json_encode(['success' => false, 'error' => 'Customer ID is required']);
        exit;
    }
    
    require_once __DIR__ . '/../config/db.php';
    
    $customerId = intval($_POST['id']);
    
    // Check if customer exists
    $checkStmt = $pdo->prepare("SELECT full_name FROM customers WHERE id = ?");
    $checkStmt->execute([$customerId]);
    $customer = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$customer) {
        echo json_encode(['success' => false, 'error' => 'Customer not found']);
        exit;
    }
    
    // Check if customer has any orders
    $orderCheckStmt = $pdo->prepare("SELECT COUNT(*) as order_count FROM orders WHERE customer_id = ?");
    $orderCheckStmt->execute([$customerId]);
    $orderCount = $orderCheckStmt->fetch()['order_count'];
    
    if ($orderCount > 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Cannot delete customer with existing orders. Please cancel or complete all orders first.'
        ]);
        exit;
    }
    
    // Check if customer has any payments
    $paymentCheckStmt = $pdo->prepare("SELECT COUNT(*) as payment_count FROM payments WHERE customer_id = ?");
    $paymentCheckStmt->execute([$customerId]);
    $paymentCount = $paymentCheckStmt->fetch()['payment_count'];
    
    if ($paymentCount > 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Cannot delete customer with payment history. Customer has ' . $paymentCount . ' payment record(s).'
        ]);
        exit;
    }
    
    // Delete customer
    $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->execute([$customerId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Customer deleted successfully'
    ]);
    
} catch (PDOException $e) {
    error_log('Delete customer error: ' . $e->getMessage());
    
    // Check for foreign key constraint
    if ($e->getCode() == 23000) {
        echo json_encode([
            'success' => false,
            'error' => 'Cannot delete customer. Customer has related records in the system.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to delete customer: ' . $e->getMessage()
        ]);
    }
} catch (Exception $e) {
    error_log('Delete customer error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to delete customer: ' . $e->getMessage()
    ]);
}
?>

