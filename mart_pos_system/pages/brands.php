<?php
// mart_pos_system/pages/brands.php
// Dynamically included into index.php

$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$conn_pos = mysqli_connect($host, $db_user, $db_pass, 'mart_pos_system');

if (!$conn_pos) {
    echo "<div class='alert alert-danger'>Database access failure: " . mysqli_connect_error() . "</div>";
    return;
}
mysqli_set_charset($conn_pos, "utf8mb4");

// Fetch brands from your POS structural schema
$sql = "SELECT * FROM brands ORDER BY id";
$result = mysqli_query($conn_pos, $sql);
?>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0 text-dark">🏷️ Brand Partner Registry</h4>
            <button class="btn btn-primary fw-semibold d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                <span>➕</span> Add Brand
            </button>
        </div>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success alert-dismissible fade show fw-medium" role="alert">
                ✨ Success! New merchant brand profile indexed properly.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 100px;">ID</th>
                        <th>Brand Name</th>
                        <th>Operational Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><strong>#<?php echo $row['id']; ?></strong></td>
                                <td class="fw-semibold text-secondary"><?php echo htmlspecialchars($row['brand_name']); ?></td>
                                <td>
                                    <span class="badge <?php echo $row['status'] == 1 ? 'bg-success text-white' : 'bg-secondary text-white'; ?> fs-7">
                                        <?php echo $row['status'] == 1 ? 'Active' : 'Disabled'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">No manufacturing brands registered yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Brand Generation Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Add New Brand Partner</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="add_brand.php" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Brand Name / Label</label>
                        <input type="text" name="brand_name" class="form-control" placeholder="e.g., Nestlé, Coca-Cola, Apple Inc." required>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit_brand" class="btn btn-primary fw-bold px-4">Create Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>