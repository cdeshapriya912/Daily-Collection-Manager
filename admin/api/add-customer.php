<?php
/**
 * Add Customer API
 * Creates a new customer in the database
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
    // Get JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    // Validate required fields
    if (empty($data['full_name'])) {
        echo json_encode(['success' => false, 'error' => 'Customer name is required']);
        exit;
    }
    
    if (empty($data['mobile'])) {
        echo json_encode(['success' => false, 'error' => 'Mobile number is required']);
        exit;
    }
    
    require_once __DIR__ . '/../config/db.php';
    
    // Generate next customer code
    $stmt = $pdo->query("SELECT customer_code FROM customers ORDER BY id DESC LIMIT 1");
    $lastCustomer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($lastCustomer) {
        // Extract number from last code (e.g., C001 -> 1)
        $lastNumber = intval(substr($lastCustomer['customer_code'], 1));
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }
    
    $customerCode = 'C' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    
    // Check if customer code already exists (safety check)
    $checkStmt = $pdo->prepare("SELECT id FROM customers WHERE customer_code = ?");
    $checkStmt->execute([$customerCode]);
    if ($checkStmt->fetch()) {
        // If exists, generate a random one
        $customerCode = 'C' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
    }
    
    // Check if mobile number already exists
    $mobileCheckStmt = $pdo->prepare("SELECT id FROM customers WHERE mobile = ?");
    $mobileCheckStmt->execute([$data['mobile']]);
    if ($mobileCheckStmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'A customer with this mobile number already exists']);
        exit;
    }
    
    // Prepare insert statement
    $sql = "INSERT INTO customers (
                customer_code,
                full_name,
                email,
                mobile,
                address,
                status,
                total_purchased,
                total_paid
            ) VALUES (?, ?, ?, ?, ?, ?, 0.00, 0.00)";
    
    $params = [
        $customerCode,
        trim($data['full_name']),
        !empty($data['email']) ? trim($data['email']) : null,
        trim($data['mobile']),
        !empty($data['address']) ? trim($data['address']) : null,
        !empty($data['status']) ? $data['status'] : 'active'
    ];
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    $customerId = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Customer added successfully',
        'customer_id' => $customerId,
        'customer_code' => $customerCode
    ]);
    
} catch (PDOException $e) {
    error_log('Add customer error: ' . $e->getMessage());
    
    // Check for duplicate entry
    if ($e->getCode() == 23000) {
        echo json_encode([
            'success' => false,
            'error' => 'A customer with this information already exists'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to add customer: ' . $e->getMessage()
        ]);
    }
} catch (Exception $e) {
    error_log('Add customer error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to add customer: ' . $e->getMessage()
    ]);
}
?>






