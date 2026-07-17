<?php
session_start();

if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];

    // Initialize the cart session array if it doesn't exist yet
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // If the accessory is already in the cart, increment the count; otherwise, set it to 1
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }

    // Send them right back to the shopping catalog with a success message
    header("Location: ../../index.php?page=catalog&status=added_to_cart");
    exit();
}
