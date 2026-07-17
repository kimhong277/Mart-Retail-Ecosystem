<?php
// process_sale.php
require_once 'db.php';

if (isset($_POST['checkout'])) {
    $invoice_no = "INV-" . time();
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $total_amount = floatval($_POST['total_amount']);
    $sale_date = date("Y-m-d H:i:s");

    $product_ids = $_POST['product_ids'] ?? [];
    $quantities  = $_POST['quantities'] ?? [];
    $prices      = $_POST['prices'] ?? [];

    if (empty($product_ids)) {
        header("Location: index.php?page=sales&status=empty_cart");
        exit();
    }

    mysqli_begin_transaction($conn);

    try {
        // 1. Save master invoice entry
        $sale_query = "INSERT INTO sales (invoice_no, total_amount, payment_method, sale_date) 
                       VALUES ('$invoice_no', $total_amount, '$payment_method', '$sale_date')";
        mysqli_query($conn, $sale_query);

        // 2. Clear product itemization allocations
        for ($i = 0; $i < count($product_ids); $i++) {
            $product_id = intval($product_ids[$i]);
            $qty = intval($quantities[$i]);

            // Deduct your inventory level states perfectly
            $update_stock_query = "UPDATE products SET quantity = quantity - $qty WHERE id = $product_id";
            mysqli_query($conn, $update_stock_query);
        }

        mysqli_commit($conn);
        header("Location: index.php?page=sales&status=success&invoice=" . $invoice_no);
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: index.php?page=sales&status=error");
        exit();
    }
}
