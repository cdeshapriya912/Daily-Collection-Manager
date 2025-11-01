<?php
/**
 * Get Customer Detail API
 * Fetches a single customer's details by ID
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

// Validate input
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'Customer ID is required']);
    exit;
}

try {
    require_once __DIR__ . '/../config/db.php';
    
    $customerId = intval($_GET['id']);
    
    // Get customer details with calculated remaining balance
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
            WHERE c.id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$customerId]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$customer) {
        echo json_encode(['success' => false, 'error' => 'Customer not found']);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'customer' => $customer
    ]);
    
} catch (Exception $e) {
    error_log('Get customer detail error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch customer details: ' . $e->getMessage()
    ]);
}
?>

