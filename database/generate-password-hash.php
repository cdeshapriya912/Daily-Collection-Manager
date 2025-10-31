<?php
/**
 * Generate Secure Password Hash (BCrypt)
 * Use this to generate password hashes for manual database password changes
 * BCrypt is secure and resistant to brute-force attacks
 */

if (php_sapi_name() === 'cli' || isset($_GET['generate'])) {
    $password = $_GET['password'] ?? $argv[1] ?? 'admin123';
    $username = $_GET['username'] ?? $argv[2] ?? 'admin';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    echo "Password: {$password}\n";
    echo "Hash: {$hash}\n";
    echo "\nSQL Update for Staff/User:\n";
    echo "UPDATE users SET password_hash = '{$hash}' WHERE username = '{$username}';\n";
    echo "\nSQL Update for Customer:\n";
    echo "UPDATE customers SET password_hash = '{$hash}' WHERE username = '{$username}';\n";
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Generate Password Hash</title>
        <style>
            body { font-family: Arial; padding: 20px; }
            input { padding: 10px; margin: 10px 0; width: 300px; }
            button { padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 5px; cursor: pointer; }
            .result { margin-top: 20px; padding: 15px; background: #f3f4f6; border-radius: 5px; }
            code { background: #1f2937; color: #10b981; padding: 2px 6px; border-radius: 3px; }
        </style>
    </head>
    <body>
        <h2>🔐 Generate Secure Password Hash (BCrypt)</h2>
        <p style="color: #666; margin-bottom: 20px;">
            This tool generates secure BCrypt password hashes for manual database password changes.
            BCrypt is industry-standard secure encryption.
        </p>
        <form method="GET">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Password:</label>
                <input type="password" name="password" placeholder="Enter new password" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Username (optional):</label>
                <input type="text" name="username" placeholder="Enter username (default: admin)" value="admin">
            </div>
            <button type="submit" name="generate">Generate Secure Hash</button>
        </form>
        <?php
        if (isset($_GET['generate'])) {
            $password = $_GET['password'] ?? '';
            $username = $_GET['username'] ?? 'admin';
            
            if (empty($password)) {
                echo '<div style="margin-top: 20px; padding: 15px; background: #fee2e2; border-radius: 5px; color: #991b1b;">';
                echo 'Please enter a password';
                echo '</div>';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                echo '<div class="result">';
                echo '<h3 style="margin-top: 0;">Generated Hash:</h3>';
                echo '<p><strong>Password:</strong> ' . htmlspecialchars($password) . '</p>';
                echo '<p><strong>Username:</strong> ' . htmlspecialchars($username) . '</p>';
                echo '<p><strong>BCrypt Hash:</strong></p>';
                echo '<p><code style="word-break: break-all; display: block; padding: 10px;">' . htmlspecialchars($hash) . '</code></p>';
                echo '<h3>SQL Queries:</h3>';
                echo '<p><strong>For Staff/User:</strong></p>';
                echo '<p><code style="word-break: break-all; display: block; padding: 10px;">UPDATE users SET password_hash = \'' . htmlspecialchars($hash) . '\' WHERE username = \'' . htmlspecialchars($username) . '\';</code></p>';
                echo '<p><strong>For Customer:</strong></p>';
                echo '<p><code style="word-break: break-all; display: block; padding: 10px;">UPDATE customers SET password_hash = \'' . htmlspecialchars($hash) . '\' WHERE username = \'' . htmlspecialchars($username) . '\';</code></p>';
                echo '<p style="margin-top: 15px; padding: 10px; background: #dbeafe; border-radius: 5px; color: #1e40af;">';
                echo '✅ <strong>Secure!</strong> BCrypt hashing provides strong password protection against brute-force attacks.';
                echo '</p>';
                echo '</div>';
            }
        }
        ?>
    </body>
    </html>
    <?php
}

