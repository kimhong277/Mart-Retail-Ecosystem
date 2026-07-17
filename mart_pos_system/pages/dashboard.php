<?php
// pages/dashboard.php
require_once 'db.php';

// 1. Fetch Total Products Count
$prod_count_query = mysqli_query($conn, "SELECT COUNT(id) AS total FROM products");
$prod_row = mysqli_fetch_assoc($prod_count_query);
$total_products = intval($prod_row['total'] ?? 0);

// 2. Fetch Cumulative Sales Revenue
$sales_revenue_query = mysqli_query($conn, "SELECT SUM(total_amount) AS total FROM sales");
$sales_row = mysqli_fetch_assoc($sales_revenue_query);
$total_revenue = floatval($sales_row['total'] ?? 0.00);

// 3. Fetch Cumulative Expenses
$expenses_query = mysqli_query($conn, "SELECT SUM(amount) AS total FROM expenses");
$exp_row = mysqli_fetch_assoc($expenses_query);
$total_expenses = floatval($exp_row['total'] ?? 0.00);

// 4. Fetch Active Categories Count
$cat_count_query = mysqli_query($conn, "SELECT COUNT(id) AS total FROM categories WHERE status = 1");
$cat_row = mysqli_fetch_assoc($cat_count_query);
$total_categories = intval($cat_row['total'] ?? 0);

// 5. Fetch Recent Sales Log for display preview
$recent_sales = mysqli_query($conn, "SELECT * FROM sales ORDER BY id DESC LIMIT 5");
?>

<div class="container-fluid px-4 pt-4">
    <div class="mb-4">
        <h2 class="h3 mb-0 text-gray-800 fw-bold">Management Dashboard</h2>
        <p class="text-muted small">Overview summary analysis of live store systems operations.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Active Catalog Items</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0"><?= $total_products ?> SKUs</h3>
                    </div>
                    <div class="bg-dark text-white rounded p-3 fs-4">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Gross Sales Income</span>
                        <h3 class="fw-bold text-success mt-1 mb-0">$<?= number_format($total_revenue, 2) ?></h3>
                    </div>
                    <div class="bg-success-subtle text-success rounded p-3 fs-4">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Logged Store Expenses</span>
                        <h3 class="fw-bold text-danger mt-1 mb-0">-$<?= number_format($total_expenses, 2) ?></h3>
                    </div>
                    <div class="bg-danger-subtle text-danger rounded p-3 fs-4">
                        <i class="bi bi-receipt-cutoff"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Product Groups</span>
                        <h3 class="fw-bold text-primary mt-1 mb-0"><?= $total_categories ?> Lines</h3>
                    </div>
                    <div class="bg-primary-subtle text-primary rounded p-3 fs-4">
                        <i class="bi bi-tags"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark"><i class="bi bi-lightning-charge me-2"></i>Recent Register Checkouts</h6>
                    <a href="index.php?page=transactions" class="btn btn-sm btn-outline-secondary py-0 small px-2">View All Logs</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Invoice No</th>
                                    <th>Method</th>
                                    <th>Total Billed</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php if ($recent_sales && mysqli_num_rows($recent_sales) > 0): ?>
                                    <?php while ($sale = mysqli_fetch_assoc($recent_sales)): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-dark"><?= htmlspecialchars($sale['invoice_no']) ?></td>
                                            <td class="text-secondary"><?= htmlspecialchars($sale['payment_method']) ?></td>
                                            <td class="fw-bold text-success">$<?= number_format($sale['total_amount'], 2) ?></td>
                                            <td class="text-muted"><?= date('M d, Y H:i', strtotime($sale['sale_date'])) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">No sale records generated inside system registries yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 p-4 bg-white h-100">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-sliders me-2"></i>Quick Action Panels</h6>
                <div class="d-grid gap-2">
                    <a href="index.php?page=sales" class="btn btn-primary py-2 fw-semibold text-start px-3"><i class="bi bi-plus-circle me-2"></i>Open POS Cashier Screen</a>
                    <a href="index.php?page=products" class="btn btn-primary py-2 fw-semibold text-start px-3"><i class="bi bi-box-seam me-2"></i>Manage Inventory Items</a>
                    <a href="index.php?page=bills" class="btn btn-primary py-2 fw-semibold text-start px-3"><i class="bi bi-receipt me-2"></i>Record Utility Expense</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // 1. MUST declare these first so the dashboard knows how to read the URL!
    const urlParams = new URLSearchParams(window.location.search);
    const statusMsg = urlParams.get('status');

    if (statusMsg) {
        let titleText = '';
        let iconType = 'success';

        if (statusMsg === 'inserted') {
            titleText = 'Product Appended to Inventory Catalog!';
        } else if (statusMsg === 'updated') {
            titleText = 'Product Spec Fields Saved!';
        } else if (statusMsg === 'unauthorized') {
            // 🛑 Handled safely here now!
            Swal.fire({
                title: 'Access Denied!',
                text: 'You do not have the required administrative clearance to view or manage staff accounts.',
                icon: 'error',
                confirmButtonColor: '#0f172a'
            });
        } else if (statusMsg === 'error') {
            titleText = 'Operation Execution Failure!';
            iconType = 'error';
        }

        if (titleText !== '' && statusMsg !== 'unauthorized') {
            Swal.fire({
                title: titleText,
                icon: iconType,
                confirmButtonColor: '#2D3748',
                timer: 2500
            });
        }

        // Clean up parameters smoothly
        if (titleText !== '') {
            window.history.replaceState({}, document.title, window.location.pathname + "?page=dashboard");
        }
    }
</script>