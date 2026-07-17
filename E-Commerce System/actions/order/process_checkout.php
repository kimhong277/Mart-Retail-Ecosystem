<?php
session_start();
require_once '../../config/db.php';

// Security Guard Check: Must be logged in and have items in the cart to check out
if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$cart_items = $_SESSION['cart'];

// 1. Fetch item entries from database to verify prices and compute total aggregate cost
$ids = implode(',', array_keys($cart_items));
$query = "SELECT product_id, price, stock_quantity FROM products WHERE product_id IN ($ids)";
$result = mysqli_query($conn, $query);

$total_order_amount = 0.00;
$products_data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $qty = $cart_items[$row['product_id']];

    // Fail early if customer attempts to buy more than what is sitting in our inventory warehouse
    if ($row['stock_quantity'] < $qty) {
        die("Error: One of your selected items has sold out or has insufficient stock levels.");
    }

    $total_order_amount += ($row['price'] * $qty);
    $products_data[] = $row;
}

// 2. MOCK PAYMENT GATEWAY INTEGRATION SIMULATION
// In a production store, you'd call a banking API here. For school, we simulate a clean authorization match.
$payment_successful = true;

if ($payment_successful) {
    // Set status straight to 'Paid' because the payment cleared successfully
    $order_status = 'Paid';

    // 3. INSERT INTO MASTER 'orders' TABLE
    $order_query = "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)";
    $order_stmt = mysqli_prepare($conn, $order_query);
    mysqli_stmt_bind_param($order_stmt, "ids", $user_id, $total_order_amount, $order_status);

    if (mysqli_stmt_execute($order_stmt)) {
        $new_order_id = mysqli_insert_id($conn);
        mysqli_stmt_close($order_stmt);

        // 4. LOOP ITEMS AND FILL THE JUNCTION AND STOCK REBALANCING CHECKS
        foreach ($products_data as $product) {
            $p_id = (int)$product['product_id'];
            $qty  = (int)$cart_items[$p_id];
            $price_snapshot = (float)$product['price'];

            // Save row record itemization history
            $detail_query = "INSERT INTO order_details (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)";
            $detail_stmt = mysqli_prepare($conn, $detail_query);
            mysqli_stmt_bind_param($detail_stmt, "iiid", $new_order_id, $p_id, $qty, $price_snapshot);
            mysqli_stmt_execute($detail_stmt);
            mysqli_stmt_close($detail_stmt);

            // Rebalance stock inventory levels down automatically
            $update_stock = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
            $stock_stmt = mysqli_prepare($conn, $update_stock);
            mysqli_stmt_bind_param($stock_stmt, "ii", $qty, $p_id);
            mysqli_stmt_execute($stock_stmt);
            mysqli_stmt_close($stock_stmt);
        }

        // 5. CLEAR SHOPPING CART MEMORY
        unset($_SESSION['cart']);

        // Success! Send them to their tracking account dashboard tab view
        header("Location: ../../customer-orders.php?checkout=success");
        exit();
    } else {
        die("Checkout database execution failure: " . mysqli_error($conn));
    }
} else {
    die("Payment Processor Declined Transaction.");
}

mysqli_close($conn);
