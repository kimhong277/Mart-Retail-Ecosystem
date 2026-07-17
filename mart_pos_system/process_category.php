<?php
// process_category.php
require_once 'db.php';

// 1. CREATE OPERATION
if (isset($_POST['add_category'])) {
    $category_name = mysqli_real_escape_string($conn, trim($_POST['category_name']));

    if (!empty($category_name)) {
        $sql = "INSERT INTO categories (category_name, status) VALUES ('$category_name', 1)";
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?page=categories&status=inserted");
            exit();
        }
    }
    header("Location: index.php?page=categories&status=error");
    exit();
}

// 2. UPDATE OPERATION
if (isset($_POST['update_category'])) {
    $id            = intval($_POST['category_id']);
    $category_name = mysqli_real_escape_string($conn, trim($_POST['category_name']));
    $status        = intval($_POST['status']);

    $sql = "UPDATE categories SET category_name = '$category_name', status = $status WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?page=categories&status=updated");
        exit();
    }
    header("Location: index.php?page=categories&status=error");
    exit();
}

// 3. HARD DELETE WITH CONSTRAINT CATCHING (Kept safe in backend)
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = intval($_GET['id']);

    try {
        $sql = "DELETE FROM categories WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?page=categories&status=deleted");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        // Safe check if products are already using this category
        header("Location: index.php?page=categories&status=constraint_error");
        exit();
    }
    header("Location: index.php?page=categories&status=error");
    exit();
}
