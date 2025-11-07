-- =====================================================
-- Add mobile column to users table (if not exists)
-- This migration ensures the mobile field is available
-- =====================================================

USE SAHANALK;

-- Check and add mobile column to users table if it doesn't exist
SET @dbname = 'SAHANALK';
SET @tablename = 'users';
SET @columnname = 'mobile';
SET @columntype = 'VARCHAR(20) DEFAULT NULL';

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE 
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT 1', -- Column exists, do nothing
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' ', @columntype, ' AFTER email')
));

PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Verify the column was added/exists
SELECT 
    COLUMN_NAME, 
    DATA_TYPE, 
    CHARACTER_MAXIMUM_LENGTH,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'SAHANALK' 
  AND TABLE_NAME = 'users'
  AND COLUMN_NAME = 'mobile';

-- Show current users and their mobile numbers
SELECT id, username, full_name, email, mobile, role_id, status 
FROM users;

-- =====================================================
-- Migration complete
-- =====================================================








