-- Fix trigger to remove updated_at column references from orders table
-- Also adds total_purchased reduction as requested
-- This fixes the error: Unknown column 'updated_at' in 'field list'

DELIMITER $$

-- Drop the existing trigger
DROP TRIGGER IF EXISTS trg_payments_update_customer_balance;

-- Recreate the trigger:
-- - Removes updated_at from orders (orders table doesn't have it)
-- - Keeps updated_at for customers (customers table has it)
-- - Adds total_purchased reduction
CREATE TRIGGER trg_payments_update_customer_balance
AFTER INSERT ON payments
FOR EACH ROW
BEGIN
    -- Update customer: increase total_paid, decrease total_purchased, update timestamp
    UPDATE customers 
    SET total_paid = total_paid + NEW.amount,
        total_purchased = GREATEST(0, total_purchased - NEW.amount),
        updated_at = CURRENT_TIMESTAMP
    WHERE id = NEW.customer_id;
    
    -- Update order balance if payment is for an order (orders table doesn't have updated_at)
    IF NEW.order_id IS NOT NULL THEN
        UPDATE orders 
        SET paid_amount = paid_amount + NEW.amount,
            remaining_balance = total_amount - (paid_amount + NEW.amount)
        WHERE id = NEW.order_id;
    END IF;
END$$

DELIMITER ;


