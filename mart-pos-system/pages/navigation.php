<?php
$conn = mysqli_connect('localhost', 'root', '', 'mart_pos_system');
// pages/dashboard.php


// Simple queries to show counters on the grid if needed later
$brand_res    = mysqli_query($conn, "SELECT * FROM brands");
$total_brands = mysqli_num_rows($brand_res);

$cat_res      = mysqli_query($conn, "SELECT * FROM categories");
$total_cats   = mysqli_num_rows($cat_res);
?>

<div class="container-fluid px-4 pt-4">
    <div class="mb-4">
        <h2 class="h3 mb-0 text-gray-800 fw-bold">Navigations</h2>
        <p class="text-muted">Quickly access and manage all your store operation modules.</p>
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <a href="index.php?page=dashboard" class="text-decoration-none">
                <div class="card h-100 shadow-sm border-0 p-3 navigation-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-1">Dashboard</h5>
                            <p class="text-muted small mb-0">Go to Overview Stats</p>
                        </div>
                        <i class="bi bi-grid-1x2-fill fs-3 text-primary opacity-75"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="index.php?page=sales" class="text-decoration-none">
                <div class="card h-100 shadow-sm border-0 p-3 navigation-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-1">Sale POS</h5>
                            <p class="text-muted small mb-0">Go to Register Screen</p>
                        </div>
                        <i class="bi bi-cart4 fs-3 text-primary opacity-75"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="index.php?page=bills" class="text-decoration-none">
                <div class="card h-100 shadow-sm border-0 p-3 navigation-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-1">Bills</h5>
                            <p class="text-muted small mb-0">View Past Receipts</p>
                        </div>
                        <i class="bi bi-receipt fs-3 text-primary opacity-75"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="index.php?page=purchases" class="text-decoration-none">
                <div class="card h-100 shadow-sm border-0 p-3 navigation-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-1">Purchases</h5>
                            <p class="text-muted small mb-0">Manage Supplier Stock</p>
                        </div>
                        <i class="bi bi-bag-check-fill fs-3 text-primary opacity-75"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="index.php?page=inventory" class="text-decoration-none">
                <div class="card h-100 shadow-sm border-0 p-3 navigation-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-1">Inventory</h5>
                            <p class="text-muted small mb-0">Check Stock Levels</p>
                        </div>
                        <i class="bi bi-boxes fs-3 text-primary opacity-75"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="index.php?page=products" class="text-decoration-none">
                <div class="card h-100 shadow-sm border-0 p-3 navigation-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="fw-bold mb-1">Products</h5>
                            <p class="text-muted small mb-0">Edit Item Catalog</p>
                        </div>
                        <i class="bi bi-box-seam-fill fs-3 text-primary opacity-75"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="index.php?page=categories" class="text-decoration-none">
                <div class="card h-100 shadow-sm border-0 p-3 navigation-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="fw-bold mb-1">Categories</h5>
                                <span class="badge bg-secondary-subtle text-body rounded-pill px-2" style="font-size: 0.75rem;">4</span>
                            </div>
                            <p class="text-muted small mb-0">Manage Groups</p>
                        </div>
                        <i class="bi bi-tags-fill fs-3 text-primary opacity-75"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="index.php?page=brands" class="text-decoration-none">
                <div class="card h-100 shadow-sm border-0 p-3 navigation-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="fw-bold mb-1">Brands</h5>
                                <span class="badge bg-secondary-subtle text-body rounded-pill px-2" style="font-size: 0.75rem;">2</span>
                            </div>
                            <p class="text-muted small mb-0">Manage Makers</p>
                        </div>
                        <i class="bi bi-patch-check-fill fs-3 text-primary opacity-75"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="p-4 rounded text-white text-center shadow-sm" style="background: linear-gradient(135deg, #FF1F57 0%, #B9003C 100%);">
                <h4 class="fw-bold mb-1">Boost Your Sales with Our POS</h4>
                <p class="mb-0 text-white-50 small">Quick Checkout • Stock Control • Database Billing Management</p>
            </div>
        </div>
    </div>
</div>