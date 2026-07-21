<?php
// store/order_success.php
require_once 'customer-session.php';

if (!isCustomerLoggedIn()) {
    header("Location: index.php");
    exit();
}

$customer = getCurrentCustomer();
$order_number = isset($_GET['order']) ? htmlspecialchars($_GET['order']) : '';

if (empty($order_number)) {
    header("Location: my-orders.php");
    exit();
}

// Fetch order from database to confirm
$conn = getStoreConnection();
$stmt = mysqli_prepare($conn, "SELECT * FROM online_orders WHERE order_number = ? AND customer_id = ?");
mysqli_stmt_bind_param($stmt, "si", $order_number, $customer['id']);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_close($conn);

if (!$order) {
    header("Location: my-orders.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - Mart Online Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .success-container {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            text-align: center;
            padding: 50px 30px;
        }

        .success-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: bounce 0.6s;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .order-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 12px;
            margin: 30px 0;
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>

    <div class="success-container p-3">
        <div class="success-card shadow-lg">
            <div class="success-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>

            <h1 class="fw-bold mb-2">Order Confirmed!</h1>
            <p class="lead mb-0">Thank you for your purchase</p>

            <div class="order-info">
                <div class="mb-3">
                    <p class="mb-1 small opacity-75">Order Number</p>
                    <h5 class="fw-bold mb-0"><?php echo $order_number; ?></h5>
                </div>
                <div class="mb-3">
                    <p class="mb-1 small opacity-75">Total Amount</p>
                    <h4 class="fw-bold mb-0">$<?php echo number_format($order['total_amount'], 2); ?></h4>
                </div>
                <div>
                    <p class="mb-1 small opacity-75">Delivery to</p>
                    <p class="mb-0"><?php echo htmlspecialchars($order['customer_phone']); ?></p>
                </div>
            </div>

            <p class="text-white-50 mb-4">
                <i class="bi bi-info-circle me-2"></i>
                We'll send you a confirmation email shortly
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <a href="my-orders.php" class="btn btn-light btn-lg rounded-3 fw-bold">
                    <i class="bi bi-box-seam me-2"></i>View My Orders
                </a>
                <a href="index.php" class="btn btn-outline-light btn-lg rounded-3 fw-bold">
                    <i class="bi bi-shop me-2"></i>Continue Shopping
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>