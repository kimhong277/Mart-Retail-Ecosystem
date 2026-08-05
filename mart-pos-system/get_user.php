<?php
// get_user.php
session_start();
// require_once 'db.php';
$conn_user = mysqli_connect('localhost', 'root', '', 'mart_pos_system');


// Access Check: Only let logged-in Admins/Managers call data endpoints
if (!isset($_SESSION['user_role']) || (strtolower($_SESSION['user_role']) !== 'admin' && strtolower($_SESSION['user_role']) !== 'manager')) {
    echo json_encode(['error' => 'Unauthorized entry breach blocked.']);
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch data safely
    $result = mysqli_query($conn_user, "SELECT id, username, fullname, role FROM users WHERE id = $id LIMIT 1");

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Return data cleanly as JSON format back to JavaScript
        echo json_encode($user);
        exit();
    }
}

echo json_encode(['error' => 'User signature node not detected.']);
exit();
