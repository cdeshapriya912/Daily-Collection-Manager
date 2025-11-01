-- ========================================================================
-- QUICK FIX: Add Images to Your Exact Products
-- ========================================================================
-- Run this SQL in phpMyAdmin to add images to the products showing in your catalog
-- ========================================================================

USE SAHANALK;

-- First, let's see your current products
SELECT id, sku, name, image_url 
FROM products 
ORDER BY id 
LIMIT 10;

-- ========================================================================
-- QUICK FIX: Update the 3 products showing in your screenshot
-- Based on the names: Headphone, iPhone 12x, Nike Shoes (or Nike Shooes)
-- ========================================================================

-- Update Headphone product
UPDATE products 
SET image_url = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop'
WHERE name LIKE '%Headphone%' OR name LIKE '%headphone%';

-- Update iPhone product
UPDATE products 
SET image_url = 'https://images.unsplash.com/photo-1592286849809-26c0415422cc?w=400&h=400&fit=crop'
WHERE name LIKE '%iPhone%' OR name LIKE '%iphone%' OR name LIKE '%phone%';

-- Update Nike Shoes product
UPDATE products 
SET image_url = 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop'
WHERE name LIKE '%Nike%' OR name LIKE '%Shoe%' OR name LIKE '%shoe%';

-- ========================================================================
-- Alternative: Update by SKU if you know them
-- ========================================================================

-- If you know the SKUs from your screenshot (P001, P002, P003):
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop' WHERE sku = 'P001';
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop' WHERE sku = 'P002';
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1592286849809-26c0415422cc?w=400&h=400&fit=crop' WHERE sku = 'P003';

-- ========================================================================
-- Verify the updates worked
-- ========================================================================

SELECT 
    sku,
    name,
    CASE 
        WHEN image_url IS NOT NULL AND image_url != '' THEN '✅ HAS IMAGE'
        ELSE '❌ NO IMAGE'
    END as status,
    LEFT(image_url, 50) as image_preview
FROM products 
ORDER BY id;

-- ========================================================================
-- If you want to update ALL products at once with category-based images
-- ========================================================================

-- Electronics products
UPDATE products p
JOIN categories c ON p.category_id = c.id
SET p.image_url = 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=400&h=400&fit=crop'
WHERE c.name = 'Electronics' 
  AND (p.image_url IS NULL OR p.image_url = '');

-- Accessory products
UPDATE products p
JOIN categories c ON p.category_id = c.id
SET p.image_url = 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop'
WHERE c.name = 'Accessory' OR c.name = 'Accessories'
  AND (p.image_url IS NULL OR p.image_url = '');

-- ========================================================================
-- After running this:
-- 1. Go back to your catalog page
-- 2. Press F12 to open browser console
-- 3. Click the "Refresh" button on the page
-- 4. Check the console - you should see:
--    📸 Product Image URLs: [...]
-- 5. Images should now appear instead of the initials (HE, I1, NS)
-- ========================================================================

