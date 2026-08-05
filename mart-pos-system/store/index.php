<?php
// store/index.php
require_once 'customer-session.php';

// Get database connection from shared helper
$conn = getStoreConnection();

// Get customer info if logged in
$customer = isCustomerLoggedIn() ? getCurrentCustomer() : null;

// Fetch Active Products in Stock
$products_query = mysqli_query($conn, "SELECT p.*, c.category_name 
                                       FROM products p 
                                       LEFT JOIN categories c ON p.category_id = c.id 
                                       WHERE p.quantity > 0 
                                       ORDER BY p.product_name ASC");
$products = [];
while ($row = mysqli_fetch_assoc($products_query)) {
    $products[] = $row;
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name ASC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mart Retail Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .product-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(102, 126, 234, 0.3) !important;
        }

        .product-image {
            background: #f8f9fa;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .product-image img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            transition: transform 0.3s;
        }

        .product-card:hover .product-image img {
            transform: scale(1.1);
        }

        .stock-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(102, 126, 234, 0.9);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .product-info {
            flex-grow: 1;
            padding: 15px;
            display: flex;
            flex-direction: column;
        }

        .category-badge {
            display: inline-block;
            background: #e7f3ff;
            color: #667eea;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 8px;
            width: fit-content;
        }

        .product-name {
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
            margin-bottom: 8px;
            flex-grow: 1;
            display: -webkit-box;
            /* -webkit-line-clamp: 2; */
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            margin-top: auto;
            margin-bottom: 12px;
        }

        .price-usd {
            font-size: 1.4rem;
            font-weight: 700;
            color: #667eea;
        }

        .price-khr {
            font-size: 0.85rem;
            color: #999;
            margin-top: 3px;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem !important;
            }
        }
    </style>
</head>

