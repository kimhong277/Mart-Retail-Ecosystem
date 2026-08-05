<?php
// store/customer-register.php
require_once 'customer-session.php';

// If already logged in, redirect to store
if (isCustomerLoggedIn()) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Mart Online Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
        }

        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
        }

        .btn-register:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
        }

        .login-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link:hover {
            text-decoration: underline;
        }

        .input-group-text {
            border-color: #e0e0e0;
            background-color: #f8f9fa;
        }

        .password-requirements {
            font-size: 0.85rem;
            margin-top: 8px;
        }

        .requirement {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6c757d;
            margin: 4px 0;
        }

        .requirement.met {
            color: #28a745;
        }

        .requirement i {
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="register-card">
        <div class="register-header">
            <i class="bi bi-shop" style="font-size: 2.5rem;"></i>
            <h2 class="fw-bold mt-2 mb-0">Mart Online</h2>
            <p class="text-white-50 mb-0 small mt-1">Create Your Account</p>
        </div>

        <div class="card-body p-4">
            <?php
            if (isset($_GET['error'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>' . htmlspecialchars($_GET['error']) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
            }
            ?>

            <form method="POST" action="process-customer-register.php" id="registerForm">
                <div class="mb-3">
                    <label class="form-label fw-bold small mb-2">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="full_name" class="form-control" placeholder="Your Full Name" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small mb-2">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small mb-2">Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                        <input type="tel" name="phone" class="form-control" placeholder="012 345 678" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small mb-2">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Create a strong password" required>
                    </div>
                    <div class="password-requirements">
                        <div class="requirement" id="req-length">
                            <i class="bi bi-circle"></i>
                            <span>At least 8 characters</span>
                        </div>
                        <div class="requirement" id="req-uppercase">
                            <i class="bi bi-circle"></i>
                            <span>One uppercase letter (A-Z)</span>
                        </div>
                        <div class="requirement" id="req-number">
                            <i class="bi bi-circle"></i>
                            <span>One number (0-9)</span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small mb-2">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-check"></i></span>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter your password" required>
                    </div>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="agree_terms" id="agreeTerms" required>
                    <label class="form-check-label small" for="agreeTerms">
                        I agree to the Terms of Service
                    </label>
                </div>

                <button type="submit" class="btn btn-register w-100 text-white mb-3">
                    <i class="bi bi-person-plus me-2"></i>Create Account
                </button>
            </form>

            <hr class="my-3">

            <div class="text-center">
                <p class="small mb-2">Already have an account?</p>
                <a href="customer-login.php" class="login-link">Sign In</a>
            </div>

            <hr class="my-3">

            <div class="text-center">
                <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i>Continue Shopping
                </a>
            </div>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('passwordInput');
        const reqLength = document.getElementById('req-length');
        const reqUppercase = document.getElementById('req-uppercase');
        const reqNumber = document.getElementById('req-number');

        passwordInput.addEventListener('input', function() {
            const pwd = this.value;

            // Check length
            if (pwd.length >= 8) {
                reqLength.classList.add('met');
                reqLength.querySelector('i').className = 'bi bi-check-circle-fill';
            } else {
                reqLength.classList.remove('met');
                reqLength.querySelector('i').className = 'bi bi-circle';
            }

            // Check uppercase
            if (/[A-Z]/.test(pwd)) {
                reqUppercase.classList.add('met');
                reqUppercase.querySelector('i').className = 'bi bi-check-circle-fill';
            } else {
                reqUppercase.classList.remove('met');
                reqUppercase.querySelector('i').className = 'bi bi-circle';
            }

            // Check number
            if (/[0-9]/.test(pwd)) {
                reqNumber.classList.add('met');
                reqNumber.querySelector('i').className = 'bi bi-check-circle-fill';
            } else {
                reqNumber.classList.remove('met');
                reqNumber.querySelector('i').className = 'bi bi-circle';
            }
        });

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                Swal.fire('Password Mismatch', 'Passwords do not match', 'error');
                return;
            }

            if (!/[A-Z]/.test(password) || !/[0-9]/.test(password) || password.length < 8) {
                e.preventDefault();
                Swal.fire('Weak Password', 'Password does not meet requirements', 'warning');
            }
        });
    </script>
</body>

</html>