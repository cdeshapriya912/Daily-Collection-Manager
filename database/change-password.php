<?php
/**
 * Change Password Utility
 * Securely change user passwords directly in the database
 * Uses BCrypt for secure password hashing
 */

require_once __DIR__ . '/../admin/config/db.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - SAHANALK</title>
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
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #10b981;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background: #059669;
        }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }
        code {
            background: #1f2937;
            color: #10b981;
            padding: 2px 8px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            word-break: break-all;
            display: block;
            padding: 10px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Change User Password</h1>
        <p class="subtitle">Securely update passwords using BCrypt encryption</p>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
            $userType = $_POST['user_type'] ?? '';
            $username = trim($_POST['username'] ?? '');
            $newPassword = $_POST['new_password'] ?? '';
            
            $errors = [];
            
            if (empty($userType) || !in_array($userType, ['user', 'customer'])) {
                $errors[] = "Please select user type";
            }
            
            if (empty($username)) {
                $errors[] = "Please enter username";
            }
            
            if (empty($newPassword)) {
                $errors[] = "Please enter new password";
            } elseif (strlen($newPassword) < 6) {
                $errors[] = "Password must be at least 6 characters";
            }
            
            if (empty($errors)) {
                try {
                    // Check if user exists
                    $table = $userType === 'user' ? 'users' : 'customers';
                    $stmt = $pdo->prepare("SELECT id FROM {$table} WHERE username = ?");
                    $stmt->execute([$username]);
                    $user = $stmt->fetch();
                    
                    if (!$user) {
                        $errors[] = "User not found with username: {$username}";
                    } else {
                        // Generate secure BCrypt hash
                        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                        
                        // Update password
                        $stmt = $pdo->prepare("UPDATE {$table} SET password_hash = ? WHERE username = ?");
                        $stmt->execute([$hash, $username]);
                        
                        if ($stmt->rowCount() > 0) {
                            echo '<div class="alert alert-success">';
                            echo '<strong>✅ Password Updated Successfully!</strong><br>';
                            echo "Password for <code>{$username}</code> has been changed.<br>";
                            echo "User Type: " . ucfirst($userType) . "<br>";
                            echo "Password is securely encrypted using BCrypt.";
                            echo '</div>';
                        } else {
                            $errors[] = "Failed to update password. User may not exist.";
                        }
                    }
                } catch (PDOException $e) {
                    $errors[] = "Database error: " . $e->getMessage();
                }
            }
            
            if (!empty($errors)) {
                echo '<div class="alert alert-error">';
                echo '<strong>❌ Error:</strong><br>';
                foreach ($errors as $error) {
                    echo "• " . htmlspecialchars($error) . "<br>";
                }
                echo '</div>';
            }
        }
        ?>

        <div class="alert alert-info">
            <strong>ℹ️ Information:</strong><br>
            • Passwords are encrypted using <strong>BCrypt</strong> (industry-standard secure hashing)<br>
            • Each password gets a unique hash (even for the same password)<br>
            • BCrypt is resistant to brute-force and rainbow table attacks<br>
            • Minimum password length: 6 characters
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="user_type">User Type *</label>
                <select id="user_type" name="user_type" required>
                    <option value="">Select user type</option>
                    <option value="user">Staff/User</option>
                    <option value="customer">Customer</option>
                </select>
            </div>

            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" placeholder="Enter username" required>
            </div>

            <div class="form-group">
                <label for="new_password">New Password *</label>
                <input type="password" id="new_password" name="new_password" placeholder="Enter new password (min 6 characters)" minlength="6" required>
            </div>

            <button type="submit" name="change_password">Change Password</button>
        </form>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <h3>Manual SQL Method</h3>
            <p style="color: #666;">Alternatively, you can manually update passwords using SQL:</p>
            <ol style="line-height: 2;">
                <li>Generate hash using: <code>database/generate-password-hash.php</code></li>
                <li>Run SQL query:</li>
            </ol>
            <code>UPDATE users SET password_hash = 'GENERATED_HASH' WHERE username = 'username';</code>
            <p style="margin-top: 15px;">
                <a href="generate-password-hash.php" style="color: #10b981;">Open Hash Generator →</a>
            </p>
        </div>
    </div>
</body>
</html>

