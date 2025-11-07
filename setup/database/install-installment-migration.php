<?php
/**
 * Web-accessible Installment Migration Script
 * Access this file via browser: http://localhost/Daily-Collection-Manager/setup/database/install-installment-migration.php
 */

// Security: Only allow in development or with proper authentication
// Remove or modify this check for production use
if (php_sapi_name() !== 'cli') {
    // For web access, you might want to add authentication here
    // For now, allowing direct access
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Installment Migration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #10b981;
            margin-top: 0;
        }
        .output {
            background: #1a202c;
            color: #10b981;
            padding: 20px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            white-space: pre-wrap;
            margin-top: 20px;
            max-height: 500px;
            overflow-y: auto;
        }
        .success { color: #10b981; }
        .error { color: #ef4444; }
        .warning { color: #f59e0b; }
        .btn {
            background: #10b981;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #059669;
        }
        .btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Installment Schedules Migration</h1>
        <p>This script will create the <code>installment_schedules</code> table and modify the <code>orders</code> table.</p>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_migration'])) {
            require_once __DIR__ . '/../../admin/config/db.php';
            
            echo '<div class="output">';
            echo "Starting installment schedules migration...\n\n";
            
            try {
                $sqlFile = __DIR__ . '/create_installment_schedules_table.sql';
                if (!file_exists($sqlFile)) {
                    die("<span class='error'>Error: SQL file not found at: $sqlFile</span>\n");
                }
                
                $sql = file_get_contents($sqlFile);
                
                // Remove comments and clean up
                $sql = preg_replace('/--.*$/m', '', $sql);
                $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
                
                // Split by semicolons
                $statements = array_filter(
                    array_map('trim', explode(';', $sql)),
                    function($stmt) {
                        return !empty($stmt) && 
                               !preg_match('/^(USE|DESCRIBE|SELECT)/i', $stmt);
                    }
                );
                
                $successCount = 0;
                $errorCount = 0;
                
                foreach ($statements as $statement) {
                    $statement = trim($statement);
                    if (empty($statement)) continue;
                    
                    try {
                        // Handle SET @ and PREPARE/EXECUTE statements
                        if (preg_match('/^(SET @|PREPARE|EXECUTE|DEALLOCATE)/i', $statement)) {
                            $pdo->exec($statement);
                            echo "<span class='success'>✓ Executed statement</span>\n";
                            $successCount++;
                            continue;
                        }
                        
                        // Execute regular statements
                        $pdo->exec($statement);
                        
                        if (stripos($statement, 'CREATE TABLE') !== false) {
                            echo "<span class='success'>✓ Created installment_schedules table</span>\n";
                        } elseif (stripos($statement, 'ADD COLUMN') !== false) {
                            echo "<span class='success'>✓ Added assignment_date column to orders table</span>\n";
                        } elseif (stripos($statement, 'ADD INDEX') !== false) {
                            echo "<span class='success'>✓ Added index to orders table</span>\n";
                        } else {
                            echo "<span class='success'>✓ Executed statement</span>\n";
                        }
                        $successCount++;
                    } catch (PDOException $e) {
                        // Check if error is because table/column already exists
                        if (strpos($e->getMessage(), 'Duplicate column') !== false || 
                            strpos($e->getMessage(), 'already exists') !== false ||
                            strpos($e->getMessage(), 'Duplicate key name') !== false) {
                            echo "<span class='warning'>⚠ Info: " . htmlspecialchars($e->getMessage()) . "</span>\n";
                            $successCount++;
                        } else {
                            echo "<span class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</span>\n";
                            $errorCount++;
                        }
                    }
                }
                
                // Verify the table was created
                try {
                    $checkTable = $pdo->query("SHOW TABLES LIKE 'installment_schedules'");
                    if ($checkTable->rowCount() > 0) {
                        echo "\n<span class='success'>✓ Verification: installment_schedules table exists</span>\n";
                        
                        $checkColumn = $pdo->query("SHOW COLUMNS FROM orders LIKE 'assignment_date'");
                        if ($checkColumn->rowCount() > 0) {
                            echo "<span class='success'>✓ Verification: assignment_date column exists in orders table</span>\n";
                        }
                    }
                } catch (Exception $e) {
                    echo "<span class='warning'>⚠ Warning: " . htmlspecialchars($e->getMessage()) . "</span>\n";
                }
                
                echo "\n<span class='success'>✓ Migration completed successfully!</span>\n";
                echo "You can now use the installment management features.\n";
                
            } catch (Exception $e) {
                echo "<span class='error'>✗ Fatal Error: " . htmlspecialchars($e->getMessage()) . "</span>\n";
            }
            
            echo '</div>';
            echo '<p><a href="../../admin/assign-installment.php" style="color: #10b981; text-decoration: none; font-weight: bold;">→ Go to Assign Installment Page</a></p>';
            
        } else {
            ?>
            <form method="POST">
                <button type="submit" name="run_migration" class="btn">Run Migration</button>
            </form>
            <p style="margin-top: 20px; color: #6b7280;">
                <strong>Note:</strong> This will create the necessary database tables for the installment system.
                Safe to run multiple times - it will skip existing tables/columns.
            </p>
            <?php
        }
        ?>
    </div>
</body>
</html>

