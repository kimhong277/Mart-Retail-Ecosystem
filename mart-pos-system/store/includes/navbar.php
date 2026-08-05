<?php

// require_once "../customer-session.php";

/**
 * Navbar Component - store/includes/navbar.php
 * Shared navbar for all store pages
 */
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="index.php">
            <i class="bi bi-shop text-primary me-2"></i>DAILY MART
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav ms-auto d-flex gap-2 align-items-center">
                <!-- Cart Button -->
                <a href="cart.php" class="btn btn-primary position-relative rounded-pill px-3">
                    <i class="bi bi-cart3 me-1"></i> Cart
                    <span id="cartCountBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">0</span>
                </a>

                <?php if ($customer): ?>
                    <!-- Customer Logged In -->
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle rounded-pill px-3" type="button" id="customerDropdown" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars(substr($customer['name'], 0, 15)); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="customerDropdown">
                            <li>
                                <span class="dropdown-item-text small text-muted">
                                    <?php echo htmlspecialchars($customer['email']); ?>
                                </span>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="my-orders.php">
                                    <i class="bi bi-box-seam me-2"></i>My Orders
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="customer-profile.php">
                                    <i class="bi bi-person me-2"></i>My Profile
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="customer-logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- Not Logged In -->
                    <a href="customer-login.php" class="btn btn-outline-light rounded-pill px-3">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Login
                    </a>
                    <a href="customer-register.php" class="btn btn-primary rounded-pill px-3">
                        <i class="bi bi-person-plus me-1"></i>Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>