<?php
// process_quotation.php
require_once 'db.php';

if (isset($_POST['save_quotation'])) {
    $quotation_no  = "QT-" . time();
    $customer_name = mysqli_real_escape_string($conn, trim($_POST['customer_name'] ?: 'Walk-in Customer'));
    $total_amount  = floatval($_POST['total_amount']);
    $valid_until   = date('Y-m-d', strtotime('+14 days')); // Estimates valid for 14 days
    $created_date  = date("Y-m-d H:i:s");

    $product_ids = $_POST['product_ids'] ?? [];

    if (empty($product_ids)) {
        header("Location: index.php?page=quotations&status=empty_cart");
        exit();
    }

    // Insert master record directly into quotations tracking map
    $sql = "INSERT INTO quotations (quotation_no, customer_name, total_amount, valid_until, created_date) 
            VALUES ('$quotation_no', '$customer_name', $total_amount, '$valid_until', '$created_date')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?page=quotations&status=success&qt=" . $quotation_no);
        exit();
    } else {
        header("Location: index.php?page=quotations&status=error");
        exit();
    }
}
