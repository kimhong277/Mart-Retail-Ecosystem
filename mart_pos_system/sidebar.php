<?php
// 1. Get the current active page handle from the browser URL parameter safely
$active_page = $_GET['page'] ?? 'dashboard'; // Defaults to dashboard if no page parameter is set

// 2. Separate check for your collapsible setting menu wrapper layout panel
$is_settings_active = ($active_page === 'settings');
$current_tab = $_GET['tab'] ?? '';
?>

<nav class="sidebar shadow position-fixed top-0 start-0 vh-100 bg-white d-flex flex-column pt-4 px-3" aria-label="Sidebar Dashboard Navigation" style="width: 280px; z-index: 1030;">
    <div class="mb-4 px-2">
        <h5 class="shadow-sm p-3 bg-dark text-white rounded rounded-3 text-center fw-bold tracking-wide m-0">
            <i class="bi bi-speedometer2 me-2 text-primary"></i>MINI POS
        </h5>
    </div>

    <div class="flex-grow-1 overflow-y-auto px-1" style="max-height: calc(100vh - 160px);">
        <ul class="navbar-nav p-0 m-0 gap-1">

            <li class="nav-item">
                <a href="index.php?page=navigation" class="nav-link text-dark fw-semibold d-flex align-items-center px-2 rounded <?= $active_page === 'navigation' ? 'bg-light text-primary active-link' : '' ?>">
                    <i class="bi bi-compass-fill me-2 <?= $active_page === 'navigation' ? 'text-primary' : 'text-secondary' ?> me-2.5 fs-5"></i> Navigation
                </a>
            </li>

            <li class="nav-item">
                <a href="index.php?page=dashboard" class="nav-link text-dark fw-semibold d-flex align-items-center px-2 rounded <?= $active_page === 'dashboard' ? 'bg-light text-primary active-link' : '' ?>">
                    <i class="bi bi-grid-1x2-fill me-2 <?= $active_page === 'dashboard' ? 'text-primary' : 'text-secondary' ?> me-2.5 fs-5"></i> Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="index.php?page=sales" class="nav-link text-dark fw-semibold d-flex align-items-center px-2 rounded <?= $active_page === 'sales' ? 'bg-light text-primary active-link' : '' ?>">
                    <i class="bi bi-cart4 me-2 <?= $active_page === 'sales' ? 'text-primary' : 'text-secondary' ?> me-2.5 fs-5"></i> Sale POS
                </a>
            </li>

            <li class="nav-item">
                <a href="index.php?page=bills" class="nav-link text-dark fw-semibold d-flex align-items-center px-2 rounded <?= $active_page === 'bills' ? 'bg-light text-primary active-link' : '' ?>">
                    <i class="bi bi-receipt me-2 <?= $active_page === 'bills' ? 'text-primary' : 'text-secondary' ?> me-2.5 fs-5"></i> Bills
                </a>
            </li>

            <li class="nav-item">
                <a href="index.php?page=purchases" class="nav-link text-dark fw-semibold d-flex align-items-center px-2 rounded <?= $active_page === 'purchases' ? 'bg-light text-primary active-link' : '' ?>">
                    <i class="bi bi-bag-check-fill me-2 <?= $active_page === 'purchases' ? 'text-primary' : 'text-secondary' ?> me-2.5 fs-5"></i> Purchases
                </a>
            </li>

            <li class="nav-item">
                <a href="index.php?page=quotations" class="nav-link text-dark fw-semibold d-flex align-items-center px-2 rounded <?= $active_page === 'quotations' ? 'bg-light text-primary active-link' : '' ?>">
                    <i class="bi bi-file-earmark-text-fill me-2 <?= $active_page === 'quotations' ? 'text-primary' : 'text-secondary' ?> me-2.5 fs-5"></i> Quotations
                </a>
            </li>

            <li class="nav-item">
                <a href="index.php?page=inventory" class="nav-link text-dark fw-semibold d-flex align-items-center px-2 rounded <?= $active_page === 'inventory' ? 'bg-light text-primary active-link' : '' ?>">
                    <i class="bi bi-boxes me-2 <?= $active_page === 'inventory' ? 'text-primary' : 'text-secondary' ?> me-2.5 fs-5"></i> Inventory
                </a>
            </li>

            <li class="nav-item">
                <a href="index.php?page=products" class="nav-link text-dark fw-semibold d-flex align-items-center px-2 rounded <?= $active_page === 'products' ? 'bg-light text-primary active-link' : '' ?>">
                    <i class="bi bi-box-seam-fill me-2 <?= $active_page === 'products' ? 'text-primary' : 'text-secondary' ?> me-2.5 fs-5"></i> Products
                </a>
            </li>

            <li class="nav-item">
                <a href="index.php?page=categories" class="nav-link text-dark fw-semibold d-flex align-items-center px-2 rounded <?= $active_page === 'categories' ? 'bg-light text-primary active-link' : '' ?>">
                    <i class="bi bi-tags-fill me-2 <?= $active_page === 'categories' ? 'text-primary' : 'text-secondary' ?> me-2.5 fs-5"></i> Categories
                </a>
            </li>

            <li class="nav-item">
                <a href="index.php?page=brands" class="nav-link text-dark fw-semibold d-flex align-items-center px-2 rounded <?= $active_page === 'brands' ? 'bg-light text-primary active-link' : '' ?>">
                    <i class="bi bi-patch-check-fill me-2 <?= $active_page === 'brands' ? 'text-primary' : 'text-secondary' ?> me-2.5 fs-5"></i> Brands
                </a>
            </li>

            <li class="nav-item">
                <a href="index.php?page=transactions" class="nav-link text-dark fw-semibold d-flex align-items-center px-2 rounded <?= $active_page === 'transactions' ? 'bg-light text-primary active-link' : '' ?>">
                    <i class="bi bi-cash-stack me-2 <?= $active_page === 'transactions' ? 'text-primary' : 'text-secondary' ?> me-2.5 fs-5"></i> Transactions
                </a>
            </li>

            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin'): ?>
                <li class="nav-item">
                    <a href="index.php?page=accounts" class="nav-link text-dark fw-semibold d-flex align-items-center px-2 rounded <?= $active_page === 'accounts' ? 'bg-light text-primary active-link' : '' ?>">
                        <i class="bi bi-shield-lock-fill me-2 <?= $active_page === 'accounts' ? 'text-primary' : 'text-secondary' ?> me-2.5 fs-5"></i> Accounts
                    </a>
                </li>

                <li class="nav-item">
                    <!-- 💡 FIXED: href pushes the page to the user tab, while onclick forces the dropdown toggle simultaneously -->
                    <a class="nav-link dropdown-toggle text-dark fw-semibold d-flex align-items-center justify-content-between px-2 rounded <?= $is_settings_active ? 'bg-light text-primary active-link' : '' ?>"
                        href="index.php?page=settings&tab=user"
                        role="button"
                        onclick="event.preventDefault(); new bootstrap.Collapse(document.getElementById('settingSubmenu')).toggle(); setTimeout(() => { window.location.href = this.href; }, 150);">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-gear-fill me-2 <?= $is_settings_active ? 'text-primary' : 'text-secondary' ?> me-2.5 fs-5"></i> Setting
                        </div>
                    </a>

                    <!-- 💡 Keeps its state open on load via PHP, but remains completely dynamic on click -->
                    <div class="collapse <?= $is_settings_active ? 'show' : '' ?>" id="settingSubmenu">
                        <ul class="nav flex-column ps-4 mt-1 gap-1 border-start ms-3 text-muted">
                            <li class="nav-item">
                                <a class="nav-link py-1 fw-medium d-flex align-items-center rounded <?= ($is_settings_active && $current_tab === 'user') ? 'text-primary fw-bold' : 'text-secondary' ?>" href="index.php?page=settings&tab=user">
                                    <i class="bi bi-people me-2"></i> Users Config
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 fw-medium d-flex align-items-center rounded <?= ($is_settings_active && $current_tab === 'profile') ? 'text-primary fw-bold' : 'text-secondary' ?>" href="index.php?page=settings&tab=profile">
                                    <i class="bi bi-building-gear me-2"></i> Profile Meta
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-1 fw-medium d-flex align-items-center rounded <?= ($is_settings_active && $current_tab === 'appearance') ? 'text-primary fw-bold' : 'text-secondary' ?>" href="index.php?page=settings&tab=appearance">
                                    <i class="bi bi-palette me-2"></i> Appearance
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>

        </ul>
    </div>

    <div class="mt-auto pb-3 pt-2 border-top px-2">
        <a href="logout.php" class="nav-link text-danger fw-semibold d-flex align-items-center py-2 rounded">
            <i class="bi bi-box-arrow-left me-2 fs-5"></i> Logout Terminal
        </a>
    </div>
</nav>