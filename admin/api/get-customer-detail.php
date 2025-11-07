<?php
/**
 * Get Customer Detail API
 * Fetches a single customer's details by ID
 */

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized - Please login first']);
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
    
    if ($customerId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid customer ID']);
        exit;
    }
    
    // Get customer details with calculated remaining balance and all registration fields
    // Using COALESCE to handle NULL values safely
    $sql = "SELECT 
                c.id,
                c.customer_code,
                COALESCE(c.first_name, '') as first_name,
                COALESCE(c.last_name, '') as last_name,
                COALESCE(c.full_name, '') as full_name,
                COALESCE(c.full_name_with_surname, '') as full_name_with_surname,
                COALESCE(c.email, '') as email,
                COALESCE(c.mobile, '') as mobile,
                COALESCE(c.address, '') as address,
                COALESCE(c.gnd, '') as gnd,
                COALESCE(c.lgi, '') as lgi,
                COALESCE(c.police_station, '') as police_station,
                COALESCE(c.nic, '') as nic,
                COALESCE(c.occupation, '') as occupation,
                COALESCE(c.residence_period, '') as residence_period,
                COALESCE(c.nic_front_path, '') as nic_front_path,
                COALESCE(c.nic_back_path, '') as nic_back_path,
                COALESCE(c.customer_photo_path, '') as customer_photo_path,
                COALESCE(c.status, 'active') as status,
                COALESCE(c.total_purchased, 0) as total_purchased,
                COALESCE(c.total_paid, 0) as total_paid,
                COALESCE((c.total_purchased - c.total_paid), 0) as remaining_balance,
                c.created_at,
                c.updated_at
            FROM customers c
            WHERE c.id = ?";
    
    $stmt = $pdo->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare SQL statement: ' . implode(', ', $pdo->errorInfo()));
    }
    
    $stmt->execute([$customerId]);
    
    if ($stmt->errorCode() !== '00000') {
        $errorInfo = $stmt->errorInfo();
        throw new Exception('SQL execution error: ' . $errorInfo[2]);
    }
    
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$customer) {
        echo json_encode(['success' => false, 'error' => 'Customer not found with ID: ' . $customerId]);
        exit;
    }
    
    // Check if installment_schedules table exists
    $tableExists = false;
    try {
        $checkTable = $pdo->query("SHOW TABLES LIKE 'installment_schedules'");
        $tableExists = $checkTable->rowCount() > 0;
    } catch (Exception $e) {
        // Table doesn't exist
        $tableExists = false;
    }
    
    // Get active installments (orders with status pending or active)
    if ($tableExists) {
        $installmentsSql = "SELECT 
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
                               o.notes,
                               (SELECT COUNT(*) FROM installment_schedules WHERE order_id = o.id AND status = 'paid') as paid_count,
                               (SELECT COUNT(*) FROM installment_schedules WHERE order_id = o.id) as total_count,
                               (SELECT COUNT(*) FROM installment_schedules WHERE order_id = o.id AND status = 'missed') as missed_count,
                               (SELECT COUNT(*) FROM installment_schedules WHERE order_id = o.id AND status = 'pending' AND schedule_date < CURDATE()) as overdue_count
                            FROM orders o
                            WHERE o.customer_id = ?
                              AND o.status IN ('pending', 'active')
                            ORDER BY o.order_date DESC";
    } else {
        // Fallback query without installment_schedules references
        $installmentsSql = "SELECT 
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
                               o.notes,
                               0 as paid_count,
                               0 as total_count,
                               0 as missed_count,
                               0 as overdue_count
                            FROM orders o
                            WHERE o.customer_id = ?
                              AND o.status IN ('pending', 'active')
                            ORDER BY o.order_date DESC";
    }
    
    $installmentsStmt = $pdo->prepare($installmentsSql);
    $installmentsStmt->execute([$customerId]);
    $activeInstallments = $installmentsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get product details for each installment
    foreach ($activeInstallments as &$installment) {
        $orderId = $installment['id'];
        
        // Get order items with product details
        $itemsSql = "SELECT 
                        oi.id,
                        oi.quantity,
                        oi.unit_price,
                        oi.subtotal,
                        p.id as product_id,
                        p.sku,
                        p.name as product_name,
                        p.image_url
                     FROM order_items oi
                     JOIN products p ON oi.product_id = p.id
                     WHERE oi.order_id = ?";
        
        $itemsStmt = $pdo->prepare($itemsSql);
        $itemsStmt->execute([$orderId]);
        $installment['products'] = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get next due payment
        if ($tableExists) {
            $nextDueSql = "SELECT 
                              schedule_date,
                              due_amount,
                              paid_amount,
                              status
                           FROM installment_schedules
                           WHERE order_id = ?
                             AND status IN ('pending', 'missed')
                           ORDER BY schedule_date ASC
                           LIMIT 1";
            
            $nextDueStmt = $pdo->prepare($nextDueSql);
            $nextDueStmt->execute([$orderId]);
            $installment['next_due'] = $nextDueStmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $installment['next_due'] = null;
        }
        
        // Calculate completion percentage
        if ($installment['total_count'] > 0) {
            $installment['completion_percentage'] = round(($installment['paid_count'] / $installment['total_count']) * 100, 2);
        } else {
            $installment['completion_percentage'] = 0;
        }
    }
    unset($installment); // Unset reference to prevent issues
    
    // Get completed installments count
    $completedCountSql = "SELECT COUNT(*) as completed_count
                          FROM orders
                          WHERE customer_id = ?
                            AND status = 'completed'";
    
    $completedCountStmt = $pdo->prepare($completedCountSql);
    $completedCountStmt->execute([$customerId]);
    $completedCount = $completedCountStmt->fetch(PDO::FETCH_ASSOC)['completed_count'];
    
    // Get ALL purchased products from all orders for "Products Purchased" section
    $allProductsSql = "SELECT 
                        oi.id,
                        oi.quantity,
                        oi.unit_price,
                        oi.subtotal,
                        p.id as product_id,
                        p.sku,
                        p.name as product_name,
                        p.image_url,
                        o.order_number,
                        o.order_date
                     FROM order_items oi
                     JOIN products p ON oi.product_id = p.id
                     JOIN orders o ON oi.order_id = o.id
                     WHERE o.customer_id = ?
                     ORDER BY o.order_date DESC, oi.id ASC";
    
    $allProductsStmt = $pdo->prepare($allProductsSql);
    $allProductsStmt->execute([$customerId]);
    $allProducts = $allProductsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'customer' => $customer,
        'active_installments' => $activeInstallments,
        'active_installments_count' => count($activeInstallments),
        'completed_installments_count' => intval($completedCount),
        'is_eligible_for_new_installment' => count($activeInstallments) === 0,
        'all_products' => $allProducts
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log('Get customer detail PDO error: ' . $e->getMessage());
    error_log('SQL Error Info: ' . print_r($pdo->errorInfo() ?? [], true));
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log('Get customer detail error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch customer details: ' . $e->getMessage()
    ]);
}
?>


