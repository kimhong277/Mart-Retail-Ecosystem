<?php
// process_quotation.php

$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$conn = mysqli_connect($host, $db_user, $db_pass, 'mart_pos_system');

if (!$conn) {
    die("Database engine link down: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_quotation'])) {

    // 1. Collect and sanitize form inputs
    $customer_name = isset($_POST['customer_name']) ? mysqli_real_escape_string($conn, trim($_POST['customer_name'])) : '';
    $total_amount  = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0.00;

    $product_ids = isset($_POST['product_ids']) ? $_POST['product_ids'] : [];
    $quantities  = isset($_POST['quantities']) ? $_POST['quantities'] : [];

    // Validation: Require customer name and at least one product
    if (empty($customer_name) || empty($product_ids) || count($product_ids) === 0) {
        header("Location: index.php?page=quotations&status=empty_cart");
        exit;
    }

    // 2. Generate a unique Quote Number (e.g. QT-20260721-4892)
    $quote_no = 'QT-' . date('Ymd') . '-' . rand(1000, 9999);
    $status   = 'Sent'; // Default status when created

    // Start database transaction
    mysqli_begin_transaction($conn);

    try {
        // 3. Insert Master Quotation record
        $stmt_quote = mysqli_prepare($conn, "INSERT INTO quotations (quote_no, customer_name, total_amount, status, created_at) VALUES (?, ?, ?, ?, NOW())");
        if (!$stmt_quote) {
            throw new Exception("Quotation prepare failed: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_quote, "ssds", $quote_no, $customer_name, $total_amount, $status);
        if (!mysqli_stmt_execute($stmt_quote)) {
            throw new Exception("Quotation insert failed: " . mysqli_stmt_error($stmt_quote));
        }

        // Get the inserted quotation ID
        $quotation_id = mysqli_insert_id($conn);

        // 4. Insert each line item into `quotation_items` table
        $stmt_items = mysqli_prepare($conn, "INSERT INTO quotation_items (quotation_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt_items) {
            throw new Exception("Quotation items prepare failed: " . mysqli_error($conn));
        }

        foreach ($product_ids as $index => $prod_id) {
            $p_id = intval($prod_id);
            $qty  = isset($quantities[$index]) ? intval($quantities[$index]) : 1;

            if ($p_id <= 0 || $qty <= 0) continue;

            // Fetch current product sale_price directly from database for accurate subtotal
            $price_res = mysqli_query($conn, "SELECT sale_price FROM products WHERE id = {$p_id} LIMIT 1");
            $price_row = mysqli_fetch_assoc($price_res);
            $unit_price = $price_row ? floatval($price_row['sale_price']) : 0.00;
            $subtotal   = $unit_price * $qty;

            mysqli_stmt_bind_param($stmt_items, "iiidd", $quotation_id, $p_id, $qty, $unit_price, $subtotal);
            if (!mysqli_stmt_execute($stmt_items)) {
                throw new Exception("Quotation line item insert failed: " . mysqli_stmt_error($stmt_items));
            }
        }

        // Commit transaction if all queries pass
        mysqli_commit($conn);

        // Redirect back with success message and generated quote number
        header("Location: index.php?page=quotations&status=success&qt=" . urlencode($quote_no));
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: index.php?page=quotations&status=error");
        exit;
    }
} else {
    header("Location: index.php?page=quotations");
    exit;
}
