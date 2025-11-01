# 📸 How to Add Product Images - Complete Guide

## ✅ Current Setup

Your catalog is **already configured** to display product images from the database!

The system reads from: **`products.image_url`** field

## 🎯 3 Ways to Add Product Images

### **Method 1: Using phpMyAdmin (Easiest)**

1. Open phpMyAdmin:
   ```
   http://localhost:8888/phpMyAdmin  (or your MAMP phpMyAdmin URL)
   ```

2. Select your database: **`SAHANALK`**

3. Click on the **`products`** table

4. Click **"Edit"** (pencil icon) next to a product

5. In the **`image_url`** field, paste an image URL:
   ```
   https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop
   ```

6. Click **"Go"** to save

7. **Refresh your catalog page** - the image should appear!

---

### **Method 2: Using SQL Queries (Bulk Update)**

Run these SQL queries in phpMyAdmin → SQL tab:

#### Example 1: Update specific product by SKU
```sql
UPDATE products 
SET image_url = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop'
WHERE sku = 'P002';
```

#### Example 2: Update specific product by ID
```sql
UPDATE products 
SET image_url = 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400&h=400&fit=crop'
WHERE id = 1;
```

#### Example 3: Update all products in a category
```sql
UPDATE products p
JOIN categories c ON p.category_id = c.id
SET p.image_url = 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=400&h=400&fit=crop'
WHERE c.name = 'Electronics';
```

#### Example 4: Update multiple products at once
```sql
-- Headphone
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop' WHERE sku = 'P002';

-- iPhone
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1592286849809-26c0415422cc?w=400&h=400&fit=crop' WHERE sku = 'P003';

-- Nike Shoes
UPDATE products SET image_url = 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop' WHERE sku = 'P001';
```

---

### **Method 3: When Adding New Products**

When you add a product via the **Add Product page**:

1. Go to: `http://localhost/Daily-Collection-Manager/admin/add-product.php`
2. Fill in the product details
3. In the **"Image URL"** field, paste an image URL
4. Click **"Add Product"**
5. The product will appear in the catalog with the image!

---

## 🖼️ Where to Get Image URLs

### **Option 1: Unsplash (Free, High-Quality)**

Visit: https://unsplash.com

1. Search for your product type (e.g., "headphones", "laptop", "shoes")
2. Click on an image you like
3. Right-click on the image → **Copy Image Address**
4. Paste the URL into the `image_url` field

**Pro Tip:** Add `?w=400&h=400&fit=crop` to the end for optimized size:
```
https://images.unsplash.com/photo-[ID]?w=400&h=400&fit=crop
```

### **Option 2: Your Own Images**

Upload to your server and use relative paths:
```
uploads/products/headphone-001.jpg
```

### **Option 3: Product URLs from Suppliers**

Use direct URLs from manufacturer websites or online stores.

---

## 🚀 Quick Test - Add Sample Images

Copy and paste this SQL into phpMyAdmin to add sample images:

```sql
-- Update products with sample images based on current products
-- (Adjust SKUs to match your actual products)

UPDATE products 
SET image_url = CASE sku
    WHEN 'P001' THEN 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop'
    WHEN 'P002' THEN 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop'
    WHEN 'P003' THEN 'https://images.unsplash.com/photo-1592286849809-26c0415422cc?w=400&h=400&fit=crop'
    ELSE image_url
END
WHERE sku IN ('P001', 'P002', 'P003');
```

After running this query:
1. Go back to your catalog page
2. Click the **"Refresh"** button
3. **Images should appear!** 🎉

---

## 🔍 Check Current Images

To see which products have images:

```sql
SELECT sku, name, image_url
FROM products
ORDER BY id;
```

To see which products **don't** have images:

```sql
SELECT sku, name
FROM products
WHERE image_url IS NULL OR image_url = ''
ORDER BY id;
```

---

## 💡 What Happens If No Image URL?

If a product doesn't have an `image_url` or it's empty:

1. **First fallback:** Shows category-specific placeholder
   - Electronics → Tech image
   - Accessories → Accessory image
   - Cables → Cable image
   - Furniture → Furniture image
   - Clothing → Clothing image

2. **Second fallback:** Shows an avatar with product's initials
   - Example: "Headphone" → Shows "HE" in a colored circle

---

## 📁 Database Field Info

**Table:** `products`  
**Field:** `image_url`  
**Type:** `VARCHAR(500)`  
**Can be NULL:** Yes  
**Purpose:** Stores the URL or path to product image

---

## ✅ Verification Steps

After adding images:

1. ✅ Run query to check image URLs:
   ```sql
   SELECT sku, name, image_url FROM products;
   ```

2. ✅ Open catalog page:
   ```
   http://localhost/Daily-Collection-Manager/admin/catalog.php
   ```

3. ✅ Click the **"Refresh"** button

4. ✅ Images should appear on product cards!

5. ✅ Check browser console (F12) for any errors

---

## 🐛 Troubleshooting

### Images not showing?

**Check 1:** Verify image URLs are in database
```sql
SELECT sku, name, image_url FROM products WHERE sku = 'P002';
```

**Check 2:** Test the image URL directly
- Copy the URL from database
- Paste it in browser address bar
- Image should load

**Check 3:** Check browser console (F12)
- Look for 404 errors or CORS errors
- Fix URLs if needed

**Check 4:** Clear cache and refresh
- Press `Ctrl+Shift+R` (Windows) or `Cmd+Shift+R` (Mac)

---

## 🎨 Recommended Image Sizes

- **Width:** 400-800px
- **Height:** 400-800px
- **Aspect Ratio:** 1:1 (square) for best display
- **Format:** JPG, PNG, or WebP
- **File Size:** < 500KB for fast loading

---

## 📝 Summary

1. Product images are stored in `products.image_url` field ✅
2. Catalog is already configured to show them ✅
3. Add URLs via phpMyAdmin, SQL, or Add Product page ✅
4. Use Unsplash for free images or your own URLs ✅
5. If no image, shows smart category-based placeholders ✅

**Your catalog will automatically show images once you add URLs to the database!** 🚀

---

**Need help?** Check the console (F12) for error messages or test the image URLs directly in your browser.

