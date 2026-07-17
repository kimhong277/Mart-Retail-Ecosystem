<?php
// mart_pos_system/pages/manage_products.php

// Connect to the POS database (Adjust path if db.php is available in root)
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$conn_pos = mysqli_connect($host, $db_user, $db_pass, 'mart_pos_system');

if (!$conn_pos) {
    die("Database access failure: " . mysqli_connect_error());
}
mysqli_set_charset($conn_pos, "utf8mb4");

// Fetch products alongside their category name
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.id DESC";
$result = mysqli_query($conn_pos, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inventory Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-4">

    <div class="container bg-white p-4 rounded shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0 text-dark">📦 Product Inventory Master List</h3>
            <!-- Trigger Modal Button -->
            <button class="btn btn-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#addProductModal">
                ➕ Add New Product
            </button>
        </div>

        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Barcode</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock Qty</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><strong>#<?php echo $row['id']; ?></strong></td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><span class="badge bg-secondary font-monospace"><?php echo htmlspecialchars($row['barcode'] ?? 'N/A'); ?></span></td>
                            <td><?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></td>
                            <td class="fw-bold text-primary">$<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <span class="badge <?php echo $row['quantity'] > 10 ? 'bg-success' : 'bg-danger'; ?> fs-6">
                                    <?php echo $row['quantity']; ?> available
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php echo $row['status'] == 1 ? 'bg-light text-success border border-success' : 'bg-light text-muted'; ?>">
                                    <?php echo $row['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No products registered in the backend catalog.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title fw-bold">Add New Catalog Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-toggle="modal" data-bs-target="#addProductModal"></button>
                </div>
                <!-- Action routes directly to the script file sitting in your root folder -->
                <form action="../add_product.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Product Name</label>
                            <input type="text" name="product_name" class="form-control" placeholder="e.g., Whole Grain Bread" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label fw-semibold">Price ($)</label>
                                <input type="number" name="price" step="0.01" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="col">
                                <label class="form-label fw-semibold">Initial Stock Qty</label>
                                <input type="number" name="quantity" class="form-control" placeholder="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Barcode String</label>
                            <input type="text" name="barcode" class="form-control" placeholder="Scan or type numbers">
                        </div>
                        <input type="hidden" name="category_id" value="1"> <!-- Default template category -->
                        <input type="hidden" name="brand_id" value="1"> <!-- Default template brand -->
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="submit_product" class="btn btn-primary fw-bold">Save to Database</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>