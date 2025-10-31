<?php
/**
 * MySQL Connection Diagnostic Tool
 * This will help identify MySQL connection issues
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html>
<head>
    <title>MySQL Connection Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; border-radius: 10px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { background: #d1fae5; border-left: 4px solid #10b981; }
        .error { background: #fee2e2; border-left: 4px solid #ef4444; }
        .warning { background: #fef3c7; border-left: 4px solid #f59e0b; }
        h1 { color: #1f2937; }
        h2 { color: #374151; margin-top: 0; }
        code { background: #f3f4f6; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New', monospace; }
        .btn { display: inline-block; background: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 10px 5px 0 0; }
        .btn:hover { background: #059669; }
        ul { line-height: 1.8; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-weight: bold; }
    </style>
</head>
<body>
    <h1>🔍 MySQL Connection Diagnostic Tool</h1>
    
    <?php
    $DB_HOST = '127.0.0.1';
    $DB_USER = 'root';
    $DB_PASS = 'root';
    $portsToTry = [8889, 3306, 3307];
    $connectionResults = [];
    
    echo '<div class="box">';
    echo '<h2>Step 1: Testing MySQL Connection</h2>';
    echo '<p>Testing connection to MySQL server...</p>';
    echo '<p><strong>Host:</strong> ' . htmlspecialchars($DB_HOST) . '</p>';
    echo '<p><strong>Username:</strong> ' . htmlspecialchars($DB_USER) . '</p>';
    echo '<p><strong>Testing ports:</strong> ' . implode(', ', $portsToTry) . '</p>';
    
    $workingPort = null;
    $workingPdo = null;
    
    foreach ($portsToTry as $port) {
        echo '<h3>Testing port ' . $port . '...</h3>';
        try {
            $dsn = "mysql:host={$DB_HOST};port={$port};charset=utf8mb4";
            $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 3
            ]);
            
            // Test query
            $pdo->query("SELECT 1");
            
            $workingPort = $port;
            $workingPdo = $pdo;
            $connectionResults[$port] = ['status' => 'success', 'message' => 'Connected successfully!'];
            
            echo '<div class="box success">';
            echo '<h3>✅ SUCCESS! Port ' . $port . ' works!</h3>';
            echo '<p>MySQL is running on port <strong>' . $port . '</strong></p>';
            echo '</div>';
            break; // Found working port, stop testing
            
        } catch (PDOException $e) {
            $errorMsg = $e->getMessage();
            $connectionResults[$port] = ['status' => 'error', 'message' => $errorMsg];
            
            echo '<div class="box error">';
            echo '<p>❌ Port ' . $port . ' failed:</p>';
            echo '<p><code>' . htmlspecialchars($errorMsg) . '</code></p>';
            echo '</div>';
        }
    }
    
    echo '</div>';
    
    if ($workingPort === null) {
        echo '<div class="box error">';
        echo '<h2>❌ No Working Connection Found</h2>';
        echo '<p><strong>MySQL server is not accessible on any tested port.</strong></p>';
        echo '<h3>How to fix:</h3>';
        echo '<ol>';
        echo '<li><strong>Start MAMP MySQL Server:</strong>';
        echo '<ul>';
        echo '<li>Open the <strong>MAMP</strong> application</li>';
        echo '<li>Click the <strong>"Start Servers"</strong> button</li>';
        echo '<li>Wait for <strong>MySQL</strong> to show a <strong>green light</strong> ✅</li>';
        echo '</ul>';
        echo '</li>';
        echo '<li><strong>Check MAMP Ports:</strong>';
        echo '<ul>';
        echo '<li>Open MAMP → <strong>Preferences</strong> → <strong>Ports</strong></li>';
        echo '<li>Note the <strong>MySQL Port</strong> number</li>';
        echo '<li>If it\'s different from 8889 or 3306, update <code>admin/config/db.php</code></li>';
        echo '</ul>';
        echo '</li>';
        echo '<li><strong>Verify MySQL is Running:</strong>';
        echo '<ul>';
        echo '<li>Check MAMP main window for green status indicators</li>';
        echo '<li>Try accessing phpMyAdmin: <a href="http://localhost:8888/phpMyAdmin/" target="_blank">http://localhost:8888/phpMyAdmin/</a></li>';
        echo '</ul>';
        echo '</li>';
        echo '</ol>';
        echo '</div>';
        
        echo '<div class="box">';
        echo '<h2>Connection Test Results Summary</h2>';
        echo '<table>';
        echo '<tr><th>Port</th><th>Status</th><th>Message</th></tr>';
        foreach ($connectionResults as $port => $result) {
            $status = $result['status'] === 'success' ? '✅ Success' : '❌ Failed';
            echo '<tr>';
            echo '<td>' . $port . '</td>';
            echo '<td>' . $status . '</td>';
            echo '<td><code>' . htmlspecialchars($result['message']) . '</code></td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
    } else {
        echo '<div class="box success">';
        echo '<h2>✅ MySQL Connection Successful!</h2>';
        echo '<p>Your MySQL server is running and accessible.</p>';
        echo '<p><strong>Working Port:</strong> <code>' . $workingPort . '</code></p>';
        
        // Check if database exists
        echo '<h3>Step 2: Checking for SAHANALK Database</h3>';
        try {
            $stmt = $workingPdo->query("SHOW DATABASES LIKE 'SAHANALK'");
            $dbExists = $stmt->rowCount() > 0;
            
            if ($dbExists) {
                echo '<p>✅ Database <code>SAHANALK</code> exists!</p>';
                
                // Check for users table
                $workingPdo->exec("USE SAHANALK");
                $stmt = $workingPdo->query("SHOW TABLES LIKE 'users'");
                if ($stmt->rowCount() > 0) {
                    $stmt = $workingPdo->query("SELECT COUNT(*) as count FROM users");
                    $userCount = $stmt->fetch()['count'];
                    echo '<p>✅ Users table exists with <strong>' . $userCount . '</strong> user(s).</p>';
                    
                    if ($userCount > 0) {
                        echo '<p><strong>✅ You can now login!</strong></p>';
                        echo '<p><a href="login.php" class="btn">Go to Login Page</a></p>';
                    } else {
                        echo '<p>⚠️ No users found. You need to create an admin user.</p>';
                    }
                } else {
                    echo '<p>⚠️ Database exists but tables are missing. Run installation.</p>';
                }
            } else {
                echo '<p>❌ Database <code>SAHANALK</code> does NOT exist.</p>';
                echo '<p>You need to create it using the installation script.</p>';
            }
        } catch (Exception $e) {
            echo '<p>⚠️ Error checking database: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        
        // Configuration recommendation
        echo '<h3>Step 3: Update Configuration</h3>';
        if ($workingPort != 8889) {
            echo '<div class="box warning">';
            echo '<p><strong>⚠️ Port Mismatch Detected!</strong></p>';
            echo '<p>Your MySQL is running on port <strong>' . $workingPort . '</strong>, but your configuration might be set to port <strong>8889</strong>.</p>';
            echo '<p><strong>Action Required:</strong> Update <code>admin/config/db.php</code>:</p>';
            echo '<pre style="background: #f3f4f6; padding: 15px; border-radius: 5px; overflow-x: auto;">';
            echo '$DB_PORT = getenv(\'DB_PORT\') ?: \'' . $workingPort . '\'; // MAMP MySQL port' . "\n";
            echo '</pre>';
            echo '</div>';
        } else {
            echo '<p>✅ Port configuration matches (8889).</p>';
        }
        
        if (!$dbExists) {
            echo '<p><a href="setup/install.php" class="btn">🚀 Run Database Installation</a></p>';
        }
        
        echo '</div>';
    }
    ?>
    
    <div class="box">
        <h2>Quick Actions</h2>
        <a href="login.php" class="btn">Go to Login</a>
        <a href="setup/install.php" class="btn">Run Installation</a>
        <a href="setup/test-db-connection.php" class="btn">Test DB Connection</a>
    </div>
</body>
</html>

