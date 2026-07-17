<?php
include_once "../../config/db.php";

if (isset($_POST['submit_edit_product'])) {

    // 1. Get hidden product_id and raw text inputs, escaping quotes basics-style
    $product_id     = mysqli_real_escape_string($conn, $_POST['product_id']);
    $category_id    = mysqli_real_escape_string($conn, $_POST['category_id']);
    $product_name   = mysqli_real_escape_string($conn, $_POST['product_name']);
    $price          = mysqli_real_escape_string($conn, $_POST['price']);
    $stock_quantity = mysqli_real_escape_string($conn, $_POST['stock_quantity']);
    $description    = mysqli_real_escape_string($conn, $_POST['description']);

    // 2. Process Image Upload ONLY if the user picked a new file
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../../uploads/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = $_FILES['image']['name'];
        $file_tmp  = $_FILES['image']['tmp_name'];

        $unique_file_name = time() . "_" . $file_name;
        $target_file = $target_dir . $unique_file_name;

        if (move_uploaded_file($file_tmp, $target_file)) {
            $db_image_path = "uploads/" . $unique_file_name;
        } else {
            $db_image_path = "";
        }
    } else {
        // No new file chosen, set it to empty so we know not to overwrite it
        $db_image_path = "";
    }

    // 3. Build the Basic UPDATE query string
    // If a new image path exists, we update the image column too. Otherwise, we leave it out.
    if (!empty($db_image_path)) {
        $db_image_path = mysqli_real_escape_string($conn, $db_image_path);
        $query = "UPDATE `products` SET 
                    `category_id` = '$category_id', 
                    `product_name` = '$product_name', 
                    `price` = '$price', 
                    `stock_quantity` = '$stock_quantity', 
                    `image` = '$db_image_path', 
                    `description` = '$description' 
                  WHERE `product_id` = '$product_id'";
    } else {
        $query = "UPDATE `products` SET 
                    `category_id` = '$category_id', 
                    `product_name` = '$product_name', 
                    `price` = '$price', 
                    `stock_quantity` = '$stock_quantity', 
                    `description` = '$description' 
                  WHERE `product_id` = '$product_id'";
    }

    // Run the query
    mysqli_query($conn, $query);

    // 4. Send user right back to the product catalog
    header("Location: ../../index.php?page=products");
    exit();
} else {
    header("Location: ../../index.php?page=products");
    exit();
}
