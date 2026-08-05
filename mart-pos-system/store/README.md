# 🛍️ Mart Online Store - Quick Start Guide

## What's Been Created

Your online store is now fully integrated with your `mart_pos_system` database. Here's what's included:

### ✨ Features

- **Public Store** - Browse all products without login
- **Customer Registration** - Create accounts with secure passwords
- **Login Required Checkout** - Only logged-in customers can purchase
- **Order Management** - Customers see their order history
- **Profile Management** - Update delivery information
- **Inventory Integration** - Uses same products from your POS system

---

## 🚀 Getting Started (3 Steps)

### Step 1: Run Database Migration

1. Open **phpMyAdmin** → Select **mart_pos_system** database
2. Go to **SQL** tab
3. Copy and paste contents of: `store/db-migration.sql`
4. Click **Go**

✅ This creates:

- `online_customers` table (customer accounts)
- Updates `online_orders` with customer_id
- Creates `online_order_items` table

### Step 2: Access Your Store

Open in browser:

```
http://localhost/mart-retail-ecosystem/mart_pos_system/store/index.php
```

### Step 3: Test the Flow

1. **Browse Store** (no login needed) ✓
2. **Add Items to Cart** ✓
3. **Click Checkout** → Redirected to login ✓
4. **Register New Account** ✓
5. **Login & Complete Order** ✓
6. **View Order History** ✓

---

## 📁 New Store Files

| File                            | Purpose                | Access                   |
| ------------------------------- | ---------------------- | ------------------------ |
| `index.php`                     | Public product catalog | Public                   |
| `cart.php`                      | Shopping cart display  | Public                   |
| `checkout.php`                  | Order placement        | **LOGIN REQUIRED**       |
| `order_success.php`             | Order confirmation     | **LOGIN REQUIRED**       |
| `my-orders.php`                 | Order history          | **LOGIN REQUIRED**       |
| `order-details.php`             | Order details view     | **LOGIN REQUIRED**       |
| `customer-profile.php`          | Account settings       | **LOGIN REQUIRED**       |
| `customer-login.php`            | Login page             | Public                   |
| `customer-register.php`         | Registration page      | Public                   |
| `customer-session.php`          | Authentication helper  | Backend                  |
| `process-customer-login.php`    | Login processor        | Backend                  |
| `process-customer-register.php` | Registration processor | Backend                  |
| `customer-logout.php`           | Logout handler         | Backend                  |
| `place_online_order.php`        | Order API              | Backend (LOGIN REQUIRED) |

---

## 🔗 Important URLs

| Page              | URL                                              |
| ----------------- | ------------------------------------------------ |
| **Public Store**  | `/store/index.php`                               |
| **Register**      | `/store/customer-register.php`                   |
| **Login**         | `/store/customer-login.php`                      |
| **Shopping Cart** | `/store/cart.php`                                |
| **Checkout**      | `/store/checkout.php` _(Login required)_         |
| **My Orders**     | `/store/my-orders.php` _(Login required)_        |
| **Profile**       | `/store/customer-profile.php` _(Login required)_ |

---

## 👤 Test Customer (Create One)

1. Go to `/store/customer-register.php`
2. Fill in:
   - **Full Name:** John Doe
   - **Email:** john@example.com
   - **Phone:** 012 345 678
   - **Password:** Test123 (must have uppercase & number)
3. Click "Create Account"
4. Auto-logged in to store

---

## 🔐 How It Works

### Public Browsing (No Login)

```
Visitor → store/index.php → Browse Products → Add to Cart
```

### Making a Purchase (Login Required)

```
Visitor → Click Checkout → Redirected to Login →
Register/Login → Proceed to Checkout →
Enter Address → Place Order → Order Saved ✓
```

### Customer Dashboard

```
Logged-in User → Profile Dropdown →
  - View Orders
  - Edit Profile
  - Logout
```

---

## 💾 Database Integration

All data is stored in your existing `mart_pos_system` database:

**New Tables:**

- `online_customers` - Customer accounts
- `online_order_items` - Individual line items

