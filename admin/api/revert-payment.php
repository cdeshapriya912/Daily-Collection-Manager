<?php
/**
 * Revert Payment API - TESTING ONLY
 * Reverts payments and marks schedules as unpaid
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
    
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        if (isset($data['revert_all']) && $data['revert_all'] && !empty($data['customer_id'])) {
            // Revert all payments for a customer
            $customerId = intval($data['customer_id']);
            
            // Get customer values BEFORE update for verification
            $customerBeforeSql = "SELECT total_paid, total_purchased FROM customers WHERE id = ? FOR UPDATE";
            $customerBeforeStmt = $pdo->prepare($customerBeforeSql);
            $customerBeforeStmt->execute([$customerId]);
            $customerBefore = $customerBeforeStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$customerBefore) {
                throw new Exception("Customer ID {$customerId} does not exist.");
            }
            
            $oldTotalPaid = floatval($customerBefore['total_paid']);
            $oldTotalPurchased = floatval($customerBefore['total_purchased']);
            
            // Get all payments for this customer
            $paymentsSql = "SELECT id, amount, order_id FROM payments WHERE customer_id = ? ORDER BY payment_date DESC";
            $paymentsStmt = $pdo->prepare($paymentsSql);
            $paymentsStmt->execute([$customerId]);
            $payments = $paymentsStmt->fetchAll(PDO::FETCH_ASSOC);
            
            $totalReverted = 0;
            $totalAmount = 0;
            
            error_log("BEFORE revert all: customer_id={$customerId}, total_paid={$oldTotalPaid}, total_purchased={$oldTotalPurchased}, payments_count=" . count($payments));
            
            foreach ($payments as $payment) {
                $amount = floatval($payment['amount']);
                $orderId = $payment['order_id'] ? intval($payment['order_id']) : null;
                
                // Revert schedules for this payment
                if ($orderId) {
                    // Get all schedules that were paid by this payment
                    $getSchedulesSql = "SELECT id, paid_amount FROM installment_schedules 
                                       WHERE order_id = ? AND payment_id = ?";
                    $getSchedulesStmt = $pdo->prepare($getSchedulesSql);
                    $getSchedulesStmt->execute([$orderId, $payment['id']]);
                    $affectedSchedules = $getSchedulesStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Update all schedules that were paid by this payment
                    $schedulesSql = "UPDATE installment_schedules 
                                    SET paid_amount = 0,
                                        status = CASE 
                                            WHEN schedule_date < CURDATE() THEN 'missed'
                                            ELSE 'pending'
                                        END,
                                        payment_id = NULL
                                    WHERE order_id = ? AND payment_id = ?";
                    $schedulesStmt = $pdo->prepare($schedulesSql);
                    $schedulesStmt->execute([$orderId, $payment['id']]);
                }
                
                // Delete payment record
                $deletePaymentSql = "DELETE FROM payments WHERE id = ?";
                $deleteStmt = $pdo->prepare($deletePaymentSql);
                $deleteStmt->execute([$payment['id']]);
                
                // Verify deletion
                if ($deleteStmt->rowCount() === 0) {
                    throw new Exception("Failed to delete payment ID: {$payment['id']}");
                }
                
                $totalAmount += $amount;
                $totalReverted++;
            }
            
            // Update customer totals AFTER all payments are deleted
            // This reverses the cumulative effect of all deleted payments
            if ($totalAmount > 0) {
                $newTotalPaid = max(0, $oldTotalPaid - $totalAmount);
                $newTotalPurchased = $oldTotalPurchased + $totalAmount;
                
                $updateCustomerSql = "UPDATE customers 
                                     SET total_paid = ?,
                                         total_purchased = ?,
                                         updated_at = CURRENT_TIMESTAMP
                                     WHERE id = ?";
                $updateCustomerStmt = $pdo->prepare($updateCustomerSql);
                $updateCustomerStmt->execute([$newTotalPaid, $newTotalPurchased, $customerId]);
                
                // Verify the update
                $verifyCustomerSql = "SELECT total_paid, total_purchased FROM customers WHERE id = ?";
                $verifyStmt = $pdo->prepare($verifyCustomerSql);
                $verifyStmt->execute([$customerId]);
                $customerAfter = $verifyStmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$customerAfter) {
                    throw new Exception("Customer ID {$customerId} not found after update.");
                }
                
                $actualTotalPaid = floatval($customerAfter['total_paid']);
                $actualTotalPurchased = floatval($customerAfter['total_purchased']);
                
                error_log("AFTER revert all: customer_id={$customerId}, total_paid={$actualTotalPaid} (expected: {$newTotalPaid}), total_purchased={$actualTotalPurchased} (expected: {$newTotalPurchased}), total_reverted={$totalAmount}");
                
                // Verify the values match what we expect
                if (abs($actualTotalPaid - $newTotalPaid) > 0.01 || abs($actualTotalPurchased - $newTotalPurchased) > 0.01) {
                    error_log("ERROR: Customer totals mismatch! Expected total_paid={$newTotalPaid}, got {$actualTotalPaid}. Expected total_purchased={$newTotalPurchased}, got {$actualTotalPurchased}");
                    throw new Exception("Customer totals update verification failed. Expected total_paid={$newTotalPaid}, got {$actualTotalPaid}");
                }
            }
            
            // Update orders - get order IDs BEFORE deleting payments
            $orderIds = [];
            foreach ($payments as $payment) {
                if (!empty($payment['order_id']) && !in_array($payment['order_id'], $orderIds)) {
                    $orderIds[] = intval($payment['order_id']);
                }
            }
            
            foreach ($orderIds as $orderId) {
                // Recalculate order balance from schedules (more accurate)
                $orderSchedulesSql = "SELECT COALESCE(SUM(paid_amount), 0) as total_paid FROM installment_schedules WHERE order_id = ?";
                $orderSchedulesStmt = $pdo->prepare($orderSchedulesSql);
                $orderSchedulesStmt->execute([$orderId]);
                $orderSchedulesData = $orderSchedulesStmt->fetch(PDO::FETCH_ASSOC);
                $newPaidAmountFromSchedules = floatval($orderSchedulesData['total_paid'] || 0);
                
                // Also check payments table
                $orderPaymentsSql = "SELECT COALESCE(SUM(amount), 0) as total_paid FROM payments WHERE order_id = ?";
                $orderPaymentsStmt = $pdo->prepare($orderPaymentsSql);
                $orderPaymentsStmt->execute([$orderId]);
                $orderPaymentData = $orderPaymentsStmt->fetch(PDO::FETCH_ASSOC);
                $newPaidAmountFromPayments = floatval($orderPaymentData['total_paid'] || 0);
                
                // Use the higher value
                $newPaidAmount = max($newPaidAmountFromSchedules, $newPaidAmountFromPayments);
                
                $orderDetailsSql = "SELECT total_amount FROM orders WHERE id = ?";
                $orderDetailsStmt = $pdo->prepare($orderDetailsSql);
                $orderDetailsStmt->execute([$orderId]);
                $orderDetails = $orderDetailsStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($orderDetails) {
                    $totalAmount = floatval($orderDetails['total_amount']);
                    $remainingBalance = max(0, $totalAmount - $newPaidAmount);
                    $status = ($remainingBalance <= 0.01) ? 'completed' : 'active';
                    
                    $updateOrderSql = "UPDATE orders 
                                      SET paid_amount = ?,
                                          remaining_balance = ?,
                                          status = ?
                                      WHERE id = ?";
                    $updateOrderStmt = $pdo->prepare($updateOrderSql);
                    $updateOrderStmt->execute([$newPaidAmount, $remainingBalance, $status, $orderId]);
                    
                    error_log("Order updated (revert all): order_id={$orderId}, paid_amount={$newPaidAmount}, remaining_balance={$remainingBalance}, status={$status}");
                }
            }
            
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => "Reverted {$totalReverted} payment(s) totaling " . number_format($totalAmount, 2),
                'reverted_count' => $totalReverted,
                'total_amount' => $totalAmount
            ]);
            
        } elseif (!empty($data['payment_id'])) {
            // Revert a specific payment
            $paymentId = intval($data['payment_id']);
            $orderId = isset($data['order_id']) && $data['order_id'] ? intval($data['order_id']) : null;
            
            // Get payment details
            $paymentSql = "SELECT id, customer_id, order_id, amount FROM payments WHERE id = ?";
            $paymentStmt = $pdo->prepare($paymentSql);
            $paymentStmt->execute([$paymentId]);
            $payment = $paymentStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$payment) {
                throw new Exception('Payment not found');
            }
            
            $amount = floatval($payment['amount']);
            $customerId = intval($payment['customer_id']);
            $paymentOrderId = $payment['order_id'] ? intval($payment['order_id']) : null;
            
            // Get customer values BEFORE update for verification
            $customerBeforeSql = "SELECT total_paid, total_purchased FROM customers WHERE id = ? FOR UPDATE";
            $customerBeforeStmt = $pdo->prepare($customerBeforeSql);
            $customerBeforeStmt->execute([$customerId]);
            $customerBefore = $customerBeforeStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$customerBefore) {
                throw new Exception("Customer ID {$customerId} does not exist.");
            }
            
            $oldTotalPaid = floatval($customerBefore['total_paid']);
            $oldTotalPurchased = floatval($customerBefore['total_purchased']);
            
            error_log("BEFORE revert: customer_id={$customerId}, total_paid={$oldTotalPaid}, total_purchased={$oldTotalPurchased}, reverting_amount={$amount}");
            
            // Revert schedules for this payment
            // First, get all schedules that were paid by this payment
            if ($paymentOrderId) {
                // Get schedules that have this payment_id
                $getSchedulesSql = "SELECT id, paid_amount FROM installment_schedules 
                                   WHERE order_id = ? AND payment_id = ?";
                $getSchedulesStmt = $pdo->prepare($getSchedulesSql);
                $getSchedulesStmt->execute([$paymentOrderId, $paymentId]);
                $affectedSchedules = $getSchedulesStmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Get payment date for fallback logic
                $paymentDateSql = "SELECT payment_date, amount FROM payments WHERE id = ?";
                $paymentDateStmt = $pdo->prepare($paymentDateSql);
                $paymentDateStmt->execute([$paymentId]);
                $paymentDateData = $paymentDateStmt->fetch(PDO::FETCH_ASSOC);
                $paymentDate = $paymentDateData ? $paymentDateData['payment_date'] : null;
                $paymentAmount = $paymentDateData ? floatval($paymentDateData['amount']) : 0;
                
                // First, update all schedules that have this payment_id
                $schedulesSql = "UPDATE installment_schedules 
                                SET paid_amount = 0,
                                    status = CASE 
                                        WHEN schedule_date < CURDATE() THEN 'missed'
                                        ELSE 'pending'
                                    END,
                                    payment_id = NULL
                                WHERE order_id = ? AND payment_id = ?";
                $schedulesStmt = $pdo->prepare($schedulesSql);
                $schedulesStmt->execute([$paymentOrderId, $paymentId]);
                $schedulesUpdated = $schedulesStmt->rowCount();
                
                // If no schedules were updated by payment_id, try to find schedules paid on the same date
                // This handles cases where payment_id wasn't set properly
                if ($schedulesUpdated === 0 && $paymentDate) {
                    // Get schedules that were paid on the payment date
                    $findSchedulesSql = "SELECT id, paid_amount, schedule_date 
                                        FROM installment_schedules 
                                        WHERE order_id = ? 
                                          AND DATE(schedule_date) = DATE(?)
                                          AND paid_amount > 0
                                          AND (payment_id IS NULL OR payment_id = ?)
                                        ORDER BY schedule_date ASC";
                    $findSchedulesStmt = $pdo->prepare($findSchedulesSql);
                    $findSchedulesStmt->execute([$paymentOrderId, $paymentDate, $paymentId]);
                    $foundSchedules = $findSchedulesStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Revert schedules that match the payment amount or were paid on that date
                    $remainingAmount = $paymentAmount;
                    foreach ($foundSchedules as $schedule) {
                        if ($remainingAmount <= 0) break;
                        
                        $schedulePaid = floatval($schedule['paid_amount']);
                        if ($schedulePaid > 0) {
                            // Revert this schedule
                            $revertScheduleSql = "UPDATE installment_schedules 
                                                 SET paid_amount = 0,
                                                     status = CASE 
                                                         WHEN schedule_date < CURDATE() THEN 'missed'
                                                         ELSE 'pending'
                                                     END,
                                                     payment_id = NULL
                                                 WHERE id = ?";
                            $revertScheduleStmt = $pdo->prepare($revertScheduleSql);
                            $revertScheduleStmt->execute([$schedule['id']]);
                            $remainingAmount -= $schedulePaid;
                        }
                    }
                }
            }
            
            // Update customer totals FIRST (before deleting payment)
            // This reverses what the trigger did when payment was inserted:
            // Trigger: total_paid += amount, total_purchased -= amount
            // Revert: total_paid -= amount, total_purchased += amount
            $newTotalPaid = max(0, $oldTotalPaid - $amount);
            $newTotalPurchased = $oldTotalPurchased + $amount;
            
            $updateCustomerSql = "UPDATE customers 
                                 SET total_paid = ?,
                                     total_purchased = ?,
                                     updated_at = CURRENT_TIMESTAMP
                                 WHERE id = ?";
            $updateCustomerStmt = $pdo->prepare($updateCustomerSql);
            $updateCustomerStmt->execute([$newTotalPaid, $newTotalPurchased, $customerId]);
            
            // Verify the update by checking customer values after
            $verifyCustomerSql = "SELECT total_paid, total_purchased FROM customers WHERE id = ?";
            $verifyStmt = $pdo->prepare($verifyCustomerSql);
            $verifyStmt->execute([$customerId]);
            $customerAfter = $verifyStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$customerAfter) {
                throw new Exception("Customer ID {$customerId} not found after update.");
            }
            
            $actualTotalPaid = floatval($customerAfter['total_paid']);
            $actualTotalPurchased = floatval($customerAfter['total_purchased']);
            
            error_log("AFTER revert: customer_id={$customerId}, total_paid={$actualTotalPaid} (expected: {$newTotalPaid}), total_purchased={$actualTotalPurchased} (expected: {$newTotalPurchased})");
            
            // Verify the values match what we expect
            if (abs($actualTotalPaid - $newTotalPaid) > 0.01 || abs($actualTotalPurchased - $newTotalPurchased) > 0.01) {
                error_log("ERROR: Customer totals mismatch! Expected total_paid={$newTotalPaid}, got {$actualTotalPaid}. Expected total_purchased={$newTotalPurchased}, got {$actualTotalPurchased}");
                throw new Exception("Customer totals update verification failed. Expected total_paid={$newTotalPaid}, got {$actualTotalPaid}");
            }
            
            // Delete payment record AFTER updating customer
            $deletePaymentSql = "DELETE FROM payments WHERE id = ?";
            $deleteStmt = $pdo->prepare($deletePaymentSql);
            $deleteStmt->execute([$paymentId]);
            
            // Verify the deletion
            if ($deleteStmt->rowCount() === 0) {
                throw new Exception('Failed to delete payment. Payment may not exist.');
            }
            
            // Update order if exists - recalculate from schedules
            if ($paymentOrderId) {
                // Recalculate order balance from schedules (more accurate)
                $orderSchedulesSql = "SELECT COALESCE(SUM(paid_amount), 0) as total_paid FROM installment_schedules WHERE order_id = ?";
                $orderSchedulesStmt = $pdo->prepare($orderSchedulesSql);
                $orderSchedulesStmt->execute([$paymentOrderId]);
                $orderSchedulesData = $orderSchedulesStmt->fetch(PDO::FETCH_ASSOC);
                $newPaidAmountFromSchedules = floatval($orderSchedulesData['total_paid'] || 0);
                
                // Also check payments table
                $orderPaymentsSql = "SELECT COALESCE(SUM(amount), 0) as total_paid FROM payments WHERE order_id = ?";
                $orderPaymentsStmt = $pdo->prepare($orderPaymentsSql);
                $orderPaymentsStmt->execute([$paymentOrderId]);
                $orderPaymentData = $orderPaymentsStmt->fetch(PDO::FETCH_ASSOC);
                $newPaidAmountFromPayments = floatval($orderPaymentData['total_paid'] || 0);
                
                // Use the higher value
                $newPaidAmount = max($newPaidAmountFromSchedules, $newPaidAmountFromPayments);
                
                $orderDetailsSql = "SELECT total_amount FROM orders WHERE id = ?";
                $orderDetailsStmt = $pdo->prepare($orderDetailsSql);
                $orderDetailsStmt->execute([$paymentOrderId]);
                $orderDetails = $orderDetailsStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($orderDetails) {
                    $totalAmount = floatval($orderDetails['total_amount']);
                    $remainingBalance = max(0, $totalAmount - $newPaidAmount);
                    $status = ($remainingBalance <= 0.01) ? 'completed' : 'active';
                    
                    $updateOrderSql = "UPDATE orders 
                                      SET paid_amount = ?,
                                          remaining_balance = ?,
                                          status = ?
                                      WHERE id = ?";
                    $updateOrderStmt = $pdo->prepare($updateOrderSql);
                    $updateOrderStmt->execute([$newPaidAmount, $remainingBalance, $status, $paymentOrderId]);
                    
                    error_log("Order updated (payment revert): order_id={$paymentOrderId}, paid_amount={$newPaidAmount}, remaining_balance={$remainingBalance}, status={$status}");
                }
            }
            
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Payment reverted successfully',
                'amount' => $amount
            ]);
            
        } elseif (!empty($data['schedule_id'])) {
            // Revert a specific schedule payment
            $scheduleId = intval($data['schedule_id']);
            $orderId = intval($data['order_id']);
            $amount = floatval($data['amount'] || 0);
            
            // Get schedule details
            $scheduleSql = "SELECT id, order_id, paid_amount, payment_id FROM installment_schedules WHERE id = ?";
            $scheduleStmt = $pdo->prepare($scheduleSql);
            $scheduleStmt->execute([$scheduleId]);
            $schedule = $scheduleStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$schedule) {
                throw new Exception('Schedule not found');
            }
            
            $paidAmount = floatval($schedule['paid_amount'] || 0);
            $paymentId = $schedule['payment_id'] ? intval($schedule['payment_id']) : null;
            
            // Get order and customer info
            $orderSql = "SELECT customer_id, total_amount FROM orders WHERE id = ?";
            $orderStmt = $pdo->prepare($orderSql);
            $orderStmt->execute([$orderId]);
            $order = $orderStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) {
                throw new Exception('Order not found');
            }
            
            $customerId = intval($order['customer_id']);
            
            // Update schedule - reset paid amount
            $updateScheduleSql = "UPDATE installment_schedules 
                                 SET paid_amount = 0,
                                     status = CASE 
                                         WHEN schedule_date < CURDATE() THEN 'missed'
                                         ELSE 'pending'
                                     END,
                                     payment_id = NULL
                                 WHERE id = ?";
            $updateScheduleStmt = $pdo->prepare($updateScheduleSql);
            $updateScheduleStmt->execute([$scheduleId]);
            
            // Update customer totals
            // Reverse what was done when payment was applied to this schedule
            if ($paidAmount > 0) {
                $updateCustomerSql = "UPDATE customers 
                                     SET total_paid = GREATEST(0, total_paid - ?),
                                         total_purchased = total_purchased + ?,
                                         updated_at = CURRENT_TIMESTAMP
                                     WHERE id = ?";
                $updateCustomerStmt = $pdo->prepare($updateCustomerSql);
                $updateCustomerStmt->execute([$paidAmount, $paidAmount, $customerId]);
                
                // Verify the update
                $rowsAffected = $updateCustomerStmt->rowCount();
                if ($rowsAffected === 0) {
                    // Check if customer exists
                    $checkCustomerSql = "SELECT id FROM customers WHERE id = ?";
                    $checkStmt = $pdo->prepare($checkCustomerSql);
                    $checkStmt->execute([$customerId]);
                    if (!$checkStmt->fetch()) {
                        throw new Exception("Customer ID {$customerId} does not exist.");
                    }
                    error_log("Warning: Customer update affected 0 rows for customer ID {$customerId}, paid_amount={$paidAmount}");
                } else {
                    error_log("Customer totals updated (schedule revert): customer_id={$customerId}, paid_amount={$paidAmount}, rows_affected={$rowsAffected}");
                }
            }
            
            // Update order balance - recalculate from schedules
            $orderSchedulesSql = "SELECT COALESCE(SUM(paid_amount), 0) as total_paid FROM installment_schedules WHERE order_id = ?";
            $orderSchedulesStmt = $pdo->prepare($orderSchedulesSql);
            $orderSchedulesStmt->execute([$orderId]);
            $orderSchedulesData = $orderSchedulesStmt->fetch(PDO::FETCH_ASSOC);
            $newPaidAmount = floatval($orderSchedulesData['total_paid'] || 0);
            
            // Also check payments table for this order (as backup)
            $orderPaymentsSql = "SELECT COALESCE(SUM(amount), 0) as total_paid_from_payments FROM payments WHERE order_id = ?";
            $orderPaymentsStmt = $pdo->prepare($orderPaymentsSql);
            $orderPaymentsStmt->execute([$orderId]);
            $orderPaymentsData = $orderPaymentsStmt->fetch(PDO::FETCH_ASSOC);
            $paidFromPayments = floatval($orderPaymentsData['total_paid_from_payments'] || 0);
            
            // Use the higher value to ensure accuracy
            $newPaidAmount = max($newPaidAmount, $paidFromPayments);
            
            $totalAmount = floatval($order['total_amount']);
            $remainingBalance = max(0, $totalAmount - $newPaidAmount);
            $status = ($remainingBalance <= 0.01) ? 'completed' : 'active';
            
            $updateOrderSql = "UPDATE orders 
                              SET paid_amount = ?,
                                  remaining_balance = ?,
                                  status = ?
                              WHERE id = ?";
            $updateOrderStmt = $pdo->prepare($updateOrderSql);
            $updateOrderStmt->execute([$newPaidAmount, $remainingBalance, $status, $orderId]);
            
            error_log("Order updated: order_id={$orderId}, paid_amount={$newPaidAmount}, remaining_balance={$remainingBalance}, status={$status}");
            
            // If payment exists and has no more schedules, optionally delete it
            if ($paymentId) {
                $remainingSchedulesSql = "SELECT COUNT(*) as count FROM installment_schedules WHERE payment_id = ? AND paid_amount > 0";
                $remainingSchedulesStmt = $pdo->prepare($remainingSchedulesSql);
                $remainingSchedulesStmt->execute([$paymentId]);
                $remainingData = $remainingSchedulesStmt->fetch(PDO::FETCH_ASSOC);
                
                if (intval($remainingData['count'] || 0) === 0) {
                    // No more schedules linked to this payment, delete it
                    $deletePaymentSql = "DELETE FROM payments WHERE id = ?";
                    $deleteStmt = $pdo->prepare($deletePaymentSql);
                    $deleteStmt->execute([$paymentId]);
                }
            }
            
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Schedule payment reverted successfully',
                'amount' => $paidAmount
            ]);
            
        } else {
            throw new Exception('Invalid request. Please provide payment_id, schedule_id, or customer_id with revert_all');
        }
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log('Revert payment error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

