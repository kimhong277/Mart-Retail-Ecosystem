<?php
// E-Commerce System/config/db_bridge.php

$host = 'localhost';
$db_user = 'root';
$db_pass = ''; // Your local MySQL password

// 1. Connect to the E-Commerce front-end database procedurally
$conn_eco = mysqli_connect($host, $db_user, $db_pass, 'e_commerce_system');
if (!$conn_eco) {
    die("E-Commerce database connection failure: " . mysqli_connect_error());
}
mysqli_set_charset($conn_eco, "utf8mb4");

// 2. Connect to the POS terminal database procedurally
$conn_pos = mysqli_connect($host, $db_user, $db_pass, 'mart_pos_system');
if (!$conn_pos) {
    die("POS database connection failure: " . mysqli_connect_error());
}
mysqli_set_charset($conn_pos, "utf8mb4");
