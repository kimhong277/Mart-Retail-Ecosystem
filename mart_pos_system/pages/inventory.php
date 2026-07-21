<?php
$conn = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
mysqli_set_charset($conn, "utf8mb4");

// Fetch active inventory levels with category information
$inventory = mysqli_query($conn, "SELECT p.*, c.category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.product_name ASC");

// Save products array for the adjustment modal dropdown selection
$adjustment_products = mysqli_query($conn, "SELECT id, product_name, quantity FROM products");
$adjustment_logs = mysqli_query($conn, "SELECT sa.*, p.product_name 
    FROM stock_adjustments sa 
    JOIN products p ON sa.product_id = p.id 
    ORDER BY sa.adjusted_at DESC LIMIT 50");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">📊 Real-Time Inventory</h2>
        <p class="text-muted small mb-0">Live overview of stock assets. Use the action button to log audit discrepancies.</p>
    </div>
    <button class="btn btn-warning fw-bold shadow-sm px-4 py-2" data-bs-toggle="modal" data-bs-target="#adjustmentModal">
        ⚙️ Log Stock Correction
    </button>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary fw-semibold">
                    <tr>
                        <th>Barcode</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th class="text-center">Current Stock</th>
                        <th>Retail Price</th>
                        <th class="text-end">Alert Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($inventory) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($inventory)): ?>
                            <tr>
                                <td><span class="text-muted small font-monospace"><?php echo htmlspecialchars($row['barcode']); ?></span></td>
                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['product_name']); ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></span></td>
                                <td class="text-center fw-bold <?php echo $row['quantity'] <= 5 ? 'text-danger' : 'text-dark'; ?>">
                                    <?php echo $row['quantity']; ?> pcs
                                </td>
                                <td class="fw-semibold text-primary">$<?php echo number_format($row['sale_price'], 2); ?></td>
                                <td class="text-end">
                                    <?php if ($row['quantity'] == 0): ?>
                                        <span class="badge bg-danger text-white px-2 py-1">Out of Stock</span>
                                    <?php elseif ($row['quantity'] <= 5): ?>
                                        <span class="badge bg-warning text-dark px-2 py-1">Low Stock Alert</span>
                                    <?php else: ?>
                                        <span class="badge bg-success text-white px-2 py-1">In Stock</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No inventory records registered.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ⚙️ STOCK CORRECTION AUDIT MODAL -->
<div class="modal fade" id="adjustmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">⚙️ File Stock Audit Correction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="adjustmentForm">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Select Target Product</label>
                        <select name="product_id" class="form-select" required>
                            <option value="">-- Choose item --</option>
                            <?php while ($p = mysqli_fetch_assoc($adjustment_products)): ?>
                                <option value="<?php echo $p['id']; ?>">
                                    <?php echo htmlspecialchars($p['product_name']); ?> (Current: <?php echo $p['quantity']; ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary">Adjustment Type</label>
                            <select name="type" class="form-select" required>
                                <option value="Correction">Data Typo Correction (+/-)</option>
                                <option value="Damaged">Damaged / Broken (-)</option>
                                <option value="Stolen">Theft / Shrinkage (-)</option>
                                <option value="Expired">Expired Goods (-)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary">Quantity Impact</label>
                            <input type="number" name="quantity_changed" class="form-control" placeholder="e.g., 5 or -3" required>
                            <small class="text-muted">Use negative numbers to subtract stock.</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Official Audit Reason / Notes</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Explain why this change is occurring..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4 shadow-sm">Apply Discrepancy Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
<hr class="my-5 opacity-25">

<div class="mb-3">
    <h3 class="fw-bold text-dark mb-1">📜 Stock Discrepancy & Correction Logs</h3>
    <p class="text-muted small">Complete auditable paper trail documenting manual adjustments, damages, or shrinkage records.</p>
</div>

<div class="card border-0 shadow-sm rounded-3 mb-5">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary fw-semibold">
                    <tr>
                        <th style="width: 80px;">Audit ID</th>
                        <th>Product Details</th>
                        <th>Adjustment Type</th>
                        <th class="text-center">Impact Qty</th>
                        <th>Official Reason / Explanatory Notes</th>
                        <th class="text-end">Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($adjustment_logs) > 0): ?>
                        <?php while ($log = mysqli_fetch_assoc($adjustment_logs)): ?>
                            <tr>
                                <td><span class="text-muted font-monospace">#<?php echo $log['id']; ?></span></td>
                                <td class="fw-semibold text-dark"><?php echo htmlspecialchars($log['product_name']); ?></td>
                                <td>
                                    <?php
                                    // Dynamic coloring based on the type of adjustment
                                    $badge_class = 'bg-secondary';
                                    if ($log['type'] == 'Damaged') $badge_class = 'bg-danger-subtle text-danger';
                                    if ($log['type'] == 'Stolen') $badge_class = 'bg-dark text-light';
                                    if ($log['type'] == 'Correction') $badge_class = 'bg-info-subtle text-info-emphasis';
                                    if ($log['type'] == 'Expired') $badge_class = 'bg-warning-subtle text-warning-emphasis';
                                    ?>
                                    <span class="badge border px-2.5 py-1.5 <?php echo $badge_class; ?>">
                                        <?php echo $log['type']; ?>
                                    </span>
                                </td>
                                <td class="text-center fw-bold <?php echo $log['quantity_changed'] < 0 ? 'text-danger' : 'text-success'; ?>">
                                    <?php echo $log['quantity_changed'] > 0 ? '+' : ''; ?><?php echo $log['quantity_changed']; ?> pcs
                                </td>
                                <td>
                                    <div class="text-truncate text-secondary small" style="max-width: 350px;" title="<?php echo htmlspecialchars($log['reason']); ?>">
                                        <?php echo htmlspecialchars($log['reason']); ?>
                                    </div>
                                </td>
                                <td class="text-end text-muted small"><?php echo $log['adjusted_at']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted small">
                                📋 System ledger is empty. No internal adjustments recorded yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('adjustmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const modal = bootstrap.Modal.getInstance(document.getElementById('adjustmentModal'));
        modal.hide();

        Swal.fire({
            title: 'Updating Ledger Records...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('/mart-retail-ecosystem/mart_pos_system/process_adjustment.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Audit Complete!',
                        text: 'Master ledger adjusted and inventory balanced.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => window.location.reload());
                } else {
                    Swal.fire('Audit Failed', data.message, 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Communication error with master engine.', 'error'));
    });
</script>