# Mart Online Store - Setup Instructions

## Overview

The Mart Online Store is a fully integrated ecommerce website built into your existing `mart_pos_system` database. It allows customers to browse products publicly and requires login to purchase.

## Key Features

✅ **Public Product Browsing** - Customers can view all products without login  
✅ **Customer Registration** - New customer accounts with secure password hashing  
✅ **Login Required for Checkout** - Checkout is protected and requires authentication  
✅ **Order Management** - Customers can view their order history  
✅ **Customer Profiles** - Manage delivery information and account details  
✅ **Integrated with POS System** - Uses same products, categories, and inventory from mart_pos_system

## Installation Steps

### 1. Database Setup

Run the SQL migration script to create customer tables:

```bash
# In phpMyAdmin or MySQL client, run:
# File: store/db-migration.sql
```

Or execute manually:

```sql
-- Creates online_customers table
-- Adds customer_id column to online_orders
-- Creates online_order_items table
```

### 2. Access the Store

**Public Store (No Login Required)**

```
http://localhost/mart-retail-ecosystem/mart_pos_system/store/index.php
```

**Customer Registration**

```
http://localhost/mart-retail-ecosystem/mart_pos_system/store/customer-register.php
```

**Customer Login**

```
http://localhost/mart-retail-ecosystem/mart_pos_system/store/customer-login.php
```

## File Structure

```
store/
├── index.php                          # Public product catalog
├── cart.php                           # Shopping cart (displays cart, redirects to login for checkout)
├── checkout.php                       # Checkout page (LOGIN REQUIRED)
├── order_success.php                  # Order confirmation page
├── my-orders.php                      # Customer order history (LOGIN REQUIRED)
├── order-details.php                  # Individual order details (LOGIN REQUIRED)
├── customer-profile.php               # Customer profile management (LOGIN REQUIRED)
├── customer-login.php                 # Login page
├── customer-register.php              # Registration page
├── customer-session.php               # Session management and DB connection helper
├── customer-logout.php                # Logout endpoint
├── process-customer-login.php         # Login form processor
├── process-customer-register.php      # Registration form processor
├── place_online_order.php             # Order submission (API endpoint)
├── db-migration.sql                   # Database setup script
├── SETUP.md                           # This file
└── place_onlice_order.php             # Legacy (deprecated)
```

## Database Tables Created

### online_customers

Stores customer account information:

- `id` - Customer ID
- `full_name` - Customer name
- `email` - Unique email address
- `phone` - Contact phone
- `password` - Hashed password (PASSWORD_DEFAULT)
- `created_at` - Account creation timestamp
- `updated_at` - Last update timestamp
- `is_active` - Account status

### online_orders

Modified to include customer reference:

- `customer_id` - Link to online_customers (nullable for backward compatibility)
- `order_number` - Unique order identifier
- `customer_name`, `customer_phone` - Legacy fields (kept for compatibility)
- `shipping_address` - Delivery address
- `total_amount` - Order total
- `order_status` - pending/completed/cancelled
- `created_at` - Order timestamp

### online_order_items

Order line items:

- `id` - Item ID
- `order_id` - Link to online_orders
- `product_id` - Link to products table
- `price` - Price at time of order
- `quantity` - Quantity ordered
- `subtotal` - Line item total

## User Flow

### Anonymous Customer

1. Visits `store/index.php` (public)
2. Browses products and categories
3. Adds items to cart
4. Clicks "Proceed to Checkout"
5. Prompted to login/register
6. Redirected to `customer-login.php`

### New Customer

1. Clicks "Create Account" on login page
2. Registers with: Full Name, Email, Phone, Password
3. Password validated (min 8 chars, uppercase, number)
4. Account auto-created in `online_customers` table
5. Auto-logged in and redirected to store

### Returning Customer

1. Visits `customer-login.php`
2. Enters email and password
3. Redirected to `checkout.php` (or store if just browsing)
4. Can proceed with order or access profile

## Key Functions

### customer-session.php

```php
isCustomerLoggedIn()          // Check if customer is authenticated
getCurrentCustomer()          // Get current customer data
logoutCustomer()             // Clear session and destroy
getStoreConnection()         // Get database connection to mart_pos_system
```

## Security Features

✅ **Password Hashing** - Uses PHP's `PASSWORD_DEFAULT` (bcrypt)  
✅ **SQL Prepared Statements** - Prevents SQL injection  
✅ **Session Protection** - Session-based authentication  
✅ **Input Validation** - Email format, password strength, required fields  
✅ **Database Foreign Keys** - Maintains referential integrity  
✅ **Logout on Close** - Session destroyed on logout

## Customization

### Change Store URLs

Modify the navbar links in `index.php`, `cart.php`, `checkout.php`:

```php
<a href="index.php">Home</a>  // Change paths here
```

### Modify Product Display

Edit the product filtering in `index.php`:

```javascript
filterCatalog(); // Modify this function for custom filtering
```

### Customize Prices

Products use `sale_price` from the products table. Edit in POS system's manage products page.

### Email Notifications (Optional)

To add order confirmation emails, modify `place_online_order.php`:

```php
// Add before mysqli_commit($conn):
mail($customer_email, 'Order Confirmation', $email_body);
```

## Common Issues

### "Database connection error"

- Verify XAMPP MySQL is running
- Check `localhost`, `root`, `''` credentials in `customer-session.php`

### Products not showing

- Check products exist in `mart_pos_system.products` table
- Verify `quantity > 0` for display
- Check category names are set correctly

### Login not working

- Clear browser cookies/cache
- Verify `online_customers` table exists (run db-migration.sql)
- Check email exists in database

### Cart not persisting

- Store uses `localStorage` (browser storage)
- Cart persists across sessions within same browser
- Clearing browser data will clear cart

## Testing Checklist

- [ ] Run db-migration.sql successfully
- [ ] Can view public store at `store/index.php`
- [ ] Can register new customer account
- [ ] Can login with customer credentials
- [ ] Can add products to cart
- [ ] Checkout redirects to login if not authenticated
- [ ] Can place order after login
- [ ] Order appears in "My Orders"
- [ ] Can view order details
- [ ] Can update profile information
- [ ] Logout works and clears session
- [ ] Inventory decrements after purchase

## Support

For issues or customization needs, refer to:

- Database schema in `db-migration.sql`
- Function documentation in `customer-session.php`
- Bootstrap 5.3 classes used for styling: https://getbootstrap.com/docs/5.3/

---

**Version**: 1.0  
**Last Updated**: 2026-07-21  
**Database**: mart_pos_system
