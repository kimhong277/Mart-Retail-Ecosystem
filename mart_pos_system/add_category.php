<?php
// mart_pos_system/add_category.php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_category'])) {

    $conn_pos = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
    if (!$conn_pos) {
        die("Connection failure: " . mysqli_connect_error());
    }
    mysqli_set_charset($conn_pos, "utf8mb4");

    $category_name = mysqli_real_escape_string($conn_pos, trim($_POST['category_name']));

    // Insert with status 1 (Active) as defined in your database schema
    $sql = "INSERT INTO categories (category_name, status) VALUES ('$category_name', 1)";

    if (mysqli_query($conn_pos, $sql)) {
        header("Location: /mart-retail-ecosystem/mart_pos_system/index.php?page=categories&success=1");
        exit;
    } else {
        echo "Error saving classification: " . mysqli_error($conn_pos);
    }

    mysqli_close($conn_pos);
} else {
    header("Location: /mart-retail-ecosystem/mart_pos_system/index.php?page=categories");
    exit;
}
