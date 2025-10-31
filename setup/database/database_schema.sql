-- =====================================================
-- SAHANALK Database Schema
-- Daily Collection Manager - Complete Database Structure
-- =====================================================

-- Drop database if exists (use with caution in production)
-- DROP DATABASE IF EXISTS SAHANALK;

-- Create Database
CREATE DATABASE IF NOT EXISTS SAHANALK 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE SAHANALK;

-- =====================================================
-- 1. ROLES TABLE
-- =====================================================
CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE COMMENT 'Role name: admin, staff',
    description VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_roles_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO roles (name, description) VALUES
('admin', 'Administrator with full access'),
('staff', 'Staff member with limited access');

-- =====================================================
-- 2. USERS TABLE (Staff with username/password)
-- =====================================================
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE COMMENT 'Login username',
    password_hash VARCHAR(255) NOT NULL COMMENT 'BCrypt password hash (secure encryption)',
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    mobile VARCHAR(20) DEFAULT NULL,
    role_id INT UNSIGNED NOT NULL DEFAULT 2 COMMENT '2 = staff, 1 = admin',
    status ENUM('active', 'disabled', 'suspended') NOT NULL DEFAULT 'active',
    last_login DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT UNSIGNED DEFAULT NULL COMMENT 'User who created this account',
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT,
    CONSTRAINT fk_users_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_users_username (username),
    INDEX idx_users_role (role_id),
    INDEX idx_users_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin user (will be created/updated during installation)
-- Admin credentials are configured during installation setup
-- This is a placeholder - the actual admin user is created via install.php
-- If you need to manually create admin, use: setup/database/generate-password-hash.php
-- Then run: INSERT INTO users (username, password_hash, full_name, email, role_id, status) VALUES (...)

-- =====================================================
-- 3. SUPPLIERS TABLE
-- =====================================================
CREATE TABLE suppliers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(200) NOT NULL,
    contact_person VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_suppliers_company (company_name),
    INDEX idx_suppliers_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. CATEGORIES TABLE
-- =====================================================
CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_categories_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default categories
INSERT INTO categories (name, description) VALUES
('Electronics', 'Electronic devices and gadgets'),
('Accessories', 'Tech accessories and peripherals'),
('Cables', 'Various types of cables and adapters'),
('Furniture', 'Office and home furniture items'),
('Clothing', 'Apparel and fashion items');

-- =====================================================
-- 5. PRODUCTS TABLE
-- =====================================================
CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(80) NOT NULL UNIQUE COMMENT 'Product SKU/ID (e.g., P001)',
    name VARCHAR(200) NOT NULL,
    description TEXT DEFAULT NULL,
    category_id INT UNSIGNED DEFAULT NULL,
    supplier_id INT UNSIGNED DEFAULT NULL,
    price_regular DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Regular/MRP price',
    price_selling DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Selling price',
    quantity INT NOT NULL DEFAULT 0 COMMENT 'Current stock quantity',
    low_stock_threshold INT NOT NULL DEFAULT 5 COMMENT 'Alert when stock <= this value',
    image_url VARCHAR(500) DEFAULT NULL COMMENT 'Product image URL or path',
    status ENUM('active', 'inactive', 'out_of_stock') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT UNSIGNED DEFAULT NULL,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    CONSTRAINT fk_products_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    CONSTRAINT fk_products_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_products_sku (sku),
    INDEX idx_products_category (category_id),
    INDEX idx_products_stock (quantity),
    INDEX idx_products_status (status),
    INDEX idx_products_supplier (supplier_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. CUSTOMERS TABLE (with username/password for portal access)
-- =====================================================
CREATE TABLE customers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_code VARCHAR(50) NOT NULL UNIQUE COMMENT 'Customer ID (e.g., C001)',
    username VARCHAR(100) DEFAULT NULL UNIQUE COMMENT 'Optional portal username',
    password_hash VARCHAR(255) DEFAULT NULL COMMENT 'Optional portal password hash (BCrypt encrypted)',
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    mobile VARCHAR(20) NOT NULL,
    address VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'inactive', 'blocked') NOT NULL DEFAULT 'active',
    total_purchased DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Total amount purchased',
    total_paid DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Total amount paid',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_customers_code (customer_code),
    INDEX idx_customers_username (username),
    INDEX idx_customers_mobile (mobile),
    INDEX idx_customers_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. ORDERS TABLE
