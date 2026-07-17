<?php
// pages/purchases.php
require_once 'db.php';

// READ: Fetch products so cashiers know exactly what items can be restocked
$products_dropdown = mysqli_query($conn, "SELECT id, product_name, quantity FROM products ORDER BY product_name ASC");

// READ: Fetch current products list to view active storage numbers in the table below
$inventory_grid = mysqli_query($conn, "SELECT p.id, p.product_name, p.quantity, p.price, c.category_name 
                                       FROM products p 
                                       LEFT JOIN categories c ON p.category_id = c.id 
                                       ORDER BY p.id DESC");
?>

<div class="container-fluid px-4 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-gray-800 fw-bold">Stock Purchases / In-flow</h2>
            <p class="text-muted small mb-0">Record factory supplier restock shipments to replenish product inventory volumes.</p>
        </div>
        <button type="button" class="btn btn-dark btn-sm fw-semibold px-4 py-2" data-bs-toggle="modal" data-bs-target="#restockModal">
            <i class="bi bi-box-seam-fill me-1"></i> Record Supply Restock
        </button>
    </div>

    <div class="card shadow-sm border-0 bg-transparent">
        <div class="card-header py-3 border-0 bg-transparent">
            <h6 class="m-0 fw-bold bg-transparent">Current Storage Levels Status</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4" style="width: 10%;">ID</th>
                            <th>Product Name</th>
                            <th>Group Category</th>
                            <th>Current Warehouse Stock</th>
                            <th>Retail Price</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php if ($inventory_grid && mysqli_num_rows($inventory_grid) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($inventory_grid)): ?>
                                <tr>
                                    <td class="ps-4 text-secondary">#<?= $row['id'] ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td class="text-secondary"><?= htmlspecialchars($row['category_name'] ?? 'Unassigned') ?></td>
                                    <td>
                                        <?php if ($row['quantity'] <= 0): ?>
                                            <span class="text-danger fw-bold"><i class="bi bi-exclamation-circle-fill"></i> 0 pcs (Out of stock)</span>
                                        <?php else: ?>
                                            <span class="text-dark fw-bold"><?= $row['quantity'] ?> pcs</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold text-success">$<?= number_format($row['price'], 2) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">No catalog files mapped inside system storage layers yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="restockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Intake Supplier Shipment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_purchase.php" method="POST">
                <div class="modal-body p-4">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Target Item to Restock <span class="text-danger">*</span></label>
                        <select name="product_id" class="form-select" required>
                            <option value="">-- Choose Product Item --</option>
                            <?php while ($prod = mysqli_fetch_assoc($products_dropdown)): ?>
                                <option value="<?= $prod['id'] ?>">
                                    <?= htmlspecialchars($prod['product_name']) ?> (Current: <?= $prod['quantity'] ?> pcs)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Manufacturer / Wholesaler Supplier</label>
                        <input type="text" name="supplier_name" class="form-control" placeholder="e.g., CP Wholesale Supply Co.">
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Quantity Received <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control" min="1" value="50" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Total Bulk Cost ($)</label>
                            <input type="number" name="purchase_cost" class="form-control" step="0.01" value="0.00" min="0">
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_purchase" class="btn btn-dark btn-sm px-4">Apply Restock Quantities</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const statusMsg = urlParams.get('status');

    if (statusMsg) {
        let titleText = '';
        let iconType = 'success';

        if (statusMsg === 'success') {
            titleText = 'Inventory Quantities Replenished Successfully!';
        } else if (statusMsg === 'invalid_input') {
            titleText = 'Invalid Quantity or Parameter Entry!';
            iconType = 'warning';
        } else if (statusMsg === 'error') {
            titleText = 'Restock Query Pipeline Failure!';
            iconType = 'error';
        }

        if (titleText !== '') {
            Swal.fire({
                title: titleText,
                icon: iconType,
                confirmButtonColor: '#2D3748',
                timer: 2500
            });
            window.history.replaceState({}, document.title, window.location.pathname + "?page=purchases");
        }
    }
</script>