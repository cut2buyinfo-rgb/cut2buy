<?php require(ROOT_PATH . '/views/partials/header.php'); ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <?php require(ROOT_PATH . '/views/partials/admin_sidebar.php'); ?>
        </div>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Categories</h1>
            </div>


 <!--  TO DISPLAY ERROR MESSAGES -->
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>


            <div class="row">
                <!-- Category List -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead><tr><th>Category Name</th><th>Slug</th><th>Actions</th></tr></thead>
                                <tbody>
                                    <?php
                                    // Function to display categories in a hierarchical list
                                    function displayCategories($categories, $level = 0) {
                                        $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                                        foreach($categories as $category) {
                                            echo '<tr>';
                                            echo '<td>' . $indent . htmlspecialchars($category['name']) . '</td>';
                                            echo '<td>' . htmlspecialchars($category['slug']) . '</td>';
                                            echo '<td class="text-end">
                                                    <button class="btn btn-sm btn-outline-primary edit-btn" 
                                                            data-id="'.$category['id'].'" 
                                                            data-name="'.htmlspecialchars($category['name']).'" 
                                                            data-parent-id="'.($category['parent_id'] ?? '').'">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <form action="/admin/categories/delete" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure? Child categories will become top-level.\');">
                                                        <input type="hidden" name="id" value="'.$category['id'].'">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                    </form>
                                                  </td>';
                                            echo '</tr>';
                                            if (isset($category['children'])) {
                                                displayCategories($category['children'], $level + 1);
                                            }
                                        }
                                    }
                                    displayCategories($category_tree);
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Add/Edit Form -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><strong id="form-title">Add New Category</strong></div>
                        <div class="card-body">
                            <form id="category-form" action="/admin/categories/store" method="POST">
                                <input type="hidden" name="id" id="category-id">
                                <div class="mb-3">
                                    <label for="categoryName" class="form-label">Category Name</label>
                                    <input type="text" id="categoryName" name="name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="parentId" class="form-label">Parent Category</label>
                                    <select id="parentId" name="parent_id" class="form-select">
                                        <option value="">None (Top-level Category)</option>
                                        <?php
                                        function displayCategoryOptions($categories, $level = 0) {
                                            $indent = str_repeat('--', $level) . ' ';
                                            foreach($categories as $category) {
                                                echo '<option value="'.$category['id'].'">'.$indent . htmlspecialchars($category['name']).'</option>';
                                                if (isset($category['children'])) {
                                                    displayCategoryOptions($category['children'], $level + 1);
                                                }
                                            }
                                        }
                                        displayCategoryOptions($category_tree);
                                        ?>
                                    </select>
                                </div>
                                <button type="submit" id="submit-btn" class="btn btn-primary w-100">Save Category</button>
                                <button type="button" id="cancel-btn" class="btn btn-secondary w-100 mt-2" style="display: none;">Cancel Edit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-btn');
    const categoryForm = document.getElementById('category-form');
    const formTitle = document.getElementById('form-title');
    const categoryIdInput = document.getElementById('category-id');
    const categoryNameInput = document.getElementById('categoryName');
    const parentIdSelect = document.getElementById('parentId');
    const submitBtn = document.getElementById('submit-btn');
    const cancelBtn = document.getElementById('cancel-btn');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const parentId = this.dataset.parentId;

            // Switch form to Edit mode
            formTitle.textContent = 'Edit Category';
            categoryForm.action = '/admin/categories/update';
            categoryIdInput.value = id;
            categoryNameInput.value = name;
            parentIdSelect.value = parentId;
            submitBtn.textContent = 'Update Category';
            cancelBtn.style.display = 'block';

            // Disable the option that matches the current category to prevent self-parenting
            for (let option of parentIdSelect.options) {
                option.disabled = (option.value == id);
            }
        });
    });

    cancelBtn.addEventListener('click', function() {
        // Switch form back to Add mode
        formTitle.textContent = 'Add New Category';
        categoryForm.action = '/admin/categories/store';
        categoryForm.reset(); // Clears all form fields
        categoryIdInput.value = '';
        submitBtn.textContent = 'Save Category';
        cancelBtn.style.display = 'none';
        
        // Re-enable all options
        for (let option of parentIdSelect.options) {
            option.disabled = false;
        }
    });
});
</script>

<?php require(ROOT_PATH . '/views/partials/footer.php'); ?>