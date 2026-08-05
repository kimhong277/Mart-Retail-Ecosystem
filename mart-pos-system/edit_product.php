<?php
// mart_pos_system/edit_product.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $conn_pos = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
    if (!$conn_pos) {
        echo json_encode(['success' => false, 'message' => 'Database link failure: ' . mysqli_connect_error()]);
        exit;
    }
    mysqli_set_charset($conn_pos, "utf8mb4");

    $product_id   = intval($_POST['product_id']);
    $product_name = mysqli_real_escape_string($conn_pos, trim($_POST['product_name']));
    $barcode      = mysqli_real_escape_string($conn_pos, trim($_POST['barcode']));
    // Accept either 'price' or 'sale_price' from your frontend form just to be safe
    $price        = floatval($_POST['price'] ?? $_POST['sale_price'] ?? 0);
    $quantity     = intval($_POST['quantity']);
    $status       = intval($_POST['status']);

    // Check file upload status parameters
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['product_image']['tmp_name'];
        $file_orig = $_FILES['product_image']['name'];
        $file_ext  = strtolower(pathinfo($file_orig, PATHINFO_EXTENSION));

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $allowed_extensions)) {
            // Unlink old asset file from storage pool to maintain space optimization limits
            $old_img_query = mysqli_query($conn_pos, "SELECT image FROM products WHERE id = $product_id");
            if ($old_img_query && mysqli_num_rows($old_img_query) > 0) {
                $old_file = mysqli_fetch_assoc($old_img_query)['image'];
                if (!empty($old_file) && $old_file !== 'default.png') {
                    $old_file_path = __DIR__ . '/assets/images/' . $old_file;
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }
            }

            $new_image_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $file_orig);
            $upload_dir = __DIR__ . '/assets/images/';

            if (move_uploaded_file($file_tmp, $upload_dir . $new_image_name)) {
                // ✅ FIXED: Changed 'price' to 'sale_price'
                $sql = "UPDATE products SET 
                        product_name = '$product_name', 
                        barcode = '$barcode', 
                        sale_price = $price, 
                        quantity = $quantity, 
                        status = $status, 
                        image = '$new_image_name' 
                        WHERE id = $product_id";
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save uploaded graphic file onto disk directory templates.']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid file signature type extension rejected.']);
            exit;
        }
    } else {
        // ✅ FIXED: Changed 'price' to 'sale_price'
        $sql = "UPDATE products SET 
                product_name = '$product_name', 
                barcode = '$barcode', 
                sale_price = $price, 
                quantity = $quantity, 
                status = $status 
                WHERE id = $product_id";
    }

    if (mysqli_query($conn_pos, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'SQL execution failure update logic mapping: ' . mysqli_error($conn_pos)]);
    }

    mysqli_close($conn_pos);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid Request routing entry protocols.']);
}
