<?php
// process_account.php
$conn = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
if (!$conn) {
    die("Database transaction access error: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ACTION A: Update details generated from the dynamic "Manage" modal
    if (isset($_POST['update_operator'])) {
        $user_id      = intval($_POST['user_id']);
        $fullname     = mysqli_real_escape_string($conn, $_POST['fullname']);
        $email        = mysqli_real_escape_string($conn, $_POST['email']);
        $role         = mysqli_real_escape_string($conn, $_POST['role']);
        $status       = intval($_POST['status']);
        $new_password = $_POST['new_password'];

        // Prevent modification of system master administrative account configuration container
        if ($user_id === 1) {
            header("Location: index.php?page=accounts&status=unauthorized");
            exit;
        }

        // Evaluate whether the administrator requested a password override
        if (!empty($new_password)) {
            // Securely hash the new credential signature string
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $update_sql = "UPDATE users SET fullname = '$fullname', email = '$email', password = '$hashed_password', role = '$role', status = $status WHERE id = $user_id";
        } else {
            // Keep the user's legacy database password parameter completely intact
            $update_sql = "UPDATE users SET fullname = '$fullname', email = '$email', role = '$role', status = $status WHERE id = $user_id";
        }

        if (mysqli_query($conn, $update_sql)) {
            header("Location: index.php?page=accounts&status=updated");
        } else {
            header("Location: index.php?page=accounts&status=error");
        }
        exit;
    }

    // ACTION B: Provision a completely new worker registration node entry
    if (isset($_POST['provision_user'])) {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
        $email    = mysqli_real_escape_string($conn, $_POST['email']);
        $role     = mysqli_real_escape_string($conn, $_POST['role']);
        $password = $_POST['password'];

        // Securely hash the initial login token verification payload
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $insert_sql = "INSERT INTO users (username, fullname, email, password, role, status, created_at) 
                       VALUES ('$username', '$fullname', '$email', '$hashed_password', '$role', 1, NOW())";

        if (mysqli_query($conn, $insert_sql)) {
            header("Location: index.php?page=accounts&status=inserted");
        } else {
            header("Location: index.php?page=accounts&status=error");
        }
        exit;
    }
    // ACTION C: Secure purge handling for user metadata node tracking lines
    if (isset($_POST['delete_operator'])) {
        $user_id = intval($_POST['user_id']);

        // Extra protection level check: Block deletion of master administrative system user #1
        if ($user_id === 1) {
            header("Location: index.php?page=accounts&status=unauthorized");
            exit;
        }

        $delete_sql = "DELETE FROM users WHERE id = $user_id";

        if (mysqli_query($conn, $delete_sql)) {
            header("Location: index.php?page=accounts&status=deleted");
        } else {
            header("Location: index.php?page=accounts&status=error");
        }
        exit;
    }
} else {
    header("Location: index.php?page=accounts");
    exit;
}
