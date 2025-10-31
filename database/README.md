# SAHANALK Database Schema Documentation

## Database Name
**SAHANALK**

## Overview
Complete MySQL database schema for Daily Collection Manager application with support for:
- User authentication (Staff with username/password)
- Customer management (with optional portal login)
- Product inventory with low stock alerts
- Order management with installment payments
- Daily collection tracking
- Bell notifications system
- SMS integration
- Settings management

## Quick Start (MAMP)

### Easiest Method - Automated Installation

1. **Start MAMP servers** (Apache & MySQL)

2. **Open your browser and navigate to:**
   ```
   http://localhost:8888/Daily-Collection-Manager/install.php
   ```
   (Adjust port if different)

3. **Click "Install Database"** button

4. **Wait for installation to complete**

5. **Test connection:**
   ```
   http://localhost:8888/Daily-Collection-Manager/test-db-connection.php
   ```

6. **Delete or protect `install.php`** after successful installation

### Alternative Methods

1. **Via phpMyAdmin:**
   ```bash
   http://localhost:8888/phpMyAdmin/
   ```
   - Create database: `SAHANALK`
   - Import: `database/sahanalk_schema.sql`

2. **Via Command Line:**
   ```bash
   mysql -u root -proot -P 8889 < database/sahanalk_schema.sql
   ```

## Installation Details

1. **Import the schema:**
   ```bash
   mysql -u root -p < database/sahanalk_schema.sql
   ```

2. **Or via phpMyAdmin:**
   - Open phpMyAdmin
   - Create a new database named `SAHANALK`
   - Import `database/sahanalk_schema.sql`

3. **Update database configuration:**
   - Edit `admin/config/db.php`
   - Set `DB_NAME` to `SAHANALK`
   - Update port to `8889` if using MAMP default

## Default Credentials

**Admin User:**
- Username: `admin`
- Password: `admin123` (⚠️ **CHANGE THIS IMMEDIATELY!**)

## Password Encryption

**Secure BCrypt Encryption:**
- All passwords are encrypted using **BCrypt** (PHP `password_hash()`)
- BCrypt is industry-standard secure hashing
- Resistant to brute-force and rainbow table attacks
- Each password gets a unique hash (even for identical passwords)

## Change Password Methods

### Method 1: Web Interface (Easiest)
1. Open: `database/change-password.php`
2. Select user type (Staff/Customer)
3. Enter username and new password
4. Click "Change Password"

### Method 2: Generate Hash Script
1. Open: `database/generate-password-hash.php`
2. Enter password
3. Copy the generated SQL query
4. Run in phpMyAdmin or MySQL client

### Method 3: PHP Script
```php
<?php
require_once 'admin/config/db.php';
$username = 'admin';
$newPassword = 'YourNewPassword123';
$hash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
$stmt->execute([$hash, $username]);
echo "Password updated!";
```

### Method 4: Direct SQL (After generating hash)
```sql
-- First generate hash using generate-password-hash.php
-- Then run:
UPDATE users SET password_hash = '$2y$10$GENERATED_HASH_HERE' WHERE username = 'admin';
```

## Database Tables

### 1. Core Tables

#### `roles`
User roles (admin, staff)

#### `users` (Staff)
- `username` - Login username (unique)
- `password_hash` - BCrypt password hash
- `full_name`, `email`, `mobile`
- `role_id` - Foreign key to roles
- `status` - active, disabled, suspended
- `last_login` - Last login timestamp

#### `customers`
- `customer_code` - Unique customer ID (e.g., C001)
- `username` - Optional portal username
- `password_hash` - Optional portal password hash
- Customer contact information
- Payment tracking fields

#### `suppliers`
- Company and contact information

#### `categories`
- Product categories

#### `products`
- `sku` - Product SKU/ID (unique)
- `name`, `description`
- `category_id`, `supplier_id`
- `price_regular`, `price_selling`
- `quantity` - Current stock
- `low_stock_threshold` - Alert threshold
- `image_url` - Product image

#### `orders`
- Order information with installment period
- Links to customer and staff

#### `order_items`
- Individual items in an order

#### `payments`
- Daily collection records
- Links to customer, order, and staff

### 2. Notification System

#### `notifications`
Bell notifications with types:
- `low_stock` - Product stock alerts
- `new_user` - New staff member added
- `payment_due` - Payment reminders
- Custom types as needed

**Audience Types:**
- `all` - All staff members
- `user` - Specific user
- `role` - Specific role

#### `user_notification_reads`
Tracks which users have read which notifications

### 3. Configuration

#### `settings`
Key-value settings storage for:
- General settings (company info)
- SMS settings
- SMTP settings
- Notification preferences
- System settings

### 4. Logging

#### `sms_logs`
All SMS send attempts with results

#### `audit_logs`
Optional audit trail for system actions

## Automatic Triggers

### 1. Low Stock Alert (`trg_products_low_stock`)
- Automatically creates notification when product stock ≤ threshold
- Notification sent to all staff members

### 2. New Staff Alert (`trg_users_new_staff`)
- Creates notification when new staff member is added
- Sent to all staff members

### 3. Customer Balance Update (`trg_payments_update_customer_balance`)
- Updates customer total_paid when payment is recorded
- Updates order balance

### 4. Stock Update (`trg_order_items_update_stock`)
- Deducts stock when order item is created

## Stored Procedures

### `sp_get_unread_notifications_count(user_id, role_id)`
Returns count of unread notifications for a user

### `sp_mark_notification_read(notification_id, user_id)`
Marks a notification as read for a user

### `sp_get_customer_balance(customer_id)`
Returns customer balance summary

## Views

### `v_customer_summary`
Customer list with calculated balances

### `v_product_summary`
Products with stock status (in_stock, low_stock, out_of_stock)

### `v_daily_collections`
Daily collection summaries grouped by date

## Notification Types

1. **Low Stock** - Triggered automatically when stock drops
2. **New User** - Triggered when staff member is added
3. **Payment Due** - Can be created via scheduled jobs
4. Custom notifications can be added as needed

## Important Notes

1. **Password Security:**
   - Always use `password_hash()` to create password hashes
   - Verify with `password_verify()`
   - Default admin password should be changed immediately

2. **Notifications:**
   - Notifications older than 30 days are filtered by default
   - Use `user_notification_reads` table to track read status
   - For "all" notifications, each user needs separate read record

3. **Stock Management:**
   - Set `low_stock_threshold` appropriately for each product
   - Stock is automatically deducted when orders are created
   - Low stock alerts trigger automatically

4. **Customer Balance:**
   - Calculated from orders and payments
   - Updated automatically via triggers
   - Use view `v_customer_summary` for reporting

## Indexes

All tables include appropriate indexes for:
- Primary keys
- Foreign keys
- Frequently queried fields
- Composite indexes for common queries

## Maintenance

### Backup
```bash
mysqldump -u root -p SAHANALK > backup_$(date +%Y%m%d).sql
```

### Cleanup Old Notifications
```sql
DELETE FROM notifications 
WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY) 
AND is_read = 1;
```

### Reset Admin Password
```sql
UPDATE users 
SET password_hash = '$2y$10$YourNewHashHere' 
WHERE username = 'admin';
```


