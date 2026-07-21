<?php
// index.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kick unauthorized visitors straight back to the login gateway entry point
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include("./includes/header.php");
?>

<div class="d-flex min-vh-100 align-items-stretch">

    <div class="flex-shrink-0" style="width: 280px; min-width: 280px;">
        <?php include("sidebar.php"); ?>
    </div>

    <div class="flex-grow-1 bg-light content d-flex flex-column min-w-0">

        <?php include("navbar.php"); ?>

        <div class="container-fluid p-4">
            <?php
            // List of valid matching sub-page file names
            $allowed_pages = [
                'navigation',
                'dashboard',
                'brands',
                'categories',
                'products',
                'inventory',
                'sales',
                'bills',
                'purchases',
                'quotations',
                'transactions',
                'accounts',
                'settings'
            ];

            $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

            if (in_array($page, $allowed_pages)) {
                if (file_exists("pages/" . $page . ".php")) {
                    include("pages/" . $page . ".php");
                } else {
                    echo "
                    <div class='alert alert-warning text-center my-4' role='alert'>
                        The layout section file <strong>'pages/" . htmlspecialchars($page) . ".php'</strong> does not exist yet.
                    </div>";
                }
            } else {
                include("pages/dashboard.php");
            }
            ?>
        </div>

    </div>
</div>

<?php
include("./includes/footer.php");
?>