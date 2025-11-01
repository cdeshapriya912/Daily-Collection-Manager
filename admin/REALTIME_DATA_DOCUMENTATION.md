# Real-Time Category Data Loading

## Overview
The Category Management page (`category.php`) loads data in **real-time** directly from the MySQL database with no caching. Product counts are calculated live from the `products` table.

## How It Works

### 1. Database Query (Real-Time Product Count)
Located in: `admin/api/get-categories.php`

```sql
SELECT 
    c.id, 
    c.name, 
    c.description, 
    c.created_at,
    c.updated_at,
    (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) as product_count
FROM categories c
ORDER BY c.name ASC
```

**Key Points:**
- The subquery `(SELECT COUNT(*) FROM products p WHERE p.category_id = c.id)` runs for **each category**
- Product counts are **calculated on-the-fly** from the actual `products` table
- No caching or stored values - always fresh from database

### 2. Cache Prevention Headers
Multiple headers ensure no caching occurs:

```php
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');
```

### 3. Frontend Real-Time Fetching
Located in: `admin/category.php` JavaScript

```javascript
const response = await fetch(url, {
    method: 'GET',
    cache: 'no-store',
    headers: { 
        'Cache-Control': 'no-cache, no-store, must-revalidate', 
        'Pragma': 'no-cache',
        'Expires': '0'
    }
});
```

**Features:**
- Timestamp parameter (`_t`) forces unique URL on each request
- Random parameter (`_r`) prevents browser caching
- Fetch API with `cache: 'no-store'` directive

## Visual Indicators

### 1. Live Data Indicator
A pulsing green dot shows the page is displaying live data:
```html
<span class="material-icons text-primary" id="liveIndicator">fiber_manual_record</span>
```

### 2. Last Update Timestamp
Shows exactly when data was last refreshed:
```
Last updated: 10:45:30 AM
```

### 3. Product Count Badges
Real-time product counts displayed as badges:
- Green badge = Category has products
- Gray badge = Empty category

## Testing Real-Time Updates

### Manual Test:
1. Open `category.php` in your browser
2. Note the product counts for each category
3. Open phpMyAdmin or MySQL console
4. Add a new product to a category:
   ```sql
   INSERT INTO products (sku, name, category_id, price_buying, price_selling, quantity)
   VALUES ('TEST001', 'Test Product', 1, 10.00, 15.00, 10);
   ```
5. Click the **Refresh** button in the category page
6. **The product count should increase immediately!**

### Automated Verification:
Visit: `admin/api/verify-realtime-data.php`

This endpoint shows:
- Database timestamp
- Total categories and products
- Real-time product counts per category
- Sample product-category mappings

## Database Tables

### Categories Table
```sql
CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Products Table (with Foreign Key)
```sql
CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(80) NOT NULL UNIQUE,
    name VARCHAR(200) NOT NULL,
    category_id INT UNSIGNED DEFAULT NULL,
    price_buying DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    price_selling DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    quantity INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_products_category 
        FOREIGN KEY (category_id) 
        REFERENCES categories(id) 
        ON DELETE SET NULL
);
```

## Performance Considerations

### Current Approach (Subquery)
**Pros:**
- Absolutely accurate real-time data
- No synchronization issues
- Simple implementation

**Cons:**
- N+1 query problem (one COUNT per category)
- May be slow with many categories and products

### For Large Datasets (Future Optimization)
If you have 1000+ categories or 100,000+ products, consider:

```sql
-- Use JOIN instead of subquery
SELECT 
    c.id, 
    c.name, 
    c.description,
    COUNT(p.id) as product_count
FROM categories c
LEFT JOIN products p ON p.category_id = c.id
GROUP BY c.id, c.name, c.description
ORDER BY c.name ASC
```

## Refresh Options

### 1. Manual Refresh
Click the **Refresh** button to reload all data from database

### 2. Search & Refresh
Typing in search and clicking Search forces a fresh database query

### 3. After Modifications
Data automatically refreshes after:
- Adding a category
- Editing a category
- Deleting a category

## Troubleshooting

### Product counts not updating?

**Check 1:** Verify foreign key relationships
```sql
SELECT p.*, c.name as category_name 
FROM products p 
LEFT JOIN categories c ON p.category_id = c.id;
```

**Check 2:** Test the API directly
Visit: `admin/api/get-categories.php`

**Check 3:** Clear browser cache
- Press `Ctrl+Shift+R` (Windows/Linux) or `Cmd+Shift+R` (Mac)

**Check 4:** Check database connection
Visit: `admin/api/verify-realtime-data.php`

### Categories not showing?

**Check 1:** Verify categories exist in database
```sql
SELECT * FROM categories;
```

**Check 2:** Check console for errors
Open browser DevTools (F12) → Console tab

**Check 3:** Verify session is active
Make sure you're logged in as admin

## Related Files

- **Frontend:** `admin/category.php` (lines 307-384)
- **API Endpoint:** `admin/api/get-categories.php`
- **Database Config:** `admin/config/db.php`
- **Database Schema:** `setup/database/database_schema.sql`
- **Verification Tool:** `admin/api/verify-realtime-data.php`

## Summary

✅ **Real-time data loading:** Every request fetches fresh data from MySQL  
✅ **No caching:** Multiple layers prevent caching at server and browser level  
✅ **Live product counts:** Calculated directly from products table using SQL subquery  
✅ **Visual feedback:** Pulsing indicator and timestamp show data freshness  
✅ **Manual refresh:** Dedicated refresh button forces data reload  

**The category page truly displays real-time data from your database!** 🎉

