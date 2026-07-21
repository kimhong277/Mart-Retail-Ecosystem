<?php
// mart_pos_system/add_product.php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_product'])) {

    $conn_pos = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
    if (!$conn_pos) {
        die("Backend connection failed: " . mysqli_connect_error());
    }
    mysqli_set_charset($conn_pos, "utf8mb4");

    $product_name = mysqli_real_escape_string($conn_pos, trim($_POST['product_name']));
    $barcode      = mysqli_real_escape_string($conn_pos, trim($_POST['barcode']));
    $category_id  = intval($_POST['category_id']);
    $brand_id     = intval($_POST['brand_id']);
    $price        = floatval($_POST['price']);
    $quantity     = intval($_POST['quantity']);

    // 🌟 IMAGE UPLOAD HANDLING ENGINE
    $image_name = 'default.png'; // Default fallback string

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp    = $_FILES['product_image']['tmp_name'];
        $file_orig   = $_FILES['product_image']['name'];
        $file_ext    = strtolower(pathinfo($file_orig, PATHINFO_EXTENSION));

        // Allowed file types filter
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $allowed_extensions)) {
            // Generate a unique file name to avoid collisions
            $image_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $file_orig);
            $upload_dir = __DIR__ . '/assets/images/';

            // Create folder dynamically if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Move file out of operating system temp space
            if (!move_uploaded_file($file_tmp, $upload_dir . $image_name)) {
                $image_name = 'default.png'; // Fallback if move fails
            }
        }
    }

    // Insert dynamic image name instead of hardcoded default string
    $sql = "INSERT INTO products (product_name, barcode, category_id, brand_id, sale_price, quantity, status, image) 
            VALUES ('$product_name', '$barcode', $category_id, $brand_id, $price, $quantity, 1, '$image_name')";

    if (mysqli_query($conn_pos, $sql)) {
        header("Location: /mart-retail-ecosystem/mart_pos_system/index.php?page=products&success=1");
        exit;
    } else {
        echo "Error saving product to catalog: " . mysqli_error($conn_pos);
    }

    mysqli_close($conn_pos);
} else {
    header("Location: /mart-retail-ecosystem/mart_pos_system/index.php?page=products");
    exit;
}
