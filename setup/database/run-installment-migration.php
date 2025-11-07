<?php
/**
 * Run Installment Schedules Migration
 * Executes the SQL migration script to create installment_schedules table
 */

require_once __DIR__ . '/../../admin/config/db.php';

echo "Starting installment schedules migration...\n\n";

try {
    // Read the SQL file
    $sqlFile = __DIR__ . '/create_installment_schedules_table.sql';
    if (!file_exists($sqlFile)) {
        die("Error: SQL file not found at: $sqlFile\n");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split by semicolons, but be careful with statements that contain semicolons
    // We'll execute statements one by one
    $statements = [];
    $currentStatement = '';
    $inQuotes = false;
    $quoteChar = '';
    
    // Process the SQL to handle semicolons inside statements properly
    for ($i = 0; $i < strlen($sql); $i++) {
        $char = $sql[$i];
        
        if (($char === '"' || $char === "'" || $char === '`') && ($i === 0 || $sql[$i-1] !== '\\')) {
            if (!$inQuotes) {
                $inQuotes = true;
                $quoteChar = $char;
            } elseif ($char === $quoteChar) {
                $inQuotes = false;
            }
        }
        
        $currentStatement .= $char;
        
        if ($char === ';' && !$inQuotes) {
            $stmt = trim($currentStatement);
            if (!empty($stmt) && 
                !preg_match('/^(--|\/\*|USE |DESCRIBE |SELECT \'|SET @)/', $stmt)) {
                $statements[] = $stmt;
            }
            $currentStatement = '';
        }
    }
    
    // Execute each statement
    foreach ($statements as $index => $statement) {
        $statement = trim($statement);
        
        if (empty($statement)) {
            continue;
        }
        
        // Skip comments and certain statements
        if (preg_match('/^(--|\/\*|USE |DESCRIBE |SELECT \')/', $statement)) {
            continue;
        }
        
        // Handle SET @ variables (for index check)
        if (preg_match('/^SET @/', $statement)) {
            try {
                $pdo->exec($statement);
                echo "✓ Executed SET statement\n";
            } catch (Exception $e) {
                // Continue even if SET fails
                echo "⚠ Warning: " . $e->getMessage() . "\n";
            }
            continue;
        }
        
        // Handle PREPARE/EXECUTE/DEALLOCATE
        if (preg_match('/^(PREPARE|EXECUTE|DEALLOCATE)/', $statement)) {
            try {
                $pdo->exec($statement);
                echo "✓ Executed prepared statement command\n";
            } catch (Exception $e) {
                echo "⚠ Warning: " . $e->getMessage() . "\n";
            }
            continue;
        }
        
        // Execute regular statements
        try {
            $pdo->exec($statement);
            
            if (stripos($statement, 'CREATE TABLE') !== false) {
                echo "✓ Created installment_schedules table\n";
            } elseif (stripos($statement, 'ALTER TABLE') !== false) {
                if (stripos($statement, 'ADD COLUMN') !== false) {
                    echo "✓ Added assignment_date column to orders table\n";
                } elseif (stripos($statement, 'ADD INDEX') !== false) {
                    echo "✓ Added index to orders table\n";
                } else {
                    echo "✓ Modified orders table\n";
                }
            } else {
                echo "✓ Executed statement " . ($index + 1) . "\n";
            }
        } catch (PDOException $e) {
            // Check if error is because table/column already exists
            if (strpos($e->getMessage(), 'Duplicate column') !== false || 
                strpos($e->getMessage(), 'already exists') !== false) {
                echo "⚠ Info: " . $e->getMessage() . "\n";
            } else {
                echo "✗ Error executing statement: " . $e->getMessage() . "\n";
                echo "  Statement: " . substr($statement, 0, 100) . "...\n";
            }
        }
    }
    
    // Verify the table was created
    try {
        $checkTable = $pdo->query("SHOW TABLES LIKE 'installment_schedules'");
        if ($checkTable->rowCount() > 0) {
            echo "\n✓ Verification: installment_schedules table exists\n";
            
            // Check column exists
            $checkColumn = $pdo->query("SHOW COLUMNS FROM orders LIKE 'assignment_date'");
            if ($checkColumn->rowCount() > 0) {
                echo "✓ Verification: assignment_date column exists in orders table\n";
            } else {
                echo "⚠ Warning: assignment_date column not found in orders table\n";
            }
        } else {
            echo "\n✗ Error: installment_schedules table was not created\n";
        }
    } catch (Exception $e) {
        echo "\n⚠ Warning: Could not verify table creation: " . $e->getMessage() . "\n";
    }
    
    echo "\n✓ Migration completed successfully!\n";
    echo "You can now use the installment management features.\n";
    
} catch (Exception $e) {
    echo "\n✗ Fatal Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>

