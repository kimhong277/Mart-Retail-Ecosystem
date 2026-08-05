<?php
// mart_pos_system/settle_bill.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bill_id'])) {
    $conn = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
        exit;
    }
    mysqli_set_charset($conn, "utf8mb4");

    $bill_id = intval($_POST['bill_id']);

    // Begin transaction for database safety
    mysqli_begin_transaction($conn);

    try {
        // 1. Fetch bill details before mutating data
        $bill_query = mysqli_query($conn, "SELECT * FROM supplier_bills WHERE id = $bill_id AND status = 'Unpaid'");
        if (!$bill_query || mysqli_num_rows($bill_query) === 0) {
            throw new Exception("Bill record not found or already settled.");
        }
        $bill_data = mysqli_fetch_assoc($bill_query);

        $supplier = mysqli_real_escape_string($conn, $bill_data['supplier_name']);
        $amount   = floatval($bill_data['amount_due']);
        $bill_no  = mysqli_real_escape_string($conn, $bill_data['bill_no']);

        // 2. Mark the target supplier invoice as Paid
        $update_bill = mysqli_query($conn, "UPDATE supplier_bills SET status = 'Paid' WHERE id = $bill_id");
        if (!$update_bill) {
            throw new Exception("Failed to update supplier bill status.");
        }

        // 3. Inject the cash out log into your exact `expenses` columns!
        $title          = "Supplier Settlement: $bill_no";
        $category       = "Inventory Supply";
        $payment_method = "Cash"; // Matches your default checkout flow

        $expense_sql = "INSERT INTO expenses (expense_title, category, amount, payment_method, expense_date) 
                        VALUES ('$title', '$category', $amount, '$payment_method', NOW())";

        if (!mysqli_query($conn, $expense_sql)) {
            throw new Exception("Failed to log entry to expenses table: " . mysqli_error($conn));
        }

        // Commit transaction if both operations pass flawlessly
        mysqli_commit($conn);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid routing gateway format constraints mapping.']);
}
