-- ============================================================
-- Migration: Rename price_regular to price_buying
-- Description: Change column name from "Regular Price" to "Buying Price"
-- Date: 2025-01-17
-- ============================================================

-- Rename column in products table
ALTER TABLE products 
CHANGE COLUMN price_regular price_buying DECIMAL(10,2) NOT NULL;

-- Verify the change
-- SELECT COLUMN_NAME, DATA_TYPE 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_NAME = 'products' AND COLUMN_NAME = 'price_buying';

