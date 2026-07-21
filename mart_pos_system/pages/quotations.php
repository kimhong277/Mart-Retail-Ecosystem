<?php
// pages/quotations.php
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$conn = mysqli_connect($host, $db_user, $db_pass, 'mart_pos_system');

if (!$conn) {
    die("Database engine link down: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

// READ 1: Fetch warehouse product details for the lookup select box
$products_list = mysqli_query($conn, "SELECT id, product_name, sale_price FROM products ORDER BY product_name ASC");

// READ 2: Fetch all saved quotes from your database table to view in the log below
$saved_quotes = mysqli_query($conn, "SELECT * FROM quotations ORDER BY id DESC");
?>

<div class="container-fluid px-4 pt-4">
    <div class="mb-4">
        <span class="text-primary fw-bold text-uppercase small d-block mb-1" style="letter-spacing: 0.05em;">Commercial Pipelines</span>
        <h2 class="h3 mb-0 text-dark fw-bold">Commercial Quotations & Estimates</h2>
        <p class="text-muted small">Generate professional pricing estimates for bulk buyers without altering live storage stock numbers.</p>
    </div>

    <form action="process_quotation.php" method="POST" class="mb-5">
        <div class="row g-4">

            <!-- Left Hand Panel: Estimate Builder Core Layout -->
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm border-0 p-4 bg-white rounded-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-calculator me-2 text-primary"></i>Estimate Builder</h5>

                    <div class="row g-3 align-items-end mb-4">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-secondary">Lookup Product Catalog</label>
                            <select id="qtProductSelector" class="form-select">
                                <option value="" disabled selected hidden>-- Choose Product Item --</option>
                                <?php while ($prod = mysqli_fetch_assoc($products_list)): ?>
                                    <option value="<?= $prod['id'] ?>" data-name="<?= htmlspecialchars($prod['product_name']) ?>" data-price="<?= $prod['sale_price'] ?>">
                                        <?= htmlspecialchars($prod['product_name']) ?> - $<?= number_format($prod['sale_price'], 2) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-dark w-100 fw-semibold" onclick="addQuoteRowItem()">
                                <i class="bi bi-plus-circle me-1"></i> Add Line Item
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase fw-semibold">
                                <tr>
                                    <th>Product Item Designation</th>
                                    <th style="width: 25%;">Qty Required</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                    <th class="text-center" style="width: 10%;">Remove</th>
                                </tr>
                            </thead>
                            <tbody class="small" id="quoteBody">
                                <tr id="quotePlaceholder">
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bi bi-file-earmark-text display-4 d-block text-black-50 mb-2"></i>
                                        No estimate lines built. Choose products above.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Hand Panel: Estimate Properties Summary Frame -->
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border-0 p-4 bg-white rounded-4 position-sticky" style="top: 20px; border-top: 4px solid #0d6efd !important;">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Estimate Properties</h5>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Client / Business Name</label>
                        <input type="text" name="customer_name" class="form-control" placeholder="e.g., Acacia Group Intl" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <span class="text-muted small fw-medium">Estimated Subtotal:</span>
                        <span class="fw-bold text-dark font-monospace" id="displayQtSubtotal">$0.00</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="h6 fw-bold mb-0 text-dark">Estimated Total:</span>
                        <span class="h4 fw-bold mb-0 text-primary font-monospace" id="displayQtGrandTotal">$0.00</span>
                    </div>

                    <input type="hidden" name="total_amount" id="formQtTotalAmount" value="0.00">

                    <button type="submit" name="save_quotation" class="btn btn-primary w-100 fw-bold py-3 text-uppercase shadow-sm rounded-3">
                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Save Proforma Quotation
                    </button>
                </div>
            </div>

        </div>
    </form>

    <!-- Historical Saved Log Table View Framework -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-header bg-white py-3 border-0 px-4">
            <h5 class="m-0 fw-bold text-dark"><i class="bi bi-folder-check me-2 text-primary"></i>Saved Quotation Logs</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase fw-semibold">
                        <tr>
                            <th class="ps-4">Quotation No</th>
                            <th>Customer / Client</th>
                            <th>Estimated Total</th>
                            <th>Pipeline Status</th>
                            <th>Date Created</th>
                            <th class="text-center" style="width: 20%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php if ($saved_quotes && mysqli_num_rows($saved_quotes) > 0): ?>
                            <?php while ($quote = mysqli_fetch_assoc($saved_quotes)): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-primary font-monospace"><?= htmlspecialchars($quote['quote_no']) ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($quote['customer_name']) ?></td>
                                    <td class="fw-bold text-success font-monospace">$<?= number_format($quote['total_amount'], 2) ?></td>
                                    <td>
                                        <?php
                                        $badge_class = 'bg-secondary-subtle text-secondary border-secondary-subtle';
                                        if ($quote['status'] === 'Approved') $badge_class = 'bg-success-subtle text-success border-success-subtle';
                                        if ($quote['status'] === 'Sent') $badge_class = 'bg-primary-subtle text-primary border-primary-subtle';
                                        if ($quote['status'] === 'Expired') $badge_class = 'bg-danger-subtle text-danger border-danger-subtle';
                                        ?>
                                        <span class="badge <?= $badge_class ?> border px-2 py-1 rounded">
                                            <?= $quote['status'] ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small"><?= date('M d, Y h:i A', strtotime($quote['created_at'])) ?></td>
                                    <td class="text-center">
                                        <!-- 🖨️ Direct Link Routing back into your print_invoice.php Engine framework -->
                                        <a href="print_invoice.php?id=<?= $quote['id'] ?>" target="_blank" class="btn btn-sm btn-outline-dark fw-bold px-3 rounded-2 shadow-sm">
                                            <i class="bi bi-printer-fill me-1"></i> Print Invoice
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-archive display-4 d-block text-black-50 mb-2"></i>
                                    No saved quotations history records found in database logs yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function addQuoteRowItem() {
        const select = document.getElementById('qtProductSelector');
        const opt = select.options[select.selectedIndex];
        if (!opt.value) return;

        const id = opt.value;
        const name = opt.getAttribute('data-name');
        const price = parseFloat(opt.getAttribute('data-price'));

        if (document.getElementById('qt_row_' + id)) {
            Swal.fire({
                icon: 'warning',
                title: 'Line Present',
                text: 'Adjust quotation quantities inside the current row.',
                confirmButtonColor: '#2D3748'
            });
            return;
        }

        const placeholder = document.getElementById('quotePlaceholder');
        if (placeholder) placeholder.style.display = 'none';

        const tr = document.createElement('tr');
        tr.id = 'qt_row_' + id;
        tr.innerHTML = `
            <td class="fw-bold text-dark">
                <input type="hidden" name="product_ids[]" value="${id}">
                ${name}
            </td>
            <td>
                <input type="number" name="quantities[]" class="form-control form-control-sm text-center font-monospace" value="1" min="1" onchange="calcQuoteRowTotal(this, ${price})" style="max-width:90px;">
            </td>
            <td class="text-secondary fw-semibold font-monospace">$${price.toFixed(2)}</td>
            <td class="fw-bold text-dark qt-subtotal font-monospace">$${price.toFixed(2)}</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1" onclick="removeQuoteRowItem(${id})">
                    <i class="bi bi-trash3-fill"></i>
                </button>
            </td>
        `;
        document.getElementById('quoteBody').appendChild(tr);
        recalculateQuoteSummary();
    }

    function removeQuoteRowItem(id) {
        document.getElementById('qt_row_' + id).remove();
        const body = document.getElementById('quoteBody');
        if (body.children.length === 1 && body.firstElementChild.id === 'quotePlaceholder') {
            document.getElementById('quotePlaceholder').style.display = 'table-row';
        } else if (body.children.length === 0) {
            body.innerHTML = `<tr id="quotePlaceholder"><td colspan="5" class="text-center text-muted py-5"><i class="bi bi-file-earmark-text display-4 d-block text-black-50 mb-2"></i>No estimate lines built. Choose products above.</td></tr>`;
        }
        recalculateQuoteSummary();
    }

    function calcQuoteRowTotal(input, price) {
        let qty = parseInt(input.value);
        if (qty < 1 || isNaN(qty)) {
            input.value = 1;
            qty = 1;
        }

        const row = input.closest('tr');
        row.querySelector('.qt-subtotal').innerText = '$' + (qty * price).toFixed(2);
        recalculateQuoteSummary();
    }

    function recalculateQuoteSummary() {
        let total = 0;
        document.querySelectorAll('.qt-subtotal').forEach(cell => {
            const val = parseFloat(cell.innerText.replace('$', ''));
            if (!isNaN(val)) total += val;
        });
        document.getElementById('displayQtSubtotal').innerText = '$' + total.toFixed(2);
        document.getElementById('displayQtGrandTotal').innerText = '$' + total.toFixed(2);
        document.getElementById('formQtTotalAmount').value = total.toFixed(2);
    }

    // ALERTS REDIRECT STATUS LISTENER
    const urlParams = new URLSearchParams(window.location.search);
    const statusMsg = urlParams.get('status');
    const qtInvoice = urlParams.get('qt');

    if (statusMsg) {
        if (statusMsg === 'success') {
            Swal.fire({
                title: 'Quotation Generated!',
                text: 'Estimation summary profile ' + (qtInvoice || '') + ' saved for accounting reviews.',
                icon: 'success',
                confirmButtonColor: '#2D3748'
            });
        } else if (statusMsg === 'empty_cart') {
            Swal.fire({
                icon: 'warning',
                title: 'Empty Estimate!',
                text: 'Add at least one product line item to generate estimates.',
                confirmButtonColor: '#2D3748'
            });
        } else if (statusMsg === 'error') {
            Swal.fire({
                icon: 'error',
                title: 'Pipeline processing error!',
                confirmButtonColor: '#2D3748'
            });
        }
        window.history.replaceState({}, document.title, window.location.pathname + "?page=quotations");
    }
</script>