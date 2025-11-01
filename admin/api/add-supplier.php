<?php
/**
 * Add Supplier API
 * Creates a new supplier in the database
 */

header('Content-Type: application/json');

session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate required fields
if (empty($data['company_name']) || empty($data['contact_person']) || empty($data['phone'])) {
    echo json_encode(['success' => false, 'error' => 'Company name, contact person, and phone are required']);
    exit;
}

try {
    require_once __DIR__ . '/../config/db.php';
    
    // Check if supplier already exists
    $checkStmt = $pdo->prepare("SELECT id FROM suppliers WHERE company_name = ? OR phone = ?");
    $checkStmt->execute([$data['company_name'], $data['phone']]);
    if ($checkStmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Supplier with this company name or phone already exists']);
        exit;
    }
    
    // Insert new supplier
    $stmt = $pdo->prepare("
        INSERT INTO suppliers (company_name, contact_person, phone, email, created_at) 
        VALUES (?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        trim($data['company_name']),
        trim($data['contact_person']),
        trim($data['phone']),
        trim($data['email'] ?? '')
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Supplier added successfully',
        'supplier_id' => $pdo->lastInsertId()
    ]);
    
} catch (Exception $e) {
    error_log('Add supplier error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to add supplier: ' . $e->getMessage()
    ]);
}
?>