<body class="bg-light">

    <?php include "./includes/navbar.php"; ?>

    <!-- Hero Section -->
    <section style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 80px 0; position: relative; overflow: hidden;">
        <div style="position: absolute; top: -50%; right: -10%; width: 400px; height: 400px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 style="font-size: 3.5rem; font-weight: 800; margin-bottom: 20px; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">Shop Premium Quality Products</h1>
                    <p style="font-size: 1.3rem; margin-bottom: 30px; opacity: 0.95;">Fast delivery • Best prices • Trusted service</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="#products" class="btn btn-light btn-lg rounded-pill fw-bold" style="padding: 12px 30px;">
                            <i class="bi bi-shop me-2"></i>Shop Now
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg rounded-pill fw-bold" style="padding: 12px 30px;">
                            <i class="bi bi-info-circle me-2"></i>Learn More
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div style="font-size: 120px; opacity: 0.3;">
                        <i class="bi bi-bag-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container py-5">
        <!-- Filters -->
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 30px;">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="storeSearch" class="form-control border-start-0"
                            placeholder="Search by product name..." onkeyup="filterCatalog()">
                    </div>
                </div>
                <div class="col-md-6">
                    <select id="categorySelect" class="form-select" onchange="filterCatalog()">
                        <option value="all">All Categories</option>
                        <?php
                        $categories = mysqli_query($conn, "SELECT * FROM categories WHERE status = 1 ORDER BY category_name ASC");
                        while ($cat = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Section Header -->
        <div class="mb-4" id="products">
            <h2 style="font-size: 2rem; font-weight: 800; margin-bottom: 10px; color: #333;">Featured Products</h2>
            <p style="color: #999; margin-bottom: 30px; font-size: 0.95rem;">Discover our best-selling items</p>
        </div>

        <!-- Product Grid -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5" id="storeGrid">
            <!-- JavaScript renders cards here -->
        </div>

        <!-- Empty State -->
        <div id="emptyState" style="display: none; text-align: center; padding: 60px 20px;">
            <div style="font-size: 3rem; color: #ccc; margin-bottom: 20px;">
                <i class="bi bi-inbox"></i>
            </div>
            <h5 style="color: #999; margin-bottom: 30px;">No Products Found</h5>
            <p class="text-muted">Try adjusting your search or filters</p>
            <button onclick="resetFilters()" class="btn btn-primary rounded-pill">
                <i class="bi bi-arrow-clockwise me-2"></i>Reset Filters
            </button>
        </div>
    </div>

    <!-- Features Section -->
    <section style="padding: 60px 0; background: #f8f9fa;" id="features">
        <div class="container">
            <div class="row">
                <div class="col-md-3" style="text-align: center; padding: 30px;">
                    <div style="font-size: 2.5rem; color: #667eea; margin-bottom: 15px;">
                        <i class="bi bi-truck"></i>
                    </div>
                    <h6 style="font-weight: 700; margin-bottom: 10px; color: #333;">Fast Delivery</h6>
                    <p style="color: #666; font-size: 0.9rem;">Quick and reliable shipping to your doorstep</p>
                </div>
                <div class="col-md-3" style="text-align: center; padding: 30px;">
                    <div style="font-size: 2.5rem; color: #667eea; margin-bottom: 15px;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h6 style="font-weight: 700; margin-bottom: 10px; color: #333;">Secure Payment</h6>
                    <p style="color: #666; font-size: 0.9rem;">100% safe and secure transactions</p>
                </div>
                <div class="col-md-3" style="text-align: center; padding: 30px;">
                    <div style="font-size: 2.5rem; color: #667eea; margin-bottom: 15px;">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </div>
                    <h6 style="font-weight: 700; margin-bottom: 10px; color: #333;">Easy Returns</h6>
                    <p style="color: #666; font-size: 0.9rem;">Hassle-free returns within 30 days</p>
                </div>
                <div class="col-md-3" style="text-align: center; padding: 30px;">
                    <div style="font-size: 2.5rem; color: #667eea; margin-bottom: 15px;">
                        <i class="bi bi-headset"></i>
                    </div>
                    <h6 style="font-weight: 700; margin-bottom: 10px; color: #333;">24/7 Support</h6>
                    <p style="color: #666; font-size: 0.9rem;">Dedicated customer service team</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // 🔒 Safely read customer session status inside script scope
        const IS_LOGGED_IN = <?php echo ($customer || isset($_SESSION['user_id']) || isset($_SESSION['customer_id'])) ? 'true' : 'false'; ?>;

        const productsData = <?= json_encode($products); ?>;
        let cart = JSON.parse(localStorage.getItem('online_cart') || '[]');

        document.addEventListener("DOMContentLoaded", function() {
            updateCartBadge();
            filterCatalog();
        });

        function filterCatalog() {
            const query = document.getElementById('storeSearch').value.toLowerCase().trim();
            const catId = document.getElementById('categorySelect').value;

            const filtered = productsData.filter(p => {
                const matchesQuery = p.product_name.toLowerCase().includes(query);
                const matchesCat = (catId === 'all') || (parseInt(p.category_id) === parseInt(catId));
                return matchesQuery && matchesCat;
            });

            renderGrid(filtered);
        }

        function resetFilters() {
            document.getElementById('storeSearch').value = '';
            document.getElementById('categorySelect').value = 'all';
            filterCatalog();
        }

        function renderGrid(items) {
            const grid = document.getElementById('storeGrid');
            const emptyState = document.getElementById('emptyState');
            grid.innerHTML = '';

            if (items.length === 0) {
                if (emptyState) emptyState.style.display = 'block';
                return;
            }

            if (emptyState) emptyState.style.display = 'none';

            items.forEach(p => {
                const imgFile = p.image ? p.image : 'default.png';
                const imgSrc = (imgFile.startsWith('http://') || imgFile.startsWith('https://')) ?
                    imgFile :
                    '/mart-retail-ecosystem/mart_pos_system/assets/images/' + imgFile;

                const priceUSD = parseFloat(p.sale_price).toFixed(2);
                const priceKHR = (p.sale_price * 4000).toLocaleString();
                const safeName = p.product_name.replace(/'/g, "\\'");

                // 🔒 Toggle button display depending on customer login state
                const actionButtonHtml = IS_LOGGED_IN ?
                    `<button class="btn btn-outline-primary w-100 rounded-3 fw-bold btn-sm" onclick="addToCart(${p.id}, '${safeName}', ${p.sale_price}, ${p.quantity})">
                       <i class="bi bi-cart-plus me-1"></i> Add To Cart
                   </button>` :
                    `<a href="customer-login.php" class="btn btn-outline-primary  border w-100 rounded-3 fw-bold btn-sm">
                       <i class="bi bi-box-arrow-in-right me-1"></i>Add to cart
                   </a>`;

                const card = `
    <div class="col-6 col-md-4 col-lg-3">
        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden product-card">
            <div class="position-relative bg-white d-flex align-items-center justify-content-center p-3" style="height: 160px;">
                <img src="${imgSrc}" class="img-fluid" style="max-height: 100%; object-fit: contain;" alt="${p.product_name}" onerror="this.src='/mart-retail-ecosystem/mart_pos_system/assets/images/default.png';">
                <span class="position-absolute top-0 end-0 m-2 badge bg-dark opacity-75 rounded-pill small">Stock: ${p.quantity}</span>
            </div>
            <div class="card-body d-flex flex-column justify-content-between p-3">
                <div>
                    <h6 class="fw-bold text-dark text-truncate mb-1" title="${p.product_name}">${p.product_name}</h6>
                </div>
                <div class="mt-3">
                    <div class="d-flex align-items-baseline justify-content-between mb-2">
                        <span class="fw-bold text-primary fs-5">$${priceUSD}</span>
                        <span class="text-muted small">៛${priceKHR}</span>
                    </div>
                    ${actionButtonHtml}
                </div>
            </div>
        </div>
    </div>`;
                grid.insertAdjacentHTML('beforeend', card);
            });
        }

        function addToCart(id, name, price, maxStock) {
            let item = cart.find(i => i.id === id);
            if (item) {
                if (item.qty < maxStock) {
                    item.qty++;
                } else {
                    Swal.fire('Stock Limit', `Only ${maxStock} items available.`, 'warning');
                    return;
                }
            } else {
                cart.push({
                    id,
                    name,
                    price,
                    qty: 1,
                    maxStock
                });
            }

            localStorage.setItem('online_cart', JSON.stringify(cart));
            updateCartBadge();

            Swal.fire({
                title: 'Added to Cart',
                text: `${name} has been added to your cart`,
                icon: 'success',
                timer: 1200,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        }

        function updateCartBadge() {
            const badge = document.getElementById('cartCountBadge');
            if (!badge) return;

            const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
            badge.textContent = totalQty;
            badge.style.display = totalQty > 0 ? 'inline-block' : 'none';
        }
    </script>
</body>

</html>