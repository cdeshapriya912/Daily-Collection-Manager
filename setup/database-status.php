<?php
/**
 * Quick Database Status Check
 * Check if database exists and MySQL is running
 */

$DB_HOST = '127.0.0.1';
$DB_PORT = '8889'; // Try this first, then 3306
$DB_USER = 'root';
$DB_PASS = 'root';
$DB_NAME = 'SAHANALK';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Status - SAHANALK</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 600px;
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
        h1 { color: #333; margin-bottom: 10px; }
        .status {
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid;
        }
        .success {
            background: #d1fae5;
            color: #065f46;
            border-left-color: #10b981;
        }
        .error {
            background: #fee2e2;
            color: #991b1b;
            border-left-color: #ef4444;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            border-left-color: #ffc107;
        }
        .info {
            background: #dbeafe;
            color: #1e40af;
            border-left-color: #3b82f6;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #10b981;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
            font-weight: 600;
        }
        .btn:hover { background: #059669; }
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
        <h1>📊 Database Status Check</h1>
        
        <?php
        $mysqlRunning = false;
        $databaseExists = false;
        $workingPort = null;
        $portsToTry = [8889, 3306];
        
        // Check if MySQL is running
        foreach ($portsToTry as $port) {
            try {
                $dsn = "mysql:host={$DB_HOST};port={$port};charset=utf8mb4";
                $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 3
                ]);
                $mysqlRunning = true;
                $workingPort = $port;
                break;
            } catch (PDOException $e) {
                continue;
            }
        }
        
        if ($mysqlRunning) {
            echo '<div class="status success">';
            echo '✅ <strong>MySQL Server is Running</strong><br>';
            echo "Port: <code>{$workingPort}</code><br>";
            echo "Host: <code>{$DB_HOST}</code>";
            echo '</div>';
            
            // Check if database exists
            try {
                $dsn = "mysql:host={$DB_HOST};port={$workingPort};dbname={$DB_NAME};charset=utf8mb4";
                $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 3
                ]);
                
                // Check tables
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = '{$DB_NAME}'");
                $tableCount = $stmt->fetch()['count'];
                
                $databaseExists = true;
                
                echo '<div class="status success">';
                echo '✅ <strong>Database Exists</strong><br>';
                echo "Database: <code>{$DB_NAME}</code><br>";
                echo "Tables: <code>{$tableCount}</code> found";
                echo '</div>';
                
                if ($tableCount < 10) {
                    echo '<div class="status warning">';
                    echo '⚠️ <strong>Database is empty or incomplete</strong><br>';
                    echo 'Expected at least 14 tables. Please run installation.';
                    echo '</div>';
                    echo '<a href="install.php" class="btn">Run Database Installation</a>';
                } else {
                    echo '<div class="status success">';
                    echo '🎉 <strong>Database is ready!</strong><br>';
                    echo 'All tables are installed.';
                    echo '</div>';
                    echo '<a href="test-db-connection.php" class="btn">Test Connection</a>';
                }
                
            } catch (PDOException $e) {
                echo '<div class="status error">';
                echo '❌ <strong>Database Does Not Exist</strong><br>';
                echo "Database <code>{$DB_NAME}</code> was not found.";
                echo '</div>';
                
                echo '<div class="status info">';
                echo '<strong>Solution:</strong> Run the database installation script to create the database and all tables.';
                echo '</div>';
                
                echo '<a href="install.php" class="btn">🚀 Install Database Now</a>';
            }
            
        } else {
            echo '<div class="status error">';
            echo '❌ <strong>MySQL Server is Not Running</strong><br>';
            echo 'Could not connect to MySQL on any port.';
            echo '</div>';
            
            echo '<div class="status info">';
            echo '<strong>To fix this:</strong><br>';
            echo '1. Open <strong>MAMP</strong> application<br>';
            echo '2. Click <strong>"Start Servers"</strong><br>';
            echo '3. Wait for <strong>MySQL</strong> to show green light<br>';
            echo '4. Refresh this page';
            echo '</div>';
        }
        ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <h3>Quick Links:</h3>
            <ul style="line-height: 2;">
                <li><a href="install.php">Install Database</a></li>
                <li><a href="test-db-connection.php">Test Connection</a></li>
                <li><a href="../login.php">Login Page</a></li>
            </ul>
        </div>
    </div>
</body>
</html>

