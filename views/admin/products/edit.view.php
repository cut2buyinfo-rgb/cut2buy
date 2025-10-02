<?php
// File: /views/admin/products/edit.view.php
// Version: FINAL - Corrected Layout & All Features Included.
require(ROOT_PATH . '/views/partials/header.php'); 
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2">
            <?php require(ROOT_PATH . '/views/partials/admin_sidebar.php'); ?>
        </div>
        
        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Edit Product</h1>
                <a href="/admin/products" class="btn btn-secondary btn-sm">Back to Products</a>
            </div>
            
            <?php if (isset($_GET['status']) && $_GET['status'] === 'updated'): ?>
                <div class="alert alert-success">Product has been updated successfully!</div>
            <?php endif; ?>
            
            <form id="product-form" action="/admin/products/update" method="POST">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="original_vendor_id" value="<?= $product['vendor_id'] ?>">

                <!-- Card 1: Basic Information -->
                <div class="card mb-4">
                    <div class="card-header"><h4>Basic Information</h4></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="5"><?= htmlspecialchars($product['description']) ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <?php foreach($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= $product['category_id'] == $category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="brand_id" class="form-label">Brand</label>
                                <select class="form-select" id="brand_id" name="brand_id">
                                    <option value="">No Brand</option>
                                    <?php foreach($brands as $brand): ?>
                                    <option value="<?= $brand['id'] ?>" <?= $product['brand_id'] == $brand['id'] ? 'selected' : '' ?>><?= htmlspecialchars($brand['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="warranty_info" class="form-label">Warranty Information</label>
                                <input type="text" class="form-control" id="warranty_info" name="warranty_info" value="<?= htmlspecialchars($product['warranty_info'] ?? '') ?>" placeholder="e.g., 1 Year Warranty">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="draft" <?= $product['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                                    <option value="published" <?= $product['status'] == 'published' ? 'selected' : '' ?>>Published</option>
                                </select>
                            </div>
                        </div>
                        <?php if (isset($user['role']) && $user['role'] === 'super_admin'): ?>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="vendor_id" class="form-label">Vendor / Seller</label>
                                <select class="form-select" id="vendor_id" name="vendor_id">
                                    <option value="">Assign to me (Admin)</option>
                                    <?php foreach($vendors as $vendor): ?>
                                    <option value="<?= $vendor['id'] ?>" <?= $product['vendor_id'] == $vendor['id'] ? 'selected' : '' ?>><?= htmlspecialchars($vendor['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                    <!-- Card 2: Product Variations (NEW STRUCTURE) -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Product Variations (by Color)</h4>
                        <button type="button" id="add-color-group-btn" class="btn btn-sm btn-secondary">
                            <i class="bi bi-plus-circle"></i> Add Color Group
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="color-groups-container">
                            <!-- Existing color groups will be loaded here by PHP -->
                            <?php foreach ($variations as $index => $group): ?>
                                <div class="border p-3 mb-3 rounded bg-light">
                                    <div class="row align-items-center mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Color Name *</label>
                                            <input type="text" name="variations[<?= $index ?>][color]" class="form-control" value="<?= htmlspecialchars($group['color']) ?>" required>
                                            <!-- We store the original color to detect if it was changed -->
                                            <input type="hidden" name="variations[<?= $index ?>][original_color]" value="<?= htmlspecialchars($group['color']) ?>">
                                        </div>
                                        <div class="col-md-8 text-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-color-group-btn">Remove Color Group</button>
                                        </div>
                                    </div>
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Size Name *</th>
                                                <th>Old Price</th>
                                                <th>Sale Price *</th>
                                                <th>SKU</th>
                                                <th>Stock *</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($group['sizes'] as $sizeIndex => $size): ?>
                                            <tr>
                                                <!-- Hidden input to keep track of existing variation IDs -->
                                                <input type="hidden" name="variations[<?= $index ?>][sizes][<?= $sizeIndex ?>][variation_id]" value="<?= $size['details']['id'] ?>">
                                                <td><input type="text" name="variations[<?= $index ?>][sizes][<?= $sizeIndex ?>][name]" class="form-control" value="<?= htmlspecialchars($size['name']) ?>" required></td>
                                                <td><input type="number" step="0.01" name="variations[<?= $index ?>][sizes][<?= $sizeIndex ?>][old_price]" class="form-control" value="<?= htmlspecialchars($size['details']['old_price']) ?>"></td>
                                                <td><input type="number" step="0.01" name="variations[<?= $index ?>][sizes][<?= $sizeIndex ?>][price]" class="form-control" value="<?= htmlspecialchars($size['details']['price']) ?>" required></td>
                                                <td><input type="text" name="variations[<?= $index ?>][sizes][<?= $sizeIndex ?>][sku]" class="form-control" value="<?= htmlspecialchars($size['details']['sku']) ?>"></td>
                                                <td><input type="number" name="variations[<?= $index ?>][sizes][<?= $sizeIndex ?>][stock]" class="form-control" value="<?= htmlspecialchars($size['details']['stock']) ?>" required></td>
                                                <td class="text-center"><button type="button" class="btn btn-link text-danger p-0 remove-size-btn" title="Remove size"><i class="bi bi-x-circle-fill"></i></button></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-sm btn-success add-size-btn"><i class="bi bi-plus"></i> Add Size</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Card 3: Modern Product Images Uploader -->
                <div class="card mb-4">
                     <div class="card-header"><h4>Manage Images</h4></div>
                     <div class="card-body">
                        <div id="image-uploader" class="image-dropzone">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                            <p>Drag & Drop new images here, or click to select files</p>
                        </div>
                        <input type="file" id="image-file-input" multiple hidden accept="image/jpeg,image/png,image/gif">
                        <div id="image-preview-container" class="mt-3 d-flex flex-wrap gap-2"></div>
                     </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg">Update Product</button>
            </form>
        </main>
    </div>
</div>

<style>
/* CSS remains the same as provided */
.image-dropzone { border: 2px dashed #ccc; border-radius: 8px; padding: 30px; text-align: center; cursor: pointer; background-color: #f9f9f9; transition: background-color 0.2s ease; }
.image-dropzone:hover { background-color: #f0f0f0; border-color: #aaa; }
.image-dropzone i { font-size: 3rem; color: #888; }
.preview-item { position: relative; display: inline-block; margin: 10px; border: 1px solid #ddd; border-radius: 5px; padding: 5px; }
.preview-item img { width: 100px; height: 100px; object-fit: cover; }
.remove-preview-btn { position: absolute; top: -10px; right: -10px; background-color: #dc3545; color: white; border: none; width: 25px; height: 25px; border-radius: 50%; font-weight: bold; cursor: pointer; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- PART 1: NEW GROUPED VARIATIONS LOGIC for EDIT PAGE ---
    let colorGroupIndex = <?= count($variations) ?>; // Start counting from the number of existing groups
    const colorGroupsContainer = document.getElementById('color-groups-container');
    const addColorGroupBtn = document.getElementById('add-color-group-btn');

    // This function creates a new "Color Group" block. It's for adding COMPLETELY new colors.
    function createColorGroup(index) {
        let sizeIndex = 0;
        const groupEl = document.createElement('div');
        groupEl.className = 'border p-3 mb-3 rounded bg-light';
        groupEl.innerHTML = `
            <div class="row align-items-center mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Color Name *</label>
                    <input type="text" name="variations[${index}][color]" class="form-control" placeholder="e.g., Green" required>
                    <!-- New groups don't have an original color -->
                    <input type="hidden" name="variations[${index}][original_color]" value="">
                </div>
                <div class="col-md-8 text-end">
                    <button type="button" class="btn btn-danger btn-sm remove-color-group-btn">Remove Color Group</button>
                </div>
            </div>
            
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Size Name *</th>
                        <th>Old Price</th>
                        <th>Sale Price *</th>
                        <th>SKU</th>
                        <th>Stock *</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Size rows will be added here -->
                </tbody>
            </table>
            <button type="button" class="btn btn-sm btn-success add-size-btn"><i class="bi bi-plus"></i> Add Size</button>
        `;

        const tbody = groupEl.querySelector('tbody');
        
        // Add one size row automatically for the new color group
        addSizeRowToTable(tbody, index, sizeIndex++);
        
        colorGroupsContainer.appendChild(groupEl);
    }
    
    // This function creates a new table row for a size.
    function addSizeRowToTable(tbody, colorIdx, sizeIdx) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <!-- New sizes don't have a variation_id -->
            <input type="hidden" name="variations[${colorIdx}][sizes][${sizeIdx}][variation_id]" value="">
            <td><input type="text" name="variations[${colorIdx}][sizes][${sizeIdx}][name]" class="form-control" placeholder="e.g., S" required></td>
            <td><input type="number" step="0.01" name="variations[${colorIdx}][sizes][${sizeIdx}][old_price]" class="form-control"></td>
            <td><input type="number" step="0.01" name="variations[${colorIdx}][sizes][${sizeIdx}][price]" class="form-control" required></td>
            <td><input type="text" name="variations[${colorIdx}][sizes][${sizeIdx}][sku]" class="form-control"></td>
            <td><input type="number" name="variations[${colorIdx}][sizes][${sizeIdx}][stock]" class="form-control" required></td>
            <td class="text-center"><button type="button" class="btn btn-link text-danger p-0 remove-size-btn" title="Remove size"><i class="bi bi-x-circle-fill"></i></button></td>
        `;
        tbody.appendChild(tr);
    }
    
    // Event listener for adding a brand new color group
    addColorGroupBtn.addEventListener('click', () => {
        createColorGroup(colorGroupIndex++);
    });

    // Use a single event listener on the container to handle all button clicks inside
    colorGroupsContainer.addEventListener('click', function(e) {
        const target = e.target;

        // Handle "Add Size" button click
        if (target.classList.contains('add-size-btn')) {
            const groupDiv = target.closest('.border');
            const tbody = groupDiv.querySelector('tbody');
            const colorInput = groupDiv.querySelector('input[name*="[color]"]');
            
            // Extract the color index from the name attribute, e.g., "variations[0][color]" -> 0
            const colorIdx = colorInput.name.match(/\[(\d+)\]/)[1];
            
            // The new size index is simply the current number of rows
            const sizeIdx = tbody.children.length;

            addSizeRowToTable(tbody, colorIdx, sizeIdx);
        }

        // Handle "Remove Size" button click
        if (target.closest('.remove-size-btn')) {
            const row = target.closest('tr');
            const tbody = row.parentElement;
            if (tbody.children.length > 1) {
                // To delete from the database, we hide the row and add a "delete" marker.
                // This is better than removing it, as the backend needs to know what to delete.
                row.style.display = 'none';
                const variationIdInput = row.querySelector('input[name*="[variation_id]"]');
                if (variationIdInput && variationIdInput.value) { // Only for existing variations
                    const deleteMarker = document.createElement('input');
                    deleteMarker.type = 'hidden';
                    deleteMarker.name = `variations_to_delete[]`;
                    deleteMarker.value = variationIdInput.value;
                    productForm.appendChild(deleteMarker);
                }
            } else {
                alert('Each color must have at least one size.');
            }
        }
        
        // Handle "Remove Color Group" button click
        if (target.classList.contains('remove-color-group-btn')) {
            if (colorGroupsContainer.children.length > 1) {
                const groupDiv = target.closest('.border');
                // Mark all variations within this group for deletion
                const variationIdInputs = groupDiv.querySelectorAll('input[name*="[variation_id]"]');
                variationIdInputs.forEach(input => {
                    if (input.value) {
                         const deleteMarker = document.createElement('input');
                         deleteMarker.type = 'hidden';
                         deleteMarker.name = `variations_to_delete[]`;
                         deleteMarker.value = input.value;
                         productForm.appendChild(deleteMarker);
                    }
                });
                // Hide the group from view
                groupDiv.style.display = 'none';
            } else {
                alert('A product must have at least one color group.');
            }
        }
    });


    // --- PART 2: AJAX IMAGE UPLOADER LOGIC (Unchanged from your provided code) ---
    const uploader = document.getElementById('image-uploader');
    const fileInput = document.getElementById('image-file-input');
    const previewContainer = document.getElementById('image-preview-container');
    const productForm = document.getElementById('product-form');

    const existingImages = <?= json_encode($images) ?>;
    existingImages.forEach(image => {
        addPreview({ id: image.id, url: '/assets/images/products/' + image.image_path, filename: image.image_path }, true);
    });

    uploader.addEventListener('click', () => fileInput.click());
    uploader.addEventListener('dragover', (e) => { e.preventDefault(); uploader.classList.add('drag-over'); });
    uploader.addEventListener('dragleave', () => { uploader.classList.remove('dragleave'); });
    uploader.addEventListener('drop', (e) => {
        e.preventDefault();
        uploader.classList.remove('drag-over');
        handleFiles(e.dataTransfer.files);
    });
    fileInput.addEventListener('change', (e) => handleFiles(e.target.files));

    function handleFiles(files) {
        for (const file of files) { uploadFile(file); }
    }

    function uploadFile(file) {
        const formData = new FormData();
        formData.append('images[]', file);

        fetch('/ajax_image_uploader.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                data.files.forEach(fileInfo => addPreview(fileInfo, false));
            } else {
                alert('Upload failed: ' + data.error);
            }
        })
        .catch(error => { 
            console.error('Upload error:', error); 
            alert('An error occurred during upload.'); 
        });
    }

    function addPreview(fileInfo, isExisting) {
        const previewItem = document.createElement('div');
        previewItem.classList.add('preview-item');
        previewItem.innerHTML = `<img src="${fileInfo.url}" alt="Image preview"><button type="button" class="remove-preview-btn">&times;</button>`;
        previewContainer.appendChild(previewItem);
        
        const removeBtn = previewItem.querySelector('.remove-preview-btn');
        
        if (isExisting) {
            removeBtn.addEventListener('click', () => {
                if (confirm('Are you sure you want to mark this image for deletion?')) {
                    const deleteInput = document.createElement('input');
                    deleteInput.type = 'hidden';
                    deleteInput.name = 'delete_images[]';
                    deleteInput.value = fileInfo.id;
                    productForm.appendChild(deleteInput);
                    previewItem.style.display = 'none';
                }
            });
        } else {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'uploaded_images[]';
            hiddenInput.value = fileInfo.filename;
            productForm.appendChild(hiddenInput);
            
            removeBtn.addEventListener('click', () => {
                previewItem.remove();
                hiddenInput.remove();
            });
        }
    }
});
</script>

<?php require __DIR__ . '/../../partials/footer.php'; ?>