# Product CRUD - Quick Reference Guide

## 🚀 Quick Start

### Add a New Product
1. Go to: `admin/add-product.php`
2. Fill required fields (marked with *)
3. Upload image (optional, max 2MB)
4. Click "Save Product"

### View All Products
1. Go to: `admin/product.php`
2. Products load automatically
3. Use search/filter to find specific products

---

## 📁 Directory Structure

```
admin/
├── add-product.php          # Add product form
├── product.php              # Product listing
├── js/
│   └── product.js          # Product listing logic
└── api/
    ├── add-product.php      # CREATE endpoint
    ├── get-products.php     # READ endpoint
    ├── update-product.php   # UPDATE endpoint
    ├── delete-product.php   # DELETE endpoint
    ├── get-categories.php   # Get categories
    └── get-suppliers.php    # Get suppliers

upload/
└── product/                 # Product images
    └── .gitignore
```

---

## 🔗 API Endpoints

| Endpoint | Method | Purpose | Parameters |
|----------|--------|---------|------------|
| `api/add-product.php` | POST | Add new product | productName, productId, category, supplier, regularPrice, sellingPrice, quantity, description, productImage |
| `api/get-products.php` | GET | Get all products | search, category, status, order_by, order_dir, limit, offset |
| `api/update-product.php` | POST | Update product | id, productName, productId, category, supplier, regularPrice, sellingPrice, quantity, description, status, productImage |
| `api/delete-product.php` | POST/GET | Delete product | id |
| `api/get-categories.php` | GET | Get categories | search |
| `api/get-suppliers.php` | GET | Get suppliers | search |

---

## 🖼️ Image Upload

### Requirements:
- **Formats:** JPG, PNG, GIF
- **Max Size:** 2MB
- **Location:** `upload/product/`
- **Naming:** `{PRODUCT_SKU}_{TIMESTAMP}.{EXT}`
  - Example: `P001_1730456789.jpg`

### Image Path in Database:
```
upload/product/P001_1730456789.jpg
```

---

## 📋 Form Fields

### Required Fields (*)
- Product Name
- Product ID (SKU) - must be unique
- Category (dropdown from database)
- Supplier (dropdown from database)
- Regular Price
- Selling Price
- Quantity

### Optional Fields
- Description
- Product Image

---

## 💡 Features

✅ Dynamic category loading from database
✅ Dynamic supplier loading from database
✅ Image upload with auto-rename
✅ Image preview before upload
✅ Search products by name or SKU
✅ Filter by category
✅ Delete with confirmation
✅ Automatic image cleanup on delete
✅ Form validation
✅ Error handling
✅ Success/error messages

---

## 🔍 Search & Filter

### Search Products:
- Enter product name or SKU in search box
- Click "Search" or press Enter
- Results update automatically

### Filter by Category:
- Select category from dropdown
- Results update automatically
- Select "All Categories" to clear filter

---

## 🛠️ Common Tasks

### Add Product with Image:
```javascript
1. Open add-product.php
2. Fill all required fields
3. Click "Upload Image"
4. Select image file (max 2MB)
5. Preview shows immediately
6. Click "Save Product"
7. Success → redirects to product list
```

### Delete Product:
```javascript
1. Open product.php
2. Find product in list
3. Click delete button (trash icon)
4. Confirm deletion
5. Product and image deleted
6. List updates automatically
```

### Search Products:
```javascript
1. Open product.php
2. Type in search box
3. Press Enter or click Search
4. Results show matching products
```

---

## 🔑 Database Schema

### Products Table Fields:
```sql
id              - Auto increment primary key
sku             - Product SKU/ID (unique)
name            - Product name
description     - Product description
category_id     - Foreign key to categories
supplier_id     - Foreign key to suppliers
price_regular   - Regular/MRP price
price_selling   - Selling price
quantity        - Stock quantity
image_url       - Image path
status          - active, inactive, out_of_stock
created_at      - Creation timestamp
updated_at      - Last update timestamp
created_by      - User who created
```

---

## ⚠️ Important Notes

1. **Product ID (SKU) must be unique**
   - System checks for duplicates
   - Error shown if duplicate found

2. **Image file size limit: 2MB**
   - Validation on client and server
   - Error shown if exceeded

3. **Categories and Suppliers**
   - Must exist in database
   - Loaded dynamically on page load

4. **Image Upload Directory**
   - Must have write permissions (755)
   - Created automatically if not exists

---

## 🐛 Quick Troubleshooting

| Problem | Solution |
|---------|----------|
| Images not uploading | Check `upload/product/` permissions (755) |
| Categories not loading | Verify database connection, check console for errors |
| Duplicate SKU error | Use different Product ID, SKU must be unique |
| File too large error | Reduce image size to under 2MB |
| Products not showing | Check browser console, verify database has data |

---

## 📞 Files to Check

### Frontend:
- `admin/add-product.php` - Add product form
- `admin/product.php` - Product listing
- `admin/js/product.js` - Product listing JavaScript

### Backend:
- `admin/api/add-product.php` - Create API
- `admin/api/get-products.php` - Read API
- `admin/api/update-product.php` - Update API
- `admin/api/delete-product.php` - Delete API

### Configuration:
- `admin/config/db.php` - Database connection

---

## 🎯 Testing URLs

```
Add Product:     http://localhost/Daily-Collection-Manager/admin/add-product.php
View Products:   http://localhost/Daily-Collection-Manager/admin/product.php
Get Categories:  http://localhost/Daily-Collection-Manager/admin/api/get-categories.php
Get Suppliers:   http://localhost/Daily-Collection-Manager/admin/api/get-suppliers.php
Get Products:    http://localhost/Daily-Collection-Manager/admin/api/get-products.php
```

---

## ✅ Implementation Status

| Feature | Status |
|---------|--------|
| Add Product Form | ✅ Complete |
| Product Listing | ✅ Complete |
| Create API | ✅ Complete |
| Read API | ✅ Complete |
| Update API | ✅ Complete |
| Delete API | ✅ Complete |
| Image Upload | ✅ Complete |
| Dynamic Categories | ✅ Complete |
| Dynamic Suppliers | ✅ Complete |
| Search Function | ✅ Complete |
| Filter Function | ✅ Complete |
| Delete Function | ✅ Complete |
| Documentation | ✅ Complete |

---

## 📚 Full Documentation

For complete details, see:
- `admin/api/README_PRODUCT_API.md` - Complete API documentation
- `PRODUCT_CRUD_IMPLEMENTATION_SUMMARY.md` - Full implementation summary

---

**Last Updated:** November 1, 2025
**Status:** Production Ready ✅

