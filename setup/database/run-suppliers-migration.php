<?php
/**
 * Database Migration Script: Create Suppliers Table
 * Run this script once to create the suppliers table in your database
 */

// Include database configuration
require_once __DIR__ . '/../../admin/config/db.php';

try {
    // Read the SQL file
    $sqlFile = __DIR__ . '/create_suppliers_table.sql';
    
    if (!file_exists($sqlFile)) {
        die("Error: SQL file not found at $sqlFile\n");
    }
    
    $sql = file_get_contents($sqlFile);
    
    if ($sql === false) {
        die("Error: Failed to read SQL file\n");
    }
    
    echo "Running suppliers table migration...\n";
    echo "================================\n\n";
    
    // Execute the SQL
    $pdo->exec($sql);
    
    echo "✅ Suppliers table created successfully!\n";
    echo "✅ Sample data inserted (if not already present)\n\n";
    
    // Verify the table was created
    $stmt = $pdo->query("SHOW TABLES LIKE 'suppliers'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Verified: suppliers table exists in database\n\n";
        
        // Count records
        $countStmt = $pdo->query("SELECT COUNT(*) as count FROM suppliers");
        $count = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "📊 Total suppliers in database: $count\n";
    } else {
        echo "❌ Warning: Could not verify table creation\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n================================\n";
echo "Migration completed successfully!\n";
echo "You can now use the Suppliers page in the admin panel.\n";
?>



