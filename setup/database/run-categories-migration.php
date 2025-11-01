<?php
/**
 * Database Migration Script: Create Categories Table
 * Run this script once to create the categories table in your database
 */

// Include database configuration
require_once __DIR__ . '/../../admin/config/db.php';

try {
    // Read the SQL file
    $sqlFile = __DIR__ . '/create_categories_table.sql';
    
    if (!file_exists($sqlFile)) {
        die("Error: SQL file not found at $sqlFile\n");
    }
    
    $sql = file_get_contents($sqlFile);
    
    if ($sql === false) {
        die("Error: Failed to read SQL file\n");
    }
    
    echo "Running categories table migration...\n";
    echo "================================\n\n";
    
    // Execute the SQL
    $pdo->exec($sql);
    
    echo "✅ Categories table created successfully!\n";
    echo "✅ Sample data inserted (if not already present)\n\n";
    
    // Verify the table was created
    $stmt = $pdo->query("SHOW TABLES LIKE 'categories'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Verified: categories table exists in database\n\n";
        
        // Count records
        $countStmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
        $count = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "📊 Total categories in database: $count\n";
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
echo "You can now use the Category page in the admin panel.\n";
?>



