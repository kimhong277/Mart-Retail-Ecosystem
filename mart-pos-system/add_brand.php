<?php
// mart_pos_system/add_brand.php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_brand'])) {

    $conn_pos = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
    if (!$conn_pos) {
        die("Connection failure: " . mysqli_connect_error());
    }
    mysqli_set_charset($conn_pos, "utf8mb4");

    $brand_name = mysqli_real_escape_string($conn_pos, trim($_POST['brand_name']));

    // Insert with status 1 (Active) as defined in your database schema
    $sql = "INSERT INTO brands (brand_name, status) VALUES ('$brand_name', 1)";

    if (mysqli_query($conn_pos, $sql)) {
        header("Location: /mart-retail-ecosystem/mart_pos_system/index.php?page=brands&success=1");
        exit;
    } else {
        echo "Error saving brand: " . mysqli_error($conn_pos);
    }

    mysqli_close($conn_pos);
} else {
    header("Location: /mart-retail-ecosystem/mart_pos_system/index.php?page=brands");
    exit;
}
