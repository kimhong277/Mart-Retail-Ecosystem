<?php
// mart_pos_system/delete_product.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $conn_pos = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
    if (!$conn_pos) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }

    $id = intval($_POST['id']);

    // 🌟 1. FETCH IMAGE STRING BEFORE DELETING RECORD
    $img_query = "SELECT image FROM products WHERE id = $id";
    $img_result = mysqli_query($conn_pos, $img_query);

    if ($img_result && mysqli_num_rows($img_result) > 0) {
        $product = mysqli_fetch_assoc($img_result);
        $filename = $product['image'];

        // 🌟 2. UNLINK (DELETE) IMAGE FROM SERVER FILE SYSTEM
        if (!empty($filename) && $filename !== 'default.png') {
            $target_file_path = __DIR__ . '/assets/images/' . $filename;
            if (file_exists($target_file_path)) {
                unlink($target_file_path); // Erases file from server storage
            }
        }
    }

    // 3. Delete row data record from database tables
    $sql = "DELETE FROM products WHERE id = $id";

    if (mysqli_query($conn_pos, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn_pos)]);
    }
    mysqli_close($conn_pos);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Request']);
}
