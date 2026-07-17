<?php
// test/save_cart.php

// Define a test cart array structure
$user_cart = [
    ['product_id' => '101', 'name' => 'Java E-Book', 'qty' => 2, 'price' => 19.99],
    ['product_id' => '102', 'name' => 'Web Dev Bootcamp', 'qty' => 1, 'price' => 49.99]
];

// Generate an 8-byte unique identifier token
$cart_id = bin2hex(random_bytes(8));
$storage_file = __DIR__ . '/stored_carts.json';

$all_carts = [];
if (file_exists($storage_file)) {
    $all_carts = json_decode(file_get_contents($storage_file), true) ?? [];
}

// Save the core structure under your token reference
$all_carts[$cart_id] = $user_cart;
file_put_contents($storage_file, json_encode($all_carts, JSON_PRETTY_PRINT));

// Configure using your local machine target IP
$checkout_url = "http://192.168.1.166/E-Commerce%20System/test/shop/checkout.php?cart_id=" . $cart_id;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Multi-Item Cart QR Generator</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>

<body style="font-family: sans-serif; text-align: center; background-color: #f4f6f9; padding: 40px;">

    <div id="main-content" style="max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; border: 1px solid #ddd; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        <h2>Order Processing</h2>
        <p style="color: #666;">Scan the QR code layout string option down below to launch checkout view</p>

        <div id="qrcode" style="display: flex; justify-content: center; margin: 25px 0;"></div>
        <p style="color: #0056b3; font-weight: bold;">⏳ Waiting for payment confirmation...</p>
    </div>

    <script>
        // Generate initial router code link
        new QRCode(document.getElementById("qrcode"), {
            text: "<?php echo $checkout_url; ?>",
            width: 180,
            height: 180
        });

        const cartId = "<?php echo $cart_id; ?>";

        // Background loop querying status changes every 2 seconds
        const checkPaymentInterval = setInterval(() => {
            fetch(`check_status.php?cart_id=${cartId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'paid') {
                        clearInterval(checkPaymentInterval);

                        // Switch UI block instantly upon sensing paid transaction update status
                        document.getElementById('main-content').innerHTML = `
                            <h1 style="color: #28a745; margin-top: 20px;">🎉 Payment Received Successfully!</h1>
                            <p style="color: #555;">Your bank transfer order has been verified via custom token checkout setup.</p>
                        `;
                    }
                })
                .catch(err => console.error("Error evaluating cart tracking:", err));
        }, 2000);
    </script>
</body>

</html>