**Modified Tables:**

- `online_orders` - Now includes `customer_id` (foreign key)

**Unchanged Tables:**

- `products` - Your existing inventory
- `categories` - Your existing categories
- `brands` - Your existing brands

✅ **Same inventory system** - When someone orders, stock decrements automatically

---

## 🎨 Customization

### Change Store Name

Edit `index.php`, `cart.php`, etc:

```html
<a class="navbar-brand" href="index.php">
  <i class="bi bi-shop me-2"></i>YOUR STORE NAME
</a>
```

### Change Colors

Bootstrap classes used: `btn-primary`, `bg-dark`, `text-primary`
Edit Bootstrap color variables or modify class names.

### Add Custom Styling

Add CSS to any page's `<style>` section:

```css
.custom-class {
  color: your-color;
}
```

---

## ⚠️ Common Issues & Fixes

| Issue                        | Solution                                  |
| ---------------------------- | ----------------------------------------- |
| **Products not showing**     | Check `quantity > 0` in products table    |
| **Login fails**              | Run db-migration.sql, verify email exists |
| **Cart empty after refresh** | Normal - uses browser localStorage        |
| **Cannot checkout**          | Must be logged in first                   |
| **Prices not correct**       | Verify `sale_price` in products table     |

---

## 🧪 Testing Checklist

Complete checklist to verify everything works:

- [ ] Store index page loads (no login required)
- [ ] Products display with correct prices
- [ ] Category filter works
- [ ] Search functionality works
- [ ] Can add items to cart
- [ ] Cart shows correct items and totals
- [ ] "Checkout" button redirects to login if not authenticated
- [ ] Can register new customer account
- [ ] Password requirements enforced (uppercase, number, 8+ chars)
- [ ] Can login with registered account
- [ ] Checkout page loads after login
- [ ] Can enter delivery address
- [ ] Order placed successfully
- [ ] Redirected to success page with order number
- [ ] Order appears in "My Orders"
- [ ] Can view order details
- [ ] Can update profile information
- [ ] Can logout successfully
- [ ] Product inventory decreased after purchase

---

## 📊 Admin Side (POS System)

Nothing changed in your main POS system! You can still:

- ✓ Add/Edit/Delete Products
- ✓ Manage Inventory
- ✓ Manage Categories
- ✓ View Orders (in `online_orders` table)
- ✓ View Customers (in `online_customers` table)

---

## 🔒 Security

Password security:

- ✓ Minimum 8 characters
- ✓ Must contain uppercase letter
- ✓ Must contain number
- ✓ Hashed with bcrypt (PASSWORD_DEFAULT)
- ✓ Never stored in plain text

Authentication:

- ✓ Session-based login
- ✓ SQL injection protection (prepared statements)
- ✓ CSRF protection via POST
- ✓ Logout clears all session data

---

## 📞 Support

### Database Connection Issue?

Check in `customer-session.php`:

```php
$host = 'localhost';
$db_user = 'root';
$db_pass = '';  // Your password (usually empty in XAMPP)
```

### Need to Add Email Notifications?

Modify `place_online_order.php` to add:

```php
mail($customer['email'], 'Order Confirmation', $message);
```

### Want to Change Redirect URLs?

Search for `header("Location:` in files to find redirects.

---

## 📝 File Descriptions

**Customer-Facing Pages:**

- `index.php` - Main product catalog
- `cart.php` - Shopping cart
- `checkout.php` - Order placement (requires login)
- `order_success.php` - Order confirmation page
- `my-orders.php` - Customer's order history
- `order-details.php` - Single order details
- `customer-profile.php` - Edit account info
- `customer-login.php` - Login form
- `customer-register.php` - Registration form

**Backend Files:**

- `customer-session.php` - Session + DB connection helper
- `process-customer-login.php` - Handles login form
- `process-customer-register.php` - Handles registration
- `customer-logout.php` - Clears session
- `place_online_order.php` - Processes order (API endpoint)

---

**Your ecommerce store is ready! 🎉**

Next: Run db-migration.sql and visit `/store/index.php`
