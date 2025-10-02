<?php
// File: /views/admin/products/create.view.php
// Version: FINAL - Complete with Warranty field.
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
                <h1 class="h2">Add New Product</h1>
            </div>
            
            <form id="product-form" action="/admin/products/store" method="POST">
                
                <!-- Card 1: Basic Information -->
                <div class="card mb-4">
                    <div class="card-header"><h4>Basic Information</h4></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="" selected disabled>Select a category</option>
                                    <?php foreach($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                             <div class="col-md-3 mb-3">
                                <label for="brand_id" class="form-label">Brand</label>
                                <select class="form-select" id="brand_id" name="brand_id">
                                    <option value="">No Brand</option>
                                    <?php foreach($brands as $brand): ?>
                                    <option value="<?= $brand['id'] ?>"><?= htmlspecialchars($brand['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- --- NEW WARRANTY FIELD --- -->
                            <div class="col-md-3 mb-3">
                                <label for="warranty_info" class="form-label">Warranty Information</label>
                                <input type="text" class="form-control" id="warranty_info" name="warranty_info" placeholder="e.g., 1 Year Brand Warranty">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="draft" selected>Draft</option>
                                    <option value="published">Published</option>
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
                                    <option value="<?= $vendor['id'] ?>"><?= htmlspecialchars($vendor['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Card 2: Product Variations (Completely New Structure) -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Product Variations (by Color)</h4>
        <button type="button" id="add-color-group-btn" class="btn btn-sm btn-secondary">
            <i class="bi bi-plus-circle"></i> Add Color Group
        </button>
    </div>
    <div class="card-body">
        <p class="text-muted small">
            First, add a color group. Then, within that color group, add as many sizes as you need, each with its own price and stock.
        </p>
        <div id="color-groups-container">
            <!-- Color groups will be added here by JavaScript -->
        </div>
    </div>
</div>
                
                <!-- Card 3: Modern Product Images Uploader -->
                <div class="card mb-4">
                     <div class="card-header"><h4>Product Images</h4></div>
                     <div class="card-body">
                        <div id="image-uploader" class="image-dropzone">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                            <p>Drag & Drop images here, or click to select files</p>
                        </div>
                        <input type="file" id="image-file-input" multiple hidden accept="image/jpeg,image/png,image/gif">
                        <div id="image-preview-container" class="mt-3 d-flex flex-wrap gap-2"></div>
                     </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg">Save Product</button>
            </form>
        </main>
    </div>
</div>




<style>
/* CSS remains the same */
.image-dropzone { border: 2px dashed #ccc; border-radius: 8px; padding: 30px; text-align: center; cursor: pointer; background-color: #f9f9f9; transition: background-color 0.2s ease; }
.image-dropzone:hover { background-color: #f0f0f0; border-color: #aaa; }
.image-dropzone i { font-size: 3rem; color: #888; }
.preview-item { position: relative; display: inline-block; margin: 10px; border: 1px solid #ddd; border-radius: 5px; padding: 5px; }
.preview-item img { width: 100px; height: 100px; object-fit: cover; }
.remove-preview-btn { position: absolute; top: -10px; right: -10px; background-color: #dc3545; color: white; border: none; width: 25px; height: 25px; border-radius: 50%; font-weight: bold; cursor: pointer; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- PART 1: GROUPED VARIATIONS LOGIC WITH AUTO-SKU ---
    let colorGroupIndex = 0;
    const colorGroupsContainer = document.getElementById('color-groups-container');
    const addColorGroupBtn = document.getElementById('add-color-group-btn');

    function createColorGroup(index) {
        let sizeIndex = 0;
        const groupEl = document.createElement('div');
        groupEl.className = 'border p-3 mb-3 rounded bg-light';
        groupEl.innerHTML = `
            <div class="row align-items-center mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Color Name *</label>
                    <input type="text" name="variations[${index}][color]" class="form-control" placeholder="e.g., Red" required>
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
                <tbody></tbody>
            </table>
            <button type="button" class="btn btn-sm btn-success add-size-btn"><i class="bi bi-plus"></i> Add Size</button>
        `;

        const tbody = groupEl.querySelector('tbody');

        function addSizeRow() {
            const tr = document.createElement('tr');
            
            // --- AUTO-SKU GENERATION LOGIC ---
            // Generates a unique-enough SKU using the current timestamp.
            const autoSku = 'C2B-' + Date.now().toString().slice(-6) + sizeIndex;

            tr.innerHTML = `
                <td><input type="text" name="variations[${index}][sizes][${sizeIndex}][name]" class="form-control" placeholder="e.g., XL" required></td>
                <td><input type="number" step="0.01" name="variations[${index}][sizes][${sizeIndex}][old_price]" class="form-control"></td>
                <td><input type="number" step="0.01" name="variations[${index}][sizes][${sizeIndex}][price]" class="form-control" required></td>
                <td><input type="text" name="variations[${index}][sizes][${sizeIndex}][sku]" class="form-control" value="${autoSku}" placeholder="Auto-generated if empty"></td>
                <td><input type="number" name="variations[${index}][sizes][${sizeIndex}][stock]" class="form-control" required></td>
                <td class="text-center"><button type="button" class="btn btn-link text-danger p-0 remove-size-btn" title="Remove size"><i class="bi bi-x-circle-fill"></i></button></td>
            `;
            tbody.appendChild(tr);
            sizeIndex++;
        }

        groupEl.querySelector('.add-size-btn').addEventListener('click', addSizeRow);
        
        tbody.addEventListener('click', function(e) {
            if (e.target.closest('.remove-size-btn')) {
                if (tbody.children.length > 1) {
                    e.target.closest('tr').remove();
                } else {
                    alert('Each color must have at least one size.');
                }
            }
        });

        addSizeRow(); // Add one size row automatically
        colorGroupsContainer.appendChild(groupEl);
    }

    addColorGroupBtn.addEventListener('click', () => {
        createColorGroup(colorGroupIndex++);
    });

    colorGroupsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-color-group-btn')) {
            if (colorGroupsContainer.children.length > 1) {
                e.target.closest('.border').remove();
            } else {
                alert('A product must have at least one color group.');
            }
        }
    });

    createColorGroup(colorGroupIndex++); // Add the first color group on page load

    // --- PART 2: AJAX IMAGE UPLOADER LOGIC (This part is unchanged) ---
    const uploader = document.getElementById('image-uploader');
    const fileInput = document.getElementById('image-file-input');
    const previewContainer = document.getElementById('image-preview-container');
    const productForm = document.getElementById('product-form');

    uploader.addEventListener('click', () => fileInput.click());
    uploader.addEventListener('dragover', (e) => { e.preventDefault(); uploader.classList.add('drag-over'); });
    uploader.addEventListener('dragleave', () => { uploader.classList.remove('drag-over'); });
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
                data.files.forEach(fileInfo => addPreview(fileInfo));
            } else {
                alert('Upload failed: '.concat(data.error));
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            alert('An error occurred during upload.');
        });
    }

    function addPreview(fileInfo) {
        const previewItem = document.createElement('div');
        previewItem.classList.add('preview-item');
        previewItem.innerHTML = `<img src="${fileInfo.url}" alt="Image preview"><button type="button" class="remove-preview-btn">&times;</button>`;
        previewContainer.appendChild(previewItem);
        
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'uploaded_images[]';
        hiddenInput.value = fileInfo.filename;
        productForm.appendChild(hiddenInput);
        
        previewItem.querySelector('.remove-preview-btn').addEventListener('click', () => {
             if (confirm('Are you sure you want to remove this uploaded image?')) {
                previewItem.remove();
                hiddenInput.remove();
            }
        });
    }
});
</script>

<?php require __DIR__ . '/../../partials/footer.php'; ?>