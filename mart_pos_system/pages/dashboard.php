<?php
// mart_pos_system/dashboard.php (or pages/dashboard.php)

// 1. Establish direct connection to the POS database procedurally
$host = 'localhost';
$db_user = 'root';
$db_pass = ''; // Your local MySQL password
$conn_pos = mysqli_connect($host, $db_user, $db_pass, 'mart_pos_system');

if (!$conn_pos) {
    die("Dashboard database access failure: " . mysqli_connect_error());
}
mysqli_set_charset($conn_pos, "utf8mb4");

// 2. Query the system to sum up total live online checkouts from the sales table
// We look specifically for payment methods tagged as 'Online Web'
$sql_metrics = "SELECT SUM(total_amount) AS total_revenue, COUNT(id) AS order_count 
                FROM sales 
                WHERE payment_method = 'Online Web'";

$res_metrics = mysqli_query($conn_pos, $sql_metrics);

$total_online_revenue = 0.00;
$total_online_orders = 0;

if ($res_metrics) {
    $row = mysqli_fetch_assoc($res_metrics);
    $total_online_revenue = floatval($row['total_revenue']);
    $total_online_orders = intval($row['order_count']);
}
?>

<div class="container-fluid px-4 pt-4">
    <div class="row my-4">
        <!-- E-Commerce Revenue Dynamic Card -->
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card bg-success text-white border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-semibold opacity-75 small">E-Store Cash-In</h6>
                            <h2 class="display-6 fw-bold my-2">$<?php echo number_format($total_online_revenue, 2); ?></h2>
                            <p class="mb-0 fs-7 opacity-75">Live digital checkout revenue</p>
                        </div>
                        <div class="fs-1 opacity-50">🌐</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- E-Commerce Order Counter Dynamic Card -->
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card bg-primary text-white border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase fw-semibold opacity-75 small">Online Orders</h6>
                            <h2 class="display-6 fw-bold my-2"><?php echo $total_online_orders; ?></h2>
                            <p class="mb-0 fs-7 opacity-75">Processed web transactions</p>
                        </div>
                        <div class="fs-1 opacity-50">📦</div>
                    </div>
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