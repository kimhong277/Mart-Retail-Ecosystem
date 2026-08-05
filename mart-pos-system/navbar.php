<nav class="navbar navbar-expand-lg navbar-dark bg-white w-100 px-3 shadow sticky-top" style="top: 0; z-index: 1030;border-color: #334155 !important;">
    <div class="container-fluid">
        <!-- 🏢 Brand Logo Section -->
        <a class="navbar-brand fw-bold text-body tracking-wide d-flex align-items-center" href="index.php?page=dashboard">
            <i class="bi bi-cpu-fill text-primary me-2"></i>MART TERMINAL
        </a>

        <!-- ⏰ Real-Time Live Clock Widget Widget Component -->
        <div class="d-none d-md-flex align-items-center text-secondary small ms-3 bg-light border px-3 py-1 rounded-pill font-monospace" style="font-size: 0.8rem;">
            <i class="bi bi-clock-fill text-primary me-2"></i>
            <span id="terminal-live-clock">Syncing clock matrix...</span>
        </div>

        <button class="navbar-toggler border-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- 💡 REMOVED: Old placeholder links (Home, Link, Dropdown) left a clean workspace layout here -->

            <div class="d-flex align-items-center gap-3 ms-auto mt-2 mt-lg-0">
                <!-- ⚡ Rapid-Action POS Link Shortcut -->
                <a href="index.php?page=sales" class="btn btn-sm btn-primary fw-bold d-flex align-items-center gap-1 px-3 rounded-2 shadow-sm" style="font-size: 0.8rem;">
                    <i class="bi bi-calculator-fill"></i> New Sale
                </a>

                <!-- Theme Toggler Component Toggle Interface Color State -->
                <button type="button"
                    class="btn btn-link nav-link p-2 border-0 text-secondary"
                    id="navbarThemeToggler"
                    onclick="toggleLayoutTheme()"
                    title="Toggle Interface Color State">
                    <i class="bi bi-moon-stars-fill fs-5" id="themeIcon"></i>
                </button>

                <!-- Operator Identity Badge Dropdown Menu Context -->
                <div class="dropdown">
                    <a class="d-flex align-items-center text-decoration-none dropdown-toggle cp-pointer"
                        href="#"
                        id="userProfileDropdown"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <div class="position-relative">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                style="width: 36px; height: 36px; font-size: 0.9rem; letter-spacing: 0.5px;">
                                <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                            </div>
                            <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-dark rounded-circle" title="Operator Active"></span>
                        </div>
                        <div class="ms-2 d-none d-sm-block text-start">
                            <p class="text-dark small fw-bold mb-0 lh-sm"><?= htmlspecialchars($_SESSION['fullname'] ?? 'Kimhong Long'); ?></p>
                            <span class="text-muted text-uppercase tracking-wider" style="font-size: 0.65rem; font-weight: 700;"><?= htmlspecialchars($_SESSION['user_role'] ?? 'Admin'); ?></span>
                        </div>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark border-secondary shadow-lg mt-2 animated fadeIn fast"
                        aria-labelledby="userProfileDropdown"
                        style="background-color: #1e293b; border-color: #334155 !important; min-width: 200px;">

                        <li class="px-3 py-2 border-bottom border-secondary mb-1">
                            <span class="text-secondary d-block small">Logged in as</span>
                            <strong class="text-white small d-block text-truncate" title="<?= htmlspecialchars($_SESSION['email'] ?? ""); ?>"><?= htmlspecialchars($_SESSION['email'] ?? ""); ?></strong>
                        </li>

                        <!-- 👥 REPAIRED: Points to your new standalone accounts view page frame -->
                        <li>
                            <a class="dropdown-item py-2 small d-flex align-items-center" href="index.php?page=accounts">
                                <i class="bi bi-person-bounding-box text-secondary me-2 fs-5"></i> Staff Accounts
                            </a>
                        </li>

                        <!-- ⚙️ REPAIRED: Points directly to your single global preferences panel framework -->
                        <li>
                            <a class="dropdown-item py-2 small d-flex align-items-center" href="index.php?page=settings">
                                <i class="bi bi-sliders2-vertical text-secondary me-2 fs-5"></i> System Settings
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider border-secondary my-1">
                        </li>

                        <li>
                            <a class="dropdown-item py-2 small d-flex align-items-center text-danger hover-bg-danger-soft" href="logout.php">
                                <i class="bi bi-box-arrow-right me-2 fs-5"></i> Secure Logout Terminal
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- ⚡ TERMINAL CLOCK REALTIME INITIALIZATION -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        function runTerminalClock() {
            const timeContainer = document.getElementById('terminal-live-clock');
            if (!timeContainer) return;

            const currentTimestamp = new Date();
            const clockString = currentTimestamp.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            }) + ' — ' + currentTimestamp.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });

            timeContainer.textContent = clockString;
        }
        setInterval(runTerminalClock, 1000);
        runTerminalClock();
    });
</script>