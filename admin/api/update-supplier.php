<?php
/**
 * Update Supplier API
 * Updates an existing supplier in the database
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
if (empty($data['id']) || empty($data['company_name']) || empty($data['contact_person']) || empty($data['phone'])) {
    echo json_encode(['success' => false, 'error' => 'ID, company name, contact person, and phone are required']);
    exit;
}

try {
    require_once __DIR__ . '/../config/db.php';
    
    // Check if supplier exists
    $checkStmt = $pdo->prepare("SELECT id FROM suppliers WHERE id = ?");
    $checkStmt->execute([$data['id']]);
    if (!$checkStmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Supplier not found']);
        exit;
    }
    
    // Check for duplicate company name or phone (excluding current supplier)
    $dupStmt = $pdo->prepare("SELECT id FROM suppliers WHERE (company_name = ? OR phone = ?) AND id != ?");
    $dupStmt->execute([$data['company_name'], $data['phone'], $data['id']]);
    if ($dupStmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Another supplier with this company name or phone already exists']);
        exit;
    }
    
    // Update supplier
    $stmt = $pdo->prepare("
        UPDATE suppliers 
        SET company_name = ?, contact_person = ?, phone = ?, email = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        trim($data['company_name']),
        trim($data['contact_person']),
        trim($data['phone']),
        trim($data['email'] ?? ''),
        $data['id']
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Supplier updated successfully'
    ]);
    
} catch (Exception $e) {
    error_log('Update supplier error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to update supplier: ' . $e->getMessage()
    ]);
}
?>

