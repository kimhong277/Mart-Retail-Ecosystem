<?php
// test/check_status.php
header('Content-Type: application/json');

$cart_id = $_GET['cart_id'] ?? '';
$storage_file = __DIR__ . '/stored_carts.json';
$status = 'pending';

if (file_exists($storage_file)) {
    $all_carts = json_decode(file_get_contents($storage_file), true);

    // Evaluate if targeted reference token exists 
    if (isset($all_carts[$cart_id])) {
        $cart = $all_carts[$cart_id];

        // Pull status flag regardless if data format includes root or structural sub-keys
        if (isset($cart['status']) && $cart['status'] === 'paid') {
            $status = 'paid';
        }
    }
}

echo json_encode(['status' => $status]);
