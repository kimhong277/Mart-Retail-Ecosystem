<?php
session_start();
// Unset all session keys
$_SESSION = array();

// Destroy the actual session file on the server
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
session_destroy();

// Route them right back to the clean login screen
header("Location: login.php");
exit();
