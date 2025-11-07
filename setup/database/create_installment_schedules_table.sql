-- =====================================================
-- Create installment_schedules table and modify orders table
-- This migration adds support for daily installment payment tracking
-- =====================================================

USE SAHANALK;

-- Create installment_schedules table to track daily payment schedules
CREATE TABLE IF NOT EXISTS installment_schedules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    schedule_date DATE NOT NULL,
    due_amount DECIMAL(10, 2) NOT NULL,
    paid_amount DECIMAL(10, 2) DEFAULT 0.00,
    status ENUM('pending', 'paid', 'missed', 'partial') DEFAULT 'pending',
    payment_id INT UNSIGNED DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_installment_schedules_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_installment_schedules_payment FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE SET NULL,
    INDEX idx_schedule_order_date (order_id, schedule_date),
    INDEX idx_schedule_status (status),
    INDEX idx_schedule_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Daily installment payment schedule tracking';

-- Modify orders table to add assignment_date column
ALTER TABLE orders 
    ADD COLUMN IF NOT EXISTS assignment_date DATE DEFAULT NULL COMMENT 'Date when installment was assigned';

-- Add index for assignment_date if it doesn't exist
-- Note: MySQL doesn't support IF NOT EXISTS for indexes directly, so we'll use a different approach
SET @index_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = 'SAHANALK' 
    AND TABLE_NAME = 'orders' 
    AND INDEX_NAME = 'idx_orders_assignment_date'
);

SET @sql = IF(@index_exists = 0,
    'ALTER TABLE orders ADD INDEX idx_orders_assignment_date (assignment_date)',
    'SELECT "Index idx_orders_assignment_date already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Show table structure
DESCRIBE installment_schedules;
DESCRIBE orders;

-- Show success message
SELECT 'Installment schedules table and orders table modifications completed successfully!' AS Status;

