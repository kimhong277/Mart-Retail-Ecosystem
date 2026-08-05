<?php
// mart_pos_system/pages/categories.php
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$conn_pos = mysqli_connect($host, $db_user, $db_pass, 'mart_pos_system');

if (!$conn_pos) {
    echo "<div class='alert alert-danger'>Database access failure: " . mysqli_connect_error() . "</div>";
    return;
}
mysqli_set_charset($conn_pos, "utf8mb4");

$sql = "SELECT * FROM categories ORDER BY id ";
$result = mysqli_query($conn_pos, $sql);
?>

<!-- Include SweetAlert2 library directly inside the view framework -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0 text-dark">🗂️ Category Inventory Hub</h4>
            <button class="btn btn-primary fw-semibold d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <span>➕</span> Add Category
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 100px;">ID</th>
                        <th>Category Name</th>
                        <th>Operational Status (Click to Toggle)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><strong>#<?php echo $row['id']; ?></strong></td>
                                <td class="fw-semibold text-secondary"><?php echo htmlspecialchars($row['category_name']); ?></td>
                                <td>
                                    <!-- Add cursor pointer style and an onclick event handler passing item row details -->
                                    <span class="badge <?php echo $row['status'] == 1 ? 'bg-success' : 'bg-secondary'; ?> fs-7 px-3 py-2"
                                        style="cursor: pointer;"
                                        id="status-badge-<?php echo $row['id']; ?>"
                                        onclick="toggleStatus(<?php echo $row['id'];
                                                                ?>, <?php echo $row['status']; ?>, '<?php echo addslashes($row['category_name']); ?>')">
                                        <?php echo $row['status'] == 1 ? 'Active' : 'Disabled'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">No classification records saved yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Category Generation Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Add New Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="add_category.php" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Category Label/Name</label>
                        <input type="text" name="category_name" class="form-control" placeholder="e.g., Groceries" required>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit_category" class="btn btn-primary fw-bold px-4">Create Group</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleStatus(id, currentStatus, name) {
        const actionText = currentStatus == 1 ? 'Disable' : 'Activate';

        Swal.fire({
            title: 'Change Status?',
            text: `Are you sure you want to ${actionText.toLowerCase()} the category "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Yes, ${actionText} it!`
        }).then((result) => {
            if (result.isConfirmed) {
                // Send request to the root-level processor file asynchronously
                const formData = new FormData();
                formData.append('id', id);
                formData.append('current_status', currentStatus);

                fetch('toggle_category_status.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Updated!',
                                text: 'The status has been updated successfully.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            // Dynamically update the badge UI in place without reloading the entire window
                            const badge = document.getElementById(`status-badge-${id}`);
                            if (data.new_status == 1) {
                                badge.className = "badge bg-success fs-7 px-3 py-2";
                                badge.innerText = "Active";
                                badge.setAttribute('onclick', `toggleStatus(${id}, 1, '${name}')`);
                            } else {
                                badge.className = "badge bg-secondary fs-7 px-3 py-2";
                                badge.innerText = "Disabled";
                                badge.setAttribute('onclick', `toggleStatus(${id}, 0, '${name}')`);
                            }
                        } else {
                            Swal.fire('Error!', data.message || 'Failed to update status.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'A system error occurred.', 'error');
                    });
            }
        });
    }
</script>
<!-- Capture redirect indicators and display beautiful confirmation modals -->
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <script>
        Swal.fire({
            title: 'Job Finished Successfully!',
            text: 'Your entries have been accurately logged inside the database.',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            // Clean URL query parameters so refreshing the browser doesn't prompt the alert again
            window.history.replaceState({}, document.title, window.location.pathname + window.location.search.split('&success')[0]);
        });
    </script>
<?php elseif (isset($_GET['error'])): ?>
    <script>
        Swal.fire({
            title: 'Operation Failed!',
            text: 'An error occurred while handling records.',
            icon: 'error',
            confirmButtonText: 'Understood'
        });
    </script>
<?php endif; ?>