<?php
/**
 * Update Schedule Entry API
 * Allows admin to manually update payment status and amount for a schedule entry
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
    if (empty($data['schedule_id'])) {
        echo json_encode(['success' => false, 'error' => 'Schedule ID is required']);
        exit;
    }
    
    $scheduleId = intval($data['schedule_id']);
    $paidAmount = isset($data['paid_amount']) ? floatval($data['paid_amount']) : null;
    $status = isset($data['status']) ? trim($data['status']) : null;
    $dueAmount = isset($data['due_amount']) ? floatval($data['due_amount']) : null;
    
    // Validate status if provided
    if ($status !== null) {
        $allowedStatuses = ['pending', 'paid', 'missed', 'partial'];
        if (!in_array($status, $allowedStatuses)) {
            echo json_encode(['success' => false, 'error' => 'Invalid status. Allowed values: pending, paid, missed, partial']);
            exit;
        }
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        // Get current schedule details
        $getScheduleSql = "SELECT 
                             isch.*,
                             o.id as order_id,
                             o.customer_id,
                             o.total_amount as order_total,
                             o.remaining_balance as order_balance
                          FROM installment_schedules isch
                          JOIN orders o ON isch.order_id = o.id
                          WHERE isch.id = ?";
        
        $getScheduleStmt = $pdo->prepare($getScheduleSql);
        $getScheduleStmt->execute([$scheduleId]);
        $schedule = $getScheduleStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$schedule) {
            throw new Exception('Schedule entry not found');
        }
        
        $oldPaidAmount = floatval($schedule['paid_amount']);
        $oldStatus = $schedule['status'];
        $currentDueAmount = floatval($schedule['due_amount']);
        
        // Determine new values
        $newPaidAmount = $paidAmount !== null ? $paidAmount : $oldPaidAmount;
        $newDueAmount = $dueAmount !== null ? $dueAmount : $currentDueAmount;
        
        // Validate paid amount doesn't exceed due amount
        if ($newPaidAmount > $newDueAmount) {
            throw new Exception('Paid amount cannot exceed due amount');
        }
        
        // Auto-determine status if not explicitly set
        if ($status === null) {
            if ($newPaidAmount <= 0) {
                $newStatus = 'pending';
            } elseif ($newPaidAmount >= $newDueAmount) {
                $newStatus = 'paid';
            } else {
                $newStatus = 'partial';
            }
        } else {
            $newStatus = $status;
        }
        
        // Calculate difference in paid amount
        $paidAmountDiff = $newPaidAmount - $oldPaidAmount;
        
        // Update schedule entry
        $updateScheduleSql = "UPDATE installment_schedules
                              SET paid_amount = ?,
                                  due_amount = ?,
                                  status = ?,
                                  updated_at = CURRENT_TIMESTAMP
                              WHERE id = ?";
        
        $updateScheduleStmt = $pdo->prepare($updateScheduleSql);
        $updateScheduleStmt->execute([
            $newPaidAmount,
            $newDueAmount,
            $newStatus,
            $scheduleId
        ]);
        
        // Update order balance if paid amount changed
        if (abs($paidAmountDiff) > 0.01) {
            $updateOrderSql = "UPDATE orders
                              SET paid_amount = paid_amount + ?,
                                  remaining_balance = remaining_balance - ?,
                                  updated_at = CURRENT_TIMESTAMP
                              WHERE id = ?";
            
            $updateOrderStmt = $pdo->prepare($updateOrderSql);
            $updateOrderStmt->execute([
                $paidAmountDiff,
                $paidAmountDiff,
                $schedule['order_id']
            ]);
            
            // Update customer totals
            $updateCustomerSql = "UPDATE customers
                                  SET total_paid = total_paid + ?,
                                      total_purchased = GREATEST(0, total_purchased - ?),
                                      updated_at = CURRENT_TIMESTAMP
                                  WHERE id = ?";
            
            $updateCustomerStmt = $pdo->prepare($updateCustomerSql);
            $updateCustomerStmt->execute([
                $paidAmountDiff,
                $paidAmountDiff,
                $schedule['customer_id']
            ]);
        }
        
        // Check if order is completed
        $checkOrderSql = "SELECT remaining_balance FROM orders WHERE id = ?";
        $checkOrderStmt = $pdo->prepare($checkOrderSql);
        $checkOrderStmt->execute([$schedule['order_id']]);
        $orderBalance = $checkOrderStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($orderBalance && floatval($orderBalance['remaining_balance']) <= 0) {
            // Mark order as completed
            $completeOrderSql = "UPDATE orders
                              SET status = 'completed',
                                  remaining_balance = 0,
                                  updated_at = CURRENT_TIMESTAMP
                              WHERE id = ?";
            
            $completeOrderStmt = $pdo->prepare($completeOrderSql);
            $completeOrderStmt->execute([$schedule['order_id']]);
        }
        
        // Commit transaction
        $pdo->commit();
        
        // Get updated schedule
        $getUpdatedSql = "SELECT * FROM installment_schedules WHERE id = ?";
        $getUpdatedStmt = $pdo->prepare($getUpdatedSql);
        $getUpdatedStmt->execute([$scheduleId]);
        $updatedSchedule = $getUpdatedStmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'message' => 'Schedule entry updated successfully',
            'schedule' => $updatedSchedule
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log('Update schedule entry error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

