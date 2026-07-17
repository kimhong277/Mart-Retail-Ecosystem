<?php
// logout.php
session_start();

// 1. Unset all session variables
$_SESSION = array();

// 2. Clear the session cookie if it exists to completely wipe traces from the browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Destroy the session on the server entirely
session_destroy();

// 4. Redirect the operator back to the clean login screen gate
header("Location: login.php");
exit();
