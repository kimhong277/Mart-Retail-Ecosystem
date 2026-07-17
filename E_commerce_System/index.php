<?php
include "./includes/header.php";

?>

<div class="d-flex flex-column min-vh-100 align-items-stretch">

    <?php include "./includes/navbar.php"; ?>

    <div class="d-flex flex-grow-1 " data-bs-theme="light">

        <div class="offcanvas-md offcanvas-start bg-white shadow flex-shrink-0" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel" style="width: 240px;">
            <div class="offcanvas-header d-md-none border-bottom">
                <h5 class="offcanvas-title fw-bold" id="sidebarMenuLabel">TAPto Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-0">
                <?php include "./includes/sidebar.php"; ?>
            </div>
        </div>
        <main class="flex-grow-1 p-4 overflow-y-auto bg-light vh-100">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

                <?php
                // 1. Get the 'page' parameter from the URL, default to 'dashboard' if empty
                $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

                // 2. Decide what content to render based on the URL parameter
                switch ($_GET['page'] ?? 'dashboard') {
                    case 'dashboard':
                        include "admin-dashboard.php";
                        break;
                    case 'products':
                        include "admin-products.php";
                        break;
                    case 'orders':
                        include "admin-orders.php";
                        break;
                    case 'cart':
                        include 'admin-carts.php';
                        break;
                    case 'analytics':
                        include 'admin-analytics.php';
                        break;
                    case 'settings':
                        include 'admin-settings.php';
                        break;
                    default:
                        // 404 Page if the user types an invalid page name
                        echo '<div class="container text-center my-5">';
                        echo '<h2>404 - Page Not Found</h2>';
                        echo '<p>Oops! The page you are looking for doesn\'t exist.</p>';
                        echo '</div>';
                        break;
                }
                ?>

            </div>
        </main>

    </div>
</div>

<?php
include "./includes/footer.php";
?>