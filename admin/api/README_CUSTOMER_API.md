# Customer API Documentation

This document describes the Customer CRUD API endpoints and their usage.

## Overview

The Customer API provides complete CRUD (Create, Read, Update, Delete) operations for managing customers in the Daily Collection Manager system.

## API Endpoints

### 1. Get Customers (List)

**Endpoint:** `GET api/get-customers.php`

**Description:** Fetches a list of customers with optional filtering and pagination.

**Parameters:**
- `search` (optional): Search by customer name, code, mobile, or email
- `status` (optional): Filter by status (`active`, `inactive`, `blocked`)
- `order_by` (optional): Sort by field (default: `created_at`)
  - Allowed values: `customer_code`, `full_name`, `email`, `mobile`, `created_at`, `remaining_balance`
- `order_dir` (optional): Sort direction (`ASC`, `DESC`, default: `DESC`)
- `limit` (optional): Number of records per page
- `offset` (optional): Pagination offset

**Response:**
```json
{
  "success": true,
  "customers": [
    {
      "id": 1,
      "customer_code": "C001",
      "full_name": "John Doe",
      "email": "john@example.com",
      "mobile": "+94771234567",
      "address": "123 Main St",
      "status": "active",
      "total_purchased": 5000.00,
      "total_paid": 3000.00,
      "remaining_balance": 2000.00,
      "created_at": "2024-01-15 10:30:00",
      "updated_at": "2024-01-20 14:45:00"
    }
  ],
  "total": 1
}
```

### 2. Get Customer Detail

**Endpoint:** `GET api/get-customer-detail.php`

**Description:** Fetches detailed information for a single customer.

**Parameters:**
- `id` (required): Customer ID

**Response:**
```json
{
  "success": true,
  "customer": {
    "id": 1,
    "customer_code": "C001",
    "full_name": "John Doe",
    "email": "john@example.com",
    "mobile": "+94771234567",
    "address": "123 Main St",
    "status": "active",
    "total_purchased": 5000.00,
    "total_paid": 3000.00,
    "remaining_balance": 2000.00,
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-20 14:45:00"
  }
}
```

### 3. Add Customer

**Endpoint:** `POST api/add-customer.php`

**Description:** Creates a new customer. Customer code is auto-generated.

**Request Body (JSON):**
```json
{
  "full_name": "John Doe",
  "email": "john@example.com",
  "mobile": "+94771234567",
  "address": "123 Main St",
  "status": "active"
}
```

**Required Fields:**
- `full_name`: Customer's full name
- `mobile`: Customer's mobile number (must be unique)

**Optional Fields:**
- `email`: Customer's email address
- `address`: Customer's address
- `status`: Customer status (`active`, `inactive`, `blocked`, default: `active`)

**Response:**
```json
{
  "success": true,
  "message": "Customer added successfully",
  "customer_id": 1,
  "customer_code": "C001"
}
```

**Error Response:**
```json
{
  "success": false,
  "error": "A customer with this mobile number already exists"
}
```

### 4. Update Customer

**Endpoint:** `POST api/update-customer.php`

**Description:** Updates an existing customer's information.

**Request Body (JSON):**
```json
{
  "id": 1,
  "full_name": "John Doe Updated",
  "email": "john.updated@example.com",
  "mobile": "+94771234567",
  "address": "456 New Address",
  "status": "active"
}
```

**Required Fields:**
- `id`: Customer ID
- `full_name`: Customer's full name
- `mobile`: Customer's mobile number

**Optional Fields:**
- `email`: Customer's email address
- `address`: Customer's address
- `status`: Customer status

**Response:**
```json
{
  "success": true,
  "message": "Customer updated successfully"
}
```

### 5. Delete Customer

**Endpoint:** `POST api/delete-customer.php`

**Description:** Deletes a customer. Cannot delete customers with existing orders or payments.

**Request Body (URL-encoded):**
```
id=1
```

**Parameters:**
- `id` (required): Customer ID to delete

**Response:**
```json
{
  "success": true,
  "message": "Customer deleted successfully"
}
```

**Error Response (Customer has orders):**
```json
{
  "success": false,
  "error": "Cannot delete customer with existing orders. Please cancel or complete all orders first."
}
```

