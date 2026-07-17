<?php
session_start();
require_once  '../../config/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_login'])) {

    // Clean form input values
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: ../../login.php?error=invalid");
        exit();
    }

    // 1. Fetch user by email using a secure Prepared Statement
    $query = "SELECT user_id, username, email, password_hash FROM users WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Check if user exists
        if ($user = mysqli_fetch_assoc($result)) {

            // 2. Cryptographically verify the password text matches the database hash
            if (password_verify($password, $user['password_hash'])) {

                // 3. Password matches! Regenerate Session ID for security hijacking protection
                session_regenerate_id(true);

                // Set the session state variables
                $_SESSION['user_id']  = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email']    = $user['email'];

                mysqli_stmt_close($stmt);
                mysqli_close($conn);

                // Success! Route them to your storefront catalog homepage
                header("Location: ../../index.php?page=catalog");
                exit();
            }
        }

        // Clean up statement if email wasn't found or password verification failed
        mysqli_stmt_close($stmt);
    }

    // Fallback failure route if authentication parameters missed matches
    mysqli_close($conn);
    header("Location: ../../login.php?error=invalid");
    exit();
} else {
    header("Location: ../../login.php");
    exit();
}
