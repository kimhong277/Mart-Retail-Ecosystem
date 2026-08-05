<?php
// store/checkout.php
require_once 'customer-session.php';

// If not logged in, redirect to login with return URL
if (!isCustomerLoggedIn()) {
    header("Location: customer-login.php?redirect=checkout.php");
    exit();
}

$customer = getCurrentCustomer();
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$conn = mysqli_connect($host, $db_user, $db_pass, 'mart_pos_system');
if (!$conn) {
    die("Database access failure.");
}
mysqli_set_charset($conn, "utf8mb4");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Mart Online Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .checkout-card {
            border-radius: 12px;
            border: 1px solid #e0e0e0;
        }

        .order-summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-checkout {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
        }

        .btn-checkout:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
        }

        .customer-badge {
            background: #e7f3ff;
            color: #0066cc;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>

<body class="bg-light ">
    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>

    <div class="container p-3" style="max-width: 1000px;">
        <!-- Progress Steps -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <!-- Step 1 -->
                    <div class="text-center flex-grow-1">
                        <div class="mx-auto mb-2" style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                            <i class="bi bi-bag-check"></i>
                        </div>
                        <p class="fw-bold mb-0">Cart</p>
                        <small class="text-muted">Review items</small>
                    </div>
                    <div style="flex: 1; height: 2px; background: #667eea; margin: 0 10px; margin-top: 25px;"></div>

                    <!-- Step 2 -->
                    <div class="text-center flex-grow-1">
                        <div class="mx-auto mb-2" style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                            2
                        </div>
                        <p class="fw-bold mb-0">Delivery</p>
                        <small class="text-muted">Shipping details</small>
                    </div>
                    <div style="flex: 1; height: 2px; background: #ddd; margin: 0 10px; margin-top: 25px;"></div>

                    <!-- Step 3 -->
                    <div class="text-center flex-grow-1">
                        <div class="mx-auto mb-2" style="width: 50px; height: 50px; background: #ddd; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #999; font-weight: bold;">
                            3
                        </div>
                        <p class="fw-bold mb-0 text-muted">Payment</p>
                        <small class="text-muted">Confirmation</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <a href="cart.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Back to Cart
            </a>
        </div>

        <div class="row g-4">
            <!-- Order Details -->
            <div class="col-lg-8">
                <div class="checkout-card p-4 mb-4">
                    <h5 class="fw-bold mb-3">Delivery Information</h5>

                    <form id="checkoutForm">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Full Name</label>
                            <input type="text" class="form-control" id="custName" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Phone Number</label>
                            <input type="tel" class="form-control" id="custPhone" value="<?php echo htmlspecialchars($customer['phone']); ?>" required placeholder="012 345 678">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Delivery Address</label>
                            <textarea class="form-control" id="custAddress" rows="3" required placeholder="House number, Street, Khan, City"></textarea>
                        </div>

                        <hr>

                        <h6 class="fw-bold mb-3">Order Items</h6>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th style="width: 80px;">Qty</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="cartItems"></tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="order-summary-card p-4 rounded-3 sticky-top" style="top: 100px;">
                    <h6 class="fw-bold mb-3">Order Summary</h6>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong id="subtotalUSD">$0.00</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom border-white-50">
                        <span>In KHR</span>
                        <strong id="subtotalKHR">៛0</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3 fs-5">
                        <span>Total Amount</span>
                        <strong id="totalUSD">$0.00</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3 small">
                        <span>Total KHR</span>
                        <strong id="totalKHR">៛0</strong>
                    </div>

                    <button type="button" class="btn btn-checkout w-100 text-white fw-bold" onclick="submitOrder()">
                        <i class="bi bi-credit-card me-2"></i>Place Order
                    </button>

                    <div class="mt-3 small text-center opacity-75">
                        <i class="bi bi-shield-check me-1"></i>Secure Checkout
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let cart = JSON.parse(localStorage.getItem('online_cart') || '[]');

        function renderCart() {
            const tbody = document.getElementById('cartItems');
            tbody.innerHTML = '';

            if (cart.length === 0) {
                document.querySelector('.container').innerHTML = `
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        Your cart is empty. <a href="index.php" class="alert-link">Continue shopping</a>
                    </div>
                `;
                return;
            }

            let total = 0;
            cart.forEach((item, idx) => {
                const subtotal = item.price * item.qty;
                total += subtotal;

                const row = `
                <tr>
                    <td>
                        <strong class="d-block text-truncate">${item.name}</strong>
                        <small class="text-muted">$${parseFloat(item.price).toFixed(2)}</small>
                    </td>
                    <td>${item.qty}</td>
                    <td class="text-end fw-bold">$${subtotal.toFixed(2)}</td>
                </tr>`;
                tbody.insertAdjacentHTML('beforeend', row);
            });

            updateTotals(total);
        }

        function updateTotals(total) {
            document.getElementById('subtotalUSD').textContent = `$${total.toFixed(2)}`;
            document.getElementById('subtotalKHR').textContent = `៛${(total * 4000).toLocaleString()}`;
            document.getElementById('totalUSD').textContent = `$${total.toFixed(2)}`;
            document.getElementById('totalKHR').textContent = `៛${(total * 4000).toLocaleString()}`;
        }

        function submitOrder() {
            if (cart.length === 0) {
                Swal.fire('Empty Cart', 'Please add items to your cart', 'warning');
                return;
            }

            const name = document.getElementById('custName').value.trim();
            const phone = document.getElementById('custPhone').value.trim();
            const address = document.getElementById('custAddress').value.trim();

            if (!name || !phone || !address) {
                Swal.fire('Missing Information', 'Please fill in all delivery details', 'warning');
                return;
            }

            const payload = {
                customer_name: name,
                customer_phone: phone,
                shipping_address: address,
                cart: cart
            };

            Swal.fire({
                title: 'Confirm Order',
                text: `Total: $${cart.reduce((sum, item) => sum + (item.price * item.qty), 0).toFixed(2)}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Place Order',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('place_online_order.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                localStorage.removeItem('online_cart');
                                Swal.fire({
                                    title: 'Order Placed!',
                                    text: `Order #${data.order_number} submitted successfully!`,
                                    icon: 'success'
                                }).then(() => {
                                    window.location.href = 'order_success.php?order=' + data.order_number;
                                });
                            } else {
                                Swal.fire('Order Failed', data.message || 'An error occurred', 'error');
                            }
                        })
                        .catch(err => {
                            Swal.fire('Error', 'Network error occurred', 'error');
                        });
                }
            });
        }

        document.addEventListener("DOMContentLoaded", renderCart);
    </script>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>