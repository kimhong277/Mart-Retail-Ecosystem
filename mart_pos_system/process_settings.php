<?php
// process_settings.php
require_once 'db.php';

if (isset($_POST['update_settings'])) {
    // Collect posted configurations array matrix
    $configs = [
        'store_name'    => $_POST['store_name'],
        'store_phone'   => $_POST['store_phone'],
        'store_email'   => $_POST['store_email'],
        'store_address' => $_POST['store_address'],
        'currency_symbol' => $_POST['currency_symbol'],
        'vat_rate'      => $_POST['vat_rate']
    ];

    $error_triggered = false;

    // Loop and apply updates to each independent metadata row entry key
    foreach ($configs as $key => $value) {
        $safe_value = mysqli_real_escape_string($conn, trim($value));
        $sql = "UPDATE settings SET setting_value = '$safe_value' WHERE setting_key = '$key'";
        $result = mysqli_query($conn, $sql);
        echo "hello";
        if (!$result) {
            echo "error" . mysqli_error($conn);
            $error_triggered = true;
        }
    }

    if (!$error_triggered) {
        header("Location: index.php?page=settings&tab=profile&status=success");
        exit();
    } else {
        header("Location: index.php?page=settings&tab=profile&status=error");
        exit();
    }
}
