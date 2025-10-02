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
                <h1 class="h2">Manage Brands</h1>
            </div>

            <div class="row">
                <!-- Brand List -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr><th>Brand Name</th><th>Slug</th><th class="text-end">Actions</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach($brands as $brand): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($brand['name']) ?></td>
                                        <td><?= htmlspecialchars($brand['slug']) ?></td>
                                        <td class="text-end">
                                            <form action="/admin/brands/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this brand? This cannot be undone.');">
                                                <input type="hidden" name="id" value="<?= $brand['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Add New Brand Form -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><strong>Add New Brand</strong></div>
                        <div class="card-body">
                            <form action="/admin/brands/store" method="POST">
                                <div class="mb-3">
                                    <label for="brandName" class="form-label">Brand Name</label>
                                    <input type="text" id="brandName" name="name" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Save Brand</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require(ROOT_PATH . '/views/partials/footer.php'); ?>