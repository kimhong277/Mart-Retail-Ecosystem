<?php
// mart_pos_system/toggle_category_status.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['current_status'])) {

    $conn_pos = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
    if (!$conn_pos) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    mysqli_set_charset($conn_pos, "utf8mb4");

    $id = intval($_POST['id']);
    // If current status is 1 (Active), toggle to 0 (Disabled), and vice versa
    $new_status = intval($_POST['current_status']) == 1 ? 0 : 1;

    $sql = "UPDATE categories SET status = $new_status WHERE id = $id";

    if (mysqli_query($conn_pos, $sql)) {
        echo json_encode(['success' => true, 'new_status' => $new_status]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn_pos)]);
    }

    mysqli_close($conn_pos);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request variables']);
}
