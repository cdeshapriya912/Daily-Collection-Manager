<?php
/**
 * Database Trigger Fix Script
 * This script fixes the trigger that references non-existent 'updated_at' column
 * Run this once: http://localhost/www/Daily-Collection-Manager/fix-database-trigger.php
 */

require_once __DIR__ . '/admin/config/db.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Database Trigger</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
        .success { color: green; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0; }
        .info { color: #0c5460; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; margin: 10px 0; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Fix Database Trigger</h1>
    <p>This script will fix the trigger that's causing the "Unknown column 'updated_at'" error.</p>
    
<?php
try {
    // SQL to fix the trigger
    $sql = "
    DROP TRIGGER IF EXISTS trg_payments_update_customer_balance;
    
    DELIMITER $$
    
    CREATE TRIGGER trg_payments_update_customer_balance
    AFTER INSERT ON payments
    FOR EACH ROW
    BEGIN
        UPDATE customers 
        SET total_paid = total_paid + NEW.amount
        WHERE id = NEW.customer_id;
        
        IF NEW.order_id IS NOT NULL THEN
            UPDATE orders 
            SET paid_amount = paid_amount + NEW.amount,
                remaining_balance = total_amount - (paid_amount + NEW.amount)
            WHERE id = NEW.order_id;
        END IF;
    END$$
    
    DELIMITER ;
    ";
    
    echo '<div class="info">Starting trigger fix...</div>';
    
    // Drop existing trigger
    $pdo->exec("DROP TRIGGER IF EXISTS trg_payments_update_customer_balance");
    echo '<div class="success">✓ Dropped existing trigger</div>';
    
    // Create new trigger (without DELIMITER - PDO handles it)
    $createTriggerSql = "
    CREATE TRIGGER trg_payments_update_customer_balance
    AFTER INSERT ON payments
    FOR EACH ROW
    BEGIN
        UPDATE customers 
        SET total_paid = total_paid + NEW.amount
        WHERE id = NEW.customer_id;
        
        IF NEW.order_id IS NOT NULL THEN
            UPDATE orders 
            SET paid_amount = paid_amount + NEW.amount,
                remaining_balance = total_amount - (paid_amount + NEW.amount)
            WHERE id = NEW.order_id;
        END IF;
    END";
    
    $pdo->exec($createTriggerSql);
    echo '<div class="success">✓ Created fixed trigger</div>';
    
    echo '<div class="success"><strong>Trigger fixed successfully!</strong><br>You can now process payments without the "updated_at" error.</div>';
    echo '<p><a href="collection.php">Go back to Collection Page</a></p>';
    
} catch (PDOException $e) {
    echo '<div class="error"><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
    echo '<div class="info">Please run the SQL manually from: <code>setup/database/fix_trigger_updated_at.sql</code></div>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
} catch (Exception $e) {
    echo '<div class="error"><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
</body>
</html>

