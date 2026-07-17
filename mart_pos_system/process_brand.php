<?php
// process_brand.php
require_once 'db.php';

// 1. CREATE OPERATION
if (isset($_POST['add_brand'])) {
    $brand_name = mysqli_real_escape_string($conn, trim($_POST['brand_name']));

    if (!empty($brand_name)) {
        $sql = "INSERT INTO brands (brand_name, status) VALUES ('$brand_name', 1)";
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?page=brands&status=inserted");
            exit();
        }
    }
    header("Location: index.php?page=brands&status=error");
    exit();
}

// 2. UPDATE OPERATION
if (isset($_POST['update_brand'])) {
    $id         = intval($_POST['brand_id']);
    $brand_name = mysqli_real_escape_string($conn, trim($_POST['brand_name']));
    $status     = intval($_POST['status']);

    $sql = "UPDATE brands SET brand_name = '$brand_name', status = $status WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?page=brands&status=updated");
        exit();
    }
    header("Location: index.php?page=brands&status=error");
    exit();
}

// 3. DELETE OPERATION (Now triggered purely through an entry script controller parameter)
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM brands WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?page=brands&status=deleted");
        exit();
    }
    header("Location: index.php?page=brands&status=error");
    exit();
}
