# Product CRUD API Documentation

This document describes the complete CRUD (Create, Read, Update, Delete) operations for the Product Management system.

## Overview

The product management system includes:
- Dynamic loading of categories and suppliers from database
- Image upload with automatic renaming based on product ID
- Complete CRUD operations via REST API
- Validation and error handling

## Directory Structure

```
admin/
├── add-product.php          # Product creation form
├── product.php              # Product listing page
└── api/
    ├── add-product.php      # Create product API
    ├── get-products.php     # Read products API
    ├── update-product.php   # Update product API
    ├── delete-product.php   # Delete product API
    ├── get-categories.php   # Fetch categories
    └── get-suppliers.php    # Fetch suppliers

upload/
└── product/                 # Product images directory
    └── .gitignore          # Git ignore for uploaded files
```

## Database Schema

### Products Table
```sql
CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(80) NOT NULL UNIQUE,
    name VARCHAR(200) NOT NULL,
    description TEXT DEFAULT NULL,
    category_id INT UNSIGNED DEFAULT NULL,
    supplier_id INT UNSIGNED DEFAULT NULL,
    price_regular DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    price_selling DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    quantity INT NOT NULL DEFAULT 0,
    image_url VARCHAR(500) DEFAULT NULL,
    status ENUM('active', 'inactive', 'out_of_stock') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT UNSIGNED DEFAULT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);
```

## API Endpoints

### 1. Create Product (Add)

**Endpoint:** `POST /admin/api/add-product.php`

**Request Type:** `multipart/form-data` (for file upload)

**Parameters:**
```javascript
{
  productName: string (required),      // Product name
  productId: string (required),        // Product SKU/ID (must be unique)
  category: integer (required),        // Category ID
  supplier: integer (required),        // Supplier ID
  regularPrice: float (required),      // Regular/MRP price
  sellingPrice: float (required),      // Selling price
  quantity: integer (required),        // Stock quantity
  description: string (optional),      // Product description
  productImage: file (optional)        // Product image (JPG, PNG, GIF, max 2MB)
}
```

**Response (Success):**
```json
{
  "success": true,
  "message": "Product added successfully",
  "product_id": 123,
  "image_path": "upload/product/P001_1234567890.jpg"
}
```

**Response (Error):**
```json
{
  "success": false,
  "error": "Error message here"
}
```

**Features:**
- Validates all required fields
- Checks for duplicate product IDs (SKU)
- Uploads and renames image based on product ID
- Creates upload directory if it doesn't exist
- Validates image type and size
- Stores relative image path in database

---

### 2. Get Products (Read)

**Endpoint:** `GET /admin/api/get-products.php`

**Query Parameters:**
```javascript
{
  search: string (optional),           // Search in name, SKU, or description
  category: integer (optional),        // Filter by category ID
  status: string (optional),           // Filter by status: active, inactive, out_of_stock
  order_by: string (optional),         // Order by field: sku, name, price_regular, price_selling, quantity, created_at
  order_dir: string (optional),        // Order direction: ASC or DESC (default: DESC)
  limit: integer (optional),           // Pagination limit
  offset: integer (optional)           // Pagination offset
}
```

**Example Request:**
```
GET /admin/api/get-products.php?search=wireless&category=1&limit=10&offset=0
```

**Response (Success):**
```json
{
  "success": true,
  "products": [
    {
      "id": 1,
      "sku": "P001",
      "name": "Wireless Headphones",
      "description": "High-quality wireless headphones",
      "category_id": 1,
      "category_name": "Electronics",
      "supplier_id": 1,
      "supplier_name": "TechSource Pvt Ltd",
      "price_regular": "120.00",
      "price_selling": "99.99",
      "quantity": 25,
      "image_url": "upload/product/P001_1234567890.jpg",
      "status": "active",
      "created_at": "2025-11-01 10:30:00",
      "updated_at": "2025-11-01 10:30:00"
    }
    // ... more products
  ],
  "total": 45
}
```

---

### 3. Update Product

**Endpoint:** `POST /admin/api/update-product.php`

**Request Type:** `multipart/form-data` (for optional file upload)

**Parameters:**
```javascript
{
  id: integer (required),              // Product ID to update
  productName: string (required),
  productId: string (required),        // Product SKU
  category: integer (required),
  supplier: integer (required),
  regularPrice: float (required),
  sellingPrice: float (required),
  quantity: integer (required),
  description: string (optional),
  status: string (optional),           // active, inactive, out_of_stock
  productImage: file (optional)        // New image (replaces old one)
}
```

**Response (Success):**
```json
{
  "success": true,
  "message": "Product updated successfully",
  "product_id": 123,
  "image_path": "upload/product/P001_1234567890.jpg"
}
```

**Features:**
- Updates product information
- Optionally updates product image
- Deletes old image when new one is uploaded
- Validates unique SKU (excluding current product)

---

### 4. Delete Product

**Endpoint:** `POST /admin/api/delete-product.php` or `GET /admin/api/delete-product.php`

**Parameters:**
```javascript
{
  id: integer (required)               // Product ID to delete
}
```

**Response (Success):**
```json
{
  "success": true,
  "message": "Product deleted successfully"
}
```

**Features:**
- Deletes product from database
- Automatically deletes associated image file
- Optional: Can be configured to prevent deletion if product has orders

