# MAMP Setup Instructions for SAHANALK Database

## Prerequisites

1. **MAMP is installed and running**
   - Download from: https://www.mamp.info/
   - Make sure MAMP servers (Apache & MySQL) are running
   - Default MySQL port in MAMP: **8889** (check MAMP preferences)

2. **Check MAMP MySQL Settings**
   - Open MAMP → Preferences → Ports
   - Note your MySQL port (usually 8889)
   - Default username: `root`
   - Default password: `root`

## Installation Methods

### Method 1: Using Install Script (Recommended)

1. **Access the install script:**
   ```
   http://localhost/Daily-Collection-Manager/install.php
   ```
   
   Or if using MAMP's default port:
   ```
   http://localhost:8888/Daily-Collection-Manager/install.php
   ```

2. **Click "Install Database" button**
   - The script will automatically:
     - Connect to MySQL
     - Create SAHANALK database
     - Create all tables
     - Insert default data

3. **Verify installation:**
   - Check for success message
   - Verify tables are created

4. **Delete install.php after successful installation** (security best practice)

### Method 2: Using phpMyAdmin

1. **Access phpMyAdmin:**
   ```
   http://localhost:8888/phpMyAdmin/
   ```

2. **Create Database:**
   - Click "New" in left sidebar
   - Database name: `SAHANALK`
   - Collation: `utf8mb4_unicode_ci`
   - Click "Create"

3. **Import SQL File:**
   - Select `SAHANALK` database
   - Click "Import" tab
   - Click "Choose File"
   - Select `database/sahanalk_schema.sql`
   - Click "Go"

4. **Verify:**
   - Check that all 14 tables are created
   - Check for default admin user

### Method 3: Using MySQL Command Line

1. **Open Terminal/Command Prompt**

2. **Navigate to MAMP MySQL binary:**
   ```bash
   # macOS
   cd /Applications/MAMP/Library/bin/
   
   # Windows
   cd C:\MAMP\bin\mysql\bin\
   ```

3. **Connect to MySQL:**
   ```bash
   # macOS
   ./mysql -u root -proot -P 8889
   
   # Windows
   mysql.exe -u root -proot -P 8889
   ```

4. **Run SQL File:**
   ```sql
   source /path/to/Daily-Collection-Manager/database/sahanalk_schema.sql
   ```

   Or copy-paste the entire SQL file content

5. **Verify:**
   ```sql
   USE SAHANALK;
   SHOW TABLES;
   SELECT * FROM users WHERE username = 'admin';
   ```

## Configure Database Connection

The database connection is already configured in `admin/config/db.php`:

```php
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_PORT = getenv('DB_PORT') ?: '3306';  // Change to 8889 if MAMP default
$DB_NAME = 'SAHANALK';
$DB_USER = 'root';
$DB_PASS = 'root';
```

**If MAMP uses port 8889**, update `admin/config/db.php`:

```php
$DB_PORT = getenv('DB_PORT') ?: '8889';
```

## Default Login Credentials

After installation, you can login with:

- **Username:** `admin`
- **Password:** `admin123`

**⚠️ IMPORTANT:** Change the admin password immediately after first login!

### How to Change Admin Password

#### Option 1: Via Application (Recommended)
1. Login with default credentials
2. Go to Settings → User Management
3. Change password

#### Option 2: Via Database
1. Generate new hash using: `database/generate-password-hash.php`
2. Update database:
   ```sql
   UPDATE users 
   SET password_hash = 'YOUR_NEW_HASH_HERE' 
   WHERE username = 'admin';
   ```

#### Option 3: Via PHP Script
```php
<?php
require_once 'admin/config/db.php';
$newPassword = 'YourNewPassword123';
$hash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = 'admin'");
$stmt->execute([$hash]);
echo "Password updated!";
```

## Verify Installation

### Check Tables
```sql
USE SAHANALK;
SHOW TABLES;
```

You should see 14 tables:
1. roles
2. users
3. suppliers
4. categories
5. products
6. customers
7. orders
8. order_items
9. payments
10. notifications
11. user_notification_reads
12. settings
13. sms_logs
14. audit_logs

### Check Default Data
```sql
-- Check roles
SELECT * FROM roles;

-- Check admin user
SELECT id, username, full_name, role_id, status FROM users WHERE username = 'admin';

-- Check categories
SELECT * FROM categories;

-- Check settings
SELECT COUNT(*) FROM settings;
```

### Test Connection
Create a test file `test-db.php`:

```php
<?php
require_once 'admin/config/db.php';

try {
    $stmt = $pdo->query("SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = 'SAHANALK'");
    $result = $stmt->fetch();
    echo "✓ Database connection successful!<br>";
    echo "✓ Found {$result['table_count']} tables in SAHANALK database";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage();
}
```

## Troubleshooting

### Issue: "Access denied for user 'root'"

**Solution:**
1. Check MAMP MySQL is running
2. Verify username/password in `admin/config/db.php`
3. Check MySQL port (8889 vs 3306)

### Issue: "Can't connect to MySQL server"

**Solution:**
1. Make sure MAMP MySQL server is running
2. Check port number (MAMP default: 8889)
3. Update `$DB_PORT` in `admin/config/db.php`

### Issue: "Unknown database 'SAHANALK'"

**Solution:**
1. Run installation script again: `install.php`
2. Or manually create database via phpMyAdmin
3. Or run SQL file again

### Issue: "Table already exists"

**Solution:**
1. Drop existing database:
   ```sql
   DROP DATABASE IF EXISTS SAHANALK;
   ```
2. Run installation again

### Issue: "Connection timeout"

**Solution:**
1. Increase timeout in `admin/config/db.php`:
   ```php
   PDO::ATTR_TIMEOUT => 60
   ```
2. Check MAMP MySQL is running properly

## Database Maintenance

### Backup Database
```bash
# Via command line
/Applications/MAMP/Library/bin/mysqldump -u root -proot -P 8889 SAHANALK > backup_$(date +%Y%m%d).sql

# Via phpMyAdmin
# Select SAHANALK → Export → Go
```

### Restore Database
```bash
# Via command line
/Applications/MAMP/Library/bin/mysql -u root -proot -P 8889 SAHANALK < backup_20240101.sql

# Via phpMyAdmin
# Select SAHANALK → Import → Choose file → Go
```

### Reset Database
```sql
DROP DATABASE IF EXISTS SAHANALK;
-- Then run install.php again
```

## Production Deployment

Before deploying to production:

1. **Change admin password**
2. **Update database credentials** in `admin/config/db.php`
3. **Delete or protect** `install.php`
4. **Use environment variables** for database credentials:
   ```php
   $DB_HOST = getenv('DB_HOST') ?: 'localhost';
   $DB_USER = getenv('DB_USER') ?: 'production_user';
   $DB_PASS = getenv('DB_PASS') ?: 'secure_password';
   ```

## Support

If you encounter any issues:

1. Check MAMP error logs
2. Check PHP error logs
3. Verify MySQL is running
4. Check port numbers match
5. Review database connection settings

---

**Installation Complete!** 🎉

Your SAHANALK database is now ready to use with the Daily Collection Manager application.

