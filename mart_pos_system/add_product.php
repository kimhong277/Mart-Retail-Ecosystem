<?php
// mart_pos_system/add_product.php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_product'])) {

    // 1. Database Connection Link
    $conn_pos = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
    if (!$conn_pos) {
        die("Backend connection failed: " . mysqli_connect_error());
    }
    mysqli_set_charset($conn_pos, "utf8mb4");

    // 2. Capture and Escape post payloads securely
    $product_name = mysqli_real_escape_string($conn_pos, trim($_POST['product_name']));
    $barcode      = mysqli_real_escape_string($conn_pos, trim($_POST['barcode']));
    $category_id  = intval($_POST['category_id']);
    $brand_id     = intval($_POST['brand_id']);
    $price        = floatval($_POST['price']);
    $quantity     = intval($_POST['quantity']);

    // 3. Execute procedural item injection query
    $sql = "INSERT INTO products (product_name, barcode, category_id, brand_id, price, quantity, status, image) 
            VALUES ('$product_name', '$barcode', $category_id, $brand_id, $price, $quantity, 1, 'default.png')";

    if (mysqli_query($conn_pos, $sql)) {
        // 🌟 SUCCESS REDIRECT: Loads index framework with the products page active
        header("Location: /mart-retail-ecosystem/mart_pos_system/index.php?page=products&success=1");
        exit;
    } else {
        echo "Error saving product to catalog: " . mysqli_error($conn_pos);
    }

    mysqli_close($conn_pos);
} else {
    // 🌟 FALLBACK SECURITY REDIRECT: Removed success status flag for non-POST visits
    header("Location: /mart-retail-ecosystem/mart_pos_system/index.php?page=products");
    exit;
}
