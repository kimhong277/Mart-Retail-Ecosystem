<?php

/**
 * Customer Session Check
 * This file initializes customer sessions for the online store
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if customer is logged in
function isCustomerLoggedIn()
{
    return isset($_SESSION['customer_id']) && !empty($_SESSION['customer_id']);
}

// Function to get current customer info
function getCurrentCustomer()
{
    if (!isCustomerLoggedIn()) {
        return null;
    }

    return [
        'id' => $_SESSION['customer_id'],
        'name' => $_SESSION['customer_name'] ?? '',
        'email' => $_SESSION['customer_email'] ?? '',
        'phone' => $_SESSION['customer_phone'] ?? ''
    ];
}

// Function to logout customer
function logoutCustomer()
{
    unset($_SESSION['customer_id']);
    unset($_SESSION['customer_name']);
    unset($_SESSION['customer_email']);
    unset($_SESSION['customer_phone']);
    session_destroy();
}

// Database connection helper
function getStoreConnection()
{
    $host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $conn = mysqli_connect($host, $db_user, $db_pass, 'mart_pos_system');

    if (!$conn) {
        die("Database connection error");
    }

    mysqli_set_charset($conn, "utf8mb4");
    return $conn;
}
