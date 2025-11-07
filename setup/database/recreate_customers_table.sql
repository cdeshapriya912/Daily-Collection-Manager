-- =====================================================
-- Recreate customers table with registration form fields
-- WARNING: This will delete all existing customer data!
-- =====================================================

USE SAHANALK;

-- Temporarily disable foreign key checks to allow table drop
SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing customers table (WARNING: All data will be lost!)
DROP TABLE IF EXISTS customers;

-- Create new customers table with all registration fields
CREATE TABLE customers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_code VARCHAR(50) NOT NULL UNIQUE COMMENT 'Customer ID (e.g., C001)',
    
    -- Basic Information
    first_name VARCHAR(100) NOT NULL COMMENT 'First name from registration',
    last_name VARCHAR(100) NOT NULL COMMENT 'Last name from registration',
    full_name VARCHAR(150) NOT NULL COMMENT 'Auto-generated: first_name + last_name',
    full_name_with_surname VARCHAR(200) DEFAULT NULL COMMENT 'Full name with surname (optional)',
    
    -- Contact Information
    email VARCHAR(150) NOT NULL,
    mobile VARCHAR(20) NOT NULL UNIQUE,
    
    -- Address Information
    address VARCHAR(500) NOT NULL COMMENT 'Permanent address',
    gnd VARCHAR(200) DEFAULT NULL COMMENT 'Grama Niladari Division',
    lgi VARCHAR(200) DEFAULT NULL COMMENT 'Local Government Institutions',
    police_station VARCHAR(150) DEFAULT NULL COMMENT 'Police station',
    
    -- Personal Information
    nic VARCHAR(12) NOT NULL UNIQUE COMMENT 'NIC ID Number (12 digits or 9+V/X)',
    occupation VARCHAR(150) DEFAULT NULL COMMENT 'Permanent Occupation',
    residence_period VARCHAR(100) DEFAULT NULL COMMENT 'Period of residence at address',
    
    -- Document Uploads
    nic_front_path VARCHAR(255) DEFAULT NULL COMMENT 'Path to NIC front image',
    nic_back_path VARCHAR(255) DEFAULT NULL COMMENT 'Path to NIC back image',
    customer_photo_path VARCHAR(255) DEFAULT NULL COMMENT 'Path to customer photo',
    
    -- Portal Access (Optional)
    username VARCHAR(100) DEFAULT NULL UNIQUE COMMENT 'Optional portal username',
    password_hash VARCHAR(255) DEFAULT NULL COMMENT 'Optional portal password hash (BCrypt encrypted)',
    
    -- Status and Financial
    status ENUM('active', 'inactive', 'blocked') NOT NULL DEFAULT 'active',
    total_purchased DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Total amount purchased',
    total_paid DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Total amount paid',
    
    -- Timestamps
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for performance
    INDEX idx_customers_code (customer_code),
    INDEX idx_customers_first_name (first_name),
    INDEX idx_customers_last_name (last_name),
    INDEX idx_customers_username (username),
    INDEX idx_customers_mobile (mobile),
    INDEX idx_customers_nic (nic),
    INDEX idx_customers_status (status),
    INDEX idx_customers_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Customer registration table with extended fields for form data and documents';

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Show table structure
DESCRIBE customers;

-- Show success message
SELECT 'Customers table recreated successfully!' AS Status;

