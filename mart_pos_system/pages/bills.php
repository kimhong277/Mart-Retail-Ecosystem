<?php
$conn = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
mysqli_set_charset($conn, "utf8mb4");

// Fetch accounts payable tracking log entries
$bills_query = mysqli_query($conn, "SELECT * FROM supplier_bills ORDER BY created_at DESC");
?>

<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">🧾 Accounts Payable / Supplier Bills</h2>
    <p class="text-muted small">Monitor financial wholesale expense invoices due to distribution companies.</p>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary fw-semibold">
                    <tr>
                        <th>Voucher ID</th>
                        <th>Supplier/Distributor</th>
                        <th>Amount Due</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($bills_query) > 0): ?>
                        <?php while ($bill = mysqli_fetch_assoc($bills_query)): ?>
                            <tr>
                                <td><span class="fw-bold text-secondary font-monospace"><?php echo $bill['bill_no']; ?></span></td>
                                <td class="fw-semibold text-dark"><?php echo htmlspecialchars($bill['supplier_name']); ?></td>
                                <td class="fw-bold text-primary">$<?php echo number_format($bill['amount_due'], 2); ?></td>
                                <td>
                                    <span class="badge px-3 py-1.5 border rounded-pill <?php echo $bill['status'] === 'Paid' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?>">
                                        <?php echo $bill['status']; ?>
                                    </span>
                                </td>
                                <td class="text-muted small"><?php echo $bill['created_at']; ?></td>
                                <td class="text-center">
                                    <?php if ($bill['status'] === 'Unpaid'): ?>
                                        <button class="btn btn-sm btn-success fw-bold px-3 shadow-sm" onclick="payBill(<?php echo $bill['id']; ?>)">
                                            💵 Mark Paid
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small fw-semibold"><i class="bi bi-check-circle-fill text-success"></i> Settled</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                📋 No outstanding supplier purchase invoices generated on record.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function payBill(billId) {
        Swal.fire({
            title: 'Confirm Payment Settlement?',
            text: "Verify that this wholesale distributor balance invoice has been financially resolved.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Mark Settled!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing Transaction...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send standard POST package string parameters back into our inline utility file execution path
                const formData = new FormData();
                formData.append('bill_id', billId);

                fetch('/mart-retail-ecosystem/mart_pos_system/settle_bill.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Invoice Cleared!', 'The bill status code metrics have been marked completely Paid.', 'success')
                                .then(() => window.location.reload());
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Communication glitch with accounting controller core.', 'error'));
            }
        });
    }
</script>