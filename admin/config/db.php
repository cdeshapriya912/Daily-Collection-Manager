<?php

declare(strict_types=1);

// Database configuration with environment variable support
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_PORT = getenv('DB_PORT') ?: '3306'; // MySQL port (3306 is standard, 8889 for MAMP default)
$DB_NAME = getenv('DB_NAME') ?: 'SAHANALK';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: 'root';

// Try multiple ports if default fails (auto-detection)
$portsToTry = [$DB_PORT, 8889, 3307]; // Try configured port first, then common alternatives

// Try to connect with database first, if fails try without database
$connected = false;
$pdo = null;
$actualPort = $DB_PORT;

// First attempt: Connect with database name (try multiple ports)
$connectionAttempts = [];
foreach ($portsToTry as $testPort) {
    try {
        $dsn = "mysql:host={$DB_HOST};port={$testPort};dbname={$DB_NAME};charset=utf8mb4";
        $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_TIMEOUT => 3,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]);
        
        // Verify connection by running a simple query
        $pdo->query("SELECT 1");
        $connected = true;
        $actualPort = $testPort;
        break; // Success, stop trying ports
        
    } catch (PDOException $e) {
        $connectionAttempts[$testPort] = $e->getMessage();
        continue; // Try next port
    }
}

if (!$connected) {
    // No connection worked, use original error handling
    $e = new PDOException("Connection failed on all ports: " . implode(", ", array_keys($connectionAttempts)));
    
} else {
    // Connection successful, set a dummy exception for the catch block
    $e = null;
}

