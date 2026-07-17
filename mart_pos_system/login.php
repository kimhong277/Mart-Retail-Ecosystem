<?php
// login.php
session_start();
include("config.php");
// Redirect instantly to index if active session keys exist already
if (isset($_SESSION['username'])) {
    header("Location: index.php?page=dashboard");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mart POS System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/dist/min.css" rel="stylesheet"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            min-height: 100vh;
        }

        .brand-side {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        .fw-mono {
            font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }
    </style>
</head>

<body>

    <div class="container d-flex justify-content-center align-items-center min-vh-100 py-5">
        <div class="card shadow border-0 w-100" style="max-width: 450px;">

            <!-- Optional Top Header/Logo Area -->
            <div class="card-header bg-primary text-white text-center py-4 border-0">
                <h3 class="fw-bold mb-0">Mart POS System</h3>
                <p class="small text-white-50 mb-0">Please sign in to access your account</p>
            </div>

            <div class="card-body p-4 p-md-5">
                <form action="process_login.php" method="POST">
                    <!-- Username Field -->
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">Username or Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="bi bi-person-fill"></i>
                            </span>
                            <input type="text" name="username" class="form-control bg-light border-start-0" required placeholder="Enter username or email">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="mb-4">
                        <label class="form-label text-dark fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input type="password" name="password" class="form-control bg-light border-start-0" required placeholder="Enter your password">
                        </div>
                    </div>

                    <!-- Remember Me / Forgot Password (Standard Login Practice) -->
                    <div class="d-flex justify-content-between align-items-center mb-4 small">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe">
                            <label class="form-check-label text-muted" for="rememberMe">Remember me</label>
                        </div>
                        <a href="forgot_password.php" class="text-decoration-none text-primary">Forgot password?</a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" name="normal_login" class="btn btn-primary w-100 py-2 fw-bold shadow-sm mb-3">
                        Sign In
                    </button>
                </form>

                <!-- Divider -->
                <div class="position-relative my-4 text-center">
                    <hr class="text-muted">
                    <span class="position-absolute top-50 start-50 translate-middle px-3 small text-muted bg-white">or</span>
                </div>

                <!-- Google Login -->
                <div class="text-center">
                    <a href="<?= getGoogleLoginUrl(); ?>" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2 py-2 fw-medium shadow-sm">
                        <i class="bi bi-google text-danger"></i> Sign in with Google
                    </a>
                </div>
            </div>

            <!-- Footer Note -->
            <div class="card-footer bg-light text-center py-3 border-0">
                <span class="text-muted small">System Version 2.4</span>
            </div>

        </div>
    </div>
    <?php if (isset($_GET['status'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Fetch current UI theme rules 
                const systemTheme = localStorage.getItem('pos_theme') || 'dark';
                const isDark = (systemTheme === 'dark');

                // Define alert configuration defaults
                let alertConfig = {
                    icon: 'error',
                    confirmButtonColor: '#38bdf8',
                    background: isDark ? '#1e293b' : '#ffffff',
                    color: isDark ? '#f8fafc' : '#1e293b'
                };

                // Determine specific text strings based on your process_login.php routing parameters
                const status = "<?= htmlspecialchars($_GET['status']); ?>";

                if (status === 'invalid' || status === 'invalid_password') {
                    alertConfig.title = 'Authentication Failed';
                    alertConfig.text = 'The credentials entered do not match our terminal security registry. Please try again.';
                } else if (status === 'suspended') {
                    alertConfig.title = 'Access Denied';
                    alertConfig.text = 'This staff profile container has been suspended. Contact system administration.';
                } else if (status === 'use_google_auth') {
                    alertConfig.icon = 'info';
                    alertConfig.title = 'Cloud Authentication Required';
                    alertConfig.text = 'This account is linked via Google Cloud Secure Protocol. Please utilize the Sign In With Google action.';
                    alertConfig.iconColor = '#ef4444';
                } else if (status === 'auth_error' || status === 'token_error') {
                    alertConfig.title = 'Secure Token Error';
                    alertConfig.text = 'Failed to handshake securely with Google OAuth core infrastructure servers.';
                }

                // Fire the constructed config alert if a valid case matched
                if (alertConfig.title) {
                    Swal.fire(alertConfig).then(() => {
                        // Instantly strip out the status flag from the browser address bar to keep things clean!
                        const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                        window.history.replaceState({
                            path: cleanUrl
                        }, '', cleanUrl);
                    });
                }
            });
        </script>
    <?php endif; ?>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const statusFlag = urlParams.get('status');

        if (statusFlag) {
            let alertTitle = '';
            let alertText = '';
            let alertIcon = 'error';

            if (statusFlag === 'invalid') {
                alertTitle = 'Authentication Failed!';
                alertText = 'The specified username handle identifier row could not be found inside system registries.';
            } else if (statusFlag === 'suspended') {
                alertTitle = 'Account Suspended!';
                alertText = 'Access revoked. This personnel clearance profile has been locked out by administrators.';
                alertIcon = 'warning';
            }

            if (alertTitle !== '') {
                Swal.fire({
                    title: alertTitle,
                    text: alertText,
                    icon: alertIcon,
                    confirmButtonColor: '#0f172a'
                });
                // Remove trailing parameters from browser window URL path nicely
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }
    </script>

</body>

</html>