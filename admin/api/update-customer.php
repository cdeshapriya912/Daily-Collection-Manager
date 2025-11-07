<?php
/**
 * Update Customer API
 * Updates an existing customer's information
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
    if (empty($data['id'])) {
        echo json_encode(['success' => false, 'error' => 'Customer ID is required']);
        exit;
    }
    
    if (empty($data['full_name'])) {
        echo json_encode(['success' => false, 'error' => 'Customer name is required']);
        exit;
    }
    
    if (empty($data['mobile'])) {
        echo json_encode(['success' => false, 'error' => 'Mobile number is required']);
        exit;
    }
    
    require_once __DIR__ . '/../config/db.php';
    
    $customerId = intval($data['id']);
    
    // Check if customer exists
    $checkStmt = $pdo->prepare("SELECT id FROM customers WHERE id = ?");
    $checkStmt->execute([$customerId]);
    if (!$checkStmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Customer not found']);
        exit;
    }
    
    // Check if mobile number is used by another customer
    $mobileCheckStmt = $pdo->prepare("SELECT id FROM customers WHERE mobile = ? AND id != ?");
    $mobileCheckStmt->execute([$data['mobile'], $customerId]);
    if ($mobileCheckStmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'A different customer with this mobile number already exists']);
        exit;
    }
    
    // Prepare update statement
    $sql = "UPDATE customers SET
                full_name = ?,
                email = ?,
                mobile = ?,
                address = ?,
                status = ?
            WHERE id = ?";
    
    $params = [
        trim($data['full_name']),
        !empty($data['email']) ? trim($data['email']) : null,
        trim($data['mobile']),
        !empty($data['address']) ? trim($data['address']) : null,
        !empty($data['status']) ? $data['status'] : 'active',
        $customerId
    ];
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    echo json_encode([
        'success' => true,
        'message' => 'Customer updated successfully'
    ]);
    
} catch (PDOException $e) {
    error_log('Update customer error: ' . $e->getMessage());
    
    // Check for duplicate entry
    if ($e->getCode() == 23000) {
        echo json_encode([
            'success' => false,
            'error' => 'A customer with this information already exists'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to update customer: ' . $e->getMessage()
        ]);
    }
} catch (Exception $e) {
    error_log('Update customer error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to update customer: ' . $e->getMessage()
    ]);
}
?>






