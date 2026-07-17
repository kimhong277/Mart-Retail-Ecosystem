<?php
// pages/products.php
require_once 'db.php';

// READ: Fetch all products with their associated category and brand names (including image column reference)
$product_sql = "SELECT p.*, c.category_name, b.brand_name 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                ORDER BY p.id DESC";
$products_result = mysqli_query($conn, $product_sql);

// Fetch active categories and brands to populate form dropdown selections safely
$categories_list = mysqli_query($conn, "SELECT id, category_name FROM categories WHERE status = 1 ORDER BY category_name ASC");
$brands_list     = mysqli_query($conn, "SELECT id, brand_name FROM brands WHERE status = 1 ORDER BY brand_name ASC");

// Convert data references to arrays so we can reuse them inside editing loops without querying MySQL repeatedly
$categories_arr = [];
while ($cat = mysqli_fetch_assoc($categories_list)) {
    $categories_arr[] = $cat;
}

$brands_arr = [];
while ($brnd = mysqli_fetch_assoc($brands_list)) {
    $brands_arr[] = $brnd;
}
?>

<div class="container-fluid px-4 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-gray-800 fw-bold">Products Catalog</h2>
            <p class="text-muted small mb-0">Manage master inventory items, barcodes, and core base pricing parameters.</p>
        </div>
        <button type="button" class="btn btn-dark btn-sm fw-semibold px-4 py-2" data-bs-toggle="modal" data-bs-target="#addProductModal">
            + Add Product
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4" style="width: 80px;">Preview</th>
                            <th>Barcode</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Stock Qty</th>
                            <th>Price</th>
                            <th class="text-center" style="width: 15%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php if ($products_result && mysqli_num_rows($products_result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($products_result)):
                                // 1. Explicitly check if the database value is empty, null, or has placeholder strings
                                if (empty($row['image']) || $row['image'] == 'default.png') {

                                    // 1. Force the category name to lowercase so the API can read it cleanly
                                    $lowercase_category = strtolower($row['category_name']);

                                    // 2. Clean up any accidental quotes safely
                                    $clean_category = str_replace(["'", '"'], "", $lowercase_category) . "?lock=" . $row['id'];;

                                    // 3. Generate the absolute placeholder URL string
                                    $img_src = "https://loremflickr.com/320/240/" . urlencode($clean_category);
                                } else {
                                    // Load the custom file path from your local folder
                                    $img_src = "uploads/" . $row['image'];
                                }

                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <img src="<?= $img_src ?>" class="rounded border p-1 bg-white" style="width: 42px; height: 42px; object-fit: contain;" alt="thumb">
                                    </td>
                                    <td class="text-secondary fw-mono"><?= htmlspecialchars($row['barcode'] ?: '------') ?></td>
                                    <td class="fw-bold text-dark"><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td class="text-secondary"><?= htmlspecialchars($row['category_name'] ?? 'Unassigned') ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['brand_name'] ?? 'Generic') ?></span></td>
                                    <td class="fw-bold"><?= $row['quantity'] ?></td>
                                    <td class="fw-bold text-success">$<?= number_format($row['price'], 2) ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#editProductModal<?= $row['id'] ?>">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editProductModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 shadow-lg">
                                            <div class="modal-header bg-dark text-white">
                                                <h5 class="modal-title fw-bold">Modify Product Item Specs</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="process_product.php" method="POST" enctype="multipart/form-data">
                                                <div class="modal-body p-4">
                                                    <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                                                    <input type="hidden" name="current_image" value="<?= $row['image'] ?>">

                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold text-secondary">Product SKU / Barcode</label>
                                                            <input type="text" name="barcode" class="form-control" value="<?= htmlspecialchars($row['barcode']) ?>">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold text-secondary">Product Title Designation <span class="text-danger">*</span></label>
                                                            <input type="text" name="product_name" class="form-control" value="<?= htmlspecialchars($row['product_name']) ?>" required>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold text-secondary">Group Category Assignment</label>
                                                            <select name="category_id" class="form-select">
                                                                <option value="0">Select Category</option>
                                                                <?php foreach ($categories_arr as $cat): ?>
                                                                    <option value="<?= $cat['id'] ?>" <?= $row['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['category_name']) ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold text-secondary">Manufacturer Brand Origin</label>
                                                            <select name="brand_id" class="form-select">
                                                                <option value="0">Select Brand</option>
                                                                <?php foreach ($brands_arr as $brnd): ?>
                                                                    <option value="<?= $brnd['id'] ?>" <?= $row['brand_id'] == $brnd['id'] ? 'selected' : '' ?>><?= htmlspecialchars($brnd['brand_name']) ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <label class="form-label small fw-bold text-secondary">Stock Levels Counter</label>
                                                            <input type="number" name="quantity" class="form-control" value="<?= $row['quantity'] ?>" min="0">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label small fw-bold text-secondary">Retail Price Tag ($)</label>
                                                            <input type="number" name="price" class="form-control" step="0.01" value="<?= $row['price'] ?>" min="0">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label small fw-bold text-secondary">Update Image File</label>
                                                            <input type="file" name="image" class="form-control" accept="image/*">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="update_product" class="btn btn-dark btn-sm px-4">Apply Data Record Updates</button>
                                                </div>
                                            </form>
                                        </div>


                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">Your storage database catalog is currently empty. Add your first item.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Add New Master Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_product.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Barcode / Unique SKU Scanner Target</label>
                            <input type="text" name="barcode" class="form-control" placeholder="e.g., 884520147962">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Product Name Label <span class="text-danger">*</span></label>
                            <input type="text" name="product_name" class="form-control" placeholder="e.g., Coca-Cola Can 330ml" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Assigned Category Selector</label>
                            <select name="category_id" class="form-select" required>
                                <option value="" disabled selected hidden>-- Choose Group Category --</option>
                                <?php foreach ($categories_arr as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Associated Manufacturing Line Brand</label>
                            <select name="brand_id" class="form-select" required>
                                <option value="" disabled selected hidden>-- Choose Brand --</option>
                                <?php foreach ($brands_arr as $brnd): ?>
                                    <option value="<?= $brnd['id'] ?>"><?= htmlspecialchars($brnd['brand_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">Opening Stock Units Quantity</label>
                            <input type="number" name="quantity" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">Base Sales Unit Retail Price Tag ($)</label>
                            <input type="number" name="price" class="form-control" step="0.01" value="0.00" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">Product Banner/Display Thumbnail</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_product" class="btn btn-dark btn-sm px-4">Save Product Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const statusMsg = urlParams.get('status');

    if (statusMsg) {
        let titleText = '';
        let iconType = 'success';

        if (statusMsg === 'inserted') {
            titleText = 'Product Appended to Inventory Catalog!';
        } else if (statusMsg === 'updated') {
            titleText = 'Product Spec Fields Saved!';
        } else if (statusMsg === 'invalid_image') {
            titleText = 'Unsupported File Upload Type!';
            iconType = 'error';
        } else if (statusMsg === 'error') {
            titleText = 'Operation Execution Failure!';
            iconType = 'error';
        }

        if (titleText !== '') {
            Swal.fire({
                title: titleText,
                icon: iconType,
                confirmButtonColor: '#2D3748',
                timer: 2500
            });
            window.history.replaceState({}, document.title, window.location.pathname + "?page=products");
        }
    }
</script>