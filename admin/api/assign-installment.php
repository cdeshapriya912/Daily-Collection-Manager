<?php
/**
 * Assign Installment API
 * Creates an order, order items, and daily installment schedules for a customer
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
    
    // Validate required fields
    if (empty($data['customer_id'])) {
        echo json_encode(['success' => false, 'error' => 'Customer ID is required']);
        exit;
    }
    
    // Support both single product (legacy) and multiple products
    if (!empty($data['products']) && is_array($data['products'])) {
        // New format: multiple products
        $products = $data['products'];
    } elseif (!empty($data['product_id'])) {
        // Legacy format: single product (convert to array)
        $products = [[
            'product_id' => intval($data['product_id']),
            'quantity' => isset($data['quantity']) ? intval($data['quantity']) : 1
        ]];
    } else {
        echo json_encode(['success' => false, 'error' => 'At least one product is required']);
        exit;
    }
    
    if (empty($products) || count($products) === 0) {
        echo json_encode(['success' => false, 'error' => 'At least one product is required']);
        exit;
    }
    
    if (empty($data['installment_period']) || !in_array($data['installment_period'], [30, 60])) {
        echo json_encode(['success' => false, 'error' => 'Installment period must be 30 or 60 days']);
        exit;
    }
    
    $customerId = intval($data['customer_id']);
    $installmentPeriod = intval($data['installment_period']);
    $assignmentDate = !empty($data['assignment_date']) ? $data['assignment_date'] : date('Y-m-d');
    $createdBy = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
    $notes = isset($data['notes']) ? trim($data['notes']) : null;
    
    // Validate assignment date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $assignmentDate)) {
        echo json_encode(['success' => false, 'error' => 'Invalid assignment date format. Use YYYY-MM-DD']);
        exit;
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        // 1. Check customer eligibility (no active orders)
        $eligibilitySql = "SELECT COUNT(*) as active_count 
                          FROM orders 
                          WHERE customer_id = ? 
                            AND status IN ('pending', 'active')";
        $eligibilityStmt = $pdo->prepare($eligibilitySql);
        $eligibilityStmt->execute([$customerId]);
        $eligibilityResult = $eligibilityStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($eligibilityResult['active_count'] > 0) {
            throw new Exception('Customer has active installment(s). Must complete existing installment before assigning new one.');
        }
        
        // Verify customer exists and is active
        $customerSql = "SELECT id, customer_code, full_name, status FROM customers WHERE id = ?";
        $customerStmt = $pdo->prepare($customerSql);
        $customerStmt->execute([$customerId]);
        $customer = $customerStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$customer) {
            throw new Exception('Customer not found');
        }
        
        if ($customer['status'] !== 'active') {
            throw new Exception('Customer is not active');
        }
        
        // 2. Validate and get all product details
        $productDetails = [];
        $totalAmount = 0;
        
        foreach ($products as $productData) {
            $productId = intval($productData['product_id']);
            $quantity = intval($productData['quantity'] ?? 1);
            
            if ($quantity < 1) {
                throw new Exception('Product quantity must be at least 1');
            }
            
            $productSql = "SELECT id, sku, name, price_selling, quantity as stock_quantity, status 
                           FROM products 
                           WHERE id = ?";
            $productStmt = $pdo->prepare($productSql);
            $productStmt->execute([$productId]);
            $product = $productStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                throw new Exception("Product ID {$productId} not found");
            }
            
            if ($product['status'] !== 'active') {
                throw new Exception("Product '{$product['name']}' is not active");
            }
            
            // Check stock availability
            if ($product['stock_quantity'] < $quantity) {
                throw new Exception("Insufficient stock for '{$product['name']}'. Available: {$product['stock_quantity']}, Requested: {$quantity}");
            }
            
            $unitPrice = floatval($product['price_selling']);
            $subtotal = $unitPrice * $quantity;
            $totalAmount += $subtotal;
            
            $productDetails[] = [
                'id' => $productId,
                'sku' => $product['sku'],
                'name' => $product['name'],
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
                'stock_quantity' => $product['stock_quantity']
            ];
        }
        
        if ($totalAmount <= 0) {
            throw new Exception('Total amount must be greater than 0');
        }
        
        // 3. Calculate daily payment
        $dailyPayment = $totalAmount / $installmentPeriod;
        
        // 4. Generate order number (ORD-YYYY-NNN)
        $year = date('Y');
        $orderNumberStmt = $pdo->prepare(
            "SELECT order_number FROM orders 
             WHERE order_number LIKE ? 
             ORDER BY id DESC LIMIT 1"
        );
        $orderNumberStmt->execute(["ORD-{$year}-%"]);
        $lastOrder = $orderNumberStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($lastOrder) {
            // Extract number from last order (e.g., ORD-2024-001 -> 1)
            $parts = explode('-', $lastOrder['order_number']);
            $lastNumber = intval($parts[2] ?? 0);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        $orderNumber = sprintf('ORD-%s-%03d', $year, $nextNumber);
        
        // 5. Create order
        $orderSql = "INSERT INTO orders (
                        order_number,
                        customer_id,
                        total_amount,
                        paid_amount,
                        remaining_balance,
                        installment_period,
                        daily_payment,
                        status,
                        assignment_date,
                        created_by,
                        notes
                     ) VALUES (?, ?, ?, 0.00, ?, ?, ?, 'active', ?, ?, ?)";
        
        $orderStmt = $pdo->prepare($orderSql);
        $orderStmt->execute([
            $orderNumber,
            $customerId,
            $totalAmount,
            $totalAmount, // remaining_balance = total_amount initially
            $installmentPeriod,
            $dailyPayment,
            $assignmentDate,
            $createdBy,
            $notes
        ]);
        
        $orderId = $pdo->lastInsertId();
        
        // 6. Create order items (with locked prices) for all products
        $orderItemSql = "INSERT INTO order_items (
                            order_id,
                            product_id,
                            quantity,
                            unit_price,
                            subtotal
                         ) VALUES (?, ?, ?, ?, ?)";
        
        $orderItemStmt = $pdo->prepare($orderItemSql);
        
        foreach ($productDetails as $product) {
            $orderItemStmt->execute([
                $orderId,
                $product['id'],
                $product['quantity'],
                $product['unit_price'],
                $product['subtotal']
            ]);
            // Note: Stock is automatically updated by trigger trg_order_items_update_stock
        }
        
        // 7. Generate daily installment schedules
        $scheduleSql = "INSERT INTO installment_schedules (
                           order_id,
                           schedule_date,
                           due_amount,
                           status
                        ) VALUES (?, ?, ?, 'pending')";
        
        $scheduleStmt = $pdo->prepare($scheduleSql);
        
        $startDate = new DateTime($assignmentDate);
        
        for ($day = 0; $day < $installmentPeriod; $day++) {
            $scheduleDate = clone $startDate;
            $scheduleDate->modify("+{$day} days");
            
            $scheduleStmt->execute([
                $orderId,
                $scheduleDate->format('Y-m-d'),
                $dailyPayment
            ]);
        }
        
        // 8. Update customer total_purchased
        $updateCustomerSql = "UPDATE customers 
                              SET total_purchased = total_purchased + ?,
                                  updated_at = CURRENT_TIMESTAMP
                              WHERE id = ?";
        
        $updateCustomerStmt = $pdo->prepare($updateCustomerSql);
        $updateCustomerStmt->execute([$totalAmount, $customerId]);
        
        // Commit transaction
        $pdo->commit();
        
        // Get created order details
        $orderDetailsSql = "SELECT 
                               o.*,
                               c.customer_code,
                               c.full_name as customer_name,
                               COUNT(DISTINCT isch.id) as schedule_count
                            FROM orders o
                            JOIN customers c ON o.customer_id = c.id
                            LEFT JOIN installment_schedules isch ON o.id = isch.order_id
                            WHERE o.id = ?
                            GROUP BY o.id";
        
        $orderDetailsStmt = $pdo->prepare($orderDetailsSql);
        $orderDetailsStmt->execute([$orderId]);
        $orderDetails = $orderDetailsStmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'message' => 'Installment assigned successfully',
            'order' => $orderDetails
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log('Assign installment error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

