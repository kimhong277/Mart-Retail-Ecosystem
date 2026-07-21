<?php
// login.php
require_once 'config.php';

$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$conn_pos = mysqli_connect($host, $db_user, $db_pass, 'mart_pos_system');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    <!-- Cleared up structural utility assets dependencies paths below -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card {
            max-width: 450px;
            border-radius: 1rem !important;
            overflow: hidden;
        }

        .card-header-gradient {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
    </style>
</head>

<body>

    <div class="container d-flex justify-content-center align-items-center min-vh-100 py-5">
        <div class="card shadow-lg border-0 w-100 login-card">

            <!-- Top Header Logo Area -->
            <div class="card-header card-header-gradient text-white text-center py-4 border-0">
                <h3 class="fw-bold mb-0 tracking-tight">Mart POS System</h3>
                <p class="small text-white-50 mb-0">Please sign in to access your account</p>
            </div>

            <div class="card-body p-4 p-md-5 bg-white">
                <form action="process_login.php" method="POST">
                    <!-- Username Field -->
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold small">Username or Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="bi bi-person-fill"></i>
                            </span>
                            <input type="text" name="username" class="form-control bg-light border-start-0" required placeholder="Enter username or email">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="mb-4">
                        <label class="form-label text-dark fw-semibold small">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input type="password" name="password" class="form-control bg-light border-start-0" required placeholder="Enter your password">
                        </div>
                    </div>

                    <!-- Remember Me / Forgot Password Links -->
                    <div class="d-flex justify-content-between align-items-center mb-4 small">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe">
                            <label class="form-check-label text-muted" for="rememberMe">Remember me</label>
                        </div>
                        <a href="forgot_password.php" class="text-decoration-none text-primary fw-semibold">Forgot password?</a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" name="normal_login" class="btn btn-primary w-100 py-2.5 fw-bold shadow-sm mb-2 rounded-3">
                        Sign In
                    </button>
                </form>

                <!-- Visual Separation Layout Frame -->
                <div class="position-relative my-4 text-center">
                    <hr class="text-muted opacity-25">
                    <span class="position-absolute top-50 start-50 translate-middle px-3 small text-muted bg-white">or</span>
                </div>

                <!-- Google OAuth Integration Portal Button Layout Route Link -->
                <div class="text-center">
                    <a href="<?php echo getGoogleLoginUrl(); ?>" class="btn btn-outline-dark w-100 d-flex align-items-center justify-content-center gap-2 py-2.5 fw-semibold shadow-sm rounded-3">
                        <i class="bi bi-google text-danger"></i> Sign in with Google
                    </a>
                </div>
            </div>

            <!-- Footer Meta Version Tag Display -->
            <div class="card-footer bg-light text-center py-3 border-0 border-top">
                <span class="text-muted font-monospace" style="font-size: 0.75rem;">System Version 2.4</span>
            </div>

        </div>
    </div>

    <!-- 🧠 UNIFIED CLIENT-SIDE ROUTING ALERT ENGINE -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');

            if (status) {
                // Read local caching variables to scale dark vs light styling formats smoothly
                const systemTheme = localStorage.getItem('pos_theme') || 'light';
                const isDark = (systemTheme === 'dark');

                let alertConfig = {
                    icon: 'error',
                    confirmButtonColor: '#0f172a',
                    background: isDark ? '#1e293b' : '#ffffff',
                    color: isDark ? '#f8fafc' : '#1e293b'
                };

                switch (status) {
                    case 'invalid':
                    case 'invalid_password':
                        alertConfig.title = 'Authentication Failed';
                        alertConfig.text = 'The specified credentials do not match our terminal registry. Please try again.';
                        break;
                    case 'suspended':
                        alertConfig.title = 'Access Denied';
                        alertConfig.text = 'This user profile container has been suspended. Please contact system administration.';
                        alertConfig.icon = 'warning';
                        break;
                    case 'use_google_auth':
                        alertConfig.title = 'Cloud SSO Required';
                        alertConfig.text = 'This account is linked via Google Cloud Secure Protocol. Please utilize the Sign In With Google framework.';
                        alertConfig.icon = 'info';
                        break;
                    case 'auth_error':
                    case 'token_error':
                        alertConfig.title = 'OAuth Handshake Failed';
                        alertConfig.text = 'Failed to securely exchange encryption token variables with Google Cloud server matrices.';
                        break;
                    case 'profile_error':
                        alertConfig.title = 'Scope Resolution Failure';
                        alertConfig.text = 'Unable to extract authenticated validation emails profile parameters context matching this client account.';
                        break;
                    case 'db_creation_failed':
                        alertConfig.title = 'Database Core Failure';
                        alertConfig.text = 'A database engine transaction initialization error blocked auto-provisioning details mapping.';
                        break;
                    case 'unauthorized':
                        alertConfig.title = 'Access Restrained';
                        alertConfig.text = 'Active session signature expired or clearance access tokens not found. Please log in first.';
                        alertConfig.icon = 'warning';
                        break;
                    default:
                        alertConfig.title = 'Operational Warning';
                        alertConfig.text = urlParams.get('msg') || 'An unexpected structural processing exception occurred.';
                        break;
                }

                // Execute the modal and wipe the address bar parameter safely immediately after
                if (alertConfig.title) {
                    Swal.fire(alertConfig).then(() => {
                        const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                        window.history.replaceState({
                            path: cleanUrl
                        }, '', cleanUrl);
                    });
                }
            }
        });
    </script>
</body>

</html>