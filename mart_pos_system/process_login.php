<?php
// process_login.php
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$conn = mysqli_connect($host, $db_user, $db_pass, 'mart_pos_system');
session_start();

// 🚀 MATCH 1: Catches name="normal_login" from your form button element
if (isset($_POST['normal_login'])) {

    // 🚀 MATCH 2: Catches name="username" from your text input field perfectly!
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'] ?? '';

    if (!empty($username)) {

        // SEPARATION LOGIC: Search database registry by username OR email string match

        $sql = "SELECT * FROM users WHERE username = '$username' OR email = '$username' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // 1️⃣ ACCESS CONTROL CHECK: Ensure the staff profile isn't suspended
            if (intval($user['status']) === 0) {
                header("Location: login.php?status=suspended");
                exit();
            }

            // 2️⃣ CLOUD ACCOUNT SAFETY GATE: Prevent manual bypass of Google Auth accounts
            if (empty($user['password']) && !empty($user['google_id'])) {
                header("Location: login.php?status=use_google_auth");
                exit();
            }

            // 3️⃣ PASSWORD VALIDATION LAYER: Check bcrypt encrypted hashes for local loggers
            if (password_verify($password, $user['password']) || $password === $user['password']) {

                // PROVISION PRIVILEGE SESSION VARIABLES (DUAL KEY SYNC)
                $_SESSION['id']        = $user['id'];       // Backup key
                $_SESSION['user_id']   = $user['id'];       // Main system key
                $_SESSION['username']  = $user['username'];
                $_SESSION['fullname']  = $user['fullname'];
                $_SESSION['email']     = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                // Forward straight to active dashboard workspace terminal triggering your SweetAlert
                header("Location: index.php?page=dashboard&msg=success");
                exit();
            } else {
                // Password validation completely failed
                header("Location: login.php?status=invalid_password");
                exit();
            }
        } else {
            // User row entry not found in SQL registry
            header("Location: login.php?status=invalid");
            exit();
        }
    }
}
header("Location: login.php");
exit();
