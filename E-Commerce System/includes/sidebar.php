<div class="p-3 w-100">
    <h6 class="text-muted text-uppercase small fw-bold px-3 mb-3">Navigation</h6>

    <?php
    // Capture the current page from the URL string to dynamically assign active layout highlights
    $current_page = $_GET['page'] ?? 'dashboard';
    ?>

    <ul class="list-group list-group-flush">
        <!-- DASHBOARD LINK -->
        <li class="list-group-item bg-transparent border-0 py-1">
            <a href="index.php?page=dashboard" class="nav-link px-3 py-2 rounded-2 <?php echo ($current_page === 'dashboard') ? 'bg-primary text-white fw-bold' : 'text-dark'; ?>">
                <i class="fa-solid fa-house me-2"></i> Dashboard
            </a>
        </li>

        <!-- PRODUCTS LINK -->
        <li class="list-group-item bg-transparent border-0 py-1">
            <a href="index.php?page=products" class="nav-link px-3 py-2 rounded-2 <?php echo ($current_page === 'products') ? 'bg-primary text-white fw-bold' : 'text-dark'; ?>">
                <i class="fa-solid fa-box me-2"></i> Products
            </a>
        </li>

        <!-- ORDERS LINK -->
        <li class="list-group-item bg-transparent border-0 py-1">
            <a href="index.php?page=orders" class="nav-link px-3 py-2 rounded-2 <?php echo ($current_page === 'orders') ? 'bg-primary text-white fw-bold' : 'text-dark'; ?>">
                <i class="fa-solid fa-cart-shopping me-2"></i> Orders
            </a>
        </li>

        <!-- CUSTOMERS LINK -->
        <li class="list-group-item bg-transparent border-0 py-1">
            <a href="index.php?page=customers" class="nav-link px-3 py-2 rounded-2 <?php echo ($current_page === 'customers') ? 'bg-primary text-white fw-bold' : 'text-dark'; ?>">
                <i class="fa-solid fa-users me-2"></i> Customers
            </a>
        </li>

        <!-- ANALYTICS LINK -->
        <li class="list-group-item bg-transparent border-0 py-1">
            <a href="index.php?page=analytics" class="nav-link px-3 py-2 rounded-2 <?php echo ($current_page === 'analytics') ? 'bg-primary text-white fw-bold' : 'text-dark'; ?>">
                <i class="fa-solid fa-chart-line me-2"></i> Analytics
            </a>
        </li>

        <!-- SETTINGS LINK -->
        <li class="list-group-item bg-transparent border-0 py-1">
            <a href="index.php?page=settings" class="nav-link px-3 py-2 rounded-2 <?php echo ($current_page === 'settings') ? 'bg-primary text-white fw-bold' : 'text-dark'; ?>">
                <i class="fa-solid fa-gear me-2"></i> Settings
            </a>
        </li>
    </ul>
</div>