<?php
// pages/accounts.php
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$conn = mysqli_connect($host, $db_user, $db_pass, 'mart_pos_system');

if (!$conn) {
    die("Database link down: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

// Fetch all operators sorted by ID descending to match your screenshot hierarchy
$staff_list = mysqli_query($conn, "SELECT * FROM users ORDER BY id ");
?>

<div class="container-fluid px-4 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="text-primary fw-bold text-uppercase small d-block mb-1" style="letter-spacing: 0.05em;">Access Control</span>
            <h2 class="h3 mb-0 text-dark fw-bold">Staff Accounts & Security</h2>
            <p class="text-muted small mb-0">Manage system operator profiles, terminal access privileges, and roles.</p>
        </div>
        <button class="btn btn-dark fw-semibold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#provisionUserModal">
            <i class="bi bi-plus-circle me-1"></i> Provision New User
        </button>
    </div>

    <!-- 📊 Saved Operator Log Table View Framework -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase fw-semibold">
                        <tr>
                            <th class="ps-4" style="width: 100px;">ID</th>
                            <th>Username Handles</th>
                            <th>Full Name Name</th>
                            <th>Assigned Role</th>
                            <th>System Status</th>
                            <th class="text-center" style="width: 15%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php if ($staff_list && mysqli_num_rows($staff_list) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($staff_list)): ?>
                                <tr>
                                    <td class="ps-4 text-muted font-monospace">#<?= $row['id'] ?></td>
                                    <td class="text-secondary fw-medium">@<?= htmlspecialchars($row['username']) ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['fullname'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php
                                        $role_badge = 'bg-secondary';
                                        $role = strtolower($row['role']);
                                        if ($role === 'admin') $role_badge = 'bg-danger';
                                        elseif ($role === 'manager') $role_badge = 'bg-info text-dark';
                                        elseif ($role === 'cashier') $role_badge = 'bg-secondary';
                                        ?>
                                        <span class="badge <?= $role_badge ?> text-uppercase px-2 py-1 small rounded-1 font-monospace" style="font-size:0.7rem;">
                                            <?= htmlspecialchars($row['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (intval($row['status']) === 1): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill fw-bold" style="font-size:0.7rem;">Authorized</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill fw-bold" style="font-size:0.7rem;">Suspended</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (intval($row['id']) === 1): ?>
                                            <button class="btn btn-sm btn-light text-muted fw-semibold border rounded-2 px-3" disabled style="font-size:0.75rem;">
                                                <i class="bi bi-lock-fill me-1"></i> System Locked
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-primary text-white fw-semibold px-3 rounded-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#manageUserModal<?= $row['id'] ?>" style="font-size:0.75rem;">
                                                <i class="bi bi-sliders me-1"></i> Manage
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <!-- 🛠️ DYNAMIC EDIT MODAL LAYER FOR EACH OPERATOR -->
                                <div class="modal fade" id="manageUserModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow rounded-4">
                                            <div class="modal-header border-bottom-0 pt-4 px-4 pb-0">
                                                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-person-gear me-2 text-primary"></i>Manage Operator #<?= $row['id'] ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="process_account.php" method="POST">
                                                <div class="modal-body p-4">
                                                    <input type="hidden" name="user_id" value="<?= $row['id'] ?>">

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-secondary">Full Name</label>
                                                        <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($row['fullname'] ?? '') ?>" required>
                                                    </div>

                                                    <!-- 📧 NEW: Email Address Field -->
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-secondary">Email Address (OAuth SSO Target)</label>
                                                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email'] ?? '') ?>" required>
                                                    </div>

                                                    <!-- 🔑 NEW: Force Password Override Field -->
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-secondary">Administrative Password Override</label>
                                                        <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current password">
                                                        <div class="form-text text-muted basic-size" style="font-size:0.75rem;">Type a new password pattern above to overwrite their credentials.</div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-secondary">Assigned Authorization Role</label>
                                                        <select name="role" class="form-select font-monospace small">
                                                            <option value="cashier" <?= $role === 'cashier' ? 'selected' : '' ?>>CASHIER</option>
                                                            <option value="manager" <?= $role === 'manager' ? 'selected' : '' ?>>MANAGER</option>
                                                            <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>ADMIN</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold text-secondary">System Access Status</label>
                                                        <select name="status" class="form-select small">
                                                            <option value="1" <?= intval($row['status']) === 1 ? 'selected' : '' ?>>Authorized (Active Clearance)</option>
                                                            <option value="0" <?= intval($row['status']) === 0 ? 'selected' : '' ?>>Suspended (Revoke Entry)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0 px-4 pb-4 pt-0 d-flex justify-content-between align-items-center">
                                                    <!-- 🗑️ Modern Delete Option Trigger Link -->
                                                    <button type="button" class="btn btn-sm btn-danger  border-0 fw-semibold" onclick="confirmDeleteOperator(<?= $row['id'] ?>, '<?= htmlspecialchars($row['username']) ?>')">
                                                        <i class="bi bi-trash3-fill me-1"></i> Delete Operator
                                                    </button>

                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-light btn-sm fw-semibold px-3" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" name="update_operator" class="btn btn-primary btn-sm fw-bold px-4 rounded-2 shadow-sm">
                                                            Save Operational Profile
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-people display-4 d-block text-black-50 mb-2"></i>
                                    No staff accounts registry entries logged yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ➕ MODAL FOR PROVISIONING NEW USERS -->
<div class="modal fade" id="provisionUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-person-plus me-2 text-success"></i>Provision New Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="process_account.php" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Username Handle</label>
                        <input type="text" name="username" class="form-control" placeholder="e.g. sreyka" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Full Name</label>
                        <input type="text" name="fullname" class="form-control" placeholder="e.g. Srey Ka" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="e.g. name@martpos.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Temporary Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-secondary">System Role</label>
                        <select name="role" class="form-select small">
                            <option value="cashier" selected>CASHIER</option>
                            <option value="manager">MANAGER</option>
                            <option value="admin">ADMIN</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 pt-0 justify-content-end gap-2">
                    <button type="button" class="btn btn-light btn-sm fw-semibold px-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="provision_user" class="btn btn-success btn-sm fw-bold px-4 rounded-2 shadow-sm">
                        Confirm Creation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 🧠 SWAL REDIRECT NOTIFICATION LISTENERS -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const statusMsg = urlParams.get('status');

        if (statusMsg) {
            let titleText = '';
            let textBody = '';
            let iconType = 'success';
            let triggerAlert = true;

            switch (statusMsg) {
                case 'inserted':
                    titleText = 'Operator Provisioned!';
                    textBody = 'The new staff profile account container has been successfully registered.';
                    break;
                case 'updated':
                    titleText = 'Profile Changes Saved!';
                    textBody = 'Staff operational records, email parameters, and passwords updated.';
                    break;
                case 'unauthorized':
                    titleText = 'Action Forbidden!';
                    textBody = 'Modifying or locking out the root primary system admin account is restricted.';
                    iconType = 'error';
                    break;
                case 'error':
                    titleText = 'Pipeline Error!';
                    textBody = 'An unexpected relational database query processing failure occurred.';
                    iconType = 'error';
                    break;
                case 'deleted':
                    titleText = 'Account Purged!';
                    textBody = 'The designated profile ledger node has been removed from terminal parameters.';
                    iconType = 'success';
                    break;
                default:
                    triggerAlert = false;
                    break;
            }

            if (triggerAlert) {
                Swal.fire({
                    title: titleText,
                    text: textBody,
                    icon: iconType,
                    confirmButtonColor: iconType === 'error' ? '#ef4444' : '#2D3748',
                    timer: 2500
                });

                // Clear layout address parameters context nicely
                window.history.replaceState({}, document.title, window.location.pathname + "?page=accounts");
            }
        }
    });
    // JavaScript: Secure Delete Event Dispatcher Trigger
    function confirmDeleteOperator(userId, username) {
        // Dismiss the active bootstrap configurations modal frame first
        const activeModal = bootstrap.Modal.getInstance(document.getElementById('manageUserModal' + userId));
        if (activeModal) activeModal.hide();

        // Trigger confirmation prompt modal window 
        Swal.fire({
            title: 'Delete @' + username + '?',
            text: "This operation will completely erase this operator profile. This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete Account',
            cancelButtonText: 'Abort'
        }).then((result) => {
            if (result.isConfirmed) {
                // Build a dynamic standard hidden submission form layout routing to the execution pipeline
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'process_account.php';

                const inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'user_id';
                inputId.value = userId;
                form.appendChild(inputId);

                const inputAction = document.createElement('input');
                inputAction.type = 'hidden';
                inputAction.name = 'delete_operator';
                inputAction.value = '1';
                form.appendChild(inputAction);

                document.body.appendChild(form);
                form.submit();
            } else {
                // If they cancel, restore the manage operational modal display smoothly
                if (activeModal) activeModal.show();
            }
        });
    }
</script>