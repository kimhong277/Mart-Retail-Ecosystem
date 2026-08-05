<?php
// store/place_online_order.php
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once 'customer-session.php';

if (!isCustomerLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Please login to place order']);
    exit;
}

$customer = getCurrentCustomer();

// Resolve Database Connection
if (function_exists('getStoreConnection')) {
    $conn = getStoreConnection();
} else {
    $conn = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
}

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}
mysqli_set_charset($conn, "utf8mb4");

// Parse Input
$raw_input = file_get_contents('php://input');
$input = json_decode($raw_input, true);

if (!$input || empty($input['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid order data or empty cart.']);
    exit;
}

$cust_name    = mysqli_real_escape_string($conn, trim($input['customer_name']));
$cust_phone   = mysqli_real_escape_string($conn, trim($input['customer_phone']));
$cust_address = mysqli_real_escape_string($conn, trim($input['shipping_address']));
$cart         = $input['cart'];

// Calculate total
$total_amount = 0;
foreach ($cart as $item) {
    $total_amount += ((float)$item['price'] * (int)$item['qty']);
}

// Generate Invoice Number matching your format (INV-...)
$invoice_no = 'INV-' . time() . rand(10, 99);
$cust_id    = isset($customer['id']) ? (int)$customer['id'] : 0;

mysqli_begin_transaction($conn);

try {
    // 1. Save to `online_orders` table (For delivery processing)
    $stmt_online = mysqli_prepare($conn, "INSERT INTO online_orders (customer_id, order_number, customer_name, customer_phone, shipping_address, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, 'Completed')");
    if ($stmt_online) {
        mysqli_stmt_bind_param($stmt_online, "issssd", $cust_id, $invoice_no, $cust_name, $cust_phone, $cust_address, $total_amount);
        mysqli_stmt_execute($stmt_online);
        $online_order_id = mysqli_insert_id($conn);
    }

    // 2. ⚡ INSERT DIRECTLY INTO POS SALES TABLE
    // Matches your exact columns: invoice_no, total_amount, order_type, sale_date, payment_method, status
    $stmt_pos = mysqli_prepare($conn, "INSERT INTO sales (invoice_no, total_amount, order_type, sale_date, payment_method, status) VALUES (?, ?, 'Online', NOW(), 'Online', 'Completed')");
    if (!$stmt_pos) {
        throw new Exception("POS Sales Insert Failed: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt_pos, "sd", $invoice_no, $total_amount);
    mysqli_stmt_execute($stmt_pos);
    $pos_sale_id = mysqli_insert_id($conn);

    // 3. Insert Items & Update Inventory
    $stmt_online_item = mysqli_prepare($conn, "INSERT INTO online_order_items (order_id, product_id, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt_pos_item    = mysqli_prepare($conn, "INSERT INTO sale_details (sale_id, product_id, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt_stock       = mysqli_prepare($conn, "UPDATE products SET quantity = quantity - ? WHERE id = ? AND quantity >= ?");

    foreach ($cart as $item) {
        $prod_id  = (int)$item['id'];
        $qty      = (int)$item['qty'];
        $price    = (float)$item['price'];
        $subtotal = $price * $qty;

        // Save line item to online order table
        if ($online_order_id && $stmt_online_item) {
            mysqli_stmt_bind_param($stmt_online_item, "iidid", $online_order_id, $prod_id, $price, $qty, $subtotal);
            mysqli_stmt_execute($stmt_online_item);
        }

        // Save line item to POS sale_items table
        if ($pos_sale_id && $stmt_pos_item) {
            mysqli_stmt_bind_param($stmt_pos_item, "iidid", $pos_sale_id, $prod_id, $price, $qty, $subtotal);
            mysqli_stmt_execute($stmt_pos_item);
        }

        // Decrement product inventory
        if ($stmt_stock) {
            mysqli_stmt_bind_param($stmt_stock, "iii", $qty, $prod_id, $qty);
            mysqli_stmt_execute($stmt_stock);

            if (mysqli_stmt_affected_rows($stmt_stock) === 0) {
                throw new Exception("Insufficient stock for product ID {$prod_id}.");
            }
        }
    }

    mysqli_commit($conn);
    echo json_encode(['success' => true, 'order_number' => $invoice_no]);
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
