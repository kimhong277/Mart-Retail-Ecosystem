<?php
// pages/categories.php
require_once 'db.php';

// READ: Fetch all categories
$result = mysqli_query($conn, "SELECT * FROM categories ORDER BY id ");
?>

<div class="container-fluid px-4 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-gray-800 fw-bold">Categories Catalog</h2>
            <p class="text-muted small mb-0">Organize your shop stock by grouping items into distinct classifications.</p>
        </div>
        <button type="button" class="btn btn-dark btn-sm fw-semibold px-4 py-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            + Add Category
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4" style="width: 10%;">ID</th>
                            <th>Category Name</th>
                            <th>Status</th>
                            <th style="width: 20%;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="ps-4"><?= $row['id'] ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['category_name']) ?></td>
                                    <td>
                                        <?php if ($row['status'] == 1): ?>
                                            <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-dark me-1" data-bs-toggle="modal" data-bs-target="#editCategoryModal<?= $row['id'] ?>">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>

                                    </td>
                                </tr>

                                <div class="modal fade" id="editCategoryModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg">
                                            <div class="modal-header bg-dark text-white">
                                                <h5 class="modal-title fw-bold">Modify Category #<?= $row['id'] ?></h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="process_category.php" method="POST">
                                                <div class="modal-body p-4">
                                                    <input type="hidden" name="category_id" value="<?= $row['id'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-secondary">Category Name</label>
                                                        <input type="text" name="category_name" class="form-control" value="<?= htmlspecialchars($row['category_name']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-secondary">Visibility Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="1" <?= $row['status'] == 1 ? 'selected' : '' ?>>Active</option>
                                                            <option value="0" <?= $row['status'] == 0 ? 'selected' : '' ?>>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="update_category" class="btn btn-dark btn-sm px-4">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">No categories configured yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Add New Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_category.php" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="category_name" class="form-control" placeholder="e.g., Beverages, Snacks, Cosmetics" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_category" class="btn btn-dark btn-sm px-4">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will attempt to remove this classification row from your SQL matrix data maps!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2D3748',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'process_category.php?action=delete&id=' + id;
            }
        })
    }

    const urlParams = new URLSearchParams(window.location.search);
    const statusMsg = urlParams.get('status');

    if (statusMsg) {
        let titleText = '';
        let textBody = '';
        let iconType = 'success';

        if (statusMsg === 'inserted') {
            titleText = 'Category Created!';
        } else if (statusMsg === 'updated') {
            titleText = 'Changes Applied Successfully!';
        } else if (statusMsg === 'deleted') {
            titleText = 'Record Erased!';
            iconType = 'info';
        } else if (statusMsg === 'constraint_error') {
            titleText = 'Action Refused!';
            textBody = 'This category is currently linked to one or more active inventory products. Clear or reassign those items first!';
            iconType = 'error';
        } else if (statusMsg === 'error') {
            titleText = 'System Error!';
            iconType = 'error';
        }

        if (titleText !== '') {
            Swal.fire({
                title: titleText,
                text: textBody,
                icon: iconType,
                confirmButtonColor: '#2D3748',
                timer: textBody === '' ? 2500 : undefined
            });
            window.history.replaceState({}, document.title, window.location.pathname + "?page=categories");
        }
    }
</script>