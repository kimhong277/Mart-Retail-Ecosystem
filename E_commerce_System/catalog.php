<?php
// E-Commerce System/catalog.php
session_start();

// 1. Connect to your E-Commerce database procedurally
$host = 'localhost';
$db_user = 'root';
$db_pass = ''; // Your local MySQL password
$conn_eco = mysqli_connect($host, $db_user, $db_pass, 'e_commerce_system');

if (!$conn_eco) {
    die("Database connection failure: " . mysqli_connect_error());
}
mysqli_set_charset($conn_eco, "utf8mb4");

// 2. Fetch all real products from your actual database table
$sql = "SELECT product_id, product_name, price, image FROM products WHERE stock_quantity > 0";
$result = mysqli_query($conn_eco, $sql);

$products = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Mart Online Storefront</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark mb-5">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">🛒 Mart Digital Store</a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cartModal">
                View Cart <span class="badge bg-danger ms-1" id="cart-badge">0</span>
            </button>
        </div>
    </nav>

    <div class="container">
        <div class="row g-4">
            <?php if (empty($products)): ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted fs-5">No products found in the database inventory. Please add items via your admin panel.</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <!-- Fallback default image placeholder if the database column string is empty -->
                            <img src="<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : 'https://via.placeholder.com/150'; ?>" class="card-img-top bg-secondary text-white text-center py-4" alt="Product Thumbnail">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <h5 class="card-title fw-bold text-dark"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                                    <p class="text-primary fw-semibold fs-5">$<?php echo number_format($product['price'], 2); ?></p>
                                </div>
                                <!-- 🌟 FIX: Pass the real primary product_id straight out of the database row -->
                                <button class="btn btn-outline-dark w-100 mt-3 fw-medium"
                                    onclick="addToCart(<?php echo $product['product_id']; ?>, '<?php echo addslashes($product['product_name']); ?>', <?php echo $product['price']; ?>)">
                                    ➕ Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- The Checkout Preview Review Slide Modal Window -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="cartModalLabel">Your Shopping Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="cart-items-list" class="mb-3">
                        <p class="text-muted text-center py-3">Your cart shopping tray is empty.</p>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5 px-1">
                        <div>Total Balance:</div>
                        <div class="text-primary" id="cart-total-amount">$0.00</div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Browsing</button>
                    <button type="button" class="btn btn-success fw-bold" id="checkout-btn" onclick="proceedToCheckout()" disabled>Proceed to Secure Payment</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let shoppingCart = [];

        function addToCart(id, name, price) {
            const existingItem = shoppingCart.find(item => item.id === id);
            if (existingItem) {
                existingItem.qty += 1;
            } else {
                shoppingCart.push({
                    id,
                    name,
                    price,
                    qty: 1
                });
            }
            renderCartUI();
        }

        function renderCartUI() {
            const listContainer = document.getElementById('cart-items-list');
            const badge = document.getElementById('cart-badge');
            const totalDisplay = document.getElementById('cart-total-amount');
            const checkoutBtn = document.getElementById('checkout-btn');

            if (shoppingCart.length === 0) {
                listContainer.innerHTML = '<p class="text-muted text-center py-3">Your cart shopping tray is empty.</p>';
                badge.innerText = '0';
                totalDisplay.innerText = '$0.00';
                checkoutBtn.disabled = true;
                return;
            }

            let totalItems = 0;
            let grandTotal = 0;
            let htmlContent = '';

            shoppingCart.forEach(item => {
                totalItems += item.qty;
                const itemTotal = item.qty * item.price;
                grandTotal += itemTotal;

                htmlContent += `
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <div>
                            <span class="fw-bold d-block text-dark">${item.name}</span>
                            <small class="text-muted">Qty: ${item.qty} @ $${item.price.toFixed(2)}</small>
                        </div>
                        <span class="fw-semibold text-secondary">$${itemTotal.toFixed(2)}</span>
                    </div>
                `;
            });

            listContainer.innerHTML = htmlContent;
            badge.innerText = totalItems;
            totalDisplay.innerText = `$${grandTotal.toFixed(2)}`;
            checkoutBtn.disabled = false;
        }

        function proceedToCheckout() {
            fetch('save_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        cart: shoppingCart
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        alert('Error initializing session token mapping configurations.');
                    }
                })
                .catch(err => console.error("Error connecting network parameters:", err));
        }
    </script>
</body>

</html>