**Error Response (Customer has payments):**
```json
{
  "success": false,
  "error": "Cannot delete customer with payment history. Customer has 5 payment record(s)."
}
```

## Database Schema

### customers Table

```sql
CREATE TABLE customers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_code VARCHAR(50) NOT NULL UNIQUE,
    username VARCHAR(100) DEFAULT NULL UNIQUE,
    password_hash VARCHAR(255) DEFAULT NULL,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    mobile VARCHAR(20) NOT NULL,
    address VARCHAR(255) DEFAULT NULL,
    status ENUM('active', 'inactive', 'blocked') NOT NULL DEFAULT 'active',
    total_purchased DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    total_paid DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
```

## Frontend Integration

### JavaScript File

**Location:** `admin/js/customer.js`

**Key Functions:**
- `loadCustomers(searchQuery)`: Loads customers from API
- `displayCustomers(customers)`: Renders customer table
- `viewCustomer(customerId)`: Opens customer detail modal
- `editCustomer(customerId)`: Opens edit modal with customer data
- `deleteCustomer(customerId, customerName)`: Deletes customer with confirmation

### Modals

1. **Add Customer Modal** (`#addCustomerModal`)
   - Form ID: `addCustomerForm`
   - Opens when "Add Customer" button is clicked

2. **Edit Customer Modal** (`#editCustomerModal`)
   - Form ID: `editCustomerForm`
   - Opens when edit button is clicked on a customer row

3. **View Customer Modal** (`#viewCustomerModal`)
   - Read-only view of customer details
   - Shows financial summary (total purchased, paid, remaining balance)
   - Opens when view button is clicked on a customer row

## Usage Examples

### Search Customers
```javascript
// Search by name, mobile, or email
const searchQuery = 'John';
loadCustomers(searchQuery);
```

### Add New Customer
```javascript
const formData = {
  full_name: 'John Doe',
  email: 'john@example.com',
  mobile: '+94771234567',
  address: '123 Main St',
  status: 'active'
};

const response = await fetch('api/add-customer.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify(formData)
});
```

### Update Customer
```javascript
const formData = {
  id: 1,
  full_name: 'John Doe Updated',
  email: 'john@example.com',
  mobile: '+94771234567',
  address: '456 New St',
  status: 'active'
};

const response = await fetch('api/update-customer.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify(formData)
});
```

### Delete Customer
```javascript
const response = await fetch('api/delete-customer.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
  body: `id=${customerId}`
});
```

## Security

- All endpoints require user authentication (session-based)
- SQL injection protection via prepared statements
- XSS protection via output escaping in HTML
- Input validation on both client and server side
- Prevents deletion of customers with related orders/payments

## Error Handling

All API endpoints return consistent error responses:

```json
{
  "success": false,
  "error": "Error message here"
}
```

Common HTTP status codes:
- `200`: Success
- `400`: Bad request (validation error)
- `401`: Unauthorized (not logged in)
- `500`: Internal server error

## Notes

1. **Auto-Generated Customer Code**: Customer codes are automatically generated in the format `C001`, `C002`, etc.
2. **Remaining Balance Calculation**: Automatically calculated as `total_purchased - total_paid`
3. **Cascading Protection**: Customers with orders or payments cannot be deleted for data integrity
4. **Mobile Uniqueness**: Mobile numbers must be unique across all customers
5. **Status Options**: `active`, `inactive`, `blocked`

## Related Files

- `admin/customer.php` - Main customer management page
- `admin/js/customer.js` - Frontend JavaScript logic
- `admin/api/get-customers.php` - List customers API
- `admin/api/get-customer-detail.php` - Get single customer API
- `admin/api/add-customer.php` - Add customer API
- `admin/api/update-customer.php` - Update customer API
- `admin/api/delete-customer.php` - Delete customer API
- `admin/assets/js/notification-dialog.js` - Success/error notifications
- `admin/assets/js/confirmation-dialog.js` - Confirmation dialogs

## Future Enhancements

1. Export customers to CSV/Excel
2. Bulk customer import
3. Customer groups/categories
4. Advanced filtering (by date range, balance range)
5. Customer activity history
6. Email/SMS notifications for customer-related events

