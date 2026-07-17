<?php
session_start();
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_manual_order'])) {

    $user_id    = (int)$_POST['user_id'];
    $product_id = (int)$_POST['product_id'];
    $quantity   = (int)$_POST['quantity'];
    $status     = $_POST['status'];

    // 1. Fetch item price from product table to calculate aggregate transaction amounts
    $price_query = "SELECT price, stock_quantity FROM products WHERE product_id = ?";
    $price_stmt  = mysqli_prepare($conn, $price_query);

    mysqli_stmt_bind_param($price_stmt, "i", $product_id);
    mysqli_stmt_execute($price_stmt);
    $price_result = mysqli_stmt_get_result($price_stmt);
    $product_data = mysqli_fetch_assoc($price_result);
    mysqli_stmt_close($price_stmt);

    if (!$product_data || $product_data['stock_quantity'] < $quantity) {
        die("Error: Insufficient stock level or item not found.");
    }

    $unit_price   = (float)$product_data['price'];
    $total_amount = $unit_price * $quantity;

    // 2. INSERT INTO 'orders' TABLE
    $order_query = "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)";
    $order_stmt  = mysqli_prepare($conn, $order_query);

    mysqli_stmt_bind_param($order_stmt, "ids", $user_id, $total_amount, $status);

    if (mysqli_stmt_execute($order_stmt)) {
        // Capture the brand new order identity key MySQL auto-generated
        $new_order_id = mysqli_insert_id($conn);
        mysqli_stmt_close($order_stmt);

        // 3. INSERT INTO 'order_details' HISTORICAL SNAPSHOT JUNCTION TABLE
        $details_query = "INSERT INTO order_details (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)";
        $details_stmt  = mysqli_prepare($conn, $details_query);

        mysqli_stmt_bind_param($details_stmt, "iiid", $new_order_id, $product_id, $quantity, $unit_price);
        mysqli_stmt_execute($details_stmt);
        mysqli_stmt_close($details_stmt);

        // 4. DECREMENT STOCK LEVEL IN PRODUCTS CATALOG
        $stock_update = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
        $stock_stmt   = mysqli_prepare($conn, $stock_update);
        mysqli_stmt_bind_param($stock_stmt, "ii", $quantity, $product_id);
        mysqli_stmt_execute($stock_stmt);
        mysqli_stmt_close($stock_stmt);

        // Everything succeeded! Return home cleanly
        header("Location: ../../index.php?page=orders&status=order_created");
        exit();
    } else {
        die("Transaction generation fault: " . mysqli_error($conn));
    }
}
mysqli_close($conn);
