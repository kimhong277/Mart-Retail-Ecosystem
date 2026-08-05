<?php

/**
 * Footer Component - store/includes/footer.php
 * Shared footer for all store pages
 */
?>
<footer class="bg-dark text-white py-5 mt-auto ">
    <div class="container">
        <div class="row g-4 mb-4">
            <!-- Brand Info -->
            <div class="col-md-3">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-shop text-primary me-2"></i>Mart Online
                </h5>
                <p class=" small mb-3 text-secondary">
                    Your trusted online shopping destination for quality products and exceptional service.
                </p>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle p-2">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle p-2">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle p-2">
                        <i class="bi bi-instagram"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-md-3">
                <h6 class="fw-bold mb-3">Quick Links</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2">
                        <a href="index.php" class="text-decoration-none text-secondary hover-primary">Shop Now</a>
                    </li>
                    <li class="mb-2">
                        <a href="index.php" class="text-decoration-none text-secondary hover-primary">Best Sellers</a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo isCustomerLoggedIn() ? 'my-orders.php' : 'customer-login.php'; ?>" class="text-decoration-none text-secondary hover-primary">My Orders</a>
                    </li>
                    <li class="mb-2">
                        <a href="<?php echo isCustomerLoggedIn() ? 'customer-profile.php' : 'customer-login.php'; ?>" class="text-decoration-none text-secondary hover-primary">My Account</a>
                    </li>
                </ul>
            </div>

            <!-- Support -->
            <div class="col-md-3">
                <h6 class="fw-bold mb-3">Support</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2 ">

                        <i class="bi bi-telephone text-secondary me-2 "></i>
                        <a href="tel:+855123456789" class="text-decoration-none text-secondary ">(+855) 123 456-789</a>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-envelope text-secondary me-2"></i>
                        <a href="mailto:support@martstore.com" class="text-decoration-none text-secondary">support@martstore.com</a>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-geo-alt text-secondary me-2"></i>
                        <span class="text-secondary">Phnom Penh, Cambodia</span>
                    </li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="col-md-3">
                <h6 class="fw-bold mb-3">Newsletter</h6>
                <p class="small text-secondary mb-3">Subscribe to get special offers and updates</p>
                <form class="d-flex" onsubmit="event.preventDefault(); alert('Newsletter signup coming soon!');">
                    <input type="email" class="form-control form-control-sm rounded-start" placeholder="Your email" required>
                    <button type="submit" class="btn btn-primary btn-sm rounded-end px-3">
                        <i class="bi bi-send"></i>
                    </button>
                </form>
            </div>
        </div>

        <hr class="bg-secondary">

        <!-- Bottom Footer -->
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="text-muted small mb-0">
                    &copy; 2026 Mart Online Store. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="text-muted small mb-0">
                    <a href="#" class="text-decoration-none text-muted">Privacy Policy</a> &nbsp;|&nbsp;
                    <a href="#" class="text-decoration-none text-muted">Terms of Service</a> &nbsp;|&nbsp;
                    <a href="#" class="text-decoration-none text-muted">Contact Us</a>
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
    .hover-primary {
        transition: color 0.3s;
    }

    .hover-primary:hover {
        color: var(--bs-primary) !important;
    }
</style>