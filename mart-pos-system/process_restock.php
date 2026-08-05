<?php
// mart_pos_system/process_restock.php
ob_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

$conn = mysqli_connect('localhost', 'root', '', 'mart_pos_system');

if (!$conn) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . mysqli_connect_error()]);
    exit;
}

mysqli_set_charset($conn, "utf8mb4");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id     = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $supplier_id    = isset($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
    $quantity_added = isset($_POST['quantity_added']) ? (int)$_POST['quantity_added'] : 0;
    $purchase_cost  = isset($_POST['purchase_cost']) ? (float)$_POST['purchase_cost'] : 0.00;

    if ($product_id <= 0 || $supplier_id <= 0 || $quantity_added <= 0) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Please select a valid product, supplier, and quantity.']);
        exit;
    }

    // Get supplier_name string safely using prepared statement
    $stmt_sup_name = mysqli_prepare($conn, "SELECT supplier_name FROM suppliers WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt_sup_name, "i", $supplier_id);
    mysqli_stmt_execute($stmt_sup_name);
    $sup_result = mysqli_stmt_get_result($stmt_sup_name);
    $sup_row = mysqli_fetch_assoc($sup_result);
    $supplier_name = $sup_row ? $sup_row['supplier_name'] : 'Unknown Supplier';

    // Generate a unique bill number to satisfy the bill_no unique index
    // $generated_bill_no = 'SUP-BILL-' . date('Ymd') . '-' . rand(1000, 9999);
    // Matches your exact old format: SUP-BILL- followed by current Unix timestamp
    $generated_bill_no = 'SUP-BILL-' . time();

    mysqli_begin_transaction($conn);

    try {
        // 1. Log entry into purchases table
        $stmt_purchases = mysqli_prepare($conn, "INSERT INTO purchases (product_id, supplier_id, quantity_added, purchase_cost, purchase_date) VALUES (?, ?, ?, ?, NOW())");
        if (!$stmt_purchases) {
            throw new Exception("Purchases table query failed: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_purchases, "iiid", $product_id, $supplier_id, $quantity_added, $purchase_cost);
        if (!mysqli_stmt_execute($stmt_purchases)) {
            throw new Exception("Failed to insert into purchases: " . mysqli_stmt_error($stmt_purchases));
        }

        // 2. Increase product stock & update supplier name in products
        $stmt_prod = mysqli_prepare($conn, "UPDATE products SET quantity = quantity + ?, supplier_name = ? WHERE id = ?");
        if (!$stmt_prod) {
            throw new Exception("Products update query failed: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_prod, "isi", $quantity_added, $supplier_name, $product_id);
        if (!mysqli_stmt_execute($stmt_prod)) {
            throw new Exception("Failed to update product stock: " . mysqli_stmt_error($stmt_prod));
        }

        // 3. Create unpaid vendor bill in supplier_bills INCLUDING unique bill_no
        $stmt_bill = mysqli_prepare($conn, "INSERT INTO supplier_bills (bill_no, supplier_name, amount_due, status, created_at) VALUES (?, ?, ?, 'Unpaid', NOW())");
        if (!$stmt_bill) {
            throw new Exception("Supplier bills table query failed: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt_bill, "ssd", $generated_bill_no, $supplier_name, $purchase_cost);
        if (!mysqli_stmt_execute($stmt_bill)) {
            throw new Exception("Failed to insert into supplier_bills: " . mysqli_stmt_error($stmt_bill));
        }

        // Commit transaction if all queries succeed
        mysqli_commit($conn);

        ob_end_clean();
        echo json_encode(['success' => true, 'message' => 'Restock processed successfully']);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
