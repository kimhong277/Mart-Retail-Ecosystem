<?php
// mart_pos_system/pages/sales.php
// Dynamically included into index.php

$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$conn_pos = mysqli_connect($host, $db_user, $db_pass, 'mart_pos_system');

if (!$conn_pos) {
    echo "<div class='alert alert-danger'>Database access failure: " . mysqli_connect_error() . "</div>";
    return;
}
mysqli_set_charset($conn_pos, "utf8mb4");

// Fetch active products to display in the grid
$sql = "SELECT p.*, c.category_name , c.status
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.quantity > 0
        ORDER BY p.product_name ASC";
$result = mysqli_query($conn_pos, $sql);

// Save items into a JSON array so JavaScript can search them instantly without reload
$products_array = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products_array[] = $row;
}

// Fetch categories for pill buttons
$categories_query = mysqli_query($conn_pos, "SELECT * FROM categories ORDER BY category_name ASC");
?>

<!-- Include SweetAlert2 for beautiful checkout confirmation popups -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="row g-4">
    <!-- LEFT SIDE: Product Search & Visual Selection Grid (60% width) -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-4">
                <div class="mb-3">
                    <h4 class="fw-bold text-dark mb-3">🛒 Register Terminal</h4>
                    <!-- Interactive Real-Time Search Bar -->
                    <div class="input-group shadow-sm rounded mb-3">
                        <span class="input-group-text bg-white border-end-0 text-muted">🔍</span>
                        <input type="text" id="posSearch" class="form-control border-start-0 py-2.5"
                            placeholder="Type item name or barcode string to filter catalog..." onkeyup="filterProducts()">
                    </div>

                    <!-- 🏷️ CATEGORY PILL TABS -->
                    <div class="d-flex gap-2 overflow-x-auto pb-2 border-bottom text-nowrap" id="categoryPillContainer">
                        <button class="btn btn-sm btn-primary rounded-pill px-3 fw-bold category-pill active" data-cat="all">
                            All Items
                        </button>
                        <?php while ($cat = mysqli_fetch_assoc($categories_query)) {
                            if ($cat['status'] === '1') { ?>
                                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-medium category-pill" data-cat="<?= $cat['id'] ?>">
                                    <?= htmlspecialchars($cat['category_name']) ?>
                                </button>
                        <?php }
                        } ?>
                    </div>
                </div>

                <!-- Scrollable Catalog Items Display Grid (Lag-Free Lazy Infinite Scroll) -->
                <div class="row row-cols-1 row-cols-md-3 g-3 overflow-y-auto" style="max-height: 520px;" id="productsGrid">
                    <!-- Cards are dynamically rendered by JS -->
                </div>

                <!-- Grid Counter Footer -->
                <div class="pt-2 mt-2 border-top text-muted small d-flex justify-content-between align-items-center">
                    <span id="posGridSummary">Showing 0 items</span>
                    <span class="badge bg-light text-secondary border font-monospace">Auto-Scroll Active</span>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Active Cart & Invoice Subtotal Panel (40% width) -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <h4 class="fw-bold text-dark mb-4 d-flex justify-content-between align-items-center">
                        <span>📝 Current Bill</span>
                        <button class="btn btn-sm btn-outline-danger border-0 fw-bold" onclick="clearCart()">Clear All</button>
                    </h4>

                    <!-- Dynamic Checkout Receipt List Table -->
                    <div class="table-responsive overflow-y-auto mb-4" style="max-height: 400px;">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th style="width: 100px;">Qty</th>
                                    <th class="text-end">Total</th>
                                    <th style="width: 40px;"></th>
                                </tr>
                            </thead>
                            <tbody id="cartTableBody">
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted" id="emptyCartRow">
                                        Cart is empty. Click items on the left to add them to the ticket!
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Subtotals and Action Checkout Button Placement -->
                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fw-semibold">Subtotal</span>
                        <span class="fw-bold text-dark fs-5" id="billSubtotal">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-muted fw-semibold">Grand Total</span>
                        <span class="fw-bold text-primary fs-3" id="billTotal">$0.00</span>
                    </div>

                    <button class="btn btn-primary btn-lg w-100 fw-bold shadow-sm py-3" id="checkoutBtn" disabled onclick="processPOSCheckout()">
                        💳 Complete In-Store Sale
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        border-color: #0d6efd !important;
    }

    .fs-8 {
        font-size: 0.75rem;
    }
</style>

