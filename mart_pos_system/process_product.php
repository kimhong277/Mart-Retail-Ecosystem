<?php
// process_product.php
require_once 'db.php';

$allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$upload_folder = "uploads/";

// Make sure target upload architecture exists on disk
if (!is_dir($upload_folder)) {
    mkdir($upload_folder, 0755, true);
}

// 1. CREATE PRODUCT OPERATION
if (isset($_POST['add_product'])) {
    $barcode      = mysqli_real_escape_string($conn, trim($_POST['barcode']));
    $product_name = mysqli_real_escape_string($conn, trim($_POST['product_name']));
    $price        = floatval($_POST['price']);
    $image        = ""; // Default clean file empty reference state

    // Modernized fast type casting constructs
    $quantity     = (int)$_POST['quantity'];
    $category_id  = (int)$_POST['category_id'];
    $brand_id     = (int)$_POST['brand_id'];

    // Bulletproof Foreign Key safety guards
    $category_val = ($category_id === 0) ? "NULL" : $category_id;
    $brand_val    = ($brand_id === 0) ? "NULL" : $brand_id;

    // Process image file stream if uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['image']['name'];
        $tmp_name  = $_FILES['image']['tmp_name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_extensions)) {
            $new_file_name = time() . '_' . uniqid() . '.' . $file_ext;
            if (move_uploaded_file($tmp_name, $upload_folder . $new_file_name)) {
                $image = $new_file_name;
            }
        } else {
            header("Location: index.php?page=products&status=invalid_image");
            exit();
        }
    }

    // REMOVED quotes from $category_val and $brand_val for native integer/NULL handling
    $sql = "INSERT INTO products (barcode, product_name, category_id, brand_id, quantity, price, image) 
            VALUES ('$barcode', '$product_name', $category_val, $brand_val, $quantity, $price, '$image')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?page=products&status=inserted");
        exit();
    }
    header("Location: index.php?page=products&status=error");
    exit();
}

// 2. UPDATE PRODUCT OPERATION
if (isset($_POST['update_product'])) {
    $barcode      = mysqli_real_escape_string($conn, trim($_POST['barcode']));
    $product_name = mysqli_real_escape_string($conn, trim($_POST['product_name']));
    $price        = floatval($_POST['price']);
    $image        = mysqli_real_escape_string($conn, $_POST['current_image']);

    // Modernized fast type casting constructs
    $id           = (int)$_POST['product_id'];
    $quantity     = (int)$_POST['quantity'];
    $category_id  = (int)$_POST['category_id'];
    $brand_id     = (int)$_POST['brand_id'];

    // Bulletproof Foreign Key safety guards
    $category_val = ($category_id === 0) ? "NULL" : $category_id;
    $brand_val    = ($brand_id === 0) ? "NULL" : $brand_id;

    // Check if user is replacing the current image file stream
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['image']['name'];
        $tmp_name  = $_FILES['image']['tmp_name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_extensions)) {
            $new_file_name = time() . '_' . uniqid() . '.' . $file_ext;
            if (move_uploaded_file($tmp_name, $upload_folder . $new_file_name)) {
                // Delete old image from disk to conserve storage
                if (!empty($image) && file_exists($upload_folder . $image)) {
                    unlink($upload_folder . $image);
                }
                $image = $new_file_name;
            }
        } else {
            header("Location: index.php?page=products&status=invalid_image");
            exit();
        }
    }

    // REMOVED quotes from $category_val and $brand_val for native integer/NULL handling
    $sql = "UPDATE products SET 
                barcode = '$barcode', 
                product_name = '$product_name', 
                category_id = $category_val, 
                brand_id = $brand_val, 
                quantity = $quantity, 
                price = $price,
                image = '$image' 
            WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?page=products&status=updated");
        exit();
    }
    header("Location: index.php?page=products&status=error");
    exit();
}