---

### 5. Get Categories

**Endpoint:** `GET /admin/api/get-categories.php`

**Query Parameters:**
```javascript
{
  search: string (optional)            // Search category name or description
}
```

**Response (Success):**
```json
{
  "success": true,
  "categories": [
    {
      "id": 1,
      "name": "Electronics",
      "description": "Electronic devices and gadgets",
      "created_at": "2025-11-01 10:00:00",
      "product_count": 15
    }
    // ... more categories
  ]
}
```

---

### 6. Get Suppliers

**Endpoint:** `GET /admin/api/get-suppliers.php`

**Query Parameters:**
```javascript
{
  search: string (optional)            // Search in company name, contact person, phone, or email
}
```

**Response (Success):**
```json
{
  "success": true,
  "suppliers": [
    {
      "id": 1,
      "company_name": "TechSource Pvt Ltd",
      "contact_person": "Nimal Perera",
      "phone": "0771234567",
      "email": "sales@techsource.lk",
      "created_at": "2025-11-01 09:00:00"
    }
    // ... more suppliers
  ]
}
```

---

## Image Upload Configuration

### Upload Directory
- **Path:** `upload/product/`
- **Permissions:** 0755 (automatically created if not exists)

### Image Requirements
- **Allowed Formats:** JPG, JPEG, PNG, GIF
- **Maximum Size:** 2MB
- **Naming Convention:** `{PRODUCT_SKU}_{TIMESTAMP}.{EXTENSION}`
  - Example: `P001_1730456789.jpg`

### Image Path Storage
- Stored in database as relative path: `upload/product/filename.jpg`
- Accessible via: `/upload/product/filename.jpg`

---

## Security Features

1. **Authentication:** All endpoints require user session (`logged_in` = true)
2. **Input Validation:** All inputs are validated and sanitized
3. **SQL Injection Prevention:** Prepared statements with PDO
4. **File Upload Security:**
   - File type validation (whitelist)
   - File size validation (2MB limit)
   - Secure file naming
5. **Error Logging:** All errors are logged to PHP error log

---

## Usage Example (JavaScript)

### Add Product
```javascript
const formData = new FormData();
formData.append('productName', 'Wireless Mouse');
formData.append('productId', 'P123');
formData.append('category', '1');
formData.append('supplier', '1');
formData.append('regularPrice', '50.00');
formData.append('sellingPrice', '39.99');
formData.append('quantity', '100');
formData.append('description', 'Ergonomic wireless mouse');
formData.append('productImage', imageFile);

const response = await fetch('api/add-product.php', {
  method: 'POST',
  body: formData
});

const data = await response.json();
if (data.success) {
  console.log('Product added!', data.product_id);
}
```

### Get Products
```javascript
const response = await fetch('api/get-products.php?search=wireless&category=1');
const data = await response.json();

if (data.success) {
  data.products.forEach(product => {
    console.log(product.name, product.price_selling);
  });
}
```

---

## Frontend Integration

### Add Product Form (`add-product.php`)
- Dynamically loads categories from database
- Dynamically loads suppliers from database
- Image preview before upload
- Form validation
- AJAX submission
- Success/error handling
- Redirects to product list on success

### Product Listing (`product.php`)
- Can be updated to load products from database
- Supports search and filtering
- Action buttons for edit/delete

---

## Error Handling

All endpoints return consistent error responses:
```json
{
  "success": false,
  "error": "Detailed error message"
}
```

Common errors:
- "Unauthorized" - User not logged in
- "Invalid request method" - Wrong HTTP method used
- "Product ID already exists" - Duplicate SKU
- "Failed to upload image" - File upload error
- "Product not found" - Invalid product ID

---

## Future Enhancements

1. **Bulk Upload:** Import products from CSV/Excel
2. **Multiple Images:** Support for product gallery
3. **Image Optimization:** Automatic resize and compression
4. **Soft Delete:** Archive products instead of permanent deletion
5. **Product Variations:** Size, color, etc.
6. **Stock Alerts:** Low stock notifications
7. **Barcode Generation:** Auto-generate barcodes for products

---

## Testing

### Test Add Product
1. Navigate to `admin/add-product.php`
2. Fill in all required fields
3. Select an image (max 2MB)
4. Click "Save Product"
5. Verify redirect to product list
6. Check image uploaded to `upload/product/` directory

### Test Get Products
1. Open browser console
2. Run: `fetch('admin/api/get-products.php').then(r => r.json()).then(console.log)`
3. Verify products returned with all fields

### Test Update Product
1. Create update form with product ID
2. Submit with modified data
3. Verify changes in database

### Test Delete Product
1. Call delete API with product ID
2. Verify product removed from database
3. Verify image file deleted from disk

---

## Troubleshooting

### Image Upload Issues
1. **Permission Error:** Ensure `upload/product/` has write permissions (0755)
2. **File Too Large:** Check PHP `upload_max_filesize` and `post_max_size` settings
3. **Invalid File Type:** Only JPG, PNG, GIF allowed

### Database Errors
1. **Foreign Key Error:** Ensure categories and suppliers exist
2. **Duplicate SKU:** Product ID must be unique
3. **Connection Error:** Check database configuration in `config/db.php`

---

## Contact & Support

For issues or questions, contact your system administrator.

