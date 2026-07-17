<?php
// pages/inventory.php
require_once 'db.php';

// Simple SQL Join Query to combine product, category, and brand details into one clean table list
$sql = "SELECT p.id, p.barcode, p.product_name, p.quantity, p.price, c.category_name, b.brand_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN brands b ON p.brand_id = b.id
        ORDER BY p.id DESC";

$result = mysqli_query($conn, $sql);
?>

<div class="container-fluid px-4 pt-4">
    <div class="mb-4">
        <h2 class="h3 mb-0 text-gray-800 fw-bold">Inventory Stock Levels</h2>
        <p class="text-muted small text-danger fw-semibold">
            <i class="bi bi-info-circle-fill"></i> Inventory is read-only. Products come from Products page; stock updates via Purchases.
        </p>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Category Name</th>
                            <th>Barcode</th>
                            <th>Product Name</th>
                            <th style="width: 15%;">Stock</th>
                            <th>Retail Price</th>
                            <th>Brand</th>
                            <th style="width: 12%;" class="text-center">Alert Status</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="ps-4 text-secondary">
                                        <?php echo htmlspecialchars($row['category_name'] ?? 'No Category'); ?>
                                    </td>

                                    <td>
                                        <span class="text-muted"><?php echo htmlspecialchars($row['barcode'] ?: '------'); ?></span>
                                    </td>

                                    <td class="fw-bold text-dark">
                                        <?php echo htmlspecialchars($row['product_name']); ?>
                                    </td>

                                    <td class="fw-bold">
                                        <?php echo $row['quantity']; ?>
                                    </td>

                                    <td class="fw-bold text-success">
                                        $<?php echo number_format($row['price'], 2); ?>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['brand_name'] ?? 'No Brand'); ?></span>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($row['quantity'] <= 0): ?>
                                            <span class="badge bg-danger text-white px-2 py-1 rounded" style="font-size: 0.75rem;">
                                                <i class="bi bi-exclamation-triangle-fill"></i> Zero Stock
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success-subtle text-success px-2 py-1 rounded" style="font-size: 0.75rem;">
                                                In Stock
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-box-seam display-4 d-block text-black-50 mb-2"></i>
                                    No items found inside your storage database catalog. Please add items on the Products page first.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>