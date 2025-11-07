<?php
/**
 * Get Installment Schedule API
 * Fetches payment schedule for an order with payment status
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

try {
    require_once __DIR__ . '/../config/db.php';
    
    // Get order_id from query parameter
    if (!isset($_GET['order_id']) || empty(trim($_GET['order_id']))) {
        echo json_encode([
            'success' => false,
            'error' => 'Order ID is required'
        ]);
        exit;
    }
    
    $orderId = intval($_GET['order_id']);
    
    // Get order details
    $orderSql = "SELECT 
                    o.*,
                    c.customer_code,
                    c.full_name as customer_name,
                    (SELECT COUNT(*) FROM installment_schedules WHERE order_id = o.id AND status = 'paid') as paid_count,
                    (SELECT COUNT(*) FROM installment_schedules WHERE order_id = o.id AND status = 'pending') as pending_count,
                    (SELECT COUNT(*) FROM installment_schedules WHERE order_id = o.id AND status = 'missed') as missed_count
                 FROM orders o
                 JOIN customers c ON o.customer_id = c.id
                 WHERE o.id = ?";
    
    $orderStmt = $pdo->prepare($orderSql);
    $orderStmt->execute([$orderId]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        echo json_encode([
            'success' => false,
            'error' => 'Order not found'
        ]);
        exit;
    }
    
    // Get installment schedules
    $scheduleSql = "SELECT 
                       isch.*,
                       p.id as payment_id,
                       p.payment_date,
                       p.amount as payment_amount,
                       p.payment_method
                    FROM installment_schedules isch
                    LEFT JOIN payments p ON isch.payment_id = p.id
                    WHERE isch.order_id = ?
                    ORDER BY isch.schedule_date ASC";
    
    $scheduleStmt = $pdo->prepare($scheduleSql);
    $scheduleStmt->execute([$orderId]);
    $schedules = $scheduleStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process schedules to determine current status
    $today = date('Y-m-d');
    $processedSchedules = [];
    
    foreach ($schedules as $schedule) {
        $scheduleDate = $schedule['schedule_date'];
        $dueDate = new DateTime($scheduleDate);
        $todayDate = new DateTime($today);
        
        // If status is pending and date has passed, mark as missed
        if ($schedule['status'] === 'pending' && $dueDate < $todayDate) {
            $schedule['status'] = 'missed';
            $schedule['is_overdue'] = true;
        } else {
            $schedule['is_overdue'] = false;
        }
        
        // Calculate days until due or days overdue
        $daysDiff = $todayDate->diff($dueDate)->days;
        if ($dueDate < $todayDate) {
            $schedule['days_overdue'] = $daysDiff;
        } elseif ($dueDate > $todayDate) {
            $schedule['days_until_due'] = $daysDiff;
        } else {
            $schedule['days_until_due'] = 0;
        }
        
        $processedSchedules[] = $schedule;
    }
    
    // Calculate summary statistics
    $totalSchedules = count($schedules);
    $paidSchedules = array_filter($schedules, function($s) { return $s['status'] === 'paid'; });
    $pendingSchedules = array_filter($schedules, function($s) { return $s['status'] === 'pending'; });
    $missedSchedules = array_filter($schedules, function($s) { return $s['status'] === 'missed'; });
    
    $totalDue = array_sum(array_column($schedules, 'due_amount'));
    $totalPaid = array_sum(array_column($paidSchedules, 'paid_amount'));
    $totalPending = array_sum(array_column($pendingSchedules, 'due_amount'));
    $totalMissed = array_sum(array_column($missedSchedules, 'due_amount'));
    
    $completionPercentage = $totalSchedules > 0 ? ($order['paid_count'] / $totalSchedules) * 100 : 0;
    
    echo json_encode([
        'success' => true,
        'order' => $order,
        'schedules' => $processedSchedules,
        'summary' => [
            'total_schedules' => $totalSchedules,
            'paid_count' => count($paidSchedules),
            'pending_count' => count($pendingSchedules),
            'missed_count' => count($missedSchedules),
            'total_due' => floatval($totalDue),
            'total_paid' => floatval($totalPaid),
            'total_pending' => floatval($totalPending),
            'total_missed' => floatval($totalMissed),
            'completion_percentage' => round($completionPercentage, 2),
            'remaining_balance' => floatval($order['remaining_balance'])
        ]
    ]);
    
} catch (Exception $e) {
    error_log('Get installment schedule error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch schedule: ' . $e->getMessage()
    ]);
}
?>

