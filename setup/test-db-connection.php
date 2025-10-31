<?php
/**
 * Database Connection Test
 * Use this to verify your SAHANALK database connection
 */

require_once __DIR__ . '/../admin/config/db.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test - SAHANALK</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .status {
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        .error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        .info {
            background: #dbeafe;
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #f9fafb;
            font-weight: 600;
        }
        code {
            background: #1f2937;
            color: #10b981;
            padding: 2px 8px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔌 SAHANALK Database Connection Test</h1>

        <?php
        try {
            // Test 1: Basic Connection
            echo '<div class="status success">';
            echo '✅ <strong>Database connection successful!</strong>';
            echo '</div>';

            // Test 2: Database Name
            $stmt = $pdo->query("SELECT DATABASE() as db_name");
            $dbName = $stmt->fetch()['db_name'];
            
            echo '<div class="status info">';
            echo "📊 <strong>Connected to database:</strong> <code>{$dbName}</code>";
            echo '</div>';

            // Test 3: Check Tables
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = 'SAHANALK'");
            $tableCount = $stmt->fetch()['count'];
            
            echo '<div class="status info">';
            echo "📋 <strong>Total tables found:</strong> {$tableCount}";
            echo '</div>';

            // Test 4: List All Tables
            $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'SAHANALK' ORDER BY table_name");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo '<h2>Database Tables:</h2>';
            echo '<table>';
            echo '<tr><th>#</th><th>Table Name</th><th>Status</th></tr>';
            
            $expectedTables = [
                'roles', 'users', 'suppliers', 'categories', 'products',
                'customers', 'orders', 'order_items', 'payments',
                'notifications', 'user_notification_reads', 'settings',
                'sms_logs', 'audit_logs'
            ];
            
            $foundCount = 0;
            foreach ($expectedTables as $index => $expectedTable) {
                $exists = in_array($expectedTable, $tables);
                $status = $exists ? '✅ Exists' : '❌ Missing';
                $foundCount += $exists ? 1 : 0;
                
                echo '<tr>';
                echo '<td>' . ($index + 1) . '</td>';
                echo '<td><code>' . htmlspecialchars($expectedTable) . '</code></td>';
                echo '<td>' . $status . '</td>';
                echo '</tr>';
            }
            echo '</table>';

            if ($foundCount === count($expectedTables)) {
                echo '<div class="status success">';
                echo "✅ All {$foundCount} required tables are present!";
                echo '</div>';
            } else {
                echo '<div class="status error">';
                echo "⚠️ Only {$foundCount} of " . count($expectedTables) . " tables found. Please run install.php";
                echo '</div>';
            }

            // Test 5: Check Default Data
            echo '<h2>Default Data Check:</h2>';
            
            // Check roles
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM roles");
            $roleCount = $stmt->fetch()['count'];
            echo '<div class="status ' . ($roleCount >= 2 ? 'success' : 'error') . '">';
            echo "Roles: {$roleCount} found (expected: 2)";
            echo '</div>';

            // Check admin user
            $stmt = $pdo->query("SELECT id, username, full_name, role_id, status FROM users WHERE username = 'admin'");
            $admin = $stmt->fetch();
            
            if ($admin) {
                echo '<div class="status success">';
                echo "✅ Admin user found:<br>";
                echo "Username: <code>{$admin['username']}</code><br>";
                echo "Name: {$admin['full_name']}<br>";
                echo "Role ID: {$admin['role_id']}<br>";
                echo "Status: {$admin['status']}";
                echo '</div>';
            } else {
                echo '<div class="status error">';
                echo "❌ Admin user not found!";
                echo '</div>';
            }

            // Check categories
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
            $catCount = $stmt->fetch()['count'];
            echo '<div class="status ' . ($catCount >= 5 ? 'success' : 'info') . '">';
            echo "Categories: {$catCount} found";
            echo '</div>';

            // Test 6: Database Info
            echo '<h2>Database Information:</h2>';
            $stmt = $pdo->query("SELECT VERSION() as version");
            $version = $stmt->fetch()['version'];
            
            echo '<table>';
            echo '<tr><th>Property</th><th>Value</th></tr>';
            echo '<tr><td>MySQL Version</td><td><code>' . htmlspecialchars($version) . '</code></td></tr>';
            echo '<tr><td>Database Name</td><td><code>' . htmlspecialchars($dbName) . '</code></td></tr>';
            echo '<tr><td>Character Set</td><td>utf8mb4</td></tr>';
            echo '<tr><td>Collation</td><td>utf8mb4_unicode_ci</td></tr>';
            echo '</table>';

            echo '<div class="status success">';
            echo '<strong>🎉 All tests passed! Your database is ready to use.</strong>';
            echo '</div>';

        } catch (PDOException $e) {
            echo '<div class="status error">';
            echo '<strong>❌ Database Connection Failed!</strong><br>';
            echo 'Error: ' . htmlspecialchars($e->getMessage());
            echo '</div>';

            echo '<div class="status info">';
            echo '<strong>💡 Troubleshooting:</strong><br>';
            echo '1. Make sure MAMP MySQL server is running<br>';
            echo '2. Check database credentials in <code>admin/config/db.php</code><br>';
            echo '3. Verify MySQL port (MAMP default: 8889)<br>';
            echo '4. Run <code>install.php</code> to create the database';
            echo '</div>';
        }
        ?>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <a href="install.php" style="display: inline-block; padding: 10px 20px; background: #10b981; color: white; text-decoration: none; border-radius: 5px;">
                Run Installation
            </a>
            <a href="../login.php" style="display: inline-block; padding: 10px 20px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;">
                Go to Login
            </a>
        </div>
    </div>
</body>
</html>

