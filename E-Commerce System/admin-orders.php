<?php
require_once 'config/db.php';
// include 'includes/header.php';

// Fetch all orders along with the user's username/email using an INNER JOIN
$query = "SELECT o.order_id, o.order_date, o.total_amount, o.status, u.email 
          FROM orders o 
          INNER JOIN users u ON o.user_id = u.user_id 
          ORDER BY o.order_date DESC";
// Fetch all customers for the dropdown selection mapping
$user_dropdown_result = mysqli_query($conn, "SELECT user_id, email FROM users ORDER BY email ASC");

// Fetch all accessories for the product selection mapping
$product_dropdown_result = mysqli_query($conn, "SELECT product_id, product_name, price FROM products WHERE stock_quantity > 0 ORDER BY product_name ASC");
$result = $conn->query($query);
?>
<div class="container-fluid px-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">Customer Orders Dashboard</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrderModal">
            <i class="fa-solid fa-plus me-2"></i>Add New Order
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">Order ID</th>
                            <th>Customer Email</th>
                            <th>Date & Time</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th class="text-center pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($order = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="fw-bold ps-3">#<?php echo $order['order_id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['email']); ?></td>
                                    <td><?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?></td>
                                    <td class="fw-bold text-success">
                                        $<?php echo number_format($order['total_amount'], 2); ?>
                                    </td>
                                    <td>
                                        <?php
                                        // Dynamically color-code your ENUM values using Bootstrap badges
                                        $status = $order['status'];
                                        $badgeClass = 'bg-warning text-dark'; // Default Pending
                                        if ($status === 'Paid') $badgeClass = 'bg-success';
                                        if ($status === 'Shipped') $badgeClass = 'bg-info text-dark';
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>">
                                            <?php echo $status; ?>
                                        </span>
                                    </td>
                                    <td class="text-center pe-3">
                                        <!-- Sends the specific order ID to a detailed breakdown sub-page -->
                                        <a href="order-invoice.php?id=<?php echo $order['order_id']; ?>" class="btn btn-outline-dark btn-sm">
                                            View Invoice
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    📁 No customer orders have been placed yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- ADD NEW ORDER INVOICE MODAL -->
<div class="modal fade" id="addOrderModal" tabindex="-1" aria-labelledby="addOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark" id="addOrderModalLabel">Create Manual Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="actions/order/add_order.php" method="POST">
                <div class="modal-body p-4">

                    <!-- 1. SELECT CUSTOMER -->
                    <div class="mb-3">
                        <label for="user_id" class="form-label small fw-bold">Assign to Customer Account</label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="" selected disabled>-- Select a Customer Email --</option>
                            <?php
                            if ($user_dropdown_result && mysqli_num_rows($user_dropdown_result) > 0) {
                                while ($user_row = mysqli_fetch_assoc($user_dropdown_result)) {
                                    echo "<option value='" . $user_row['user_id'] . "'>" . htmlspecialchars($user_row['email']) . "</option>";
                                }
                            } else {
                                echo "<option value='' disabled>No registered customers found.</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- 2. SELECT PRODUCT ACCESSSORY -->
                    <div class="mb-3">
                        <label for="product_id" class="form-label small fw-bold">Select Product Accessory</label>
                        <select class="form-select" id="product_id" name="product_id" required>
                            <option value="" selected disabled>-- Select an Item --</option>
                            <?php
                            if ($product_dropdown_result && mysqli_num_rows($product_dropdown_result) > 0) {
                                while ($prod_row = mysqli_fetch_assoc($product_dropdown_result)) {
                                    echo "<option value='" . $prod_row['product_id'] . "'>" . htmlspecialchars($prod_row['product_name']) . " ($" . number_format($prod_row['price'], 2) . ")</option>";
                                }
                            } else {
                                echo "<option value='' disabled>No in-stock accessories available.</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- 3. SELECT QUANTITY -->
                    <div class="row">
                        <div class="col-6">
                            <label for="quantity" class="form-label small fw-bold">Purchase Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1" required>
                        </div>
                        <div class="col-6">
                            <label for="status" class="form-label small fw-bold">Order Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Pending" selected>Pending</option>
                                <option value="Paid">Paid</option>
                                <option value="Shipped">Shipped</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit_manual_order" class="btn btn-primary px-4">Generate Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$conn->close();
// include 'includes/footer.php';
?>