<?php
// store/order-details.php
require_once 'customer-session.php';

if (!isCustomerLoggedIn()) {
    header("Location: customer-login.php");
    exit();
}

$customer = getCurrentCustomer();
$order_id = (int)($_GET['id'] ?? 0);

if ($order_id <= 0) {
    header("Location: my-orders.php");
    exit();
}

$conn = getStoreConnection();

// Fetch order details
$stmt = mysqli_prepare($conn, "SELECT * FROM online_orders WHERE id = ? AND customer_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $order_id, $customer['id']);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$order) {
    header("Location: my-orders.php");
    exit();
}

// Fetch order items
$stmt = mysqli_prepare($conn, "SELECT oi.*, p.product_name FROM online_order_items oi 
                               LEFT JOIN products p ON oi.product_id = p.id 
                               WHERE oi.order_id = ?");
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Mart Online Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>

    <div class="container py-5">
        <div class="mb-4">
            <a href="my-orders.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Back to Orders
            </a>
        </div>

        <div class="row g-4">
            <!-- Order Information -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="fw-bold mb-0">
                            Order #<?php echo htmlspecialchars($order['order_number']); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <small class="text-muted">Order Date</small>
                                <p class="fw-bold"><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Status</small>
                                <p class="fw-bold">
                                    <span class="badge bg-<?php echo ($order['order_status'] === 'completed') ? 'success' : (($order['order_status'] === 'cancelled') ? 'danger' : 'warning'); ?>">
                                        <?php echo ucfirst($order['order_status']); ?>
                                    </span>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <h6 class="fw-bold mb-3">Delivery Address</h6>
                        <p class="text-muted mb-4">
                            <?php echo nl2br(htmlspecialchars($order['shipping_address'] ?? $order['customer_address'])); ?>
                        </p>

                        <h6 class="fw-bold mb-3">Items Ordered</h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th style="width: 80px;">Qty</th>
                                    <th style="width: 100px;">Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['product_name'] ?? 'Unknown Product'); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        <td class="text-end fw-bold">$<?php echo number_format($item['subtotal'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="fw-bold mb-0">Order Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <strong>$<?php echo number_format($order['total_amount'], 2); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="small text-muted">In KHR</span>
                            <strong class="small">៛<?php echo number_format($order['total_amount'] * 4000); ?></strong>
                        </div>

                        <div class="d-flex justify-content-between fs-5 mb-4">
                            <span class="fw-bold">Total</span>
                            <strong class="text-primary">$<?php echo number_format($order['total_amount'], 2); ?></strong>
                        </div>

                        <div class="alert alert-info small">
                            <i class="bi bi-info-circle me-2"></i>
                            For order inquiries, please contact support
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>