<?php
// mart_pos_system/process_adjustment.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
        exit;
    }

    $product_id       = intval($_POST['product_id']);
    $type             = mysqli_real_escape_string($conn, $_POST['type']);
    $quantity_changed = intval($_POST['quantity_changed']);
    $reason           = mysqli_real_escape_string($conn, trim($_POST['reason']));

    if ($product_id <= 0 || $quantity_changed == 0 || empty($reason)) {
        echo json_encode(['success' => false, 'message' => 'All validation audit inputs are required.']);
        exit;
    }

    mysqli_begin_transaction($conn);

    try {
        // 1. Verify that this operation won't drive stock numbers into impossible negative ranges
        $check = mysqli_query($conn, "SELECT quantity FROM products WHERE id = $product_id");
        $current_qty = mysqli_fetch_assoc($check)['quantity'];

        if (($current_qty + $quantity_changed) < 0) {
            throw new Exception("Invalid adjustment. Stock balance cannot fall below 0 units.");
        }

        // 2. Drop historical trail log entry inside the adjustments table ledger
        $log_sql = "INSERT INTO stock_adjustments (product_id, type, quantity_changed, reason) 
                    VALUES ($product_id, '$type', $quantity_changed, '$reason')";
        if (!mysqli_query($conn, $log_sql)) {
            throw new Exception("Failed to commit trail block to the stock adjustment ledger.");
        }

        // 3. Update the master product inventory table balance
        $update_sql = "UPDATE products SET quantity = quantity + $quantity_changed WHERE id = $product_id";
        if (!mysqli_query($conn, $update_sql)) {
            throw new Exception("Master inventory adjustment write sequence aborted.");
        }


        mysqli_commit($conn);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    mysqli_close($conn);
}
