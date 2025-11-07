<?php
/**
 * Simple Installment Migration Script
 * Run this directly via browser to create the tables
 */

// Check authentication if coming from admin area
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Allow access without auth for setup, but you can add auth here if needed
}

require_once __DIR__ . '/../../admin/config/db.php';

header('Content-Type: text/html; charset=utf-8');

// Set active page for menu highlighting
$activePage = 'install-installment';

// Check if we should show the admin menu
$showMenu = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Install Installment Tables</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <?php if ($showMenu): ?>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              primary: "#10b981",
              "background-light": "#f7fafc",
              "card-light": "#ffffff",
              "text-light": "#4a5568",
              "heading-light": "#1a202c",
              "border-light": "#e2e8f0",
            },
          },
        },
      };
    </script>
    <link rel="stylesheet" href="../../admin/assets/css/common.css">
    <?php endif; ?>
    <style>
        <?php if (!$showMenu): ?>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .content-wrapper { max-width: 800px; margin: 50px auto; padding: 20px; }
        <?php endif; ?>
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #10b981; margin-top: 0; }
        .success { color: #10b981; font-weight: bold; }
        .error { color: #ef4444; font-weight: bold; }
        .info { color: #3b82f6; }
        .output { background: #1a202c; color: #10b981; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap; margin-top: 15px; }
    </style>
</head>
<body class="<?php echo $showMenu ? 'bg-background-light' : ''; ?>">
    <?php if ($showMenu): ?>
    <div class="flex h-screen">
        <?php include __DIR__ . '/../../admin/partials/menu.php'; ?>
        <div class="flex-1 flex flex-col overflow-y-auto">
            <div class="p-6 lg:p-8">
    <?php else: ?>
    <div class="content-wrapper">
    <?php endif; ?>
    <div class="container">
        <h1>Install Installment Schedules Table</h1>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['run'])) {
            echo '<div class="output">';
            echo "Starting migration...\n\n";
            
            try {
                // 1. Create installment_schedules table
                echo "Creating installment_schedules table...\n";
                $createTableSql = "CREATE TABLE IF NOT EXISTS installment_schedules (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    order_id INT UNSIGNED NOT NULL,
                    schedule_date DATE NOT NULL,
                    due_amount DECIMAL(10, 2) NOT NULL,
                    paid_amount DECIMAL(10, 2) DEFAULT 0.00,
                    status ENUM('pending', 'paid', 'missed', 'partial') DEFAULT 'pending',
                    payment_id INT UNSIGNED DEFAULT NULL,
                    notes TEXT DEFAULT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    CONSTRAINT fk_installment_schedules_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                    CONSTRAINT fk_installment_schedules_payment FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE SET NULL,
                    INDEX idx_schedule_order_date (order_id, schedule_date),
                    INDEX idx_schedule_status (status),
                    INDEX idx_schedule_order (order_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                COMMENT='Daily installment payment schedule tracking'";
                
                $pdo->exec($createTableSql);
                echo "<span class='success'>✓ Created installment_schedules table</span>\n\n";
                
                // 2. Add assignment_date column to orders table
                echo "Adding assignment_date column to orders table...\n";
                
                // Check if column already exists
                $checkColumn = $pdo->query("SHOW COLUMNS FROM orders LIKE 'assignment_date'");
                if ($checkColumn->rowCount() == 0) {
                    $alterSql = "ALTER TABLE orders ADD COLUMN assignment_date DATE DEFAULT NULL COMMENT 'Date when installment was assigned'";
                    $pdo->exec($alterSql);
                    echo "<span class='success'>✓ Added assignment_date column</span>\n\n";
                } else {
                    echo "<span class='info'>ℹ assignment_date column already exists</span>\n\n";
                }
                
                // 3. Add index if it doesn't exist
                echo "Adding index for assignment_date...\n";
                $checkIndex = $pdo->query("SHOW INDEX FROM orders WHERE Key_name = 'idx_orders_assignment_date'");
                if ($checkIndex->rowCount() == 0) {
                    $indexSql = "ALTER TABLE orders ADD INDEX idx_orders_assignment_date (assignment_date)";
                    $pdo->exec($indexSql);
                    echo "<span class='success'>✓ Added index</span>\n\n";
                } else {
                    echo "<span class='info'>ℹ Index already exists</span>\n\n";
                }
                
                // 4. Verify
                echo "Verifying installation...\n";
                $verifyTable = $pdo->query("SHOW TABLES LIKE 'installment_schedules'");
                if ($verifyTable->rowCount() > 0) {
                    echo "<span class='success'>✓ Verification: installment_schedules table exists</span>\n";
                    
                    $verifyColumn = $pdo->query("SHOW COLUMNS FROM orders LIKE 'assignment_date'");
                    if ($verifyColumn->rowCount() > 0) {
                        echo "<span class='success'>✓ Verification: assignment_date column exists</span>\n";
                    }
                }
                
                echo "\n<span class='success'>\n✓✓✓ Migration completed successfully! ✓✓✓</span>\n";
                echo "\nYou can now use the installment management features.\n";
                echo "<a href='../../admin/assign-installment.php' style='color: #10b981; text-decoration: none; font-weight: bold;'>→ Go to Assign Installment Page</a>\n";
                
            } catch (Exception $e) {
                echo "<span class='error'>\n✗ Error: " . htmlspecialchars($e->getMessage()) . "</span>\n";
                echo "\nPlease check your database connection and try again.\n";
            }
            
            echo '</div>';
        } else {
            ?>
            <p>This will create the <code>installment_schedules</code> table and modify the <code>orders</code> table.</p>
            <form method="POST">
                <button type="submit" style="background: #10b981; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin-top: 20px;">
                    Run Migration
                </button>
            </form>
            <p style="margin-top: 20px; color: #6b7280;">
                <strong>Or</strong> click this link: 
                <a href="?run=1" style="color: #10b981;">Run Migration (GET)</a>
            </p>
            <?php
        }
        ?>
    </div>
    <?php if ($showMenu): ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    </div>
    <?php endif; ?>
</body>
</html>

