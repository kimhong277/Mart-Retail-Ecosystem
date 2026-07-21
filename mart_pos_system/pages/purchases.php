<?php
// mart_pos_system/pages/purchases.php
$conn = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

// 1. Fetch active products along with their retail sale_price for restock calculations
$products_list = mysqli_query($conn, "SELECT id, product_name, quantity, sale_price FROM products WHERE status = 1 ORDER BY product_name ASC");

// 2. Fetch suppliers from dedicated suppliers table
$suppliers_res = mysqli_query($conn, "SELECT id, supplier_name FROM suppliers ORDER BY supplier_name ASC");

// 3. Fetch purchase history JOINED with product and supplier details
$purchase_history = mysqli_query($conn, "
    SELECT p.*, prod.product_name, prod.image, s.supplier_name 
    FROM purchases p 
    LEFT JOIN products prod ON p.product_id = prod.id 
    LEFT JOIN suppliers s ON p.supplier_id = s.id 
    ORDER BY p.purchase_date DESC
");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">🛒 Supply Restock Logs</h2>
        <p class="text-muted small mb-0">Record incoming supplier shipments to increase warehouse stock and generate vendor bills.</p>
    </div>
    <!-- Button to open the interactive restock modal -->
    <button class="btn btn-dark fw-bold shadow-sm px-4 py-2" data-bs-toggle="modal" data-bs-target="#restockModal">
        📦 Record Supply Restock
    </button>
</div>

<!-- Purchase History Logs Table -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary fw-semibold">
                    <tr>
                        <th style="width: 80px;">Log ID</th>
                        <th>Product Details</th>
                        <th>Supplier / Vendor</th>
                        <th class="text-center">Qty Added</th>
                        <th class="text-end">Wholesale Bill Cost</th>
                        <th class="text-end">Date Received</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($purchase_history && mysqli_num_rows($purchase_history) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($purchase_history)): ?>
                            <tr>
                                <td><span class="text-muted font-monospace">#<?php echo $row['id']; ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="/mart-retail-ecosystem/mart_pos_system/assets/images/<?php echo !empty($row['image']) ? $row['image'] : 'default.png'; ?>"
                                            class="rounded border object-fit-contain bg-light" style="width: 35px; height: 35px;"
                                            onerror="this.src='/mart-retail-ecosystem/mart_pos_system/assets/images/default.png';">
                                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($row['product_name'] ?? 'Deleted Product'); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary border px-2.5 py-1.5">
                                        <?php echo htmlspecialchars($row['supplier_name'] ?? 'General Supplier'); ?>
                                    </span>
                                </td>
                                <td class="text-center fw-bold text-success">+<?php echo $row['quantity_added']; ?> pcs</td>
                                <td class="text-end fw-semibold text-dark">$<?php echo number_format($row['purchase_cost'], 2); ?></td>
                                <td class="text-end text-muted small"><?php echo $row['purchase_date']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                📋 No supply restock shipments logged on file yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 📦 INCOMING SUPPLY RESTOCK MODAL -->
<div class="modal fade" id="restockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">📦 Log Incoming Supplier Supply</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="restockForm">
                <div class="modal-body p-4">

                    <!-- Select Product Field -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Select Product Item</label>
                        <select name="product_id" id="productSelect" class="form-select" required onchange="autoCalculate75PercentRule()">
                            <option value="" data-sale="0">-- Choose item to replenish --</option>
                            <?php if ($products_list && mysqli_num_rows($products_list) > 0): ?>
                                <?php while ($p = mysqli_fetch_assoc($products_list)): ?>
                                    <option value="<?php echo $p['id']; ?>" data-sale="<?php echo $p['sale_price']; ?>">
                                        <?php echo htmlspecialchars($p['product_name']); ?>
                                        ($<?php echo number_format($p['sale_price'], 2); ?>/pc | Stock: <?php echo $p['quantity']; ?>)
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Select Supplier Field -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Supplier / Distributor Name</label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="" selected disabled>-- Select Supplier / Distributor --</option>
                            <?php if ($suppliers_res && mysqli_num_rows($suppliers_res) > 0): ?>
                                <?php while ($sup = mysqli_fetch_assoc($suppliers_res)): ?>
                                    <option value="<?php echo $sup['id']; ?>"><?php echo htmlspecialchars($sup['supplier_name']); ?></option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Quantity and Dynamic Total Bill Cost -->
                    <div class="row g-3 align-items-end">
                        <!-- Quantity Added Field -->
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary mb-1">Quantity Added (pcs)</label>
                            <input type="number" id="qtyInput" name="quantity_added" class="form-control" min="1" placeholder="e.g. 100" required oninput="autoCalculate75PercentRule()">
                        </div>

                        <!-- Total Bill Cost Field with Inline Badge -->
                        <div class="col-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold text-secondary mb-0">Total Bill Cost ($)</label>
                                <span class="badge bg-light text-muted border fw-normal" style="font-size: 0.7rem;">75% Wholesale</span>
                            </div>
                            <input type="number" id="totalCostInput" name="purchase_cost" class="form-control fw-bold text-success" step="0.01" min="0" placeholder="0.00" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">Process Restock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Live calculation applying the 75% wholesale cost rule based on product's unit sale_price
    function autoCalculate75PercentRule() {
        const productSelect = document.getElementById('productSelect');
        const qtyInput = document.getElementById('qtyInput');
        const totalCostInput = document.getElementById('totalCostInput');

        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const salePrice = parseFloat(selectedOption.getAttribute('data-sale')) || 0;
        const qty = parseInt(qtyInput.value) || 0;

        if (qty > 0 && salePrice > 0) {
            // Retail Value = Quantity * Sale Price
            // Wholesale Bill = 75% of Retail Value
            const totalWholesaleBill = (qty * salePrice) * 0.75;
            totalCostInput.value = totalWholesaleBill.toFixed(2);
        }
    }

    // Ajax Submission logic for restock modal
    document.getElementById('restockForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const modalElement = document.getElementById('restockModal');
        const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
        modal.hide();

        Swal.fire({
            title: 'Processing Restock...',
            text: 'Writing physical counts and filing vendor bill records...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData(this);

        fetch('/mart-retail-ecosystem/mart_pos_system/process_restock.php', {
                method: 'POST',
                body: formData
            })
            .then(async res => {
                const isJson = res.headers.get('content-type')?.includes('application/json');
                const data = isJson ? await res.json() : null;

                if (!res.ok) {
                    throw new Error((data && data.message) ? data.message : 'Server returned an error');
                }
                return data;
            })
            .then(data => {
                if (data && data.success) {
                    Swal.fire({
                        title: 'Stock & Bills Updated!',
                        text: 'Inventory increased and fresh unpaid bill generated successfully.',
                        icon: 'success',
                        timer: 1800,
                        showConfirmButton: false
                    }).then(() => window.location.reload());
                } else {
                    Swal.fire('Error Processing Restock', (data && data.message) ? data.message : 'Failed to process restock', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('System Error', err.message || 'Communication failure with server.', 'error');
            });
    });
</script>