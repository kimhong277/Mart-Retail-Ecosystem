<?php
// Ensure this script is only accessed through your database-connected application
if (!isset($conn)) {
    require_once 'config/db.php';
}

// Start session tracking if it hasn't been started in your core index wrapper yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cart_items = $_SESSION['cart'] ?? [];
$subtotal = 0.00;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Active Shopping Session Cart</h2>
    <span class="badge bg-primary p-2">Items inside: <?php echo !empty($cart_items) ? array_sum($cart_items) : 0; ?></span>
</div>

<?php if (!empty($cart_items)):
    // Extract array keys to query only the specific products sitting in the cart session
    $ids = implode(',', array_keys($cart_items));
    $query = "SELECT * FROM products WHERE product_id IN ($ids)";
    $result = mysqli_query($conn, $query);
?>
    <div class="row g-4">
        <!-- Items Grid Column -->
        <div class="col-xl-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-3">Product Name</th>
                                    <th>Unit Price</th>
                                    <th>Quantity</th>
                                    <th>Total Price</th>
                                    <th class="text-center pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($item = mysqli_fetch_assoc($result)):
                                    $qty = $cart_items[$item['product_id']];
                                    $item_total = $item['price'] * $qty;
                                    $subtotal += $item_total;
                                ?>
                                    <tr>
                                        <td class="fw-semibold text-dark ps-3"><?php echo htmlspecialchars($item['product_name']); ?></td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        <td class="fw-bold"><?php echo $qty; ?></td>
                                        <td class="fw-bold text-success">$<?php echo number_format($item_total, 2); ?></td>
                                        <td class="text-center pe-3">
                                            <!-- Points back to a background operation file -->
                                            <a href="actions/cart/remove_item.php?id=<?php echo $item['product_id']; ?>" class="btn btn-outline-danger btn-sm">
                                                <i class="fa-solid fa-trash"></i> Remove
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Checkout Column Panel -->
        <div class="col-xl-4">
            <div class="card shadow-sm border-0 bg-light p-4">
                <h5 class="fw-bold text-dark mb-3">Order Invoice Summary</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal:</span>
                    <span class="fw-bold text-dark">$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-4">
                    <span class="fw-bold text-dark">Total Amount:</span>
                    <span class="fs-4 fw-bold text-primary">$<?php echo number_format($subtotal, 2); ?></span>
                </div>

                <!-- Form routing directly to your automated processing engine code -->
                <form action="actions/order/process_checkout.php" method="POST">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                        Confirm Purchase & Pay
                    </button>
                </form>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Empty Fallback layout state graphic -->
    <div class="card border-0 shadow-sm text-center py-5">
        <div class="card-body text-muted py-5">
            <i class="fa-solid fa-cart-shopping fa-3x mb-3"></i>
            <h5>Your testing cart is currently empty!</h5>
            <p class="small">Add products from your product catalog tab to see them populate here.</p>
        </div>
    </div>
<?php endif; ?>