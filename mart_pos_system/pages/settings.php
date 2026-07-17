<?php
// pages/settings.php
require_once 'db.php';

// 1. Capture the target sub-tab from the sidebar URL, default to 'user' config if blank
$current_tab = $_GET['tab'] ?? 'user';
?>

<div class="container-fluid px-4 pt-4">
    <div class="mb-4">
        <h2 class="h3 mb-0 text-gray-800 fw-bold">System Configuration Settings</h2>
        <p class="text-muted small mb-0">Update global organizational store identity profiles, terminal access keys, and interface styles.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm bordered mb-4">
                <div class="card-body p-4">

                    <?php if ($current_tab === 'user'): ?>
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center text-dark">
                                <i class="bi bi-people-fill fs-4 me-2 text-primary"></i>
                                <h5 class="fw-bold mb-0">Users Configuration</h5>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="bi bi-person-plus-fill me-1"></i> Add New User
                            </button>
                        </div>

                        <p class="text-muted small">Manage system profile access permissions, terminal sessions settings, and staff operational logs.</p>
                        <hr class="border-secondary opacity-20">

                        <div class="table-responsive rounded-3 shadow-sm mt-3">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>USERNAME</th>
                                        <th>FULL NAME</th>
                                        <th>Email</th>
                                        <th>ROLE STATUS</th>
                                        <th class="text-center">ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch existing system users
                                    $users_query = mysqli_query($conn, "SELECT id, username, fullname,email, role FROM users ORDER BY id DESC");
                                    if (mysqli_num_rows($users_query) > 0):
                                        while ($user = mysqli_fetch_assoc($users_query)):
                                            // Normalize the role string to lowercase for safe color checking
                                            $normalized_role = strtolower($user['role']);

                                            // Determine badge style color dynamically based on user privilege role
                                            $badge_class = 'bg-secondary text-light';
                                            if ($normalized_role === 'admin') {
                                                $badge_class = 'bg-danger text-white';
                                            } elseif ($normalized_role === 'manager') {
                                                $badge_class = 'bg-info text-dark';
                                            }
                                    ?>
                                            <tr>
                                                <td class="fw-bold text-secondary">#<?php echo $user['id']; ?></td>
                                                <td class="text-white fw-semibold"><?php echo htmlspecialchars($user['username']); ?></td>
                                                <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td>
                                                    <span class="badge <?php echo $badge_class; ?> px-2 py-1 small">
                                                        <?php echo strtoupper($user['role']); ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-light py-0 px-2 me-1 edit-user-btn"
                                                        data-id="<?php echo $user['id']; ?>"
                                                        title="Edit Permissions">
                                                        <i class="bi bi-pencil-square text-primary"></i>
                                                    </button>

                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-light py-0 px-2"
                                                        onclick="confirmDelete(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>')"
                                                        title="Delete User">
                                                        <i class="bi bi-trash3-fill text-danger"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php
                                        endwhile;
                                    else:
                                        ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted small">No secondary system users registered yet.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="modal fade" id="addUserModal" static tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content style-dark border-0 shadow" style="background-color: #1e293b; color: #f1f5f9;">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold" id="addUserModalLabel">
                                            <i class="bi bi-person-plus-fill me-2 text-primary"></i>Create Terminal Operator
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="process_user.php" method="POST">
                                        <div class="modal-body py-4">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-secondary">Account Login Name (Username)</label>
                                                    <input type="text" name="username" class="form-control" placeholder="e.g. kimhong" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-secondary">Staff Complete Full Name</label>
                                                    <input type="text" name="fullname" class="form-control" placeholder="e.g. Kimhong Long" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-secondary">Email</label>
                                                    <input type="email" name="email" class="form-control" placeholder="e.g. kimhonglong@gmail.com" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-secondary">Secure Entry Access Password</label>
                                                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-secondary">System Assignment Privilege Level</label>
                                                    <select name="role" id="edit_role" class="form-select" required>
                                                        <option value="Cashier">Cashier (Standard Terminal Operator)</option>
                                                        <option value="Manager">Manager (Operational Store Supervisor)</option>
                                                        <option value="Admin">Administrator (Full Control Access)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" name="register_user" class="btn btn-primary btn-sm px-4">
                                                <i class="bi bi-shield-lock-fill me-1"></i> Authorize Profile
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow" style="background-color: #1e293b; color: #f1f5f9;">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold" id="editUserModalLabel">
                                            <i class="bi bi-pencil-square me-2 text-primary"></i>Modify Operator Terminal Profile
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="process_user.php" method="POST">
                                        <input type="hidden" name="user_id" id="edit_user_id">

                                        <div class="modal-body py-4">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-secondary">Account Login Name (Username)</label>
                                                    <input type="text" name="username" id="edit_username" class="form-control" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-secondary">Staff Complete Full Name</label>
                                                    <input type="text" name="fullname" id="edit_fullname" class="form-control" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-secondary">Email</label>
                                                    <input type="email" name="email" id="email" class="form-control" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-secondary">New Access Password (Leave blank to keep current)</label>
                                                    <input type="password" name="password" id="edit_password" class="form-control" placeholder="••••••••">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-secondary">System Assignment Privilege Level</label>
                                                    <select name="role" id="edit_role" class="form-select">
                                                        <option value="Cashier">CASHIER</option>
                                                        <option value="Manager">MANAGER</option>
                                                        <option value="Admin">ADMIN</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" name="update_user" class="btn btn-primary btn-sm px-4">
                                                <i class="bi bi-cloud-arrow-up-fill me-1"></i> Commit Modifications
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                    <?php elseif ($current_tab === 'profile'): ?>
                        <div class="d-flex align-items-center mb-4 text-dark">
                            <i class="bi bi-building-gear fs-4 me-2 text-primary"></i>
                            <h5 class="fw-bold mb-0">Store Metadata Profile</h5>
                        </div>

                        <form action="process_settings.php" method="POST">
                            <?php
                            // 1. Fetch all rows from your settings table
                            $settings_query = mysqli_query($conn, "SELECT setting_key, setting_value FROM settings");

                            // 2. Initialize an empty array to store your mapped values
                            $app_config = [];

                            if ($settings_query) {
                                while ($row = mysqli_fetch_assoc($settings_query)) {
                                    // Map the database key directly to the array index
                                    $app_config[$row['setting_key']] = $row['setting_value'];
                                }
                            }

                            // 3. Provide safe fallback defaults just in case a row gets deleted accidentally
                            $store_name     = $app_config['store_name'] ?? 'Mart POS System';
                            $store_phone    = $app_config['store_phone'] ?? '012 345 678';
                            $store_email    = $app_config['store_email'] ?? 'contact@martpos.com';
                            $store_address  = $app_config['store_address'] ?? 'Phnom Penh, Cambodia';
                            $currency       = $app_config['currency_symbol'] ?? '$';
                            $vat_rate       = $app_config['vat_rate'] ?? 0;
                            ?>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary">Business Store Name</label>
                                    <input type="text" name="store_name" class="form-control" value="<?php echo htmlspecialchars($store_name); ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary">Contact Hotline Phone</label>
                                    <input type="text" name="store_phone" class="form-control" value="<?php echo htmlspecialchars($store_phone); ?>" required>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label small fw-bold text-secondary">Official Communications Email</label>
                                    <input type="email" name="store_email" class="form-control" value="<?php echo htmlspecialchars($store_email); ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary">Currency Symbol</label>
                                    <select name="currency_symbol" class="form-select">
                                        <option value="$" <?php echo ($currency === '$') ? 'selected' : ''; ?>>$ (USD)</option>
                                        <option value="៛" <?php echo ($currency === '៛') ? 'selected' : ''; ?>>៛ (KHR)</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary">Tax Rate / VAT (%)</label>
                                    <div class="input-group">
                                        <input type="number" name="vat_rate" class="form-control" value="<?php echo htmlspecialchars($vat_rate); ?>">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary">Physical Operational Shop Address</label>
                                    <textarea name="store_address" class="form-control" rows="3"><?php echo htmlspecialchars($store_address); ?></textarea>
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" name="update_settings" class="btn btn-dark btn-sm px-4 py-2">
                                    <i class="bi bi-cloud-arrow-up-fill me-1"></i> Save Global Configurations
                                </button>
                            </div>
                        </form>


                    <?php elseif ($current_tab === 'appearance'): ?>
                        <div class="d-flex align-items-center mb-4 text-dark">
                            <i class="bi bi-palette-fill fs-4 me-2 text-primary"></i>
                            <h5 class="fw-bold mb-0">Interface Appearance</h5>
                        </div>
                        <p class="text-muted small">Customize layout coloration matrices, themes, dark/light node configurations, and table densification properties.</p>
                        <hr>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="border p-3 rounded text-center bg-light text-dark cp-pointer" onclick="changeSystemTheme('light')" style="cursor: pointer;">
                                    <i class="bi bi-sun fs-2 text-warning mb-2 d-block"></i>
                                    <span class="small fw-bold">Light Minimalist</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="border p-3 rounded text-center bg-dark text-white cp-pointer" onclick="changeSystemTheme('dark')" style="cursor: pointer;">
                                    <i class="bi bi-moon-stars fs-2 text-primary mb-2 d-block"></i>
                                    <span class="small fw-bold">Midnight Dark</span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body p-4 small text-secondary">
                    <h6 class="fw-bold text-dark d-flex align-items-center mb-3">
                        <i class="bi bi-info-circle-fill text-dark me-2"></i> Global Variable Bindings
                    </h6>
                    <p class="mb-0">Modifying these operational parameters changes structural store profile identity values, company credentials printed on client quotation slips/sale receipts, and tax calculations globally throughout the terminal app stack environment.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ⚙️ LOCAL CONFIGURATION SETTINGS TOGGLE ACTION ONLY -->
