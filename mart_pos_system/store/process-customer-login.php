<?php
// store/process-customer-login.php
require_once 'customer-session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: customer-login.php");
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$remember_me = isset($_POST['remember_me']);

if (empty($email) || empty($password)) {
    header("Location: customer-login.php?error=" . urlencode("Email and password are required"));
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: customer-login.php?error=" . urlencode("Invalid email format"));
    exit();
}

$conn = getStoreConnection();

// Check if customer exists
$stmt = mysqli_prepare($conn, "SELECT id, full_name, email, phone, password FROM online_customers WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    mysqli_close($conn);
    header("Location: customer-login.php?error=" . urlencode("Email not found. Please create an account."));
    exit();
}

$customer = mysqli_fetch_assoc($result);

// Verify password
if (!password_verify($password, $customer['password'])) {
    mysqli_close($conn);
    header("Location: customer-login.php?error=" . urlencode("Invalid password"));
    exit();
}

// Set session variables
$_SESSION['customer_id'] = $customer['id'];
$_SESSION['customer_name'] = $customer['full_name'];
$_SESSION['customer_email'] = $customer['email'];
$_SESSION['customer_phone'] = $customer['phone'];

// Set remember me cookie (30 days)
if ($remember_me) {
    setcookie('customer_id', $customer['id'], time() + (30 * 24 * 60 * 60), '/store/');
}

mysqli_close($conn);

// Redirect to cart if coming from checkout, otherwise to store
$redirect = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : 'index.php';
header("Location: " . $redirect);
exit();
