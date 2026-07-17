<?php
// pages/brands.php
require_once 'db.php';

// READ: Fetch all existing brands
$result = mysqli_query($conn, "SELECT * FROM brands ORDER BY id ");
?>

<div class="container-fluid px-4 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-gray-800 fw-bold">Brands Catalog</h2>
            <p class="text-muted small mb-0">Manage manufacturer profiles and branding lines.</p>
        </div>
        <button type="button" class="btn btn-dark btn-sm fw-semibold px-4 py-2" data-bs-toggle="modal" data-bs-target="#addBrandModal">
            + Add Brand
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4" style="width: 10%;">ID</th>
                            <th>Brand Name</th>
                            <th>Status</th>
                            <th style="width: 20%;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="ps-4"><?= $row['id'] ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['brand_name']) ?></td>
                                    <td>
                                        <?php if ($row['status'] == 1): ?>
                                            <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-dark me-1" data-bs-toggle="modal" data-bs-target="#editBrandModal<?= $row['id'] ?>">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                        <!-- <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $row['id'] ?>)">
                                            <i class="bi bi-trash"></i> Delete
                                        </button> -->
                                    </td>
                                </tr>

                                <div class="modal fade" id="editBrandModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg">
                                            <div class="modal-header bg-dark text-white">
                                                <h5 class="modal-title fw-bold">Modify Brand #<?= $row['id'] ?></h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="process_brand.php" method="POST">
                                                <div class="modal-body p-4">
                                                    <input type="hidden" name="brand_id" value="<?= $row['id'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-secondary">Brand Name</label>
                                                        <input type="text" name="brand_name" class="form-control" value="<?= htmlspecialchars($row['brand_name']) ?>" required>
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
                                                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="update_brand" class="btn btn-dark btn-sm px-4">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">No brands configured yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addBrandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Add New Brand</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_brand.php" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Brand Name <span class="text-danger">*</span></label>
                        <input type="text" name="brand_name" class="form-control" placeholder="e.g., Coca-Cola Company" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_brand" class="btn btn-dark btn-sm px-4">Save Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // 1. DYNAMIC DELETION PROMPT
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently remove this brand row item from your system catalog storage layers!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2D3748',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Forward browser location execution context tracking straight to deletion route
                window.location.href = 'process_brand.php?action=delete&id=' + id;
            }
        })
    }

    // 2. DETECT STATUS PASS-BACK CONSTANTS FROM URL PARAMETERS
    const urlParams = new URLSearchParams(window.location.search);
    const statusMsg = urlParams.get('status');

    if (statusMsg) {
        let titleText = '';
        let iconType = 'success';

        if (statusMsg === 'inserted') {
            titleText = 'New Brand Added Successfully!';
        } else if (statusMsg === 'updated') {
            titleText = 'Brand Changes Saved!';
        } else if (statusMsg === 'deleted') {
            titleText = 'Record Erased Successfully!';
            iconType = 'info';
        } else if (statusMsg === 'error') {
            titleText = 'Operation Execution Error!';
            iconType = 'error';
        }

        if (titleText !== '') {
            Swal.fire({
                title: titleText,
                icon: iconType,
                confirmButtonColor: '#2D3748',
                timer: 3000
            });

            // Optional clean up: Remove the query parameters from browser text tracking box window area safely
            window.history.replaceState({}, document.title, window.location.pathname + "?page=brands");
        }
    }
</script>