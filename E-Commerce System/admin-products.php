<?php
include_once "./config/db.php";

$query = "SELECT * FROM `products` ORDER BY product_id DESC";
$result = mysqli_query($conn, $query);

$cat_query = "SELECT * FROM `categories` ORDER BY category_name ASC";
$cat_result = mysqli_query($conn, $cat_query);
?>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">Product Catalog</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="fa-solid fa-plus me-2"></i>Add New Product
        </button>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
        <?php
        if ($result && mysqli_num_rows($result) > 0):
            while ($product = mysqli_fetch_assoc($result)):
                $image = !empty($product['image']) ? $product['image'] : 'https://via.placeholder.com/300x200?text=No+Image';
                $status = isset($product['stock_quantity']) && $product['stock_quantity'] > 0 ? 'In Stock' : 'Out of Stock';
                $statusClass = $status === 'In Stock' ? 'bg-success' : 'bg-danger';
        ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="position-relative">
                            <img src="<?php echo htmlspecialchars($image); ?>" class="card-img-top object-fit-cover" alt="<?php echo htmlspecialchars($product['product_name']); ?>" style="height: 180px;">
                            <span class="position-absolute top-0 end-0 text-white small fw-bold px-2 py-1 m-2 rounded <?php echo $statusClass; ?>">
                                <?php echo $status; ?>
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column p-3">
                            <h6 class="card-title text-dark fw-bold mb-1 text-truncate" title="<?php echo htmlspecialchars($product['product_name']); ?>">
                                <?php echo htmlspecialchars($product['product_name']); ?>
                            </h6>
                            <p class="card-text text-muted small flex-grow-1 text-truncate-2 mb-2">
                                <?php echo htmlspecialchars($product['description'] ?? ''); ?>
                            </p>

                            <div class="d-flex justify-content-between align-items-center mt-auto pt-2">
                                <span class="fs-5 fw-bold text-primary">$<?php echo number_format($product['price'], 2); ?></span>
                                <span class="text-muted small">Stock: <?php echo (int)($product['stock_quantity'] ?? 0); ?></span>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent border-top-0 p-3 pt-0 d-flex gap-2">
                            <a href="index.php?page=products&action=edit&id=<?php echo $product['product_id']; ?>" class="btn btn-outline-secondary btn-sm flex-grow-1">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                            </a>
                            <a href="actions/product/delete_product.php?id=<?php echo $product['product_id']; ?>"
                                class="btn btn-outline-danger btn-sm px-3"
                                onclick="return confirm('Are you sure you want to delete this product?');">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="position-relative">
                            <img src="<?php echo htmlspecialchars($image); ?>" class="card-img-top object-fit-cover" alt="<?php echo htmlspecialchars($product['product_name']); ?>" style="height: 180px;">
                            <span class="position-absolute top-0 end-0 text-white small fw-bold px-2 py-1 m-2 rounded <?php echo $statusClass; ?>">
                                <?php echo $status; ?>
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column p-3">
                            <h6 class="card-title text-dark fw-bold mb-1 text-truncate" title="<?php echo htmlspecialchars($product['product_name']); ?>">
                                <?php echo htmlspecialchars($product['product_name']); ?>
                            </h6>
                            <p class="card-text text-muted small flex-grow-1 text-truncate-2 mb-2">
                                <?php echo htmlspecialchars($product['description'] ?? ''); ?>
                            </p>

                            <div class="d-flex justify-content-between align-items-center mt-auto pt-2">
                                <span class="fs-5 fw-bold text-primary">$<?php echo number_format($product['price'], 2); ?></span>
                                <span class="text-muted small">Stock: <?php echo (int)($product['stock_quantity'] ?? 0); ?></span>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent border-top-0 p-3 pt-0 d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm flex-grow-1" data-bs-toggle="modal" data-bs-target="#editModal-<?php echo $product['product_id']; ?>">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm px-3" onclick="confirmDelete(<?php echo $product['product_id']; ?>)">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
    </div>
<?php
            endwhile;
        else:
?>
<div class="d-flex justify-content-center align-items-center w-100">
    <div class="text-center  py-5 ">
        <div class="text-muted">
            <i class="fa-solid fa-box-open fa-3x mb-3"></i>
            <h5>No products found</h5>
            <p class="small">Start by adding your first product to the inventory database.</p>
        </div>
    </div>
</div>
<?php endif; ?>
</div>
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark" id="addProductModalLabel">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="../actions/product/add_product.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">

                    <div class="mb-3">
                        <label for="product_name" class="form-label small fw-bold">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" required placeholder="e.g., Wireless Mouse">
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label small fw-bold">Product Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="" selected disabled>-- Select a Category --</option>
                            <?php
                            // Loop through the categories database table to make options
                            if ($cat_result && mysqli_num_rows($cat_result) > 0) {
                                while ($category = mysqli_fetch_assoc($cat_result)) {
                                    echo "<option value='" . $category['category_id'] . "'>" . htmlspecialchars($category['category_name']) . "</option>";
                                }
                            } else {
                                echo "<option value='' disabled>No categories found. Please add one first!</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="price" class="form-label small fw-bold">Price ($)</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required placeholder="0.00">
                        </div>
                        <div class="col-6">
                            <label for="stock_quantity" class="form-label small fw-bold">Stock Quantity</label>
                            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" required placeholder="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label small fw-bold">Product Picture</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        <!-- <div class="form-text mt-1" style="font-size: 0.75rem;">Upload .jpg, .png, or .webp files.</div> -->
                    </div>

                    <div class="mb-0">
                        <label for="description" class="form-label small fw-bold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter product specifications or details..."></textarea>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit_add_product" class="btn btn-primary px-4">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="editModal-<?php echo $product['product_id']; ?>" tabindex="-1" aria-labelledby="editModalLabel-<?php echo $product['product_id']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark" id="editModalLabel-<?php echo $product['product_id']; ?>">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="../actions/product/edit_product.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body text-start p-4">

                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Product Name</label>
                        <input type="text" class="form-control" name="product_name" required value="<?php echo htmlspecialchars($product['product_name']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Product Category</label>
                        <select class="form-select" name="category_id" required>
                            <?php
                            // Run a fresh query inside the loop to build categories dropdown
                            $cat_run = mysqli_query($conn, "SELECT * FROM `categories` ORDER BY category_name ASC");
                            while ($cat = mysqli_fetch_assoc($cat_run)) {
                                // If this category matches the product's current category, mark it 'selected'
                                $selected = ($cat['category_id'] == $product['category_id']) ? 'selected' : '';
                                echo "<option value='" . $cat['category_id'] . "' $selected>" . htmlspecialchars($cat['category_name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Price ($)</label>
                            <input type="number" step="0.01" class="form-control" name="price" required value="<?php echo $product['price']; ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Stock Quantity</label>
                            <input type="number" class="form-control" name="stock_quantity" required value="<?php echo $product['stock_quantity']; ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Product Picture (Leave blank to keep current)</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <div class="form-text text-muted mt-1" style="font-size:0.75rem;">
                            Current file: <?php echo basename($product['image']); ?>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-bold">Description</label>
                        <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                    </div>

                </div>

                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit_edit_product" class="btn btn-success px-4">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function confirmDelete(id) {
        if (confirm("Are you sure you want to delete this product?")) {
            window.location.href = "index.php?page=products&action=delete&id=" + id;
        }
    }
</script>