// If connection failed, continue with error handling
if (!$connected && $e) {
    // Check error type
    $errorMsg = $e->getMessage();
    $isDatabaseNotFound = strpos($errorMsg, "Unknown database") !== false || 
                          strpos($errorMsg, "1049") !== false;
    $isConnectionRefused = strpos($errorMsg, "refused") !== false ||
                           strpos($errorMsg, "2002") !== false ||
                           strpos($errorMsg, "Connection failed on all ports") !== false;
    
    // If connection refused, try to find working port
    if ($isConnectionRefused) {
        $workingPort = null;
        foreach ($portsToTry as $testPort) {
            try {
                $dsn = "mysql:host={$DB_HOST};port={$testPort};charset=utf8mb4";
                $testPdo = new PDO($dsn, $DB_USER, $DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 3
                ]);
                $testPdo->query("SELECT 1");
                $workingPort = $testPort;
                break;
            } catch (PDOException $e3) {
                continue;
            }
        }
        
        if ($workingPort === null) {
            // MySQL server is not running or connection failed
            error_log("Database connection failed: MySQL server not accessible on any port");
            
            if (php_sapi_name() !== 'cli') {
                http_response_code(500);
                $installPath = '/Daily-Collection-Manager/setup/install.php';
                if (isset($_SERVER['HTTP_HOST'])) {
                    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                    $host = $_SERVER['HTTP_HOST'];
                    // Handle different path formats
                    $basePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(__DIR__));
                    $basePath = str_replace('\\', '/', $basePath);
                    $installUrl = $protocol . '://' . $host . $basePath . '/setup/install.php';
                    
                    die('
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>MySQL Server Not Running</title>
                        <style>
                            body { font-family: Arial, sans-serif; max-width: 700px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
                            .error-box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                            h1 { color: #e74c3c; margin-top: 0; }
                            .button { display: inline-block; background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin-top: 20px; font-weight: bold; }
                            .button:hover { background: #059669; }
                            .button-secondary { background: #6366f1; }
                            .button-secondary:hover { background: #4f46e5; }
                            ul { line-height: 1.8; }
                            code { background: #f3f4f6; padding: 2px 6px; border-radius: 3px; }
                        </style>
                    </head>
                    <body>
                        <div class="error-box">
                            <h1>⚠️ MySQL Server Not Running</h1>
                            <p><strong>The MySQL server is not accessible.</strong> The connection was refused on all tested ports.</p>
                            
                            <h3>How to Fix:</h3>
                            <ol>
                                <li><strong>Start MAMP MySQL Server:</strong>
                                    <ul>
                                        <li>Open the <strong>MAMP</strong> application</li>
                                        <li>Click the <strong>"Start Servers"</strong> button</li>
                                        <li>Wait for <strong>MySQL</strong> to show a <strong>green light</strong> ✅</li>
                                    </ul>
                                </li>
                                <li><strong>Check MAMP Port Settings:</strong>
                                    <ul>
                                        <li>Open MAMP → <strong>Preferences</strong> → <strong>Ports</strong></li>
                                        <li>Note the <strong>MySQL Port</strong> number (usually 8889)</li>
                                        <li>If different, update <code>admin/config/db.php</code></li>
                                    </ul>
                                </li>
                                <li><strong>Verify MySQL is Running:</strong>
                                    <ul>
                                        <li>Check MAMP main window for green status indicators</li>
                                        <li>Try accessing phpMyAdmin to confirm MySQL is working</li>
                                    </ul>
                                </li>
                            </ol>
                            
                            <p><strong>Tested ports:</strong> ' . implode(', ', $portsToTry) . '</p>
                            
                            <p style="margin-top: 30px;">
                                <a href="' . $installUrl . '" class="button">🚀 Run Database Installation</a>
                                <a href="check-mysql.php" class="button button-secondary">🔍 Run Connection Diagnostic</a>
                            </p>
                        </div>
                    </body>
                    </html>
                    ');
                } else {
                    die('MySQL server not running. Please start MAMP MySQL server.');
                }
            } else {
                throw new Exception("MySQL server not accessible on any port. Please start MAMP MySQL server.");
            }
        }
    }
    
    // If database doesn't exist, try connecting without database name
    if ($isDatabaseNotFound) {
        $portToUse = $workingPort ?? $actualPort ?? $DB_PORT;
        try {
            // Connect without database to check if MySQL is running
            $dsn = "mysql:host={$DB_HOST};port={$portToUse};charset=utf8mb4";
            $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5
            ]);
            
            // Database doesn't exist - show helpful message
            error_log("Database '{$DB_NAME}' does not exist. Please run install.php to create it.");
            
            if (php_sapi_name() !== 'cli') {
                http_response_code(500);
                // Calculate relative path to install.php based on current file location
                $installPath = '/Daily-Collection-Manager/setup/install.php';
                if (isset($_SERVER['HTTP_HOST'])) {
                    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                    $installUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . $installPath;
                    die('
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Database Not Found</title>
                        <style>
                            body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
                            .error-box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                            h1 { color: #e74c3c; margin-top: 0; }
                            .button { display: inline-block; background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin-top: 20px; font-weight: bold; }
                            .button:hover { background: #059669; }
                            code { background: #f3f4f6; padding: 2px 6px; border-radius: 3px; }
                        </style>
                    </head>
                    <body>
                        <div class="error-box">
                            <h1>⚠️ Database Not Found</h1>
                            <p>The database <code>' . htmlspecialchars($DB_NAME) . '</code> does not exist.</p>
                            <p>You need to create the database first using the installation script.</p>
                            <h3>Steps to fix:</h3>
                            <ol>
                                <li>Make sure <strong>MAMP MySQL server is running</strong> (check MAMP app)</li>
                                <li>Click the button below to run the installation</li>
                                <li>Or manually go to: <code>setup/install.php</code></li>
                            </ol>
                            <a href="' . $installUrl . '" class="button">🚀 Run Database Installation</a>
                        </div>
                    </body>
                    </html>
                    ');
                } else {
                    die("Database '{$DB_NAME}' does not exist. Please run setup/install.php to create the database.");
                }
            } else {
                throw new Exception("Database '{$DB_NAME}' does not exist. Please run install.php to create it.");
            }
            
        } catch (PDOException $e2) {
            // MySQL server is not running or connection failed
            error_log("Database connection failed: " . $e2->getMessage());
            
            if (php_sapi_name() !== 'cli') {
                http_response_code(500);
                $installPath = '/Daily-Collection-Manager/setup/install.php';
                if (isset($_SERVER['HTTP_HOST'])) {
                    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                    $installUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . $installPath;
                    die('
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Database Connection Failed</title>
                        <style>
                            body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
                            .error-box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                            h1 { color: #e74c3c; margin-top: 0; }
                            .button { display: inline-block; background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin-top: 20px; font-weight: bold; }
                            ul { line-height: 1.8; }
                        </style>
                    </head>
                    <body>
                        <div class="error-box">
                            <h1>⚠️ Database Connection Failed</h1>
                            <p>Could not connect to MySQL server. Please check:</p>
                            <ul>
                                <li><strong>1. MAMP MySQL server is running</strong> (check MAMP app for green light)</li>
                                <li><strong>2. Port is correct</strong> (MAMP default: 8889, check in MAMP → Preferences → Ports)</li>
                                <li><strong>3. Credentials are correct</strong> (default: root/root)</li>
                            </ul>
                            <p><strong>Error:</strong> ' . htmlspecialchars($e2->getMessage()) . '</p>
                            <a href="' . $installUrl . '" class="button">🚀 Try Installation Again</a>
                        </div>
                    </body>
                    </html>
                    ');
                } else {
                    die('Database connection failed. Please check:<br>1. MAMP MySQL server is running<br>2. Port is correct (MAMP default: 8889)<br>3. Credentials are correct<br><br><a href="../../setup/install.php">Run Database Installation</a>');
                }
            } else {
                throw $e2;
            }
        }
    } else {
        // Other connection error
        error_log("Database connection failed: " . $e->getMessage());
        
        if (php_sapi_name() !== 'cli') {
            http_response_code(500);
            $installPath = '/Daily-Collection-Manager/setup/install.php';
            if (isset($_SERVER['HTTP_HOST'])) {
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                $installUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . $installPath;
                die('
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Database Error</title>
                    <style>
                        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
                        .error-box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                        h1 { color: #e74c3c; }
                        .button { display: inline-block; background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
                    </style>
                </head>
                <body>
                    <div class="error-box">
                        <h1>Database Connection Error</h1>
                        <p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
                        <a href="' . $installUrl . '" class="button">🚀 Run Database Installation</a>
                    </div>
                </body>
                </html>
                ');
            } else {
                die('Database connection failed. Error: ' . htmlspecialchars($e->getMessage()) . '<br><br><a href="../../setup/install.php">Run Database Installation</a>');
            }
        } else {
            throw $e;
        }
    }
}
