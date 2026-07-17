<?php
// pages/bills.php
require_once 'db.php';

// READ: Fetch all expense entries ordered by the most recent records first
$expense_sql = "SELECT * FROM expenses ORDER BY id DESC";
$expense_result = mysqli_query($conn, $expense_sql);

// Calculate global aggregate totals for summary card visualization metrics
$total_expense_sum = 0.00;
?>

<div class="container-fluid px-4 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-gray-800 fw-bold">Bills & Expenses Log</h2>
            <p class="text-muted small mb-0">Track recurring utilities, facility rents, and daily shop operational expenditures.</p>
        </div>
        <button type="button" class="btn btn-dark btn-sm fw-semibold px-4 py-2" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
            <i class="bi bi-receipt me-1"></i> Record New Expense
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header py-3 border-0">
            <h6 class="m-0 fw-bold text-dark">Business Cash Outflows Ledger</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4" style="width: 10%;">ID</th>
                            <th>Expense Reference Title</th>
                            <th>Category Type</th>
                            <th>Payment Channel</th>
                            <th>Amount Paid</th>
                            <th>Date & Time Logged</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php if ($expense_result && mysqli_num_rows($expense_result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($expense_result)): ?>
                                <?php $total_expense_sum += floatval($row['amount']); ?>
                                <tr>
                                    <td class="ps-4 text-secondary">#<?= $row['id'] ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['expense_title']) ?></td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1 rounded">
                                            <?= htmlspecialchars($row['category']) ?>
                                        </span>
                                    </td>
                                    <td class="text-secondary"><?= htmlspecialchars($row['payment_method']) ?></td>
                                    <td class="fw-bold text-danger">-$<?= number_format($row['amount'], 2) ?></td>
                                    <td class="text-muted"><?= $row['expense_date'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-file-earmark-spreadsheet display-4 d-block text-black-50 mb-2"></i>
                                    No operational expense files or utility bills logged for this cycle period yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">File Business Outflow Expense</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_bill.php" method="POST">
                <div class="modal-body p-4">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Expense Description Title <span class="text-danger">*</span></label>
                        <input type="text" name="expense_title" class="form-control" placeholder="e.g., Monthly Shop Electricity Bill" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Expense Category Group</label>
                        <select name="expense_category" class="form-select" required>
                            <option value="Utilities">Utilities (Electricity / Water)</option>
                            <option value="Rent">Facility Office Rent</option>
                            <option value="Internet / Telecom">Internet & WiFi Subscriptions</option>
                            <option value="Salaries">Staff Salaries / Compensation</option>
                            <option value="Marketing">Advertising & Promos</option>
                            <option value="Others">Miscellaneous Expenditures</option>
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Amount Outflow ($) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01" placeholder="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Payment Method Channel</label>
                            <select name="payment_method" class="form-select">
                                <option value="Cash">Cash Drawer</option>
                                <option value="ABA Bank QR">ABA Mobile QR Bank Out</option>
                                <option value="Credit Line">Company Credit Card</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_expense" class="btn btn-dark btn-sm px-4">Log Expense Outflow</button>
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
            titleText = 'Operational Expense Recorded and Logged Safely!';
        } else if (statusMsg === 'invalid_input') {
            titleText = 'Invalid Voucher Entries or Amounts Detected!';
            iconType = 'warning';
        } else if (statusMsg === 'error') {
            titleText = 'Data Pipeline Intercepted an Entry Error!';
            iconType = 'error';
        }

        if (titleText !== '') {
            Swal.fire({
                title: titleText,
                icon: iconType,
                confirmButtonColor: '#2D3748',
                timer: 2500
            });
            window.history.replaceState({}, document.title, window.location.pathname + "?page=bills");
        }
    }
</script>