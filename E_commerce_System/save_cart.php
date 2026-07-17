<?php
// E_commerce_System/save_cart.php
header('Content-Type: application/json');

// Capture incoming raw raw data stream from the catalog fetch action
$raw_input = file_get_contents('php://input');
$decoded_data = json_decode($raw_input, true);

if (!isset($decoded_data['cart']) || empty($decoded_data['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Cart payload data parsing exception.']);
    exit;
}

// 1. Generate a unique system identifier token for this specific transaction
$cart_id = bin2hex(random_bytes(8));

// 2. 🌟 RELATIVE SHARED PATHWAY: Points directly over to the management panel workspace
$storage_file = dirname(__DIR__, 1) . '/mart_pos_system/stored_carts.json';

// Ensure directory layout path mapping is verified before writing data configurations
$target_dir = dirname($storage_file);
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// 3. Extract existing storage dataset rows if available
$all_carts = [];
if (file_exists($storage_file)) {
    $all_carts = json_decode(file_get_contents($storage_file), true) ?? [];
}

// 4. Format payload matrix structures cleanly for processing maps
$processed_cart = [];
foreach ($decoded_data['cart'] as $item) {
    $processed_cart[] = [
        'product_id' => $item['id'],
        'name'       => $item['name'],
        'qty'        => intval($item['qty']),
        'price'      => floatval($item['price'])
    ];
}

// Set tracking flags to initial verification pending states
$processed_cart['status'] = 'pending';

// Append this current request transaction directly to the central ledger array
$all_carts[$cart_id] = $processed_cart;

// 5. Commit structured updates to the system mapping directory file
file_put_contents($storage_file, json_encode($all_carts, JSON_PRETTY_PRINT));

// 6. Return response to tell the modal window where to route the customer
$redirect_url = "checkout.php?cart_id=" . $cart_id;
echo json_encode([
    'success' => true,
    'redirect_url' => $redirect_url
]);
