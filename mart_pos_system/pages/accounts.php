<?php
// Put this at the very top of pages/accounts.php
require_once 'db.php';

// If the user is NOT an Admin, block them immediately
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    echo "<script>window.location.href = 'index.php?page=dashboard&status=unauthorized';</script>";
    exit();
}

// READ: Fetch all registered platform accounts
$users_result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<div class="container-fluid px-4 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-gray-800 fw-bold">Staff Accounts & Security</h2>
            <p class="text-muted small mb-0">Manage system operator profiles, terminal access privileges, and roles.</p>
        </div>
        <button type="button" class="btn btn-dark btn-sm fw-semibold px-4 py-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
            + Provision New User
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4" style="width: 10%;">ID</th>
                            <th>Username Handles</th>
                            <th>Full Name Name</th>
                            <th>Assigned Role</th>
                            <th>System Status</th>
                            <th class="text-center" style="width: 15%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php if ($users_result && mysqli_num_rows($users_result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($users_result)):
                                $normalized_role = strtolower($row['role']);

                                // Determine badge style color dynamically based on user privilege role
                                $badge_class = 'bg-secondary text-light';
                                if ($normalized_role === 'admin') {
                                    $badge_class = 'bg-danger text-white';
                                } elseif ($normalized_role === 'manager') {
                                    $badge_class = 'bg-info text-dark';
                                } ?>
                                <tr>
                                    <td class="ps-4 text-secondary">#<?= $row['id'] ?></td>
                                    <td class="fw-mono text-dark font-bold">@<?= htmlspecialchars($row['username']) ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['fullname']) ?></td>
                                    <td>
                                        <!-- <span class="badge bg-dark-subtle text-dark px-3 py-1 rounded">
                                            <?= htmlspecialchars($row['role']) ?>
                                        </span> -->
                                        <span class="badge <?php echo $badge_class; ?> px-2 py-1 small">
                                            <?php echo strtoupper($row['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] == 1): ?>
                                            <span class="badge bg-success text-white px-3 py-1 rounded-pill">Authorized</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger text-white px-3 py-1 rounded-pill">Suspended</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($row['id'] == 1): ?>
                                            <span class="badge bg-light text-muted border"><i class="bi bi-lock-fill"></i> System Locked</span>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $row['id'] ?>">
                                                <i class="bi bi-sliders2-vertical"></i> Manage
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editUserModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg">
                                            <div class="modal-header bg-dark text-white">
                                                <h5 class="modal-title fw-bold">Edit Account Privileges</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="process_account.php" method="POST">
                                                <div class="modal-body p-4">
                                                    <input type="hidden" name="user_id" value="<?= $row['id'] ?>">

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-secondary">Username (Immutable)</label>
                                                        <input type="text" class="form-control bg-light" value="@<?= htmlspecialchars($row['username']) ?>" disabled>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-secondary">Full Employee Name</label>
                                                        <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($row['fullname']) ?>" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-secondary">System Authorization Role</label>
                                                        <select name="role" class="form-select">
                                                            <option value="Cashier" <?= $row['role'] === 'Cashier' ? 'selected' : '' ?>>Cashier Operator</option>
                                                            <option value="Manager" <?= $row['role'] === 'Manager' ? 'selected' : '' ?>>Store Manager</option>
                                                            <option value="Admin" <?= $row['role'] === 'Admin' ? 'selected' : '' ?>>Root Administrator</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-secondary">Access Control Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="1" <?= $row['status'] == 1 ? 'selected' : '' ?>>Active / Allow Login</option>
                                                            <option value="0" <?= $row['status'] == 0 ? 'selected' : '' ?>>Suspended / Revoke Access</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="update_user" class="btn btn-dark btn-sm px-4">Update Profile</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">No platform accounts configured yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Add Staff Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_account.php" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Unique Handle Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" placeholder="e.g., sreyneat168" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Full Legal Name <span class="text-danger">*</span></label>
                        <input type="text" name="fullname" class="form-control" placeholder="e.g., Sreyneat Meas" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Privilege Group Permission Tier</label>
                        <select name="role" class="form-select">
                            <option value="Cashier">Cashier Operator</option>
                            <option value="Manager">Store Manager</option>
                            <option value="Admin">Root Administrator</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_user" class="btn btn-dark btn-sm px-4">Provision Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Using 'var' and a unique name prevents block-scope conflicts across your dashboard wrapper
    <?php if (isset($_SESSION['status_msg'])): ?>
        var accountsStatusMsg = "<?= $_SESSION['status_msg'] ?>";
        <?php unset($_SESSION['status_msg']); // Clear it instantly 
        ?>
    <?php else: ?>
        var accountsStatusMsg = null;
    <?php endif; ?>

    console.log("Accounts Status Message captured:", accountsStatusMsg);

    if (accountsStatusMsg) {
        let titleText = '';
        let textBody = '';
        let iconType = 'success';
        let confirmBtnColor = '#2D3748';

        if (accountsStatusMsg === 'inserted') {
            titleText = 'New Profile Provisioned!';
        } else if (accountsStatusMsg === 'updated') {
            titleText = 'Staff Credentials Updated!';
        } else if (accountsStatusMsg === 'duplicate') {
            titleText = 'Username Taken!';
            textBody = 'That exact login handle username is already configured inside the system matrices.';
            iconType = 'error';
        } else if (accountsStatusMsg === 'deleted') {
            titleText = 'Record Erased!';
            iconType = 'info';
        } else if (accountsStatusMsg === 'protected_admin') {
            titleText = 'Action Prohibited!';
            textBody = 'The master default administrator account (ID #1) is core to system stability and cannot be suspended, demoted, or deleted.';
            iconType = 'error';
            confirmBtnColor = '#0f172a';
        } else if (accountsStatusMsg === 'error') {
            titleText = 'Data pipeline processing failure!';
            iconType = 'error';
        }

        if (titleText !== '') {
            try {
                Swal.fire({
                    title: titleText,
                    text: textBody,
                    icon: iconType,
                    confirmButtonColor: confirmBtnColor,
                    timer: textBody === '' ? 2500 : undefined
                });
            } catch (e) {
                alert(titleText + (textBody ? "\n" + textBody : ""));
            }
        }
    }
</script>