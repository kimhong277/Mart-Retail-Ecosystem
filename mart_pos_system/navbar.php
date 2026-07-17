<nav class="navbar navbar-expand-lg navbar-light w-100 px-3 shadow" style=" border-color: #334155 !important;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-body tracking-wide" href="#">
            <i class="bi bi-cpu-fill text-primary me-2"></i>MART TERMINAL
        </a>

        <button class="navbar-toggler border-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
                <li class="nav-item">
                    <a class="nav-link active fw-bold text-primary" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Dropdown
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark border-secondary shadow-lg mt-2" aria-labelledby="navbarDropdown" style="background-color: #1e293b;">
                        <li><a class="dropdown-item" href="#">Action</a></li>
                        <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li>
                            <hr class="dropdown-divider border-secondary">
                        </li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3 ms-auto">

                <button type="button"
                    class="btn btn-link nav-link p-2 border-0 text-secondary"
                    id="navbarThemeToggler"
                    onclick="toggleLayoutTheme()"
                    title="Toggle Interface Color State">
                    <i class="bi bi-moon-stars-fill fs-5" id="themeIcon"></i>
                </button>

                <div class="dropdown">
                    <a class="d-flex align-items-center text-decoration-none dropdown-toggle cp-pointer"
                        href="#"
                        id="userProfileDropdown"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <div class="position-relative">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm "
                                style="width: 36px; height: 36px; font-size: 0.9rem; letter-spacing: 0.5px;">
                                <?= strtoupper(substr($_SESSION['username'] ?? '', 0, 1)); ?>
                            </div>
                            <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-dark rounded-circle" title="Operator Active"></span>
                        </div>
                        <div class="ms-2 d-none d-sm-block text-start">
                            <p class="text-white small fw-bold mb-0 lh-sm"><?= htmlspecialchars($_SESSION['fullname'] ?? 'Kimhong Long'); ?></p>
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

                        <li>
                            <a class="dropdown-item py-2 small d-flex align-items-center" href="index.php?page=settings&tab=user">
                                <i class="bi bi-person-bounding-box text-secondary me-2 fs-5"></i> My Account Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 small d-flex align-items-center" href="index.php?page=settings&tab=profile">
                                <i class="bi bi-sliders2-vertical text-secondary me-2 fs-5"></i> System Settings
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider border-secondary my-1">
                        </li>

                        <li>
                            <a class="dropdown-item py-2 small d-flex align-items-center text-danger hover-bg-danger-soft"
                                href="logout.php">
                                <!-- onclick="return confirm('Terminate terminal operator session and secure lock interface?');"> -->
                                <i class="bi bi-box-arrow-right me-2 fs-5"></i> Secure Logout Terminal
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>