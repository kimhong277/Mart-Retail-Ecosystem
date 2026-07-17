<?php
session_start();
require_once 'config/db.php';
include 'includes/header.php';

$cart_items = $_SESSION['cart'] ?? [];
$subtotal = 0.00;
?>

<div class="container py-4">
    <h2 class="mb-4 fw-bold text-dark">Your Shopping Cart</h2>

    <?php if (!empty($cart_items)):
        // Extract array keys to query only the specific products sitting in the cart session
        $ids = implode(',', array_keys($cart_items));
        $query = "SELECT * FROM products WHERE product_id IN ($ids)";
        $result = mysqli_query($conn, $query);
    ?>
        <div class="row g-4">
            <!-- Items Grid Column -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 p-3">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th>Product Details</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($item = mysqli_fetch_assoc($result)):
                                    $qty = $cart_items[$item['product_id']];
                                    $item_total = $item['price'] * $qty;
                                    $subtotal += $item_total;
                                ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                        </td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        <td class="fw-semibold"><?php echo $qty; ?></td>
                                        <td class="fw-bold text-primary">$<?php echo number_format($item_total, 2); ?></td>
                                        <td>
                                            <a href="actions/cart/remove_item.php?id=<?php echo $item['product_id']; ?>" class="text-danger">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary Panel Box Column -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 bg-light p-4">
                    <h5 class="fw-bold text-dark mb-3">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-bold text-dark">$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2 mb-4">
                        <span class="fw-bold">Estimated Total</span>
                        <span class="fs-4 fw-bold text-primary">$<?php echo number_format($subtotal, 2); ?></span>
                    </div>

                    <!-- Form routing directly to our automated processing engine -->
                    <form action="actions/order/process_checkout.php" method="POST">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                            Proceed to Checkout & Pay
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="text-muted">
                <i class="fa-solid fa-cart-shopping fa-3x mb-3"></i>
                <h5>Your shopping cart is currently empty!</h5>
                <a href="index.php?page=catalog" class="btn btn-primary btn-sm mt-3 px-4">Browse Accessories</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>