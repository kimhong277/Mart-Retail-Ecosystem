<!-- store/cart.php -->
<?php
require_once 'customer-session.php';
$customer = isCustomerLoggedIn() ? getCurrentCustomer() : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Mart Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .btn-checkout {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
        }

        .btn-checkout:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
        }

        .cart-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            border-left: 4px solid #667eea;
            margin-bottom: 12px;
            transition: all 0.3s;
        }

        .cart-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 25px;
            position: sticky;
            top: 100px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .summary-row.total {
            border: none;
            padding: 0;
            font-size: 1.4rem;
            font-weight: 700;
            margin-top: 15px;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
    </style>
</head>

<!-- <body class="bg-light"> -->

<body class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>

    <div class="container" style="max-width: 1000px; padding: 40px 20px;">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">
                    <i class="bi bi-cart3 text-primary me-2"></i>Shopping Cart
                </h2>
                <p class="text-muted">Review your items before checkout</p>
            </div>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Continue Shopping
            </a>
        </div>

        <div class="row g-4">
            <!-- Cart Items -->
            <div class="col-lg-7">
                <div id="cartItemsContainer">
                    <div class="empty-cart">
                        <div class="empty-icon">
                            <i class="bi bi-cart-x"></i>
                        </div>
                        <h5 class="text-muted mb-3">Your cart is empty</h5>
                        <a href="index.php" class="btn btn-primary rounded-pill">
                            <i class="bi bi-shop me-1"></i>Start Shopping
                        </a>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-5">
                <div class="summary-card">
                    <h6 class="fw-bold mb-3" style="font-size: 1.1rem;">
                        <i class="bi bi-receipt me-2"></i>Order Summary
                    </h6>

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <strong id="subtotalUSD">$0.00</strong>
                    </div>

                    <div class="summary-row">
                        <span class="small">In KHR</span>
                        <strong class="small" id="subtotalKHR">៛0</strong>
                    </div>

                    <div class="summary-row total">
                        <span>Total</span>
                        <strong id="totalUSD">$0.00</strong>
                    </div>

                    <button type="button" class="btn btn-checkout w-100 text-white fw-bold mt-4" onclick="submitOrder(event)" id="checkoutBtn">
                        <i class="bi bi-credit-card me-2"></i>Proceed to Checkout
                    </button>

                    <div class="mt-3 small text-center" style="opacity: 0.8;">
                        <i class="bi bi-shield-check me-1"></i>Secure Checkout
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let cart = JSON.parse(localStorage.getItem('online_cart') || '[]');

        document.addEventListener("DOMContentLoaded", renderCart);

        function renderCart() {
            const container = document.getElementById('cartItemsContainer');
            container.innerHTML = '';

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="empty-cart">
                        <div class="empty-icon">
                            <i class="bi bi-cart-x"></i>
                        </div>
                        <h5 class="text-muted mb-3">Your cart is empty</h5>
                        <a href="index.php" class="btn btn-primary rounded-pill">
                            <i class="bi bi-shop me-1"></i>Start Shopping
                        </a>
                    </div>
                `;
                document.getElementById('subtotalUSD').textContent = '$0.00';
                document.getElementById('subtotalKHR').textContent = '៛0';
                document.getElementById('totalUSD').textContent = '$0.00';
                document.getElementById('totalKHR').textContent = '៛0';
                return;
            }

            let grandTotal = 0;
            cart.forEach((item, idx) => {
                const subtotal = item.price * item.qty;
                grandTotal += subtotal;

                const itemHtml = `
                <div class="cart-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1">${item.name}</h6>
                            <p class="text-muted small mb-2">$${parseFloat(item.price).toFixed(2)} x ${item.qty} = <strong class="text-dark">$${subtotal.toFixed(2)}</strong></p>
                            <div class="d-flex gap-2 align-items-center">
                                <button class="btn btn-sm btn-outline-secondary" onclick="decreaseQty(${idx})" title="Decrease">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <input type="number" class="form-control form-control-sm" value="${item.qty}" min="1" max="${item.maxStock}" 
                                       style="width: 60px; text-align: center;" onchange="changeQty(${idx}, this.value)">
                                <button class="btn btn-sm btn-outline-secondary" onclick="increaseQty(${idx})" title="Increase">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline-danger ms-3" onclick="removeItem(${idx})" title="Remove">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>`;
                container.insertAdjacentHTML('beforeend', itemHtml);
            });

            document.getElementById('subtotalUSD').textContent = `$${grandTotal.toFixed(2)}`;
            document.getElementById('subtotalKHR').textContent = `៛${(grandTotal * 4000).toLocaleString()}`;
            document.getElementById('totalUSD').textContent = `$${grandTotal.toFixed(2)}`;
            document.getElementById('totalKHR').textContent = `៛${(grandTotal * 4000).toLocaleString()}`;
        }

        function changeQty(idx, qty) {
            qty = parseInt(qty);
            if (qty <= 0) {
                removeItem(idx);
                return;
            }
            if (qty > cart[idx].maxStock) {
                Swal.fire('Stock Limit', `Only ${cart[idx].maxStock} units available.`, 'warning');
                cart[idx].qty = cart[idx].maxStock;
            } else {
                cart[idx].qty = qty;
            }
            localStorage.setItem('online_cart', JSON.stringify(cart));
            renderCart();
        }

        function increaseQty(idx) {
            if (cart[idx].qty < cart[idx].maxStock) {
                cart[idx].qty++;
                localStorage.setItem('online_cart', JSON.stringify(cart));
                renderCart();
            } else {
                Swal.fire('Stock Limit', `Only ${cart[idx].maxStock} units available.`, 'warning');
            }
        }

        function decreaseQty(idx) {
            if (cart[idx].qty > 1) {
                cart[idx].qty--;
                localStorage.setItem('online_cart', JSON.stringify(cart));
                renderCart();
            }
        }

        function removeItem(idx) {
            Swal.fire({
                title: 'Remove Item?',
                text: 'Are you sure you want to remove this item?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    cart.splice(idx, 1);
                    localStorage.setItem('online_cart', JSON.stringify(cart));
                    renderCart();
                    Swal.fire('Removed!', 'Item has been removed from your cart', 'success');
                }
            });
        }

        function submitOrder(e) {
            if (e) e.preventDefault();

            if (cart.length === 0) {
                Swal.fire('Cart Empty', 'Add items before proceeding.', 'info');
                return;
            }

            // Check if customer is logged in
            const isLoggedIn = <?php echo json_encode(isCustomerLoggedIn()); ?>;

            if (!isLoggedIn) {
                Swal.fire({
                    title: 'Login Required',
                    text: 'You must login to proceed with checkout',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Go to Login',
                    cancelButtonText: 'Continue Shopping'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'customer-login.php?redirect=checkout.php';
                    }
                });
                return;
            }

            // Redirect to checkout page
            window.location.href = 'checkout.php';
        }
    </script>
</body>

</html>