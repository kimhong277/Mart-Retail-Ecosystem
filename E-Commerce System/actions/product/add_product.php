<?php
include_once "../../config/db.php";

if (isset($_POST['submit_add_product'])) {

    // 1. Get inputs AND use escape string so apostrophes (like Lay's) don't break the query
    $category_id    = mysqli_real_escape_string($conn, $_POST['category_id']);
    $product_name   = mysqli_real_escape_string($conn, $_POST['product_name']);
    $price          = mysqli_real_escape_string($conn, $_POST['price']);
    $stock_quantity = mysqli_real_escape_string($conn, $_POST['stock_quantity']);
    $description    = mysqli_real_escape_string($conn, $_POST['description']);

    // 2. Handle the File Upload
    $target_dir = "../../uploads/"; // Path to your uploads folder

    // Create the folder automatically if it doesn't exist yet
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Get file info from $_FILES array
    $file_name = $_FILES['image']['name'];
    $file_tmp  = $_FILES['image']['tmp_name'];

    $unique_file_name = time() . "_" . $file_name;
    $target_file = $target_dir . $unique_file_name;

    if (move_uploaded_file($file_tmp, $target_file)) {
        $db_image_path = "uploads/" . $unique_file_name;
    } else {
        $db_image_path = "https://via.placeholder.com/300x200?text=No+Image";
    }

    // Escape image path just in case file names have symbols
    $db_image_path = mysqli_real_escape_string($conn, $db_image_path);

    // 3. FIXED: Included category_id inside columns and VALUES
    $query = "INSERT INTO `products` (category_id, product_name, price, stock_quantity, image, description) 
              VALUES ('$category_id', '$product_name', '$price', '$stock_quantity', '$db_image_path', '$description')";

    mysqli_query($conn, $query);

    header("Location: ../../index.php?page=products");
    exit();
} else {
    header("Location: ../../index.php?page=products");
    exit();
}
