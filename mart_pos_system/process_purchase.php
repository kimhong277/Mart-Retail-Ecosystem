<?php
// process_purchase.php
require_once 'db.php';

if (isset($_POST['add_purchase'])) {
    $product_id = intval($_POST['product_id']);
    $supplier_name = mysqli_real_escape_string($conn, trim($_POST['supplier_name']));
    $qty_added = intval($_POST['quantity']);
    $purchase_cost = floatval($_POST['purchase_cost']);
    $purchase_date = date("Y-m-d H:i:s");

    if ($product_id <= 0 || $qty_added <= 0) {
        header("Location: index.php?page=purchases&status=invalid_input");
        exit();
    }

    // Begin standard procedural query transaction
    mysqli_begin_transaction($conn);

    try {
        // 1. Optional logic step: Log entry into a purchases record sheet if you track custom business expenses
        // $expense_sql = "INSERT INTO purchases (product_id, supplier_name, quantity, cost, purchase_date) VALUES ($product_id, '$supplier_name', $qty_added, $purchase_cost, '$purchase_date')";
        // mysqli_query($conn, $expense_sql);

        // 2. REPLENISH INVENTORY STOCK QUANTITY LEVEL COUNTERS DIRECTLY
        $update_stock_sql = "UPDATE products SET quantity = quantity + $qty_added WHERE id = $product_id";
        mysqli_query($conn, $update_stock_sql);

        // Commit all changes to the database
        mysqli_commit($conn);
        header("Location: index.php?page=purchases&status=success");
        exit();
    } catch (Exception $e) {
        // Rollback changes instantly if any script exception is triggered
        mysqli_rollback($conn);
        header("Location: index.php?page=purchases&status=error");
        exit();
    }
}
