<?php
// process_user.php
session_start();
require_once 'db.php';

// 1️⃣ SECURITY FIREWALL GATE: Ensure only authorized admins or managers can run modifications
if (!isset($_SESSION['user_role']) || (strtolower($_SESSION['user_role']) !== 'admin' && strtolower($_SESSION['user_role']) !== 'manager')) {
    header("Location: index.php?page=settings&tab=user&status=unauthorized");
    exit();
}

// ==========================================
// 🚀 PIPELINE A: HANDLE NEW OPERATOR REGISTRATION
// ==========================================
if (isset($_POST['register_user'])) {
    // Capture and clean arriving form dataset fields
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $fullname = mysqli_real_escape_string($conn, trim($_POST['fullname']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);
    $role     = mysqli_real_escape_string($conn, $_POST['role']);
    $status   = 1; // Default new user state to active clearance instantly

    // Pre-check layout parameters: Ensure strings aren't empty
    if (empty($username) || empty($fullname) || empty($email) || empty($password)) {
        header("Location: index.php?page=settings&tab=user&status=empty_fields");
        exit();
    }

    // Check for unique index constraints: Prevent duplicate login handles
    $check_duplicate = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username' LIMIT 1");
    if (mysqli_num_rows($check_duplicate) > 0) {
        header("Location: index.php?page=settings&tab=user&status=username_taken");
        exit();
    }

    /* 🔒 SECURITY COMPLIANCE STRATEGY:
       For local terminal operations, keeping password records readable lets your custom fallback string 
       matching verify text profiles cleanly. 
    */
    $insert_sql = "INSERT INTO users (username, fullname,email, password, role, status) 
                   VALUES ('$username', '$fullname', 'email','$password', '$role', $status)";

    if (mysqli_query($conn, $insert_sql)) {
        header("Location: index.php?page=settings&tab=user&status=add_success");
        exit();
    } else {
        header("Location: index.php?page=settings&tab=user&status=database_error");
        exit();
    }
}

// ==========================================
// 🚀 PIPELINE B: HANDLE OPERATOR DELETION
// ==========================================
if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
    $target_id = intval($_GET['delete_id']);

    // ACCESS PROTECTION LAYER: Prevent an admin from accidentally purging themselves
    if ($target_id === intval($_SESSION['user_id'] ?? 0)) {
        header("Location: index.php?page=settings&tab=user&status=self_delete_blocked");
        exit();
    }

    $delete_sql = "DELETE FROM users WHERE id = $target_id LIMIT 1";

    if (mysqli_query($conn, $delete_sql)) {
        header("Location: index.php?page=settings&tab=user&status=delete_success");
        exit();
    } else {
        header("Location: index.php?page=settings&tab=user&status=database_error");
        exit();
    }
}
// ==========================================
// 🚀 PIPELINE C: HANDLE OPERATOR RECORD UPDATE Modifications
// ==========================================
if (isset($_POST['update_user'])) {
    $user_id  = intval($_POST['user_id']);
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $fullname = mysqli_real_escape_string($conn, trim($_POST['fullname']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $role     = mysqli_real_escape_string($conn, $_POST['role']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($fullname)) {
        header("Location: index.php?page=settings&tab=user&status=empty_fields");
        exit();
    }

    // Base query update core parameters string
    $update_sql = "UPDATE users SET username='$username', fullname='$fullname',email= '$email', role='$role' ";

    // If a new string has been typed into the password block, add it to update criteria cleanly
    if (!empty($password)) {
        $update_sql .= ", password='$password' ";
    }

    $update_sql .= " WHERE id = $user_id LIMIT 1";

    if (mysqli_query($conn, $update_sql)) {
        header("Location: index.php?page=settings&tab=user&status=update_success");
        exit();
    } else {
        header("Location: index.php?page=settings&tab=user&status=database_error");
        exit();
    }
}

// Default fallback escape if no parameter matches arriving corridor flags
header("Location: index.php?page=settings&tab=user");
exit();
