<?php
/**
 * Quick Fix for Database Trigger - Fixes updated_at column error
 * Run this: http://localhost/www/Daily-Collection-Manager/fix-trigger-now.php
 */

require_once __DIR__ . '/admin/config/db.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Database Trigger</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #155724; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 15px 0; }
        .error { color: #721c24; padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 15px 0; }
        .info { color: #0c5460; padding: 15px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; margin: 15px 0; }
        .sql-box { background: #f8f9fa; padding: 15px; border-radius: 4px; border-left: 4px solid #007bff; margin: 15px 0; }
        pre { margin: 0; font-family: 'Courier New', monospace; font-size: 13px; }
        .btn { display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; margin-top: 15px; }
        .btn:hover { background: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Fix Database Trigger</h1>
        <p>This script fixes the trigger that's causing the "Unknown column 'updated_at'" error.</p>
        
<?php
try {
    // Step 1: Drop existing trigger
    echo '<div class="info">Step 1: Dropping existing trigger...</div>';
    $pdo->exec("DROP TRIGGER IF EXISTS trg_payments_update_customer_balance");
    echo '<div class="success">✓ Trigger dropped successfully</div>';
    
    // Step 2: Create new trigger without updated_at on orders, and include total_purchased reduction
    echo '<div class="info">Step 2: Creating fixed trigger...</div>';
    
    // Use a stored procedure approach or direct execution
    // Since PDO might not handle DELIMITER well, we'll use a simpler approach
    // Note: customers table has updated_at, orders table does NOT have updated_at
    $triggerSql = "CREATE TRIGGER trg_payments_update_customer_balance
AFTER INSERT ON payments
FOR EACH ROW
BEGIN
    -- Update customer: increase total_paid, decrease total_purchased, update timestamp
    UPDATE customers 
    SET total_paid = total_paid + NEW.amount,
        total_purchased = GREATEST(0, total_purchased - NEW.amount),
        updated_at = CURRENT_TIMESTAMP
    WHERE id = NEW.customer_id;
    
    -- Update order balance if payment is for an order (orders table doesn't have updated_at)
    IF NEW.order_id IS NOT NULL THEN
        UPDATE orders 
        SET paid_amount = paid_amount + NEW.amount, 
            remaining_balance = total_amount - (paid_amount + NEW.amount) 
        WHERE id = NEW.order_id;
    END IF;
END";
    
    // Execute the trigger creation
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec($triggerSql);
    
    echo '<div class="success">✓ Trigger created successfully</div>';
    echo '<div class="success"><strong>✅ SUCCESS!</strong><br>Trigger fixed! The "updated_at" error should be resolved now.</div>';
    echo '<p><a href="collection.php" class="btn">Go to Collection Page</a></p>';
    
} catch (PDOException $e) {
    $errorMsg = $e->getMessage();
    echo '<div class="error"><strong>❌ ERROR:</strong> ' . htmlspecialchars($errorMsg) . '</div>';
    
    // Check if it's a syntax error that might need DELIMITER
    if (strpos($errorMsg, 'syntax') !== false || strpos($errorMsg, 'DELIMITER') !== false) {
        echo '<div class="info">';
        echo '<strong>PDO cannot execute multi-statement triggers directly.</strong><br>';
        echo 'Please run this SQL manually in phpMyAdmin or MySQL command line:';
        echo '</div>';
    } else {
        echo '<div class="info">Please run the SQL manually in phpMyAdmin:</div>';
    }
    
    echo '<div class="sql-box">';
    echo '<strong>SQL to run:</strong><br><pre>';
    echo "DROP TRIGGER IF EXISTS trg_payments_update_customer_balance;\n\n";
    echo "DELIMITER $$\n\n";
    echo "CREATE TRIGGER trg_payments_update_customer_balance\n";
    echo "AFTER INSERT ON payments\n";
    echo "FOR EACH ROW\n";
    echo "BEGIN\n";
    echo "    -- Update customer: increase total_paid, decrease total_purchased\n";
    echo "    UPDATE customers \n";
    echo "    SET total_paid = total_paid + NEW.amount,\n";
    echo "        total_purchased = GREATEST(0, total_purchased - NEW.amount),\n";
    echo "        updated_at = CURRENT_TIMESTAMP\n";
    echo "    WHERE id = NEW.customer_id;\n";
    echo "    \n";
    echo "    -- Update order balance (orders table doesn't have updated_at)\n";
    echo "    IF NEW.order_id IS NOT NULL THEN\n";
    echo "        UPDATE orders \n";
    echo "        SET paid_amount = paid_amount + NEW.amount, \n";
    echo "            remaining_balance = total_amount - (paid_amount + NEW.amount) \n";
    echo "        WHERE id = NEW.order_id;\n";
    echo "    END IF;\n";
    echo "END$$\n\n";
    echo "DELIMITER ;";
    echo '</pre></div>';
    
    echo '<div class="info">';
    echo '<strong>Instructions:</strong><br>';
    echo '1. Open phpMyAdmin<br>';
    echo '2. Select your database<br>';
    echo '3. Go to SQL tab<br>';
    echo '4. Copy and paste the SQL above<br>';
    echo '5. Click "Go" to execute<br>';
    echo '</div>';
    
} catch (Exception $e) {
    echo '<div class="error"><strong>ERROR:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
    </div>
</body>
</html>

