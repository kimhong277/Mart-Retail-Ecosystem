<?php
// mart_pos_system/print_invoice.php
$conn = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
mysqli_set_charset($conn, "utf8mb4");

$quote_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch master quotation details
$quote_query = mysqli_query($conn, "SELECT * FROM quotations WHERE id = $quote_id");
$quote = mysqli_fetch_assoc($quote_query);

if (!$quote) {
    die("Error: Target transactional invoice record statement not found.");
}

// Fetch individual structural item strings grouped within the invoice layout
$items_query = mysqli_query($conn, "
    SELECT qi.*, p.product_name 
    FROM quotation_items qi 
    LEFT JOIN products p ON qi.product_id = p.id 
    WHERE qi.quotation_id = $quote_id
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice - <?php echo $quote['quote_no']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
        }

        .invoice-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            max-width: 850px;
            margin: 30px auto;
            padding: 50px;
        }

        /* 🖨️ CRITICAL CSS PRINT AGENT RESET CONTROLS */
        @media print {
            body {
                background: #fff;
                color: #000;
            }

            .invoice-card {
                box-shadow: none;
                max-width: 100%;
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Non-printable top action routing panel -->

        <div style="max-width: 850px; margin: 20px auto;" class=" d-flex justify-content-between align-items-center no-print" style="max-width: 850px; margin: 20px auto 0 auto;">
            <a href="index.php?page=quotations" class="btn btn-secondary fw-bold px-3">← Back to Terminal</a>
            <button class="btn btn-dark fw-bold px-4" onclick="window.print()"><i class="bi bi-printer"></i> Print Out</button>
        </div>

        <!-- Printable Invoice Sheet Layout Frame -->
        <div class="invoice-card">
            <div class="row align-items-center mb-5">
                <div class="col-6">
                    <h3 class="fw-extrabold text-dark tracking-tight mb-1" style="font-weight:800;">MART RETAIL SYSTEM</h3>
                    <p class="text-muted small mb-0">Phnom Penh, Cambodia<br>Contact: support@martdomain.com</p>
                </div>
                <div class="col-6 text-end">
                    <h2 class="text-uppercase fw-bold text-secondary tracking-wide mb-1">INVOICE</h2>
                    <!-- <span class="badge bg-success-subtle text-success border px-3 py-1.5 rounded-pill fw-bold">APPROVED STATUS</span> -->
                </div>
            </div>

            <hr class="my-4 opacity-50">

            <div class="row mb-5">
                <div class="col-6">
                    <span class="text-muted text-uppercase small fw-bold d-block mb-2">Billed / Consigned To:</span>
                    <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($quote['customer_name']); ?></h5>
                    <p class="text-muted small mb-0">Phone: <?php echo htmlspecialchars($quote['customer_phone'] ?? 'N/A'); ?></p>
                </div>
                <div class="col-6 text-end">
                    <span class="text-muted text-uppercase small fw-bold d-block mb-2">Billing Reference:</span>
                    <p class="mb-1"><strong>Invoice No:</strong> <span class="font-monospace text-primary fw-bold"><?php echo $quote['quote_no']; ?></span></p>
                    <p class="small text-muted mb-0"><strong>Issued Date:</strong> <?php echo date('M d, Y', strtotime($quote['created_at'])); ?></p>
                </div>
            </div>

            <!-- Line Item Ledger Structure Table -->
            <table class="table table-striped align-middle mb-4">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" style="width: 50px;">#</th>
                        <th scope="col">Product Name</th>
                        <th scope="col" class="text-center" style="width: 100px;">Qty</th>
                        <th scope="col" class="text-end" style="width: 130px;">Unit Price</th>
                        <th scope="col" class="text-end" style="width: 140px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 1;
                    while ($item = mysqli_fetch_assoc($items_query)):
                    ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>
                            <td class="fw-semibold text-dark"><?php echo htmlspecialchars($item['product_name'] ?? 'Custom Resource Item'); ?></td>
                            <td class="text-center font-monospace"><?php echo $item['quantity']; ?></td>
                            <td class="text-end font-monospace">$<?php echo number_format($item['unit_price'], 2); ?></td>
                            <td class="text-end fw-bold text-dark font-monospace">$<?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Total Summarization Stack Block -->
            <div class="row justify-content-end pt-3">
                <div class="col-5">
                    <div class="d-flex justify-content-between p-2 bg-light border-bottom rounded-top">
                        <span class="text-muted small fw-semibold">Subtotal:</span>
                        <span class="font-monospace">$<?php echo number_format($quote['total_amount'], 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between p-2 bg-light border-bottom">
                        <span class="text-muted small fw-semibold">Tax (0%):</span>
                        <span class="font-monospace">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between p-3 bg-dark text-white rounded-bottom align-items-center">
                        <span class="fw-bold uppercase small">Total Balance Due:</span>
                        <h4 class="fw-bold mb-0 font-monospace text-white">$<?php echo number_format($quote['total_amount'], 2); ?></h4>
                    </div>
                </div>
            </div>

            <!-- Footer terms boilerplate statement block -->
            <div class="text-center text-muted small mt-5 pt-5 border-top border-light">
                <p class="mb-1 fw-bold">Thank you for your business transactions with Mart Retail!</p>
                <p class="text-opacity-50">Payments are typically settled within regular corporate windows via cash clearing or target credit lines.</p>
            </div>
        </div>
    </div>

</body>

</html>