-- =====================================================
CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) NOT NULL UNIQUE COMMENT 'Order number (e.g., ORD-2024-001)',
    customer_id INT UNSIGNED NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    paid_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    remaining_balance DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    installment_period INT UNSIGNED DEFAULT 30 COMMENT 'Payment period in days',
    daily_payment DECIMAL(10, 2) DEFAULT 0.00 COMMENT 'Daily payment amount',
    status ENUM('pending', 'active', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    order_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_by INT UNSIGNED DEFAULT NULL COMMENT 'Staff who created order',
    notes TEXT DEFAULT NULL,
    CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    CONSTRAINT fk_orders_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_orders_number (order_number),
    INDEX idx_orders_customer (customer_id),
    INDEX idx_orders_status (status),
    INDEX idx_orders_date (order_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. ORDER ITEMS TABLE
-- =====================================================
CREATE TABLE order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10, 2) NOT NULL COMMENT 'Price at time of order',
    subtotal DECIMAL(10, 2) NOT NULL COMMENT 'quantity * unit_price',
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    INDEX idx_order_items_order (order_id),
    INDEX idx_order_items_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 9. PAYMENTS TABLE (Daily Collections)
-- =====================================================
CREATE TABLE payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    order_id INT UNSIGNED DEFAULT NULL COMMENT 'Associated order if any',
    amount DECIMAL(10, 2) NOT NULL COMMENT 'Payment amount collected',
    payment_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    payment_method ENUM('cash', 'card', 'bank_transfer', 'mobile') DEFAULT 'cash',
    remaining_balance DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Balance after this payment',
    sms_sent TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'SMS notification sent',
    notes TEXT DEFAULT NULL,
    collected_by INT UNSIGNED DEFAULT NULL COMMENT 'Staff who collected payment',
    CONSTRAINT fk_payments_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    CONSTRAINT fk_payments_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    CONSTRAINT fk_payments_collector FOREIGN KEY (collected_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_payments_customer (customer_id),
    INDEX idx_payments_date (payment_date),
    INDEX idx_payments_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 10. NOTIFICATIONS TABLE (Bell Notifications)
-- =====================================================
CREATE TABLE notifications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL COMMENT 'low_stock, new_user, payment_due, etc.',
    title VARCHAR(200) NOT NULL,
    message TEXT DEFAULT NULL,
    data_json JSON DEFAULT NULL COMMENT 'Additional data in JSON format',
    audience_type ENUM('all', 'user', 'role') NOT NULL DEFAULT 'all' COMMENT 'Target audience',
    user_id INT UNSIGNED DEFAULT NULL COMMENT 'Specific user if audience_type = user',
    role_id INT UNSIGNED DEFAULT NULL COMMENT 'Specific role if audience_type = role',
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    read_at DATETIME DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_notifications_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    INDEX idx_notifications_type (type),
    INDEX idx_notifications_user (user_id, is_read),
    INDEX idx_notifications_audience (audience_type),
    INDEX idx_notifications_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 11. USER NOTIFICATION READS (Track which users read which notifications)
-- =====================================================
CREATE TABLE user_notification_reads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    notification_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    read_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_unr_notification FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE,
    CONSTRAINT fk_unr_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uk_unr_user_notification (notification_id, user_id),
    INDEX idx_unr_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 12. SETTINGS TABLE (App Configuration)
-- =====================================================
CREATE TABLE settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(120) NOT NULL UNIQUE,
    setting_value TEXT DEFAULT NULL,
    setting_type VARCHAR(50) DEFAULT 'string' COMMENT 'string, number, boolean, json',
    description VARCHAR(255) DEFAULT NULL,
    category VARCHAR(50) DEFAULT 'general' COMMENT 'general, sms, smtp, system',
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT UNSIGNED DEFAULT NULL,
    CONSTRAINT fk_settings_updater FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_settings_key (setting_key),
    INDEX idx_settings_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, setting_type, description, category) VALUES
('company_name', 'Daily Collection Manager', 'string', 'Company name', 'general'),
('company_email', 'info@dailycollection.com', 'string', 'Company email', 'general'),
('company_phone', '+94 77 123 4567', 'string', 'Company phone', 'general'),
('company_address', '123 Main Street, Colombo, Sri Lanka', 'string', 'Company address', 'general'),
('sms_gateway', 'textlk', 'string', 'SMS gateway provider', 'sms'),
('sms_sender_id', 'SahanaLK', 'string', 'SMS sender ID', 'sms'),
('sms_enabled', '1', 'boolean', 'Enable SMS notifications', 'sms'),
('sms_test_mode', '0', 'boolean', 'SMS test mode', 'sms'),
('smtp_host', '', 'string', 'SMTP host', 'smtp'),
('smtp_port', '587', 'number', 'SMTP port', 'smtp'),
('smtp_encryption', 'tls', 'string', 'SMTP encryption', 'smtp'),
('email_notifications', '1', 'boolean', 'Enable email notifications', 'notifications'),
('payment_reminders', '1', 'boolean', 'Enable payment reminders', 'notifications'),
('low_stock_alerts', '1', 'boolean', 'Enable low stock alerts', 'notifications'),
('currency', 'LKR', 'string', 'Default currency', 'system'),
('date_format', 'Y-m-d', 'string', 'Date format', 'system'),
('timezone', 'Asia/Colombo', 'string', 'Timezone', 'system'),
('auto_backup', '1', 'boolean', 'Automatic backup enabled', 'backup');

-- =====================================================
-- 13. SMS LOGS TABLE
-- =====================================================
CREATE TABLE sms_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recipient VARCHAR(20) NOT NULL COMMENT 'Phone number in format 94XXXXXXXXX',
    sender_id VARCHAR(11) NOT NULL DEFAULT 'SahanaLK',
    message TEXT NOT NULL,
    gateway VARCHAR(50) NOT NULL DEFAULT 'textlk',
    http_code INT NOT NULL DEFAULT 0,
    success TINYINT(1) NOT NULL DEFAULT 0,
    response_json JSON DEFAULT NULL,
    raw_response TEXT DEFAULT NULL,
    error_message VARCHAR(500) DEFAULT NULL,
    sent_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    related_type VARCHAR(50) DEFAULT NULL COMMENT 'payment, order, etc.',
    related_id INT UNSIGNED DEFAULT NULL,
    INDEX idx_sms_logs_recipient (recipient),
    INDEX idx_sms_logs_sent (sent_at),
    INDEX idx_sms_logs_success (success)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 14. AUDIT LOGS TABLE (Optional - for tracking changes)
-- =====================================================
CREATE TABLE audit_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED DEFAULT NULL,
    action VARCHAR(100) NOT NULL COMMENT 'create, update, delete, login, etc.',
    table_name VARCHAR(100) DEFAULT NULL,
    record_id INT UNSIGNED DEFAULT NULL,
    old_values JSON DEFAULT NULL,
    new_values JSON DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(500) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_audit_logs_user (user_id),
    INDEX idx_audit_logs_action (action),
    INDEX idx_audit_logs_table (table_name),
    INDEX idx_audit_logs_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TRIGGERS FOR AUTOMATIC NOTIFICATIONS
-- =====================================================

DELIMITER $$

-- Trigger: Low Stock Alert
-- Creates notification when product stock drops to or below threshold
CREATE TRIGGER trg_products_low_stock 
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    -- Check if stock dropped to or below threshold and stock actually changed
    IF NEW.quantity <= NEW.low_stock_threshold 
       AND NEW.quantity <> OLD.quantity 
       AND NEW.quantity >= 0
       AND OLD.low_stock_threshold > 0 THEN
        
        INSERT INTO notifications (
            type, 
            title, 
            message, 
            data_json, 
            audience_type, 
            user_id
        ) VALUES (
            'low_stock',
            CONCAT('Low Stock Alert: ', NEW.name),
            CONCAT('Product ', NEW.sku, ' (', NEW.name, ') has only ', NEW.quantity, ' units remaining. Threshold: ', NEW.low_stock_threshold),
            JSON_OBJECT(
                'product_id', NEW.id,
                'sku', NEW.sku,
                'name', NEW.name,
                'stock', NEW.quantity,
                'threshold', NEW.low_stock_threshold
            ),
            'all',
            NULL
        );
    END IF;
END$$

-- Trigger: New User Added
-- Creates notification when a new staff member is added
CREATE TRIGGER trg_users_new_staff 
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    -- Only notify for new active staff members (not during initial admin creation)
    IF NEW.role_id = 2 AND NEW.status = 'active' THEN
        INSERT INTO notifications (
            type, 
            title, 
            message, 
            data_json, 
            audience_type, 
            user_id
        ) VALUES (
            'new_user',
            'New Staff Member Added',
            CONCAT('New staff member ', NEW.full_name, ' (', NEW.username, ') has been added to the system.'),
            JSON_OBJECT(
                'user_id', NEW.id,
                'username', NEW.username,
                'full_name', NEW.full_name,
                'role_id', NEW.role_id,
                'created_by', NEW.created_by
            ),
            'all',
            NULL
        );
    END IF;
END$$

-- Trigger: Payment Due Reminder (Optional - can be triggered by scheduled job)
-- This would need to be called via stored procedure or cron job
-- Example: Check daily for customers with due payments

-- Trigger: Update Customer Balance on Payment
CREATE TRIGGER trg_payments_update_customer_balance
AFTER INSERT ON payments
FOR EACH ROW
BEGIN
    UPDATE customers 
    SET total_paid = total_paid + NEW.amount,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = NEW.customer_id;
    
    -- Update order balance if payment is for an order
    IF NEW.order_id IS NOT NULL THEN
        UPDATE orders 
        SET paid_amount = paid_amount + NEW.amount,
            remaining_balance = total_amount - (paid_amount + NEW.amount),
            updated_at = CURRENT_TIMESTAMP
        WHERE id = NEW.order_id;
    END IF;
END$$

-- Trigger: Update Product Stock on Order Item Creation
CREATE TRIGGER trg_order_items_update_stock
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    UPDATE products 
    SET quantity = quantity - NEW.quantity,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = NEW.product_id 
      AND quantity >= NEW.quantity; -- Prevent negative stock
END$$

DELIMITER ;

-- =====================================================
-- STORED PROCEDURES
-- =====================================================

DELIMITER $$

-- Procedure: Get Unread Notifications Count for User
CREATE PROCEDURE sp_get_unread_notifications_count(IN p_user_id INT UNSIGNED, IN p_role_id INT UNSIGNED)
BEGIN
    SELECT COUNT(*) as unread_count
    FROM notifications n
    LEFT JOIN user_notification_reads unr ON n.id = unr.notification_id AND unr.user_id = p_user_id
    WHERE unr.id IS NULL
      AND (
          n.audience_type = 'all' 
          OR (n.audience_type = 'user' AND n.user_id = p_user_id)
          OR (n.audience_type = 'role' AND n.role_id = p_role_id)
      )
      AND n.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY); -- Only show last 30 days
