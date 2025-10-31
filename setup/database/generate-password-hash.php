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
        <?php
        if (isset($_GET['generate'])) {
            $password = $_GET['password'] ?? '';
            $username = $_GET['username'] ?? 'admin';
            
            if (empty($password)) {
                // Error: show form with error
                echo '<h2>🔐 Generate Secure Password Hash (BCrypt)</h2>';
                echo '<div style="margin-top: 20px; padding: 15px; background: #fee2e2; border-radius: 5px; color: #991b1b;">';
                echo 'Please enter a password';
                echo '</div>';
                echo '<p style="margin-top: 20px;"><a href="?' . http_build_query([]) . '" style="color: #10b981; text-decoration: none;">← Back to Generator</a></p>';
            } else {
                // Result page - show only hash
                $hash = password_hash($password, PASSWORD_DEFAULT);
                ?>
                <div style="max-width: 800px; margin: 0 auto; padding: 40px 20px;">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <h2 style="color: #10b981; margin-bottom: 10px;">✅ Password Hash Generated</h2>
                        <p style="color: #666;">Click the hash below to select, or use the copy button</p>
                    </div>
                    
                    <div style="background: #1f2937; padding: 30px; border-radius: 12px; margin: 30px 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <code id="hashOutput" style="color: #10b981; font-size: 16px; word-break: break-all; display: block; line-height: 1.8; user-select: all; cursor: text; font-family: 'Courier New', monospace;"><?php echo htmlspecialchars($hash); ?></code>
                    </div>
                    
                    <button onclick="copyHash()" style="width: 100%; padding: 15px; background: #10b981; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 16px; margin-bottom: 15px;">📋 Copy Hash to Clipboard</button>
                    
                    <div id="copyMessage" style="margin-top: 15px; padding: 15px; background: #d1fae5; border-radius: 8px; color: #065f46; display: none; text-align: center; font-weight: bold;">✓ Hash copied to clipboard!</div>
                    
                    <div style="text-align: center; margin-top: 40px;">
                        <a href="?" style="color: #10b981; text-decoration: none; font-weight: bold;">← Generate Another Hash</a>
                    </div>
                </div>
                <script>
                    function copyHash() {
                        const hashElement = document.getElementById("hashOutput");
                        const hashText = hashElement.textContent;
                        navigator.clipboard.writeText(hashText).then(function() {
                            const message = document.getElementById("copyMessage");
                            message.style.display = "block";
                            setTimeout(function() {
                                message.style.display = "none";
                            }, 3000);
                        });
                    }
                    // Auto-select hash on page load
                    window.onload = function() {
                        const hashElement = document.getElementById("hashOutput");
                        if (hashElement) {
                            const range = document.createRange();
                            range.selectNode(hashElement);
                            window.getSelection().removeAllRanges();
                            window.getSelection().addRange(range);
                        }
                    };
                </script>
                <?php
            }
        } else {
            // Show form
            ?>
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
        }
        ?>
    </body>
    </html>
    <?php
}

