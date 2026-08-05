<?php
// mart_pos_system/place_pos_order.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_data'])) {

    $conn_pos = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
    if (!$conn_pos) {
        echo json_encode(['success' => false, 'message' => 'Database link failed: ' . mysqli_connect_error()]);
        exit;
    }
    mysqli_set_charset($conn_pos, "utf8mb4");

    $cart = json_decode($_POST['cart_data'], true);
    if (empty($cart)) {
        echo json_encode(['success' => false, 'message' => 'Cart processing array is empty.']);
        exit;
    }

    // Start database transaction
    mysqli_begin_transaction($conn_pos);

    try {
        // 1. Calculate Grand Total
        $grand_total = 0;
        foreach ($cart as $item) {
            $grand_total += floatval($item['price']) * intval($item['qty']);
        }

        // 2. Generate a professional dynamic invoice code matching your system layout
        $invoice_no = "INV-" . time();

        // 3. Insert into the main `sales` table (Using your exact database columns!)
        $sale_query = "INSERT INTO sales (invoice_no, total_amount, sale_date, payment_method, order_type, status) 
                       VALUES ('$invoice_no', $grand_total, NOW(), 'Cash', 'POS', 'Completed')";

        if (!mysqli_query($conn_pos, $sale_query)) {
            throw new Exception("Failed to log master sales record: " . mysqli_error($conn_pos));
        }

        // Grab the newly generated auto-increment id from the master sales table
        $sale_id = mysqli_insert_id($conn_pos);

        // 4. Loop over items to record item details and deduct stock counts
        foreach ($cart as $item) {
            $product_id = intval($item['id']);
            $qty        = intval($item['qty']);
            $price      = floatval($item['price']);
            $subtotal   = $price * $qty;

            // Verify current stock availability check
            $check_stock = mysqli_query($conn_pos, "SELECT quantity FROM products WHERE id = $product_id");
            if (!$check_stock || mysqli_num_rows($check_stock) == 0) {
                throw new Exception("Product ID #$product_id not found in registry catalog.");
            }
            $current_stock = mysqli_fetch_assoc($check_stock)['quantity'];

            if ($current_stock < $qty) {
                throw new Exception("Inventory conflict for product. Insufficient stock remaining.");
            }

            // A: Deduct the purchased amounts from your catalog table pool counts
            $update_stock = "UPDATE products SET quantity = quantity - $qty WHERE id = $product_id";
            if (!mysqli_query($conn_pos, $update_stock)) {
                throw new Exception("Stock reduction failure: " . mysqli_error($conn_pos));
            }

            // B: Populate your `sale_details` breakdown pivot table item rows!
            $detail_query = "INSERT INTO sale_details (sale_id, product_id, price, quantity, subtotal) 
                             VALUES ($sale_id, $product_id, $price, $qty, $subtotal)";
            if (!mysqli_query($conn_pos, $detail_query)) {
                throw new Exception("Failed to populate sales itemized list: " . mysqli_error($conn_pos));
            }
        }

        // Commit transaction if all operations pass flawlessly
        mysqli_commit($conn_pos);

        // Return success along with the generated invoice number back to frontend JS if needed!
        echo json_encode(['success' => true, 'invoice_no' => $invoice_no]);
    } catch (Exception $e) {
        // Rollback any database alterations if a mid-loop item throws an error
        mysqli_rollback($conn_pos);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    mysqli_close($conn_pos);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid gateway request routing protocol.']);
}
