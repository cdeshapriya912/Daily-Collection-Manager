<?php
/**
 * SAHANALK Database Installation Script
 * For MAMP Server Setup
 * 
 * This script will create the SAHANALK database and all required tables
 */

// Prevent direct access in production
// if (php_sapi_name() !== 'cli' && $_SERVER['HTTP_HOST'] !== 'localhost') {
//     die('Installation script can only be run locally');
// }

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration (MAMP defaults)
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_PORT = getenv('DB_PORT') ?: '3306'; // MySQL port (3306 is standard, 8889 for MAMP default)
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: 'root';
$DB_NAME = isset($_POST['db_name']) ? trim($_POST['db_name']) : (getenv('DB_NAME') ?: 'SAHANALK');

$errors = [];
$messages = [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAHANALK Database Installation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 800px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 2rem;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        .status-box {
            background: #f7f9fc;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #10b981;
        }
        .status-box.error {
            border-left-color: #ef4444;
            background: #fef2f2;
        }
        .status-box.warning {
            border-left-color: #f59e0b;
            background: #fffbeb;
        }
        .message {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .message.success {
            background: #d1fae5;
            color: #065f46;
        }
        .message.error {
            background: #fee2e2;
            color: #991b1b;
        }
        .message.info {
            background: #dbeafe;
            color: #1e40af;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #10b981;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 20px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #059669;
        }
        .btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }
        .config-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .config-table th,
        .config-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .config-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
        }
        .code {
            background: #1f2937;
            color: #10b981;
            padding: 2px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }
        .step {
            margin: 30px 0;
            padding: 20px;
            background: #f9fafb;
            border-radius: 10px;
        }
        .step h3 {
            color: #333;
            margin-bottom: 15px;
        }
        .progress-bar {
            width: 100%;
            height: 30px;
            background: #e5e7eb;
            border-radius: 15px;
            overflow: hidden;
            margin: 20px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #059669);
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Daily Collection Manager Database Installation</h1>
        <p class="subtitle">Daily Collection Manager - Database Setup for MAMP</p>

        <?php
        // Step 1: Test MySQL Connection
        if (!isset($_POST['install'])) {
            ?>
            <div class="status-box">
                <h3>Configuration</h3>
                <p>Please verify your MAMP MySQL settings and choose a database name:</p>
                <form method="POST" id="configForm">
                <table class="config-table">
                    <tr>
                        <th>Setting</th>
                        <th>Value</th>
                    </tr>
                    <tr>
                        <td>Host</td>
                        <td><span class="code"><?php echo htmlspecialchars($DB_HOST); ?></span></td>
                    </tr>
                    <tr>
                        <td>Port</td>
                        <td><span class="code"><?php echo htmlspecialchars($DB_PORT); ?></span> <small style="color: #666;">(will try 8889, 3306 if needed)</small></td>
                    </tr>
                    <tr>
                        <td>Username</td>
                        <td><span class="code"><?php echo htmlspecialchars($DB_USER); ?></span></td>
                    </tr>
                    <tr>
                        <td>Password</td>
                        <td><span class="code"><?php echo str_repeat('*', strlen($DB_PASS)); ?></span></td>
                    </tr>
                    <tr>
                        <td>Database Name *</td>
                        <td>
                            <input type="text" 
                                   id="db_name" 
                                   name="db_name" 
                                   value="<?php echo htmlspecialchars($DB_NAME); ?>" 
                                   required
                                   pattern="[a-zA-Z0-9_]+"
                                   title="Only letters, numbers, and underscores allowed"
                                   style="width: 100%; padding: 8px; border: 2px solid #e5e7eb; border-radius: 5px; font-family: 'Courier New', monospace;"
                                   placeholder="Enter database name">
                            <small style="color: #666; display: block; margin-top: 5px;">
                                Only letters, numbers, and underscores (e.g., SAHANALK, daily_collection)
                            </small>
                        </td>
                    </tr>
                </table>
                <div style="margin-top: 15px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 5px;">
                    <strong>⚠️ Before Installing:</strong>
                    <ol style="margin: 10px 0 0 20px; line-height: 1.8;">
                        <li>Open <strong>MAMP</strong> application</li>
                        <li>Click <strong>"Start Servers"</strong> button</li>
                        <li>Wait for <strong>Apache</strong> and <strong>MySQL</strong> to show <strong>green lights</strong> ✅</li>
                        <li>If MySQL won't start, check MAMP → Preferences → Ports for conflicts</li>
                    </ol>
                </div>
                </form>
            </div>

            <button type="submit" form="configForm" name="install" class="btn">
                🚀 Install Database
            </button>
            <?php
        } else {
            // Get database name from POST
            $DB_NAME = isset($_POST['db_name']) ? trim($_POST['db_name']) : 'SAHANALK';
            
            // Validate database name
            if (empty($DB_NAME)) {
                $errors[] = "✗ Database name cannot be empty";
            } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $DB_NAME)) {
                $errors[] = "✗ Invalid database name. Only letters, numbers, and underscores are allowed.";
            } elseif (strlen($DB_NAME) > 64) {
                $errors[] = "✗ Database name is too long (max 64 characters)";
            }
            
            // Installation Process
            if (empty($errors)) {
                // Show progress bar
                echo '<div class="progress-bar"><div class="progress-fill" id="progress" style="width: 0%">0%</div></div>';
                flush();

                // Step 1: Connect to MySQL (without database)
                // Try multiple ports in case MAMP uses different port
                $portsToTry = [$DB_PORT, 3306, 8889];
                $connected = false;
                $actualPort = null;
                
                foreach ($portsToTry as $port) {
                    try {
                        $dsn = "mysql:host={$DB_HOST};port={$port};charset=utf8mb4";
                        $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_TIMEOUT => 5
                        ]);
                        
                        $actualPort = $port;
                        $DB_PORT = $port; // Update to working port
                        $connected = true;
                        $messages[] = "✓ Successfully connected to MySQL server on port {$port}";
                        updateProgress(10);
                        break;
                        
                    } catch (PDOException $e) {
                        // Continue trying other ports
                        continue;
                    }
                }
                
                if (!$connected) {
                    $errors[] = "✗ Failed to connect to MySQL server";
                    $errors[] = "Tried ports: " . implode(', ', $portsToTry);
                    $errors[] = "";
                    $errors[] = "Please check:";
                    $errors[] = "1. MAMP is running - Open MAMP and click 'Start Servers'";
                    $errors[] = "2. MySQL server is started (green light in MAMP)";
                    $errors[] = "3. Check MySQL port in MAMP → Preferences → Ports";
                    $errors[] = "4. Default MAMP MySQL port is usually 8889 or 3306";
                    $errors[] = "";
                    $errors[] = "To find your MySQL port:";
                    $errors[] = "• Open MAMP → Preferences → Ports";
                    $errors[] = "• Note the MySQL port number";
                    $errors[] = "• Update admin/config/db.php with correct port";
                }

                if (empty($errors)) {
                    // Step 2: Create Database
                    try {
                        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$DB_NAME}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        $messages[] = "✓ Database '{$DB_NAME}' created successfully";
                        updateProgress(20);
                        
                        // Switch to database
                        $pdo->exec("USE `{$DB_NAME}`");
                        
                    } catch (PDOException $e) {
                        $errors[] = "✗ Failed to create database: " . $e->getMessage();
                    }
                }

                if (empty($errors)) {
                    // Step 3: Read and execute SQL file
                    try {
                        $sqlFile = __DIR__ . '/database/database_schema.sql';
                        
                        if (!file_exists($sqlFile)) {
                            throw new Exception("SQL file not found: {$sqlFile}");
                        }
                        
                        $sql = file_get_contents($sqlFile);
                        
                        // Replace database name in SQL file (in case it's hardcoded)
                        $sql = preg_replace('/CREATE\s+DATABASE\s+IF\s+NOT\s+EXISTS\s+`?[^`\s]+`?/i', "CREATE DATABASE IF NOT EXISTS `{$DB_NAME}`", $sql);
                        $sql = preg_replace('/USE\s+`?[^`\s;]+`?/i', "USE `{$DB_NAME}`", $sql);
                        
                        // Remove CREATE DATABASE and USE statements (already done programmatically)
                        $sql = preg_replace('/^CREATE\s+DATABASE.*?;/ims', '', $sql);
                        $sql = preg_replace('/^USE\s+.*?;/ims', '', $sql);
                        
                        // Remove DELIMITER commands
                        $sql = preg_replace('/^DELIMITER\s+.*$/im', '', $sql);
                        
                        // Extract triggers and stored procedures FIRST (they use $$ as delimiter)
                        // Parse line by line to properly extract blocks
                        $lines = explode("\n", $sql);
                        $blocks = [];
                        $blocksMap = [];
                        $newSql = '';
                        $inBlock = false;
                        $currentBlock = '';
                        $blockType = '';
                        $blockStartLine = 0;
                        $blockIdx = 0;
                        
                        for ($i = 0; $i < count($lines); $i++) {
                            $line = $lines[$i];
                            $trimmed = trim($line);
                            
                            // Check if we're starting a trigger/procedure/function block
                            if (preg_match('/CREATE\s+(TRIGGER|PROCEDURE|FUNCTION)\s+/i', $trimmed) && !$inBlock) {
                                $inBlock = true;
                                $currentBlock = $line . "\n";
                                $blockStartLine = $i;
                                continue;
                            }
                            
                            // If we're in a block, collect lines until we see END$$
                            if ($inBlock) {
                                $currentBlock .= $line . "\n";
                                
                                // Check if this line ends the block
                                if (preg_match('/END\s*\$\$/i', $trimmed)) {
                                    $placeholder = "___BLOCK_{$blockIdx}___";
                                    $blocks[$placeholder] = $currentBlock;
                                    $blocksMap[$blockStartLine] = $placeholder;
                                    $newSql .= $placeholder . "\n";
                                    $inBlock = false;
                                    $currentBlock = '';
                                    $blockIdx++;
                                    continue;
                                }
                            }
                            
                            // If not in a block, add to new SQL
                            if (!$inBlock) {
                                $newSql .= $line . "\n";
                            }
                        }
                        
                        // Replace remaining $$ with semicolon
                        $newSql = preg_replace('/\$\$/s', ';', $newSql);
                        
                        // Now split the remaining SQL by semicolon
                        $statements = [];
                        
                        // Remove comments
                        $newSql = preg_replace('/^--.*$/m', '', $newSql);
                        
                        // Split by semicolon
                        $parts = explode(';', $newSql);
                        
                        foreach ($parts as $part) {
                            $part = trim($part);
                            
                            if (empty($part) || strlen($part) < 5) continue;
                            
                            // Check if this part contains a block placeholder
                            if (preg_match('/___BLOCK_(\d+)___/', $part, $matches)) {
                                $idx = (int)$matches[1];
                                $placeholder = "___BLOCK_{$idx}___";
                                if (isset($blocks[$placeholder])) {
                                    // Replace END$$ with END;
                                    $block = preg_replace('/END\s*\$\$/is', 'END;', $blocks[$placeholder]);
                                    // Also replace any remaining $$
                                    $block = preg_replace('/\$\$/s', ';', $block);
                                    $statements[] = trim($block);
                                }
                                // Remove placeholder
                                $part = preg_replace('/___BLOCK_\d+___/', '', $part);
                                $part = trim($part);
                            }
                            
                            if (!empty($part) && strlen($part) > 5) {
                                $statements[] = $part . ';';
                            }
                        }
                        
                        // Add any blocks that weren't inserted (safety check)
                        foreach ($blocks as $placeholder => $block) {
                            $block = preg_replace('/END\s*\$\$/is', 'END;', $block);
                            $block = preg_replace('/\$\$/s', ';', $block);
                            $block = trim($block);
                            if (!in_array($block, $statements)) {
                                $statements[] = $block;
                            }
                        }
                        
                        // Clean up statements - remove empty ones
                        $statements = array_values(array_filter($statements, function($stmt) {
                            $stmt = trim($stmt);
                            // Filter out very short or empty statements
                            return !empty($stmt) && strlen($stmt) > 10 && 
                                   !preg_match('/^(DELIMITER|--)/i', $stmt);
                        }));
                        
                        $totalStatements = count($statements);
                        $executed = 0;
                        $failed = 0;
                        
                        foreach ($statements as $index => $statement) {
                            $statement = trim($statement);
                            if (empty($statement)) continue;
                            
                            try {
                                // Enable multiple statements for this connection
                                $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
                                $pdo->exec($statement);
                                $executed++;
                                
                                // Progress update
                                if ($totalStatements > 10) {
                                    $progress = 20 + (($executed / $totalStatements) * 60);
                                    updateProgress($progress);
                                }
                                
                            } catch (PDOException $e) {
                                $errorMsg = $e->getMessage();
                                // Skip errors for things that might already exist
                                if (strpos($errorMsg, 'already exists') === false && 
                                    strpos($errorMsg, 'Duplicate') === false &&
                                    strpos($errorMsg, 'Unknown database') === false) {
                                    $failed++;
                                    // Only show first few errors to avoid spam
                                    if ($failed <= 3) {
                                        $errors[] = "✗ SQL Error (statement " . ($index + 1) . "): " . substr($errorMsg, 0, 150);
                                        // Show first 200 chars of problematic statement for debugging
                                        $errors[] = "   Statement: " . substr(preg_replace('/\s+/', ' ', $statement), 0, 200);
                                    }
                                } else {
                                    // Count as executed even if it already exists
                                    $executed++;
                                }
                            }
                        }
                        
                        $messages[] = "✓ Executed {$executed} of {$totalStatements} SQL statements";
                        if ($failed > 0) {
                            $messages[] = "⚠ {$failed} statements had errors (may already exist)";
                        }
                        updateProgress(80);
                        
                    } catch (Exception $e) {
                        $errors[] = "✗ Failed to execute SQL file: " . $e->getMessage();
                    }
                }

                if (empty($errors)) {
                    // Step 4: Verify Installation
                    try {
                        // Make sure we're using the selected database
                        $pdo->exec("USE `{$DB_NAME}`");
                        
                        $tables = [
                            'roles', 'users', 'suppliers', 'categories', 'products',
                            'customers', 'orders', 'order_items', 'payments',
                            'notifications', 'user_notification_reads', 'settings',
                            'sms_logs', 'audit_logs'
                        ];
                        
                        $createdTables = [];
                        foreach ($tables as $table) {
                            $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
                            if ($stmt->rowCount() > 0) {
                                $createdTables[] = $table;
                            }
                        }
                        
                        $messages[] = "✓ Created " . count($createdTables) . " tables in database '{$DB_NAME}'";
                        
                        // Check for roles
                        $stmt = $pdo->query("SELECT COUNT(*) FROM roles");
                        $roleCount = $stmt->fetchColumn();
                        if ($roleCount >= 2) {
                            $messages[] = "✓ Roles created ({$roleCount} roles)";
                        }
                        
                        $messages[] = "✓ Ready for admin account setup";
                        
                        updateProgress(100);
                        
                    } catch (PDOException $e) {
                        $errors[] = "✗ Verification failed: " . $e->getMessage();
                    }
                }
            }

            // Display Results
            if (empty($errors)) {
                // Check if admin setup form was submitted
                if (isset($_POST['setup_admin'])) {
                    // Handle admin user setup
                    $admin_username = trim($_POST['admin_username'] ?? 'admin');
                    $admin_password = $_POST['admin_password'] ?? 'admin@123';
                    $admin_email = trim($_POST['admin_email'] ?? 'admin@example.com');
                    
                    // Validate inputs
                    if (empty($admin_username)) {
                        $errors[] = "✗ Admin username cannot be empty";
                    } elseif (strlen($admin_username) > 100) {
                        $errors[] = "✗ Admin username is too long (max 100 characters)";
                    }
                    
                    if (empty($admin_password)) {
                        $errors[] = "✗ Admin password cannot be empty";
                    } elseif (strlen($admin_password) < 6) {
                        $errors[] = "✗ Admin password must be at least 6 characters";
                    }
                    
                    if (!empty($admin_email) && !filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "✗ Invalid email address format";
                    }
                    
                    if (empty($errors)) {
                        try {
                            // Make sure we're using the correct database
                            $pdo->exec("USE `{$DB_NAME}`");
                            
                            // Hash password using BCrypt
                            $password_hash = password_hash($admin_password, PASSWORD_DEFAULT);
                            
                            // Check if admin user already exists
                            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                            $checkStmt->execute([$admin_username]);
                            $existingUser = $checkStmt->fetch();
                            
                            if ($existingUser) {
                                // Update existing admin user
                                $updateStmt = $pdo->prepare("
                                    UPDATE users 
                                    SET password_hash = ?, 
                                        email = ?, 
                                        full_name = 'Administrator',
                                        role_id = 1,
                                        status = 'active',
                                        updated_at = NOW()
                                    WHERE username = ?
                                ");
                                $updateStmt->execute([$password_hash, $admin_email, $admin_username]);
                                $messages[] = "✓ Admin user updated successfully";
                            } else {
                                // Insert new admin user
                                $insertStmt = $pdo->prepare("
                                    INSERT INTO users (username, password_hash, full_name, email, role_id, status, created_at, updated_at) 
                                    VALUES (?, ?, 'Administrator', ?, 1, 'active', NOW(), NOW())
                                ");
                                $insertStmt->execute([$admin_username, $password_hash, $admin_email]);
                                $messages[] = "✓ Admin user created successfully";
                            }
                            
                            // Show success message and login link
                            echo '<div class="status-box">';
                            echo '<h3>✅ Installation Complete!</h3>';
                            echo '<p>The <strong>' . htmlspecialchars($DB_NAME) . '</strong> database has been created successfully.</p>';
                            echo '<p>Admin user has been configured.</p>';
                            echo '</div>';
                            
                            echo '<div class="step">';
                            echo '<h3>Setup Complete!</h3>';
                            echo '<div style="background: #d1fae5; padding: 20px; border-radius: 10px; margin: 20px 0;">';
                            echo '<h4 style="margin-top: 0; color: #065f46;">Admin Credentials:</h4>';
                            echo '<table class="config-table" style="background: white;">';
                            echo '<tr><th style="width: 150px;">Username:</th><td><code>' . htmlspecialchars($admin_username) . '</code></td></tr>';
                            echo '<tr><th>Email:</th><td><code>' . htmlspecialchars($admin_email) . '</code></td></tr>';
                            echo '<tr><th>Password:</th><td><code>' . str_repeat('*', strlen($admin_password)) . '</code></td></tr>';
                            echo '</table>';
                            echo '</div>';
                            echo '</div>';
                            
                            echo '<div class="step">';
                            echo '<h3>Next Steps:</h3>';
                            echo '<ol style="margin-left: 20px; line-height: 2;">';
                            echo '<li>Delete or protect this <code>install.php</code> file for security</li>';
                            echo '<li>Update <code>admin/config/db.php</code> if needed (DB_NAME is set to: <code>' . htmlspecialchars($DB_NAME) . '</code>)</li>';
                            echo '<li><strong>⚠️ IMPORTANT:</strong> Keep your admin credentials secure!</li>';
                            echo '</ol>';
                            echo '</div>';
                            
                            echo '<a href="../login.php" class="btn" style="margin-top: 20px;">Go to Login Page</a>';
                            
                        } catch (PDOException $e) {
                            $errors[] = "✗ Failed to setup admin user: " . $e->getMessage();
                        }
                    }
                } else {
                    // Show admin setup form after successful installation
                    echo '<div class="status-box">';
                    echo '<h3>✅ Database Installation Successful!</h3>';
                    echo '<p>The <strong>' . htmlspecialchars($DB_NAME) . '</strong> database has been created successfully.</p>';
                    echo '<p>Please configure your admin account:</p>';
                    echo '</div>';
                    
                    echo '<div class="step">';
                    echo '<h3>Admin Account Setup</h3>';
                    echo '<form method="POST" id="adminSetupForm">';
                    echo '<input type="hidden" name="install" value="1">';
                    echo '<input type="hidden" name="db_name" value="' . htmlspecialchars($DB_NAME) . '">';
                    
                    echo '<table class="config-table">';
                    echo '<tr>';
                    echo '<th style="width: 200px;">Admin Username *</th>';
                    echo '<td>';
                    echo '<input type="text" 
                                 name="admin_username" 
                                 id="admin_username"
                                 value="admin" 
                                 required
                                 pattern="[a-zA-Z0-9_]+"
                                 title="Only letters, numbers, and underscores allowed"
                                 style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 5px; font-family: \'Courier New\', monospace;">';
                    echo '<small style="color: #666; display: block; margin-top: 5px;">Login username for admin account</small>';
                    echo '</td>';
                    echo '</tr>';
                    
                    echo '<tr>';
                    echo '<th>Admin Password *</th>';
                    echo '<td>';
                    echo '<input type="password" 
                                 name="admin_password" 
                                 id="admin_password"
                                 value="admin@123" 
                                 required
                                 minlength="6"
                                 style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 5px; font-family: \'Courier New\', monospace;">';
                    echo '<small style="color: #666; display: block; margin-top: 5px;">Minimum 6 characters. Keep this secure!</small>';
                    echo '</td>';
                    echo '</tr>';
                    
                    echo '<tr>';
                    echo '<th>Admin Email</th>';
                    echo '<td>';
                    echo '<input type="email" 
                                 name="admin_email" 
                                 id="admin_email"
                                 value="admin@example.com" 
                                 style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 5px; font-family: \'Courier New\', monospace;">';
                    echo '<small style="color: #666; display: block; margin-top: 5px;">Email address for admin account</small>';
                    echo '</td>';
                    echo '</tr>';
                    echo '</table>';
                    
                    echo '<div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 5px;">';
                    echo '<strong>⚠️ Security Note:</strong>';
                    echo '<ul style="margin: 10px 0 0 20px; line-height: 1.8;">';
                    echo '<li>Choose a strong password for your admin account</li>';
                    echo '<li>You can change these credentials later in the application</li>';
                    echo '<li>Make sure to remember your password!</li>';
                    echo '</ul>';
                    echo '</div>';
                    
                    echo '<button type="submit" name="setup_admin" class="btn" style="margin-top: 20px;">✓ Complete Setup & Create Admin Account</button>';
                    echo '</form>';
                    echo '</div>';
                }
                
            } else {
                echo '<div class="status-box error">';
                echo '<h3>❌ Installation Failed</h3>';
                echo '<p>Please fix the following errors:</p>';
                echo '</div>';
            }

            // Display Messages
            if (!empty($messages)) {
                echo '<div class="status-box">';
                echo '<h3>Installation Log:</h3>';
                foreach ($messages as $msg) {
                    echo '<div class="message success">' . htmlspecialchars($msg) . '</div>';
                }
                echo '</div>';
            }

            if (!empty($errors)) {
                echo '<div class="status-box error">';
                echo '<h3>Errors:</h3>';
                foreach ($errors as $error) {
                    echo '<div class="message error">' . htmlspecialchars($error) . '</div>';
                }
                echo '</div>';
                echo '<form method="POST" style="margin-top: 20px;">';
                echo '<input type="hidden" name="db_name" value="' . htmlspecialchars($DB_NAME) . '">';
                echo '<button type="submit" name="install" class="btn">Try Again</button>';
                echo '</form>';
            }
        }

        function updateProgress($percent) {
            echo "<script>document.getElementById('progress').style.width = '{$percent}%'; document.getElementById('progress').textContent = '{$percent}%';</script>";
            flush();
            ob_flush();
        }
        ?>
    </div>
</body>
</html>

