-- =====================================================
-- Update customers table to add registration form fields
-- =====================================================

USE SAHANALK;

-- Add new columns to customers table
ALTER TABLE customers
  ADD COLUMN IF NOT EXISTS first_name VARCHAR(100) DEFAULT NULL COMMENT 'First name from registration' AFTER full_name,
  ADD COLUMN IF NOT EXISTS last_name VARCHAR(100) DEFAULT NULL COMMENT 'Last name from registration' AFTER first_name,
  ADD COLUMN IF NOT EXISTS full_name_with_surname VARCHAR(200) DEFAULT NULL COMMENT 'Full name with surname' AFTER last_name,
  ADD COLUMN IF NOT EXISTS nic VARCHAR(12) DEFAULT NULL COMMENT 'NIC ID Number (12 digits or 9+V/X)' AFTER mobile,
  ADD COLUMN IF NOT EXISTS gnd VARCHAR(200) DEFAULT NULL COMMENT 'Grama Niladari Division' AFTER address,
  ADD COLUMN IF NOT EXISTS lgi VARCHAR(200) DEFAULT NULL COMMENT 'Local Government Institutions' AFTER gnd,
  ADD COLUMN IF NOT EXISTS police_station VARCHAR(150) DEFAULT NULL COMMENT 'Police station' AFTER lgi,
  ADD COLUMN IF NOT EXISTS occupation VARCHAR(150) DEFAULT NULL COMMENT 'Permanent Occupation' AFTER police_station,
  ADD COLUMN IF NOT EXISTS residence_period VARCHAR(100) DEFAULT NULL COMMENT 'Period of residence at address' AFTER occupation,
  ADD COLUMN IF NOT EXISTS nic_front_path VARCHAR(255) DEFAULT NULL COMMENT 'Path to NIC front image' AFTER residence_period,
  ADD COLUMN IF NOT EXISTS nic_back_path VARCHAR(255) DEFAULT NULL COMMENT 'Path to NIC back image' AFTER nic_front_path,
  ADD COLUMN IF NOT EXISTS customer_photo_path VARCHAR(255) DEFAULT NULL COMMENT 'Path to customer photo' AFTER nic_back_path;

-- Add indexes for searchable fields
ALTER TABLE customers
  ADD INDEX IF NOT EXISTS idx_customers_nic (nic),
  ADD INDEX IF NOT EXISTS idx_customers_first_name (first_name),
  ADD INDEX IF NOT EXISTS idx_customers_last_name (last_name);

-- Show updated table structure
DESCRIBE customers;





