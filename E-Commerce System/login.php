<?php
session_start();
include 'includes/header.php';
?>

<div class="row justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="col-sm-9 col-md-4">

        <!-- Display Feedback Messages -->
        <?php if (isset($_GET['registration']) && $_GET['registration'] === 'success'): ?>
            <!-- FIXED: Changed data-bs-dismiss to "alert" so the close button works -->
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>Account created! Please login below.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid'): ?>
            <!-- FIXED: Changed data-bs-dismiss to "alert" so the close button works -->
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i>Invalid email address or password.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow border-0 p-3">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fa-solid fa-lock text-primary fa-3x mb-2"></i>
                    <h4 class="fw-bold text-dark">Welcome Back</h4>
                    <p class="text-muted small">Sign in to manage your e-commerce account</p>
                </div>

                <form action="actions/auth/auth-login.php" method="POST">
                    <!-- EMAIL FIELD -->
                    <div class="mb-3">
                        <label for="email" class="form-label small fw-bold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control" required placeholder="name@example.com">
                        </div>
                    </div>

                    <!-- PASSWORD FIELD -->
                    <div class="mb-4">
                        <label for="password" class="form-label small fw-bold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-key"></i></span>
                            <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
                        </div>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <button type="submit" name="submit_login" class="btn btn-primary w-100 py-2 fw-bold shadow-sm ">
                        Sign In
                    </button>
                </form>

                <!-- Divider between traditional login and social login -->
                <div class="position-relative my-4 text-center">
                    <hr class="text-muted">
                    <span class="position-absolute top-50 start-50 translate-middle px-2 small text-muted bg-white">or</span>
                </div>

                <!-- Google Login -->
                <div class="mb-4">
                    <a href="<?php //getGoogleLoginUrl(); 
                                ?>" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2 py-2 fw-medium shadow-sm">
                        <!-- Swapped to FontAwesome to match the rest of your form icons, but kept your bootstrap fallback comment if needed -->
                        <i class="fa-brands fa-google text-danger"></i> Sign in with Google
                    </a>
                </div>

                <div class="text-center">
                    <p class="small text-muted mb-0">Don't have an account? <a href="register.php" class="fw-bold text-decoration-none">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>