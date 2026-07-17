<?php
// test/shop/checkout.php

$cart_id = isset($_GET['cart_id']) ? trim($_GET['cart_id']) : null;
$storage_file = dirname(__DIR__, 1) . '/stored_carts.json';
$cart_contents = null;
$all_carts = [];

if (file_exists($storage_file)) {
    $all_carts = json_decode(file_get_contents($storage_file), true);
    if ($cart_id && isset($all_carts[$cart_id])) {
        $cart_contents = $all_carts[$cart_id];
    }
}

// Global flag to trigger the success script animation downstream
$payment_success_triggered = false;

// SIMULATOR HANDLER: Flips status flag when you click "Confirm Payment"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simulate_payment'])) {
    if ($cart_id && isset($all_carts[$cart_id])) {
        $all_carts[$cart_id]['status'] = 'paid';
        file_put_contents($storage_file, json_encode($all_carts, JSON_PRETTY_PRINT));

        // Turn on the trigger flag instead of sending a raw script redirect right away
        $payment_success_triggered = true;
    }
}

if ($cart_contents) {
    if (isset($cart_contents['status']) && $cart_contents['status'] === 'paid' && !$payment_success_triggered) {
        echo "<div style='text-align: center; font-family: sans-serif; padding: 50px;'><h2>🎉 Order Completed!</h2><p><a href='../save_cart.php?cart_id={$cart_id}'>Return to Receipt Dashboard</a></p></div>";
        exit;
    }

    $grand_total = 0;
    $item_list_html = "";
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
    echo "<div style='text-align: center; font-family: sans-serif; padding: 50px;'><h3>Error: Cart matching session ID not found.</h3></div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Checkout Processing Panel</title>
    <!-- 🌟 Inject SweetAlert2 Framework via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body style="font-family: sans-serif; background-color: #f4f6f9; padding: 20px;">

    <div style="max-width: 500px; margin: 30px auto; background: white; border: 1px solid #ddd; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">

        <h2 style="text-align: center; margin-bottom: 5px; color: #1a202c;">Payment Gateway</h2>
        <p style="text-align: center; color: #4a5568; font-size: 14px; margin-top: 0;">Demo Environment Integration Interface</p>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

        <h3 style="margin-bottom: 15px; font-size: 16px; color: #333;">Order Summary</h3>
        <?php echo $item_list_html; ?>

        <hr style="border: 0; border-top: 1px dashed #ccc; margin: 20px 0;">

        <div style="display: flex; justify-content: space-between; font-size: 20px; font-weight: bold; margin-bottom: 25px;">
            <div>Total:</div>
            <div style="color: #0056b3;">$<?php echo number_format($grand_total, 2); ?></div>
        </div>

        <!-- THE MAIN CLICK ACTION BUTTON -->
        <form method="POST">
            <input type="hidden" name="simulate_payment" value="1">
            <button type="submit" style="width: 100%; background: #28a745; color: white; border: none; padding: 14px; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: bold;">
                ✅ Authorize & Pay Now
            </button>
        </form>
    </div>

    <script>
        // 🌟 Executed only after the POST submit triggers the processing block variables
        <?php if ($payment_success_triggered): ?>
            Swal.fire({
                title: 'Payment Successful!',
                text: 'Updating local balance token structures...',
                icon: 'success',
                timer: 2000, // 👈 2000 milliseconds = 2 seconds
                timerProgressBar: true,
                showConfirmButton: false,
                willClose: () => {
                    // Route right back to save_cart when the layout timer expires
                    window.location.href = '../save_cart.php?cart_id=<?php echo $cart_id; ?>';
                }
            });
        <?php endif; ?>
    </script>
</body>

</html>