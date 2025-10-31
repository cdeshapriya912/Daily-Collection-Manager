# Password Management Guide

## Password Security

The SAHANALK database uses **BCrypt** encryption for all passwords, which provides:
- ✅ Industry-standard secure hashing
- ✅ Protection against brute-force attacks
- ✅ Unique hash for each password (salt included)
- ✅ Resistant to rainbow table attacks

## Changing Passwords

### Option 1: Web Interface (Recommended)

Use the built-in password change utility:

```
http://localhost:8888/Daily-Collection-Manager/database/change-password.php
```

**Steps:**
1. Select user type (Staff/User or Customer)
2. Enter username
3. Enter new password (minimum 6 characters)
4. Click "Change Password"

### Option 2: Generate Hash Tool

Use the hash generator to get SQL-ready hash:

```
http://localhost:8888/Daily-Collection-Manager/database/generate-password-hash.php
```

**Steps:**
1. Enter password
2. Enter username (optional)
3. Copy the generated SQL query
4. Run in phpMyAdmin or MySQL client

### Option 3: PHP Script

Create a PHP file to change password:

```php
<?php
require_once __DIR__ . '/admin/config/db.php';

$username = 'admin'; // Change this
$newPassword = 'NewSecurePassword123'; // Change this

// Generate secure BCrypt hash
$hash = password_hash($newPassword, PASSWORD_DEFAULT);

// Update in database
try {
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
    $stmt->execute([$hash, $username]);
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Password updated successfully for user: {$username}\n";
    } else {
        echo "❌ User not found: {$username}\n";
    }
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
```

### Option 4: Direct SQL (Advanced)

**Step 1:** Generate hash first using `generate-password-hash.php`

**Step 2:** Run SQL query:

```sql
-- For Staff/Users
UPDATE users 
SET password_hash = '$2y$10$YOUR_GENERATED_HASH_HERE' 
WHERE username = 'admin';

-- For Customers
UPDATE customers 
SET password_hash = '$2y$10$YOUR_GENERATED_HASH_HERE' 
WHERE username = 'customer_username';
```

## Password Verification

When verifying passwords in PHP code:

```php
<?php
// Get password hash from database
$stmt = $pdo->prepare("SELECT password_hash FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

// Verify password
if ($user && password_verify($inputPassword, $user['password_hash'])) {
    echo "Password is correct!";
} else {
    echo "Invalid password!";
}
```

## Examples

### Change Admin Password

**Via Web Interface:**
1. Go to `database/change-password.php`
2. Select "Staff/User"
3. Username: `admin`
4. Enter new password
5. Submit

**Via SQL (after generating hash):**
```sql
UPDATE users 
SET password_hash = '$2y$10$...generated_hash...' 
WHERE username = 'admin';
```

### Change Customer Password

**Via Web Interface:**
1. Go to `database/change-password.php`
2. Select "Customer"
3. Enter customer username
4. Enter new password
5. Submit

### Bulk Password Reset

```php
<?php
require_once __DIR__ . '/admin/config/db.php';

$users = [
    ['username' => 'user1', 'password' => 'NewPass123'],
    ['username' => 'user2', 'password' => 'NewPass456'],
];

foreach ($users as $user) {
    $hash = password_hash($user['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
    $stmt->execute([$hash, $user['username']]);
    echo "Updated password for: {$user['username']}\n";
}
```

## Security Best Practices

1. ✅ **Use BCrypt** (already implemented)
2. ✅ **Minimum 8+ characters** for passwords
3. ✅ **Change default passwords** immediately after installation
4. ✅ **Use strong passwords** (mix of letters, numbers, symbols)
5. ✅ **Don't store plain text passwords** in code or database
6. ✅ **Use HTTPS** in production to protect password transmission

## Troubleshooting

### Password Not Working After Change

1. Verify hash was generated correctly
2. Check username is correct (case-sensitive)
3. Ensure SQL query executed successfully
4. Clear any cached sessions

### Can't Generate Hash

1. Make sure PHP version is 5.5.0 or higher
2. Check `password_hash()` function is available
3. Use `generate-password-hash.php` via web browser

### Need to Reset Multiple Passwords

Use the bulk password reset example above, or:
- Use phpMyAdmin to run multiple UPDATE queries
- Create a custom PHP script for batch updates

---

**Remember:** Always keep passwords secure and change default passwords immediately after installation!

