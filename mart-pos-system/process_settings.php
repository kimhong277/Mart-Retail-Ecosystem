<?php
// process_settings.php
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$conn = mysqli_connect($host, $db_user, $db_pass, 'mart_pos_system');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_store_settings'])) {

    // Core parameters mapping match tracking target keys
    $fields = ['store_name', 'store_phone', 'store_email', 'store_address', 'currency_symbol'];

    // Execute updates inside an atomic database transaction
    mysqli_begin_transaction($conn);

    try {
        foreach ($fields as $key) {
            if (isset($_POST[$key])) {
                $val = mysqli_real_escape_string($conn, $_POST[$key]);

                // Using standard Upsert (INSERT ... ON DUPLICATE KEY UPDATE) logic
                $sql = "INSERT INTO system_settings (setting_key, setting_value) 
                        VALUES ('$key', '$val') 
                        ON DUPLICATE KEY UPDATE setting_value = '$val'";

                if (!mysqli_query($conn, $sql)) {
                    throw new Exception("Failed to write configurations values mapping: " . $key);
                }
            }
        }

        // Everything saved flawlessly
        mysqli_commit($conn);
        header("Location: index.php?page=settings&status=updated");
        exit;
    } catch (Exception $e) {
        // Rollback state settings if a write fails
        mysqli_rollback($conn);
        header("Location: index.php?page=settings&status=error");
        exit;
    }
} else {
    header("Location: index.php?page=settings");
    exit;
}
