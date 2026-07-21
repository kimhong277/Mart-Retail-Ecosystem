<?php
// store/process-customer-register.php
require_once 'customer-session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: customer-register.php");
    exit();
}

$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');
$agree_terms = isset($_POST['agree_terms']);

// Validation
$errors = [];

if (empty($full_name)) {
    $errors[] = "Full name is required";
}

if (empty($email)) {
    $errors[] = "Email is required";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}

if (empty($phone)) {
    $errors[] = "Phone number is required";
}

if (empty($password)) {
    $errors[] = "Password is required";
} elseif (strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters";
} elseif (!preg_match('/[A-Z]/', $password)) {
    $errors[] = "Password must contain at least one uppercase letter";
} elseif (!preg_match('/[0-9]/', $password)) {
    $errors[] = "Password must contain at least one number";
}

if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match";
}

if (!$agree_terms) {
    $errors[] = "You must agree to the Terms of Service";
}

if (!empty($errors)) {
    header("Location: customer-register.php?error=" . urlencode(implode(", ", $errors)));
    exit();
}

$conn = getStoreConnection();

// Check if email already exists
$stmt = mysqli_prepare($conn, "SELECT id FROM online_customers WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    mysqli_close($conn);
    header("Location: customer-register.php?error=" . urlencode("Email already registered. Please login or use a different email."));
    exit();
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert customer
$stmt = mysqli_prepare($conn, "INSERT INTO online_customers (full_name, email, phone, password, created_at) VALUES (?, ?, ?, ?, NOW())");
mysqli_stmt_bind_param($stmt, "ssss", $full_name, $email, $phone, $hashed_password);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_close($conn);
    header("Location: customer-register.php?error=" . urlencode("Registration failed. Please try again."));
    exit();
}

$customer_id = mysqli_insert_id($conn);

// Auto-login the customer
$_SESSION['customer_id'] = $customer_id;
$_SESSION['customer_name'] = $full_name;
$_SESSION['customer_email'] = $email;
$_SESSION['customer_phone'] = $phone;

mysqli_close($conn);

// Redirect to store
header("Location: index.php?success=" . urlencode("Account created successfully!"));
exit();