<!-- DYNAMIC CORE CART CONTROLLER SCRIPT -->
<script>
    const allProducts = <?= json_encode($products_array); ?>;
    let activeCategory = 'all';
    let filteredProducts = [...allProducts];
    let cart = [];

    // ⚡ PERFORMANCE CONFIGURATION (Prevents DOM Lag)
    const BATCH_SIZE = 12;
    let currentRenderIndex = 0;

    document.addEventListener("DOMContentLoaded", function() {
        // Initial catalog filter & render
        filterProducts();

        // Category Pill Filter Click Handlers
        document.querySelectorAll('.category-pill').forEach(pill => {
            pill.addEventListener('click', function() {
                document.querySelectorAll('.category-pill').forEach(p => {
                    p.classList.remove('btn-primary', 'fw-bold', 'active');
                    p.classList.add('btn-outline-secondary', 'fw-medium');
                });

                this.classList.remove('btn-outline-secondary', 'fw-medium');
                this.classList.add('btn-primary', 'fw-bold', 'active');

                activeCategory = this.dataset.cat;
                filterProducts();
            });
        });

        // ⚡ INFINITE SCROLL EVENT LISTENER
        const gridContainer = document.getElementById('productsGrid');
        gridContainer.addEventListener('scroll', function() {
            // Check if user scrolled near the bottom (within 60px)
            if (gridContainer.scrollTop + gridContainer.clientHeight >= gridContainer.scrollHeight - 60) {
                renderNextBatch();
            }
        });
    });

    // 1. Live Search & Category Filter Engine
    function filterProducts() {
        const query = document.getElementById('posSearch').value.toLowerCase().trim();

        // Filter dataset in-memory
        filteredProducts = allProducts.filter(prod => {
            const prodName = (prod.product_name || '').toLowerCase();
            const prodBarcode = (prod.barcode || '').toLowerCase();
            const matchesQuery = prodName.includes(query) || prodBarcode.includes(query);
            const matchesCat = (activeCategory === 'all') || (parseInt(prod.category_id) === parseInt(activeCategory));

            return matchesQuery && matchesCat;
        });

        // Reset scroll position & render index
        const container = document.getElementById('productsGrid');
        container.innerHTML = '';
        container.scrollTop = 0;
        currentRenderIndex = 0;

        if (filteredProducts.length === 0) {
            container.innerHTML = `<div class="col-12 w-100 text-center py-5 text-muted">No items match your filter selection.</div>`;
            document.getElementById('posGridSummary').textContent = 'Showing 0 items';
            return;
        }

        // Render first batch of 12 items
        renderNextBatch();
    }

    // 2. Render Next Batch into Grid (Lag-Free)
    function renderNextBatch() {
        if (currentRenderIndex >= filteredProducts.length) return;

        const container = document.getElementById('productsGrid');
        const batch = filteredProducts.slice(currentRenderIndex, currentRenderIndex + BATCH_SIZE);

        batch.forEach(prod => {
            const imageFile = prod.image ? prod.image : 'default.png';

            // Image Resolver: supports both web URLs and local paths
            let imagePath = '';
            if (imageFile.startsWith('http://') || imageFile.startsWith('https://')) {
                imagePath = imageFile;
            } else {
                imagePath = "/mart-retail-ecosystem/mart_pos_system/assets/images/" + imageFile;
            }

            const categoryLabel = prod.category_name ? prod.category_name : 'General';
            const barcodeLabel = prod.barcode ? prod.barcode : 'N/A';
            const price = parseFloat(prod.sale_price).toFixed(2);
            const nameEscaped = prod.product_name.replace(/'/g, "\\'");

            const cardHTML = `
                <div class="col product-item-card">
                    <div class="card h-100 shadow text-center card-hover" style="cursor: pointer; transition: transform 0.2s;">
                        <div class="position-relative bg-white rounded-top overflow-hidden d-flex align-items-center justify-content-center" style="height: 140px; width: 100%;">
                            <img src="${imagePath}"
                                class="img-fluid p-2"
                                alt=""
                                style="max-height: 100%; max-width: 100%; object-fit: contain;"
                                onerror="this.onerror=null; this.src='/mart-retail-ecosystem/mart_pos_system/assets/images/default.png';">
                        </div>

                        <div class="card-body d-flex flex-column justify-content-between p-3">
                            <div>
                                ` +
                // <-- <span class="badge bg-light text-dark mb-2 fs-8">${categoryLabel}</span> !-->

                ` <h6 class="fw-bold text-secondary mb-1 text-truncate" title="${prod.product_name}">${prod.product_name}</h6>
                                <small class="text-muted font-monospace d-block mb-2">#${barcodeLabel}</small>
                            </div>
                            <div>
                                <h5 class="fw-bold text-primary mb-1">$${price}</h5>
                                <small class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-8">
                                    Stock: ${prod.quantity}
                                </small>
                            </div>
                        </div>
                        <button class="btn btn-primary mx-2 mb-2" onclick="addToCart(${prod.id}, '${nameEscaped}', ${prod.sale_price}, ${prod.quantity})">Add to Cart</button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', cardHTML);
        });

        currentRenderIndex += batch.length;
        document.getElementById('posGridSummary').textContent = `Loaded ${currentRenderIndex} of ${filteredProducts.length} items`;
    }

    // 3. Add Item to Cart Array
    function addToCart(id, name, price, maxStock) {
        const existingIndex = cart.findIndex(item => item.id === id);

        if (existingIndex > -1) {
            if (cart[existingIndex].qty >= maxStock) {
                Swal.fire('Stock Limit!', `Only ${maxStock} units are available in inventory.`, 'warning');
                return;
            }
            cart[existingIndex].qty += 1;
        } else {
            cart.push({
                id,
                name,
                price,
                qty: 1,
                maxStock
            });
        }
        renderCart();
    }

    // 4. Update Individual Quantity Inside Input Row
    function updateQty(id, newQty) {
        const index = cart.findIndex(item => item.id === id);
        if (index === -1) return;

        newQty = parseInt(newQty);
        if (isNaN(newQty) || newQty <= 0) {
            removeFromCart(id);
            return;
        }

        if (newQty > cart[index].maxStock) {
            Swal.fire('Stock Limit!', `Only ${cart[index].maxStock} units available.`, 'warning');
            cart[index].qty = cart[index].maxStock;
        } else {
            cart[index].qty = newQty;
        }
        renderCart();
    }

    // 5. Remove Item Row from Basket Array
    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        renderCart();
    }

    // 6. Reset Cart Core Array entirely
    function clearCart() {
        cart = [];
        renderCart();
    }

    // 7. Redraw receipt layout blocks matching memory metrics
    function renderCart() {
        const tbody = document.getElementById('cartTableBody');
        const emptyRow = document.getElementById('emptyCartRow');
        const checkoutBtn = document.getElementById('checkoutBtn');

        // Clear dynamic rows
        const manualRows = tbody.querySelectorAll('.cart-item-row');
        manualRows.forEach(row => row.remove());

        if (cart.length === 0) {
            emptyRow.style.display = "";
            document.getElementById('billSubtotal').innerText = "$0.00";
            document.getElementById('billTotal').innerText = "$0.00";
            checkoutBtn.disabled = true;
            return;
        }

        emptyRow.style.display = "none";
        checkoutBtn.disabled = false;
        let total = 0;

        cart.forEach(item => {
            const rowTotal = item.price * item.qty;
            total += rowTotal;

            const tr = document.createElement('tr');
            tr.className = 'cart-item-row';
            tr.innerHTML = `
            <td>
                <span class="fw-semibold text-secondary d-block text-truncate" style="max-width: 150px;">${item.name}</span>
                <small class="text-muted">$${item.price.toFixed(2)} each</small>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm text-center fw-bold" 
                       value="${item.qty}" min="1" max="${item.maxStock}" 
                       onchange="updateQty(${item.id}, this.value)">
            </td>
            <td class="text-end fw-bold text-dark">$${rowTotal.toFixed(2)}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-link text-danger p-0" onclick="removeFromCart(${item.id})">❌</button>
            </td>
        `;
            tbody.appendChild(tr);
        });

        document.getElementById('billSubtotal').innerText = `$${total.toFixed(2)}`;
        document.getElementById('billTotal').innerText = `$${total.toFixed(2)}`;
    }

    // 8. Dispatch JSON Array payloads asynchronously to root operations
    function processPOSCheckout() {
        Swal.fire({
            title: 'Complete Checkout?',
            text: `Collect cash payment totaling ${document.getElementById('billTotal').innerText}. Confirm order completion?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Complete Order!'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('cart_data', JSON.stringify(cart));

                fetch('/mart-retail-ecosystem/mart_pos_system/place_pos_order.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Receipt printed and inventory decremented cleanly.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Checkout Refused', data.message || 'Error processing sales line items.', 'error');
                        }
                    })
                    .catch(() => Swal.fire('Error!', 'System communication loss processing sale.', 'error'));
            }
        });
    }
</script>