END$$

-- Procedure: Mark Notification as Read
CREATE PROCEDURE sp_mark_notification_read(IN p_notification_id INT UNSIGNED, IN p_user_id INT UNSIGNED)
BEGIN
    INSERT INTO user_notification_reads (notification_id, user_id)
    VALUES (p_notification_id, p_user_id)
    ON DUPLICATE KEY UPDATE read_at = CURRENT_TIMESTAMP;
END$$

-- Procedure: Get Customer Balance Summary
CREATE PROCEDURE sp_get_customer_balance(IN p_customer_id INT UNSIGNED)
BEGIN
    SELECT 
        c.id,
        c.customer_code,
        c.full_name,
        c.mobile,
        COALESCE(SUM(o.total_amount), 0) as total_orders,
        COALESCE(SUM(p.amount), 0) as total_paid,
        COALESCE(SUM(o.total_amount), 0) - COALESCE(SUM(p.amount), 0) as remaining_balance
    FROM customers c
    LEFT JOIN orders o ON c.id = o.customer_id AND o.status IN ('pending', 'active')
    LEFT JOIN payments p ON c.id = p.customer_id
    WHERE c.id = p_customer_id
    GROUP BY c.id, c.customer_code, c.full_name, c.mobile;
END$$

DELIMITER ;

