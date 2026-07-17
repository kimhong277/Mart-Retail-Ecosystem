<?php
// 1. Include your database connection
// Two levels up (../../) to get out of actions/product/ and find config/
include_once "../../config/db.php";

// 2. Check if an ID was passed in the URL string
if (isset($_GET['id'])) {

    // Clean the ID basics-style to prevent query breaks
    $product_id = mysqli_real_escape_string($conn, $_GET['id']);

    // 3. Run the basic delete query
    $query = "DELETE FROM `products` WHERE `product_id` = '$product_id'";
    mysqli_query($conn, $query);

    // 4. Send the user back to the main layout page seamlessly
    header("Location: ../../index.php?page=products");
    exit();
} else {
    // If someone accesses this file directly without an ID, kick them back safely
    header("Location: ../../index.php?page=products");
    exit();
}