<script>
    function changeSystemTheme(themeName) {
        // 1. Instantly update the look on the current screen
        document.documentElement.setAttribute('data-bs-theme', themeName);

        // 2. Write it to global browser storage so the layout handler picks it up on other pages
        localStorage.setItem('pos_theme', themeName);
    }

    function confirmDelete(userId, username) {
        // Check your active UI style preference
        const systemTheme = localStorage.getItem('pos_theme') || 'dark';
        const isDark = (systemTheme === 'dark');

        Swal.fire({
            title: 'Deauthorize Personnel?',
            text: `Are you absolutely sure you want to completely remove "${username}" from the terminal registry?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', // Sleek dashboard red
            cancelButtonColor: '#64748b', // Muted gray
            confirmButtonText: 'Yes, Purge Profile',
            cancelButtonText: 'Cancel',
            background: isDark ? '#1e293b' : '#ffffff',
            color: isDark ? '#f8fafc' : '#1e293b',
            iconColor: '#ef4444'
        }).then((result) => {
            if (result.isConfirmed) {
                // Forward the window directly to your backend script with the target ID parameter
                window.location.href = `process_user.php?delete_id=${userId}`;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const systemTheme = localStorage.getItem('pos_theme') || 'dark';
        const isDark = (systemTheme === 'dark');

        const swalConfig = {
            background: isDark ? '#1e293b' : '#ffffff',
            color: isDark ? '#f8fafc' : '#1e293b'
        };

        // ==========================================
        // 🚨 DELETION & EXCEPTION ALERTS
        // ==========================================
        if (status === 'delete_success') {
            Swal.fire({
                ...swalConfig,
                icon: 'success',
                title: 'Profile Purged',
                text: 'The operator registration index was erased from terminal memory databases smoothly.',
                confirmButtonColor: '#3b82f6'
            });
            window.history.replaceState({}, document.title, window.location.pathname + "?page=settings&tab=user");
        } else if (status === 'self_delete_blocked') {
            Swal.fire({
                ...swalConfig,
                icon: 'error',
                title: 'Operation Refused',
                text: 'Terminal architecture cannot dismantle its own active workspace session container profile.',
                confirmButtonColor: '#ef4444'
            });
            window.history.replaceState({}, document.title, window.location.pathname + "?page=settings&tab=user");
        } else if (status === 'unauthorized') {
            Swal.fire({
                ...swalConfig,
                icon: 'warning',
                title: 'Security Violation',
                text: 'Your current operational clearance level is insufficient to request index updates.',
                confirmButtonColor: '#eab308'
            });
            window.history.replaceState({}, document.title, window.location.pathname + "?page=settings&tab=user");
        } else if (status === 'database_error') {
            Swal.fire({
                ...swalConfig,
                icon: 'error',
                title: 'Engine Query Error',
                text: 'The local database storage core rejected the structural deletion script constraint.',
                confirmButtonColor: '#ef4444'
            });
            window.history.replaceState({}, document.title, window.location.pathname + "?page=settings&tab=user");
        }

        // ==========================================
        // 🚀 NEW USER CREATION ALERTS (FIXED NESTING)
        // ==========================================
        else if (status === 'add_success') {
            Swal.fire({
                ...swalConfig,
                icon: 'success',
                title: 'Operator Authorized',
                text: 'The new cashier profile was committed securely into core system registries.',
                confirmButtonColor: '#3b82f6'
            });
            window.history.replaceState({}, document.title, window.location.pathname + "?page=settings&tab=user");
        } else if (status === 'username_taken') {
            Swal.fire({
                ...swalConfig,
                icon: 'error',
                title: 'Handle Conflict',
                text: 'The specified login username identifier is already allocated to another staff active container.',
                confirmButtonColor: '#ef4444'
            });
            window.history.replaceState({}, document.title, window.location.pathname + "?page=settings&tab=user");
        } else if (status === 'empty_fields') {
            Swal.fire({
                ...swalConfig,
                icon: 'warning',
                title: 'Validation Void',
                text: 'Operational constraints require all metadata credential fields to be populated completely.',
                confirmButtonColor: '#eab308'
            });
            window.history.replaceState({}, document.title, window.location.pathname + "?page=settings&tab=user");
        }
    }); // Active Listener ends safely here
    // Append this inside the script area of your settings.php file
    document.addEventListener('DOMContentLoaded', () => {
        // Instantiate Bootstrap modal controller element instance
        const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));

        // Attach click listeners to all Edit User buttons dynamically
        document.querySelectorAll('.edit-user-btn').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');

                // 🚀 FIRE DATA FETCH API CALL TO THE SERVER ENGINE
                fetch(`get_user.php?id=${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Fetch Aborted',
                                text: data.error
                            });
                            return;
                        }

                        // ⚙️ Inject database row values into form entry containers
                        document.getElementById('edit_user_id').value = data.id;
                        document.getElementById('edit_username').value = data.username;
                        document.getElementById('edit_fullname').value = data.fullname;

                        // Normalize and bind select menu value cleanly
                        // ✅ MATCH THE DB STRING CASE EXACTLY:
                        document.getElementById('edit_role').value = data.role;

                        // Clear out password container input explicitly for structural updates
                        document.getElementById('edit_password').value = '';

                        // Open the updated dark theme modal popup grid layout!
                        editModal.show();
                    })
                    .catch(error => {
                        console.error('Core engine pipeline crash:', error);
                    });
            });
        });
    });
</script>