-- =====================================================
-- VIEWS FOR COMMON QUERIES
-- =====================================================

-- View: Customer Summary with Balance
CREATE OR REPLACE VIEW v_customer_summary AS
SELECT 
    c.id,
    c.customer_code,
    c.full_name,
    c.email,
    c.mobile,
    c.status,
    COUNT(DISTINCT o.id) as total_orders,
    COALESCE(SUM(o.total_amount), 0) as total_ordered,
    COALESCE(SUM(p.amount), 0) as total_paid,
    COALESCE(SUM(o.total_amount), 0) - COALESCE(SUM(p.amount), 0) as remaining_balance,
    MAX(p.payment_date) as last_payment_date,
    c.created_at
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id AND o.status IN ('pending', 'active')
LEFT JOIN payments p ON c.id = p.customer_id
GROUP BY c.id, c.customer_code, c.full_name, c.email, c.mobile, c.status, c.created_at;

-- View: Product Summary with Stock Status
CREATE OR REPLACE VIEW v_product_summary AS
SELECT 
    p.id,
    p.sku,
    p.name,
    p.price_regular,
    p.price_selling,
    p.quantity as stock_quantity,
    p.low_stock_threshold,
    CASE 
        WHEN p.quantity <= p.low_stock_threshold THEN 'low_stock'
        WHEN p.quantity = 0 THEN 'out_of_stock'
        ELSE 'in_stock'
    END as stock_status,
    c.name as category_name,
    s.company_name as supplier_name,
    p.status,
    p.created_at,
    p.updated_at
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN suppliers s ON p.supplier_id = s.id;

