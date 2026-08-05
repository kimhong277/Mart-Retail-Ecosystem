<?php
// mart_pos_system/pages/products.php
// Note: This file is dynamically included into index.php, so database setups happen inline.

// 1. Establish direct connection to the POS engine database
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$conn_pos = mysqli_connect($host, $db_user, $db_pass, 'mart_pos_system');

if (!$conn_pos) {
    echo "<div class='alert alert-danger'>Database access failure: " . mysqli_connect_error() . "</div>";
    return; // Stop processing this sub-file safely
}
mysqli_set_charset($conn_pos, "utf8mb4");

// Fetch products alongside their assigned category context[cite: 2]
$sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.id ";
$result = mysqli_query($conn_pos, $sql);

// 🌟 NEW QUERY: Fetch active categories for the dropdown selection
$sql_categories = "SELECT id, category_name FROM categories WHERE status = 1 ORDER BY category_name ASC";
$categories_result = mysqli_query($conn_pos, $sql_categories);

// Fetch active brands for selection
$sql_brands = "SELECT id, brand_name FROM brands WHERE status = 1 ORDER BY brand_name ASC";
$brands_result = mysqli_query($conn_pos, $sql_brands);
?>

<!-- 🌟 UI Container Wrapper Optimized for index.php's fluid grid layout -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0 text-dark">📦 Product Master List</h4>
            <!-- Add Item Action Trigger Modal -->
            <button class="btn btn-primary fw-semibold d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-plus" style="-webkit-text-stroke: 1px;"></i> Add New Product
            </button>
        </div>

        <!-- Dynamic Success Toast/Alert Status Banner -->
        <!-- <?php
                // if (isset($_GET['success']) && $_GET['success'] == 1): 
                ?>
            <div class="alert alert-success alert-dismissible fade show fw-medium" role="alert">
                ✨ Success! New product added to inventory records successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
        // endif; 
        ?> -->

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr class="text-center">
                        <th style="width: 80px;">ID</th>
                        <th style="width: 70px;">Image</th>
                        <th>Product Name</th>
                        <th>Barcode</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock Qty</th>
                        <th>Status</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <?php
                            // Target the correct asset path folder structure
                            $image_file = !empty($row['image']) ? $row['image'] : 'default.png';
                            $image_path = "/mart-retail-ecosystem/mart_pos_system/assets/images/" . $image_file;
                            ?>
                            <tr id="product-row-<?php echo $row['id']; ?>" class="text-center">
                                <td><strong><?php echo $row['id']; ?></strong></td>
                                <td>
                                    <div class="ratio ratio-4x3 rounded-top overflow-hidden d-flex align-items-center justify-content-center" style="height: 60px;">
                                        <img src="<?php echo $image_path; ?>"
                                            class="w-100 h-100  object-fit-contain"
                                            alt="<?php echo htmlspecialchars($row['product_name']); ?>"
                                            onerror="this.src='/mart-retail-ecosystem/mart_pos_system/assets/images/default.png';">
                                        <!-- style="background-color: #ffffff;" -->
                                    </div>
                                </td>
                                <td class="fw-semibold text-secondary"><?php echo htmlspecialchars($row['product_name']); ?></td>
                                <td><span class="badge bg-secondary font-monospace px-2 py-1.5 fs-7"><?php echo htmlspecialchars($row['barcode'] ?? 'N/A'); ?></span></td>
                                <td><?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></td>
                                <td class="fw-bold text-primary">$<?php echo number_format($row['sale_price'], 2); ?></td>
                                <td>
                                    <span class="badge <?php echo $row['quantity'] > 10 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?> px-2.5 py-1.5 fs-7">
                                        <?php echo $row['quantity']; ?> remaining
                                    </span>
                                </td>
                                <td>
                                    <span class="border badge <?php echo $row['status'] == 1 ? 'bg-success text-white' : 'bg-secondary text-white'; ?> fs-7">
                                        <?php echo $row['status'] == 1 ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center gap-2 h-100">
                                        <button class="btn btn-sm btn-warning fw-bold shadow-sm"
                                            onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger fw-bold shadow-sm"
                                            onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo addslashes($row['product_name']); ?>')">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <!-- 🌟 THIS CATCHES THE EMPTY STATE -->
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <span class="fs-1 d-block mb-2">📦</span>
                                    <h5 class="fw-bold">No Products Found</h5>
                                    <p class=" mb-0">Your catalog is currently empty. Click the "Add New Product" button to get started.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Product Creation Modal (Appended perfectly to the footer space cleanly) -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="addProductModalLabel">Add New Catalog Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Form points directly back to root file executing processing context -->
            <form action="add_product.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Product Name</label>
                        <input type="text" name="product_name" class="form-control" placeholder="e.g., Premium Organic Apple" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Product Graphic/Image</label>
                        <input type="file" name="product_image" class="form-control" accept="image/*">
                        <small class="text-muted fs-8">Supports JPG, PNG or WebP files.</small>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary">Price ($)</label>
                            <input type="number" name="price" step="0.01" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary">Initial Stock Qty</label>
                            <input type="number" name="quantity" class="form-control" placeholder="0" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary">Barcode String</label>
                        <input type="text" name="barcode" class="form-control" placeholder="Scan or type numbers">
                    </div>
                    <div class=" row mb-3">
                        <div class="col-6">

                            <label class="form-label fw-semibold text-secondary">Product Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="" disabled selected>Select a category...</option>
                                <?php
                                if (mysqli_num_rows($categories_result) > 0) {
                                    // Reset pointer just in case, then loop through categories
                                    mysqli_data_seek($categories_result, 0);
                                    while ($cat = mysqli_fetch_assoc($categories_result)) {
                                        echo "<option value='" . $cat['id'] . "'>" . htmlspecialchars($cat['category_name']) . "</option>";
                                    }
                                } else {
                                    echo "<option value='' disabled>No active categories found</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary">Product Brand</label>
                            <select name="brand_id" class="form-select" required>
                                <option value="" disabled selected>Select a brand...</option>
                                <?php
                                if (mysqli_num_rows($brands_result) > 0) {
                                    mysqli_data_seek($brands_result, 0);
                                    while ($brd = mysqli_fetch_assoc($brands_result)) {
                                        echo "<option value='" . $brd['id'] . "'>" . htmlspecialchars($brd['brand_name']) . "</option>";
                                    }
                                } else {
                                    echo "<option value='' disabled>No active brands found</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>


                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit_product" class="btn btn-primary fw-bold px-4">Save to Database</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Product Modal Structure -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="editProductModalLabel">✏️ Edit Catalog Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="edit_product.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <!-- Hidden input to track product ID -->
                    <input type="hidden" name="product_id" id="edit_id">

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Product Name</label>
                        <input type="text" name="product_name" id="edit_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Product Graphic/Image (Leave blank to keep current)</label>
                        <input type="file" name="product_image" class="form-control" accept="image/*">
                        <div class="mt-2 text-center">
                            <small class="text-muted d-block mb-1">Current File Frame View:</small>
                            <img id="edit_img_preview" src="" class="rounded border object-fit-contain" style="height: 60px; max-width: 100px;">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary">Price ($)</label>
                            <input type="number" name="price" id="edit_price" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary">Stock Qty</label>
                            <input type="number" name="quantity" id="edit_quantity" class="form-control" min="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Barcode String</label>
                        <input type="text" name="barcode" id="edit_barcode" class="form-control">
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-secondary">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary fw-bold px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_product" class="btn btn-primary fw-bold px-4 shadow-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // 1. Pop open modal and fill initial column indexes
    function openEditModal(product) {
        document.getElementById('edit_id').value = product.id;
        document.getElementById('edit_name').value = product.product_name;
        document.getElementById('edit_price').value = product.sale_price;
        document.getElementById('edit_quantity').value = product.quantity;
        document.getElementById('edit_barcode').value = product.barcode;
        document.getElementById('edit_status').value = product.status;

        const imgName = product.image ? product.image : 'default.png';
        document.getElementById('edit_img_preview').src = "/mart-retail-ecosystem/mart_pos_system/assets/images/" + imgName;

        const myModal = new bootstrap.Modal(document.getElementById('editProductModal'));
        myModal.show();
    }

    // 2. Intercept submit and fire modern SweetAlert loading animation
    document.getElementById('editProductModal').querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault(); // Pause natural native layout jump page routing

        // Hide the interactive modal overlay frame first
        const modalElement = document.getElementById('editProductModal');
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) modalInstance.hide();

        Swal.fire({
            title: 'Updating Catalog Record...',
            text: 'Please wait while file updates are verified.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Capture standard form datasets containing image streams
        const formData = new FormData(this);
        formData.append('update_product', '1');

        fetch('/mart-retail-ecosystem/mart_pos_system/edit_product.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Changes Saved!',
                        text: 'The product catalog has been updated successfully.',
                        icon: 'success',
                        timer: 1800,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload(); // Refresh viewport matrices to show modified datasets
                    });
                } else {
                    Swal.fire('Modification Denied', data.message || 'Error executing record sync.', 'error');
                }
            })
            .catch(() => Swal.fire('Error!', 'System communication loss processing record updates.', 'error'));
    });
</script>
<script>
    function confirmDelete(id, name) {
        if (typeof Swal === 'undefined') {
            alert("SweetAlert library failed to initialize.");
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: `Permanently remove "${name}" from the ecosystem?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id', id);

                // 🌟 ABSOLUTE ROUTE FIX: Hardcodes the precise path starting from localhost root
                fetch('/mart-retail-ecosystem/mart_pos_system/delete_product.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => {
                        // Check if the file actually exists or throws a 404/500 server error
                        if (!res.ok) {
                            throw new Error(`Server returned status status: ${res.status}`);
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Item removed successfully.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            const targetRow = document.getElementById(`product-row-${id}`);
                            if (targetRow) {
                                targetRow.remove();
                            } else {
                                window.location.reload();
                            }
                        } else {
                            Swal.fire('Error!', data.message || 'Operation failed.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        // 💡 If this fires now, open your browser Console (F12) to see the exact network error string
                        Swal.fire('Error!', 'System communication loss. Check network panel.', 'error');
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