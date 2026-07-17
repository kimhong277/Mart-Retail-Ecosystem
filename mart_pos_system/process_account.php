<?php
// process_account.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';

// 1. CREATE USER ACCOUNT
if (isset($_POST['add_user'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $fullname = mysqli_real_escape_string($conn, trim($_POST['fullname']));
    $role     = mysqli_real_escape_string($conn, $_POST['role']);
    $created  = date("Y-m-d H:i:s");

    if (!empty($username) && !empty($fullname)) {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
        if (mysqli_num_rows($check) > 0) {
            $_SESSION['status_msg'] = 'duplicate';
            header("Location: index.php?page=accounts");
            exit();
        }

        $sql = "INSERT INTO users (username, fullname, role, status, created_at) 
                VALUES ('$username', '$fullname', '$role', 1, '$created')";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['status_msg'] = 'inserted';
            header("Location: index.php?page=accounts");
            exit();
        }
    }
    $_SESSION['status_msg'] = 'error';
    header("Location: index.php?page=accounts");
    exit();
}

// 2. UPDATE USER DETAILS
if (isset($_POST['update_user'])) {
    $id       = intval($_POST['user_id']);
    $fullname = mysqli_real_escape_string($conn, trim($_POST['fullname']));
    $role     = mysqli_real_escape_string($conn, $_POST['role']);
    $status   = intval($_POST['status']);

    // Safeguard: Block suspending or changing the role of the primary master administrator
    if ($id === 1 && ($status === 0 || $role !== 'Admin')) {
        $_SESSION['status_msg'] = 'protected_admin';
        header("Location: index.php?page=accounts");
        exit();
    }

    $sql = "UPDATE users SET fullname = '$fullname', role = '$role', status = $status WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['status_msg'] = 'updated';
        header("Location: index.php?page=accounts");
        exit();
    }
    $_SESSION['status_msg'] = 'error';
    header("Location: index.php?page=accounts");
    exit();
}

// 3. HARD DELETE SAFEGUARD
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = intval($_GET['id']);

    if ($id === 1) {
        $_SESSION['status_msg'] = 'protected_admin';
        header("Location: index.php?page=accounts");
        exit();
    }

    $sql = "DELETE FROM users WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['status_msg'] = 'deleted';
        header("Location: index.php?page=accounts");
        exit();
    }
    $_SESSION['status_msg'] = 'error';
    header("Location: index.php?page=accounts");
    exit();
}
