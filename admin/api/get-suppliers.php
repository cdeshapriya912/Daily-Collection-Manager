<?php
/**
 * Get Suppliers API
 * Fetches all suppliers or filtered suppliers from database
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
    
    // Build query with optional search filter
    $sql = "SELECT id, company_name, contact_person, phone, email, created_at 
            FROM suppliers 
            WHERE 1=1";
    
    $params = [];
    
    // Search filter
    if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
        $search = '%' . trim($_GET['search']) . '%';
        $sql .= " AND (company_name LIKE ? OR contact_person LIKE ? OR phone LIKE ? OR email LIKE ?)";
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
    }
    
    $sql .= " ORDER BY company_name ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'suppliers' => $suppliers
    ]);
    
} catch (Exception $e) {
    error_log('Get suppliers error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch suppliers: ' . $e->getMessage()
    ]);
}
?>