-- View: Daily Collection Summary
CREATE OR REPLACE VIEW v_daily_collections AS
SELECT 
    DATE(p.payment_date) as collection_date,
    COUNT(*) as payment_count,
    SUM(p.amount) as total_collected,
    COUNT(DISTINCT p.customer_id) as unique_customers,
    COUNT(DISTINCT p.collected_by) as staff_count
FROM payments p
GROUP BY DATE(p.payment_date)
ORDER BY collection_date DESC;

-- =====================================================
-- INDEXES FOR PERFORMANCE (Additional)
-- =====================================================

-- Composite indexes for common queries
CREATE INDEX idx_customers_balance ON customers(status, total_purchased, total_paid);
CREATE INDEX idx_orders_customer_status ON orders(customer_id, status);
CREATE INDEX idx_payments_customer_date ON payments(customer_id, payment_date DESC);
CREATE INDEX idx_products_stock_status ON products(status, quantity, low_stock_threshold);

-- =====================================================
-- SAMPLE DATA (Optional - for testing)
-- =====================================================

-- Sample Supplier
INSERT INTO suppliers (company_name, contact_person, phone, email, status) VALUES
('Tech Suppliers Ltd', 'John Smith', '94771234567', 'contact@techsuppliers.lk', 'active');

-- Sample Products
INSERT INTO products (sku, name, description, category_id, supplier_id, price_regular, price_selling, quantity, low_stock_threshold, status) VALUES
('P001', 'Wireless Headphones', 'High-quality wireless headphones with noise cancellation', 1, 1, 120.00, 99.99, 25, 5, 'active'),
('P002', 'Laptop Stand', 'Adjustable aluminum laptop stand', 2, 1, 60.00, 49.99, 12, 3, 'active'),
('P003', 'USB-C Cable', 'Fast charging USB-C cable', 3, 1, 25.00, 19.99, 50, 10, 'active');

-- =====================================================
-- GRANT PERMISSIONS (Adjust as needed for your MySQL users)
-- =====================================================

-- Example (uncomment and adjust):
-- GRANT SELECT, INSERT, UPDATE, DELETE ON SAHANALK.* TO 'dcm_user'@'localhost';
-- FLUSH PRIVILEGES;

-- =====================================================
-- END OF SCHEMA
-- =====================================================


