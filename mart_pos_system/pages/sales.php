<?php
// pages/sales.php
require_once 'db.php';

// Fetch available products list using your exact schema 'image' property
$products_list = mysqli_query($conn, "SELECT p.id, p.product_name, p.price, p.quantity, p.image,c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE quantity > 0 ORDER BY product_name ASC");
?>

<div class="container-fluid px-4 pt-4">
    <div class="mb-4">
        <h2 class="h3 mb-0 text-gray-800 fw-bold">Point of Sale (POS) Terminal</h2>
        <p class="text-muted small">Select active warehouse products to build real-time client sales checkout invoices.</p>
    </div>

    <form action="process_sale.php" method="POST" id="posForm">
        <div class="row g-4">

            <div class="col-12 col-lg-8">

                <div class="card shadow-sm border-0 mb-4 bg-light">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-grid-3x3-gap-fill me-2"></i>Product Catalog</h5>
                    </div>
                    <div class="card-body" style="max-height: 450px; overflow-y: auto;">
                        <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
                            <?php while ($prod = mysqli_fetch_assoc($products_list)):
                                // 1. Explicitly check if the database value is empty, null, or has placeholder strings
                                if (empty($prod['image']) || $prod['image'] == 'default.png') {

                                    // 1. Force the category name to lowercase so the API can read it cleanly
                                    $lowercase_category = strtolower($prod['category_name']);

                                    // 2. Clean up any accidental quotes safely
                                    $clean_category = str_replace(["'", '"'], "", $lowercase_category);

                                    // 3. Generate the absolute placeholder URL string
                                    $img_src = "https://loremflickr.com/320/240/" . urlencode($clean_category) . "?lock=" . $prod['id'];;
                                } else {
                                    // Load the custom file path from your local folder
                                    $img_src = "uploads/" . $prod['image'];
                                }
                            ?>
                                <div class="col">
                                    <div class="card h-100 border-0 shadow-sm product-item-card"
                                        onclick="addCardToCart(<?= $prod['id'] ?>, '<?= addslashes($prod['product_name']) ?>', <?= $prod['price'] ?>, <?= $prod['quantity'] ?>)"
                                        style="cursor: pointer; transition: transform 0.2s;">

                                        <div class="p-2 text-center bg-white rounded-top" style="height: 120px;">
                                            <img src="<?= $img_src; ?>" class="img-fluid h-100" style="object-fit: contain;" alt="Product">

                                        </div>

                                        <div class="card-body p-2 d-flex flex-column justify-content-between">
                                            <div class="mb-2">
                                                <p class="small fw-bold mb-1 text-dark text-truncate"><?= htmlspecialchars($prod['product_name']) ?></p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-success small fw-bold">$<?= number_format($prod['price'], 2) ?></span>
                                                    <span class="badge bg-secondary" style="font-size: 0.7rem;"><?= $prod['quantity'] ?> In Stock</span>
                                                </div>
                                            </div>

                                            <div class="btn btn-dark btn-sm w-100 py-1 fw-semibold text-center mt-2 add-cart-btn-indicator" style="font-size: 0.8rem;">
                                                <i class="bi bi-cart-plus-fill me-1"></i> Add To Cart
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-cart-check-fill me-2"></i>Current Basket</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="cartTable">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Item Designation</th>
                                    <th style="width: 20%;">Qty</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                    <th class="text-center" style="width: 10%;">Remove</th>
                                </tr>
                            </thead>
                            <tbody class="small" id="cartBody">
                                <tr id="emptyCartPlaceholder">
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bi bi-basket2 display-4 d-block text-black-50 mb-2"></i>
                                        Basket empty. Select items from the catalog above.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border-0 p-4 bg-white position-sticky" style="top: 20px;">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-receipt-cutoff me-2"></i>Payment Summary</h5>
                    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                        <span class="text-muted small fw-medium">Order Subtotal:</span>
                        <span class="fw-bold text-dark" id="displaySubtotal">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="h6 fw-bold mb-0 text-dark">Grand Payable Total:</span>
                        <span class="h4 fw-bold mb-0 text-dark" id="displayGrandTotal">$0.00</span>
                    </div>

                    <input type="hidden" name="total_amount" id="formTotalAmount" value="0.00">

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary">Settlement Method</label>
                        <select name="payment_method" class="form-select fw-semibold" required>
                            <option value="Cash">Cash Drawer</option>
                            <option value="ABA Pay / QR">ABA Pay Mobile QR Code</option>
                            <option value="Bank Wire">Bank Wire Transfer</option>
                        </select>
                    </div>

                    <button type="submit" name="checkout" class="btn btn-dark w-100 fw-bold py-3 shadow-sm text-uppercase">
                        <i class="bi bi-wallet2 me-2"></i> Process POS Checkout
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

