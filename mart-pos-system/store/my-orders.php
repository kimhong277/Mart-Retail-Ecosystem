<?php
// store/my-orders.php
require_once 'customer-session.php';

if (!isCustomerLoggedIn()) {
    header("Location: customer-login.php");
    exit();
}

$customer = getCurrentCustomer();
$conn = getStoreConnection();

// Fetch customer orders
$stmt = mysqli_prepare($conn, "SELECT * FROM online_orders WHERE customer_id = ? ORDER BY created_at DESC");
mysqli_stmt_bind_param($stmt, "i", $customer['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Mart Online Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>

    <div class="container py-5">
        <div class="mb-4">
            <h2 class="fw-bold mb-2">
                <i class="bi bi-box-seam me-2 text-primary"></i>My Orders
            </h2>
            <p class="text-muted">View and track your orders</p>
        </div>

        <?php if (empty($orders)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                You haven't placed any orders yet. <a href="index.php" class="alert-link">Start shopping</a>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($orders as $order): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="card-title fw-bold mb-0">
                                        Order #<?php echo htmlspecialchars($order['order_number']); ?>
                                    </h6>
                                    <span class="badge bg-<?php echo ($order['order_status'] === 'completed') ? 'success' : (($order['order_status'] === 'cancelled') ? 'danger' : 'warning'); ?>">
                                        <?php echo ucfirst($order['order_status']); ?>
                                    </span>
                                </div>

                                <div class="small text-muted mb-3">
                                    <div><i class="bi bi-calendar me-1"></i><?php echo date('M d, Y', strtotime($order['created_at'])); ?></div>
                                    <div><i class="bi bi-phone me-1"></i><?php echo htmlspecialchars($order['customer_phone']); ?></div>
                                </div>

                                <hr>

                                <div class="mb-3">
                                    <small class="text-muted">Total Amount</small>
                                    <div class="fs-5 fw-bold text-primary">
                                        $<?php echo number_format($order['total_amount'], 2); ?>
                                    </div>
                                </div>

                                <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary w-100">
                                    <i class="bi bi-eye me-1"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Store
            </a>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>