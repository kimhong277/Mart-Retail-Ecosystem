<?php
// store/customer-profile.php
require_once 'customer-session.php';

if (!isCustomerLoggedIn()) {
    header("Location: customer-login.php");
    exit();
}

$customer = getCurrentCustomer();
$message = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($full_name) || empty($phone)) {
        $message = '<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>All fields are required</div>';
    } else {
        $conn = getStoreConnection();
        $stmt = mysqli_prepare($conn, "UPDATE online_customers SET full_name = ?, phone = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $full_name, $phone, $customer['id']);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['customer_name'] = $full_name;
            $_SESSION['customer_phone'] = $phone;
            $customer = getCurrentCustomer();
            $message = '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Profile updated successfully</div>';
        } else {
            $message = '<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>Update failed. Please try again.</div>';
        }
        mysqli_close($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Mart Online Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
    </style>
</head>

<body class="bg-light">
    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header profile-header">
                        <h4 class="fw-bold mb-0">
                            <i class="bi bi-person-circle me-2"></i>My Profile
                        </h4>
                    </div>

                    <div class="card-body p-4">
                        <?php echo $message; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Full Name</label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Email Address</label>
                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($customer['email']); ?>" disabled>
                                <small class="text-muted">Email cannot be changed</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 fw-bold rounded-3">
                                <i class="bi bi-check-circle me-2"></i>Save Changes
                            </button>
                        </form>

                        <hr class="my-4">

                        <div>
                            <p class="small text-muted mb-2">Account created: <?php echo date('M d, Y', strtotime($customer['created_at'] ?? date('Y-m-d'))); ?></p>
                            <a href="change-password.php" class="btn btn-outline-secondary btn-sm w-100 mb-2">
                                <i class="bi bi-key me-2"></i>Change Password
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-3 text-center">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Store
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>