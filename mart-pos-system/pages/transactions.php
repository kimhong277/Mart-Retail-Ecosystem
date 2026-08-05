<?php
// pages/transactions.php
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$conn = mysqli_connect($host, $db_user, $db_pass, 'mart_pos_system');

$cash_in  = 0.00;
$cash_out = 0.00;

// 1. Compute dynamic aggregate Cash Inflow from Sales records
$sales_sum_query = mysqli_query($conn, "SELECT SUM(total_amount) AS total FROM sales");
if ($sales_sum_query) {
    $sales_row = mysqli_fetch_assoc($sales_sum_query);
    $cash_in = floatval($sales_row['total'] ?? 0.00);
}

// 2. Compute dynamic aggregate Cash Outflow from Expenses records
$expense_sum_query = mysqli_query($conn, "SELECT SUM(amount) AS total FROM expenses");
if ($expense_sum_query) {
    $expense_row = mysqli_fetch_assoc($expense_sum_query);
    $cash_out = floatval($expense_row['total'] ?? 0.00);
}

// 3. Formulate Net Operational Cash Balance
$cash_balance = $cash_in - $cash_out;

// 4. COMBINED CHRONOLOGICAL LEDGER: Pull Sales & Expenses ordered by date
// Using SQL UNION lets us merge both records seamlessly for audit logs
$ledger_sql = "
    (SELECT 'Income' AS trans_type, invoice_no AS ref_no, 'Customer POS Sale' AS details, payment_method, total_amount AS amount, sale_date AS trans_date FROM sales)
    UNION ALL
    (SELECT 'Expense' AS trans_type, CAST(id AS CHAR) AS ref_no, expense_title AS details, payment_method, amount, expense_date AS trans_date FROM expenses)
    ORDER BY trans_date DESC
";
$ledger_result = mysqli_query($conn, $ledger_sql);
?>

<div class="container-fluid px-4 pt-4">
    <div class="mb-4">
        <h2 class="h3 mb-0 text-gray-800 fw-bold">Financial Transactions</h2>
        <p class="text-muted small">Real-time consolidated audit matrix across revenue channels and operational overheads.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 border-start border-success border-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Total Cash In (Revenue)</span>
                        <h3 class="fw-bold text-success mt-1 mb-0">$<?= number_format($cash_in, 2) ?></h3>
                    </div>
                    <div class="bg-success-subtle text-success rounded p-3 fs-4">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 border-start border-danger border-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Total Cash Out (Expenses)</span>
                        <h3 class="fw-bold text-danger mt-1 mb-0">-$<?= number_format($cash_out, 2) ?></h3>
                    </div>
                    <div class="bg-danger-subtle text-danger rounded p-3 fs-4">
                        <i class="bi bi-graph-down-arrow"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 border-start border-primary border-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Net Register Balance</span>
                        <h3 class="fw-bold <?= $cash_balance >= 0 ? 'text-primary' : 'text-warning' ?> mt-1 mb-0">$<?= number_format($cash_balance, 2) ?></h3>
                    </div>
                    <div class="bg-primary-subtle text-primary rounded p-3 fs-4">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="m-0 fw-bold text-dark"><i class="bi bi-clock-history me-2"></i>Master Cash Stream Audit Logs</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Type</th>
                            <th>Reference Key</th>
                            <th>Transaction Description Details</th>
                            <th>Channel</th>
                            <th>Amount Value ($)</th>
                            <th>Timestamp Logged</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php if ($ledger_result && mysqli_num_rows($ledger_result) > 0): ?>
                            <?php while ($trans = mysqli_fetch_assoc($ledger_result)): ?>
                                <tr>
                                    <td class="ps-4">
                                        <?php if ($trans['trans_type'] === 'Income'): ?>
                                            <span class="badge bg-success-subtle text-success px-2 py-1 rounded">
                                                <i class="bi bi-arrow-down-left-square-fill me-1"></i> Cash-In
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded">
                                                <i class="bi bi-arrow-up-right-square-fill me-1"></i> Cash-Out
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-mono text-secondary"><?= htmlspecialchars($trans['ref_no']) ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($trans['details']) ?></td>
                                    <td class="text-secondary"><?= htmlspecialchars($trans['payment_method']) ?></td>
                                    <td class="fw-bold <?= $trans['trans_type'] === 'Income' ? 'text-success' : 'text-danger' ?>">
                                        <?= $trans['trans_type'] === 'Income' ? '+$' : '-$' ?><?= number_format($trans['amount'], 2) ?>
                                    </td>
                                    <td class="text-muted"><?= $trans['trans_date'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-cash-stack display-4 d-block text-black-50 mb-2"></i>
                                    No financial movements processed inside database registries for this reporting period yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>