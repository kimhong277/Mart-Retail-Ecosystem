<?php
$conn = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
if (!$conn) {
    die("Database engine link down: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

// --- Existing Metrics ---
$income_query = mysqli_query($conn, "SELECT SUM(total_amount) AS total_income FROM sales");
$total_income = floatval(mysqli_fetch_assoc($income_query)['total_income'] ?? 0.00);

$web_income_query = mysqli_query($conn, "SELECT SUM(total_amount) AS web_income FROM sales WHERE order_type = 'online'");
$web_income = floatval(mysqli_fetch_assoc($web_income_query)['web_income'] ?? 0.00);

$pos_income_query = mysqli_query($conn, "SELECT SUM(total_amount) AS pos_income FROM sales WHERE order_type = 'POS'");
$pos_income = floatval(mysqli_fetch_assoc($pos_income_query)['pos_income'] ?? 0.00);

$outcome_query = mysqli_query($conn, "SELECT SUM(amount) AS total_outcome FROM expenses");
$total_outcome = floatval(mysqli_fetch_assoc($outcome_query)['total_outcome'] ?? 0.00);
$current_balance = $total_income - $total_outcome;

// --- 📊 ADVANCED REAL-WORLD ENTERPRISE ANALYTICS ---
$valuation_query = mysqli_query($conn, "SELECT SUM(quantity * sale_price * 0.60) AS asset_value FROM products");
$inventory_valuation = floatval(mysqli_fetch_assoc($valuation_query)['asset_value'] ?? 0.00);

$low_stock_query = mysqli_query($conn, "SELECT COUNT(*) AS low_count FROM products WHERE quantity <= 5");
$low_stock_alerts = intval(mysqli_fetch_assoc($low_stock_query)['low_count'] ?? 0);

$expense_burn_ratio = ($total_income > 0) ? round(($total_outcome / $total_income) * 100, 1) : 0;

$top_products = mysqli_query($conn, "SELECT product_name, quantity, sale_price FROM products ORDER BY quantity DESC LIMIT 3");

// --- 📈 7-DAY TIMELINE TREND QUERIES ---
$sales_trend_query = mysqli_query($conn, "
    SELECT 
        DATE(sale_date) as order_date,
        SUM(CASE WHEN order_type = 'POS' THEN total_amount ELSE 0 END) as pos_sales,
        SUM(CASE WHEN order_type = 'Web' THEN total_amount ELSE 0 END) as web_sales
    FROM sales 
    WHERE sale_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(sale_date)
    ORDER BY order_date ASC
");

$expense_trend_query = mysqli_query($conn, "
    SELECT 
        DATE(expense_date) as exp_date,
        SUM(amount) as daily_expense
    FROM expenses
    WHERE expense_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(expense_date)
    ORDER BY exp_date ASC
");

$dates = [];
$pos_data = [];
$web_data = [];
$expense_data = [];

for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $dates[] = date('M d', strtotime($d));
    $pos_data[$d] = 0;
    $web_data[$d] = 0;
    $expense_data[$d] = 0;
}

while ($row = mysqli_fetch_assoc($sales_trend_query)) {
    if (isset($pos_data[$row['order_date']])) {
        $pos_data[$row['order_date']] = floatval($row['pos_sales']);
        $web_data[$row['order_date']] = floatval($row['web_sales']);
    }
}

while ($row = mysqli_fetch_assoc($expense_trend_query)) {
    if (isset($expense_data[$row['exp_date']])) {
        $expense_data[$row['exp_date']] = floatval($row['daily_expense']);
    }
}
?>

<!-- Main Container Wrapper -->
<div class="container-fluid px-4 py-3">

    <!-- Top Greeting Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="text-primary fw-bold text-uppercase tracking-wider small d-block mb-1" style="letter-spacing: 0.05em;">Ecosystem Overview</span>
            <h2 class="fw-extrabold text-dark tracking-tight mb-0" style="font-weight: 800;">Mart Operational Terminal</h2>
        </div>
        <div class="bg-white px-3 py-2 rounded-3 shadow-sm border small text-muted fw-semibold">
            <i class="bi bi-clock-history text-primary me-1"></i> Live Metric Feed
        </div>
    </div>

    <!-- 🌟 METRICS GRID -->
    <div class="row g-4 mb-4">

        <!-- Total Income Card -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 position-relative overflow-hidden h-100 bg-white" style="border-left: 5px solid #10b981 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase tracking-wide d-block mb-1" style="font-size: 0.75rem;">Total Gross Income</span>
                            <h2 class="fw-extrabold text-dark tracking-tight mb-0" style="font-weight: 700;">$<?php echo number_format($total_income, 2); ?></h2>
                        </div>
                        <div class="p-3 rounded-3 bg-success-subtle text-success">
                            <span class="fs-4 lh-1">📥</span>
                        </div>
                    </div>
                    <div class="pt-3 border-top border-light d-flex justify-content-between align-items-center gap-2">
                        <span class="badge bg-light text-primary border px-3 py-2 small fw-semibold">
                            🏪 POS Counter: <strong>$<?php echo number_format($pos_income, 2); ?></strong>
                        </span>
                        <span class="badge bg-light text-info border px-3 py-2 small fw-semibold">
                            🌐 Web Channel: <strong>$<?php echo number_format($web_income, 2); ?></strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Outcome Card -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 position-relative overflow-hidden h-100 bg-white" style="border-left: 5px solid #ef4444 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase tracking-wide d-block mb-1" style="font-size: 0.75rem;">Total Expenses Out</span>
                            <h2 class="fw-extrabold text-dark tracking-tight mb-0" style="font-weight: 700;">$<?php echo number_format($total_outcome, 2); ?></h2>
                        </div>
                        <div class="p-3 rounded-3 bg-danger-subtle text-danger">
                            <span class="fs-4 lh-1">📤</span>
                        </div>
                    </div>
                    <div class="pt-3 border-top border-light text-muted small">
                        <i class="bi bi-info-circle text-danger me-1"></i> Sum of shop bills & material expenses
                    </div>
                </div>
            </div>
        </div>

        <!-- Current liquid Vault Cash Card -->
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card border-0 shadow-lg rounded-4 text-white h-100" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="text-white-50 small fw-bold text-uppercase tracking-wide d-block mb-1" style="font-size: 0.75rem;">Current Cash Balance</span>
                            <h2 class="fw-extrabold text-white tracking-tight mb-0" style="font-weight: 700;">$<?php echo number_format($current_balance, 2); ?></h2>
                        </div>
                        <div class="p-3 rounded-3 bg-secondary bg-opacity-25 text-white">
                            <span class="fs-4 lh-1">🏦</span>
                        </div>
                    </div>
                    <div class="pt-3 border-top border-secondary border-opacity-25 text-success-emphasis small fw-semibold d-flex align-items-center">
                        <span class="spinner-grow spinner-grow-sm text-success me-2" role="status" style="width: 0.65rem; height: 0.65rem;"></span>
                        <span class="text-success small">Real-time dynamic checkout ledger</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- 📈 GRAPH LAYER PANEL -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="p-2 bg-primary-subtle rounded text-primary small">📊</span>
                <h5 class="fw-bold text-dark mb-0">Multi-Channel Cash Performance Trend</h5>
            </div>
            <p class="text-muted small mb-0">7-day historical timeline comparing register transaction velocity against operations costs.</p>
        </div>
        <div class="card-body p-4">
            <div style="position: relative; height: 340px; width: 100%;">
                <canvas id="cashFlowChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Advanced Analytical KPI Breakdown Framework -->
    <div class="row g-4 mb-5">

        <!-- Left Hand Column: Operational Ratios & Vital Signs -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-1">
                    <div class="d-flex align-items-center gap-2">
                        <span class="p-2 bg-secondary-subtle rounded text-dark small">🧠</span>
                        <h6 class="fw-bold text-dark mb-0">Financial Health & Resource Ratios</h6>
                    </div>
                </div>
                <div class="card-body px-4 pb-4 pt-2">

                    <!-- Capital Value Assets Meter -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between text-muted small mb-1 fw-semibold">
                            <span>Inventory Assets Valuation</span>
                            <span class="text-dark fw-bold">$<?php echo number_format($inventory_valuation, 2); ?></span>
                        </div>
                        <div class="progress rounded-pill" style="height: 8px;">
                            <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: 65%;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted text-opacity-50 font-monospace" style="font-size: 0.7rem;">Capital tied up in wholesale floor stock items.</small>
                    </div>

                    <!-- Expense Capital Burn Rate Metric -->
                    <div class="mb-2">
                        <div class="d-flex justify-content-between text-muted small mb-1 fw-semibold">
                            <span>Operational Expense Burn Rate</span>
                            <span class="<?php echo $expense_burn_ratio > 50 ? 'text-danger' : 'text-success'; ?> fw-bold">
                                <?php echo $expense_burn_ratio; ?>%
                            </span>
                        </div>
                        <div class="progress rounded-pill" style="height: 8px;">
                            <div class="progress-bar <?php echo $expense_burn_ratio > 50 ? 'bg-danger' : 'bg-success'; ?> rounded-pill"
                                role="progressbar" style="width: <?php echo min($expense_burn_ratio, 100); ?>%;"></div>
                        </div>
                        <small class="text-muted text-opacity-50 font-monospace" style="font-size: 0.7rem;">Percentage of raw revenue consumed by business overhead logs.</small>
                    </div>

                </div>
            </div>
        </div>

        <!-- Right Hand Column: Live Inventory Health & Fast Movers -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-1">
                    <div class="d-flex align-items-center gap-2">
                        <span class="p-2 bg-warning-subtle rounded text-warning-emphasis small">⚠️</span>
                        <h6 class="fw-bold text-dark mb-0">Warehouse Discrepancies & Velocity</h6>
                    </div>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="row g-3">

                        <!-- Warehouse Risk Stat Badge Widget -->
                        <div class="col-6">
                            <div class="p-3 rounded-3 border <?php echo $low_stock_alerts > 0 ? 'bg-danger-subtle border-danger-subtle' : 'bg-light'; ?> text-center">
                                <span class="text-muted small d-block mb-1 fw-semibold">Low Stock Assets</span>
                                <span class="h3 fw-bold <?php echo $low_stock_alerts > 0 ? 'text-danger' : 'text-dark'; ?>">
                                    <?php echo $low_stock_alerts; ?>
                                </span>
                                <small class="d-block text-muted small mt-1">Requires reorder logs</small>
                            </div>
                        </div>

                        <!-- Channels Health Verification Frame -->
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 border text-center h-100 d-flex flex-column justify-content-center">
                                <span class="text-muted small d-block mb-1 fw-semibold">Active Channels</span>
                                <span class="h4 fw-bold text-dark mb-0">2 / 2 Active</span>
                                <small class="text-success small fw-semibold mt-1">
                                    <span class="d-inline-block bg-success rounded-circle me-1" style="width:6px; height:6px;"></span> POS + Web Online
                                </small>
                            </div>
                        </div>

                    </div>

                    <!-- Snapshot List: Fast Moving Target Items Display Mini Table -->
                    <div class="mt-4 pt-3 border-top border-light">
                        <span class="text-muted small fw-bold text-uppercase tracking-wider d-block mb-2" style="font-size: 0.7rem;">Catalog Stock Volume Leaders</span>
                        <div class="d-flex flex-column gap-2">
                            <?php while ($item = mysqli_fetch_assoc($top_products)): ?>
                                <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded-2 border border-light">
                                    <span class="small fw-semibold text-dark"><?php echo htmlspecialchars($item['product_name']); ?></span>
                                    <span class="badge bg-secondary-subtle text-secondary rounded px-2 py-1 font-monospace small">
                                        <?php echo $item['quantity']; ?> available units
                                    </span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- 📈 CHART GENERATION ENGINE ---
        const ctx = document.getElementById('cashFlowChart').getContext('2d');

        const chartLabels = <?php echo json_encode(array_values($dates)); ?>;
        const posSalesDataset = <?php echo json_encode(array_values($pos_data)); ?>;
        const webSalesDataset = <?php echo json_encode(array_values($web_data)); ?>;
        const expensesDataset = <?php echo json_encode(array_values($expense_data)); ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                        label: '🏪 POS Shop Sales',
                        data: posSalesDataset,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.05)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: '🌐 Online Web Sales',
                        data: webSalesDataset,
                        borderColor: '#0dcaf0',
                        backgroundColor: 'transparent',
                        borderWidth: 3,
                        tension: 0.3,
                        borderDash: [5, 5]
                    },
                    {
                        label: '📉 Operational Expenses',
                        data: expensesDataset,
                        borderColor: '#dc3545',
                        backgroundColor: 'transparent',
                        borderWidth: 2.5,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                weight: 'bold',
                                size: 12
                            },
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) {
                                    label += '$' + context.parsed.y.toLocaleString(undefined, {
                                        minimumFractionDigits: 2
                                    });
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            color: 'rgba(0,0,0,0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // --- ⚙️ ROUTING & SYSTEM NOTIFICATION ENGINE ---
        const urlParams = new URLSearchParams(window.location.search);
        const statusMsg = urlParams.get('status');

        if (statusMsg) {
            let titleText = '';
            let textBody = '';
            let iconType = 'success';
            let triggerAlert = true;
            let autoCloseTimer = 2500;

            switch (statusMsg) {
                case 'inserted':
                    titleText = 'Product Appended!';
                    textBody = 'The item has been successfully added to the master catalog.';
                    break;
                case 'updated':
                    titleText = 'Changes Saved!';
                    textBody = 'Product specification fields have been updated successfully.';
                    break;
                case 'deleted':
                    titleText = 'Item Removed';
                    textBody = 'The product profile has been permanently deleted from the database.';
                    iconType = 'warning';
                    break;
                case 'restock_success':
                    titleText = 'Stock Replenished!';
                    textBody = 'Physical inventory counts increased and an unpaid supplier bill has been generated.';
                    break;
                case 'bill_paid':
                    titleText = 'Invoice Settled!';
                    textBody = 'The supplier bill is marked as Paid and a cash outflow has been logged to Expenses.';
                    break;
                case 'audit_logged':
                    titleText = 'Audit Discrepancy Logged!';
                    textBody = 'The internal stock correction ledger has updated your inventory counts.';
                    iconType = 'info';
                    break;
                case 'sale_success':
                    titleText = 'Transaction Complete!';
                    textBody = 'POS invoice created, stock deducted, and income updated.';
                    break;
                case 'unauthorized':
                    titleText = 'Access Denied!';
                    textBody = 'You do not have the required administrative clearance to access this module.';
                    iconType = 'error';
                    autoCloseTimer = null;
                    break;
                case 'error':
                    titleText = 'Execution Failure!';
                    textBody = urlParams.get('msg') || 'An unexpected operational database error occurred.';
                    iconType = 'error';
                    autoCloseTimer = null;
                    break;
                default:
                    triggerAlert = false;
                    break;
            }

            if (triggerAlert) {
                Swal.fire({
                    title: titleText,
                    text: textBody,
                    icon: iconType,
                    confirmButtonColor: iconType === 'error' ? '#0f172a' : '#2D3748',
                    timer: autoCloseTimer
                });

                const activePage = urlParams.get('page') || 'dashboard';
                window.history.replaceState({}, document.title, window.location.pathname + "?page=" + activePage);
            }
        }
    });
</script>