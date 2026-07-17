<?php
// E-Commerce System/checkout.php
session_start();
require_once __DIR__ . '/config/db_bridge.php';

$cart_id = isset($_GET['cart_id']) ? trim($_GET['cart_id']) : null;
$storage_file = dirname(__DIR__, 1) . '/mart_pos_system/stored_carts.json';
$cart_contents = null;
$all_carts = [];

if (file_exists($storage_file)) {
    $all_carts = json_decode(file_get_contents($storage_file), true);
    if ($cart_id && isset($all_carts[$cart_id])) {
        $cart_contents = $all_carts[$cart_id];
    }
}

$payment_success_triggered = false;
$grand_total = 0;
$item_list_html = "";

if ($cart_contents) {
    foreach ($cart_contents as $key => $item) {
        if ($key === 'status') continue;
        $item_total = $item['qty'] * $item['price'];
        $grand_total += $item_total;

        $item_list_html .= "
        <div style='display: flex; justify-content: space-between; margin-bottom: 15px;'>
            <div>
                <strong>" . htmlspecialchars($item['name']) . "</strong><br>
                <small>Qty: {$item['qty']} @ $" . number_format($item['price'], 2) . " each</small>
            </div>
            <div>$" . number_format($item_total, 2) . "</div>
        </div>";
    }
} else {
    echo "<div style='text-align: center; font-family: sans-serif; padding: 50px;'><h3>Error: Active checkout session data invalid.</h3></div>";
    exit;
}

// Processing Execution via procedural code blocks
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simulate_payment'])) {
    if ($cart_id && isset($all_carts[$cart_id]) && $all_carts[$cart_id]['status'] !== 'paid') {

        $simulated_user_id = 1; // Default customer reference id

        // Disable autocommit to manually handle transactions procedurally
        mysqli_autocommit($conn_eco, FALSE);
        mysqli_autocommit($conn_pos, FALSE);

        $transaction_success = true;

        // ==========================================
        // 🛒 STEP A: UPDATE E-COMMERCE ENGINE DATABASE
        // ==========================================

        // 1. Write core order row[cite: 1]
        $sql_order = "INSERT INTO orders (user_id, total_amount, status) VALUES ($simulated_user_id, $grand_total, 'Paid')";
        if (mysqli_query($conn_eco, $sql_order)) {
            $new_order_id = mysqli_insert_id($conn_eco);
        } else {
            $transaction_success = false;
        }

        // ==========================================
        // 🏪 STEP B: UPDATE POS MANAGEMENT DATABASE
        // ==========================================

        // 2. Generate invoice layout code and log the terminal sale receipt[cite: 2]
        $invoice_no = "INV-WEB-" . strtoupper(bin2hex(random_bytes(4)));
        $sql_pos_sale = "INSERT INTO sales (invoice_no, total_amount, payment_method) VALUES ('$invoice_no', $grand_total, 'Online Web')";
        if ($transaction_success && mysqli_query($conn_pos, $sql_pos_sale)) {
            $new_pos_sale_id = mysqli_insert_id($conn_pos);
        } else {
            $transaction_success = false;
        }

        // 3. Process item listings line by line loop
        if ($transaction_success) {
            foreach ($cart_contents as $key => $item) {
                if ($key === 'status') continue;

                $product_id = intval($item['product_id']);
                $qty = intval($item['qty']);
                $price = floatval($item['price']);
                $escaped_name = mysqli_real_escape_string($conn_pos, $item['name']);

                // A. Save detailed item layout to E-commerce logs[cite: 1]
                $sql_det = "INSERT INTO order_details (order_id, product_id, quantity, price_at_purchase) VALUES ($new_order_id, $product_id, $qty, $price)";
                if (!mysqli_query($conn_eco, $sql_det)) {
                    $transaction_success = false;
                    break;
                }

                // B. Translate item to POS environment by querying the matching item name[cite: 2]
                $sql_find = "SELECT id FROM products WHERE product_name = '$escaped_name' LIMIT 1";
                $res_find = mysqli_query($conn_pos, $sql_find);

                if ($res_find && mysqli_num_rows($res_find) > 0) {
                    $pos_product = mysqli_fetch_assoc($res_find);
                    $pos_prod_id = intval($pos_product['id']);
                    $subtotal = $qty * $price;

                    // C. Record internal sale items log inside the POS ledger[cite: 2]
                    $sql_pos_det = "INSERT INTO sale_details (sale_id, product_id, price, quantity, subtotal) VALUES ($new_pos_sale_id, $pos_prod_id, $price, $qty, $subtotal)";
                    if (!mysqli_query($conn_pos, $sql_pos_det)) {
                        $transaction_success = false;
                        break;
                    }

                    // D. DECREMENT QUANTITY: Deduct the stock count instantly from physical inventory[cite: 2]
                    $sql_deduct = "UPDATE products SET quantity = quantity - $qty WHERE id = $pos_prod_id";
                    if (!mysqli_query($conn_pos, $sql_deduct)) {
                        $transaction_success = false;
                        break;
                    }
                }
            }
        }

        // Complete the procedural checkout verification
        if ($transaction_success) {
            mysqli_commit($conn_eco);
            mysqli_commit($conn_pos);

            // Clean up temporary local bridging records
            $all_carts[$cart_id]['status'] = 'paid';
            file_put_contents($storage_file, json_encode($all_carts, JSON_PRETTY_PRINT));

            $payment_success_triggered = true;
        } else {
            mysqli_rollback($conn_eco);
            mysqli_rollback($conn_pos);
            die("Transaction process interrupted due to database record updates conflicts.");
        }

        // Reset autocommit settings back to true
        mysqli_autocommit($conn_eco, TRUE);
        mysqli_autocommit($conn_pos, TRUE);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Secure Checkout Gateway</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body style="font-family: sans-serif; background-color: #f4f6f9; padding: 20px;">

    <div style="max-width: 500px; margin: 30px auto; background: white; border: 1px solid #ddd; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; margin-bottom: 5px; color: #1a202c;">Secure Payment Gateway</h2>
        <p style="text-align: center; color: #666; font-size: 14px; margin-top: 0;">Order Processing Gateway</p>
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

        <h3 style="margin-bottom: 15px; font-size: 16px; color: #333;">Order Summary</h3>
        <?php echo $item_list_html; ?>

        <hr style="border: 0; border-top: 1px dashed #ccc; margin: 20px 0;">
        <div style="display: flex; justify-content: space-between; font-size: 20px; font-weight: bold; margin-bottom: 25px;">
            <div>Total Amount:</div>
            <div style="color: #0056b3;">$<?php echo number_format($grand_total, 2); ?></div>
        </div>

        <form method="POST">
            <input type="hidden" name="simulate_payment" value="1">
            <button type="submit" style="width: 100%; background: #28a745; color: white; border: none; padding: 14px; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold;">
                ✅ Confirm & Pay Now
            </button>
        </form>
    </div>

    <script>
        <?php if ($payment_success_triggered): ?>
            Swal.fire({
                title: 'Payment Successful!',
                text: 'Order logged & inventory balanced across systems.',
                icon: 'success',
                timer: 2200,
                timerProgressBar: true,
                showConfirmButton: false,
                willClose: () => {
                    window.location.href = 'catalog.php';
                }
            });
        <?php endif; ?>
    </script>
</body>

</html>