<style>
    .product-item-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }

    .product-item-card:hover .add-cart-btn-indicator {
        background-color: #198754 !important;
        border-color: #198754 !important;
    }

    .card-body::-webkit-scrollbar {
        width: 6px;
    }

    .card-body::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .card-body::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
</style>

<script>
    function addCardToCart(id, name, price, maxStock) {
        if (document.getElementById('row_item_' + id)) {
            Swal.fire({
                icon: 'warning',
                title: 'Item Already Added',
                text: 'Increase the quantity in your cart!',
                confirmButtonColor: '#2D3748'
            });
            return;
        }

        const placeholder = document.getElementById('emptyCartPlaceholder');
        if (placeholder) placeholder.style.display = 'none';

        const tr = document.createElement('tr');
        tr.id = 'row_item_' + id;
        tr.innerHTML = `
            <td class="fw-bold text-dark">
                <input type="hidden" name="product_ids[]" value="${id}">
                ${name}
            </td>
            <td>
                <input type="number" name="quantities[]" class="form-control form-control-sm qty-input" value="1" min="1" max="${maxStock}" onchange="recalculateTotals(this, ${price}, ${maxStock})">
            </td>
            <td class="text-secondary fw-semibold">
                <input type="hidden" name="prices[]" value="${price}">
                $${price.toFixed(2)}
            </td>
            <td class="fw-bold text-dark item-subtotal">$${price.toFixed(2)}</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1" onclick="removeCartItemRow(${id})">
                    <i class="bi bi-trash3-fill"></i>
                </button>
            </td>
        `;

        document.getElementById('cartBody').appendChild(tr);
        updateGlobalBillSummary();
    }

    function removeCartItemRow(id) {
        const row = document.getElementById('row_item_' + id);
        if (row) row.remove();

        const body = document.getElementById('cartBody');
        if (body.children.length === 1 && body.firstElementChild.id === 'emptyCartPlaceholder') {
            document.getElementById('emptyCartPlaceholder').style.display = 'table-row';
        } else if (body.children.length === 0) {
            body.innerHTML = `<tr id="emptyCartPlaceholder"><td colspan="5" class="text-center text-muted py-5"><i class="bi bi-basket2 display-4 d-block text-black-50 mb-2"></i>Basket empty. Select items from the catalog above.</td></tr>`;
        }
        updateGlobalBillSummary();
    }

    function recalculateTotals(input, price, maxStock) {
        let qty = parseInt(input.value);
        if (qty > maxStock) {
            Swal.fire({
                icon: 'error',
                title: 'Insufficient Stock Levels',
                text: 'Max available inventory left inside storage layers for this item is: ' + maxStock + ' pcs!',
                confirmButtonColor: '#2D3748'
            });
            input.value = maxStock;
            qty = maxStock;
        }
        if (qty < 1 || isNaN(qty)) {
            input.value = 1;
            qty = 1;
        }

        const row = input.closest('tr');
        const subtotalCell = row.querySelector('.item-subtotal');
        subtotalCell.innerText = '$' + (qty * price).toFixed(2);
        updateGlobalBillSummary();
    }

    function updateGlobalBillSummary() {
        let grandTotal = 0;
        const subtotals = document.querySelectorAll('.item-subtotal');

        subtotals.forEach(cell => {
            const val = parseFloat(cell.innerText.replace('$', ''));
            if (!isNaN(val)) grandTotal += val;
        });

        document.getElementById('displaySubtotal').innerText = '$' + grandTotal.toFixed(2);
        document.getElementById('displayGrandTotal').innerText = '$' + grandTotal.toFixed(2);
        document.getElementById('formTotalAmount').value = grandTotal.toFixed(2);
    }
</script>