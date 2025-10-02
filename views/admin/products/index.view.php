<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2">
          <?php require __DIR__ . '/../../partials/admin_sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1>Manage Products</h1>
                <a href="/admin/products/create" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add New Product
                </a>
            </div>

            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?= $_SESSION['flash_message']['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['flash_message']['text']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['flash_message']); ?>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <!-- === NEW 'Image' COLUMN ADDED === -->
                                    <th style="width: 10%;">Image</th> 
                                    <th style="width: 30%;">Name</th>
                                    <th>Category</th>
                                    <th>Price Range</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No products found. Add one to get started!</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $product): ?>
                                    <tr>
                                        <!-- === NEW 'Image' CELL ADDED === -->
                                        <td>
                                            <img src="/assets/images/products/<?= htmlspecialchars($product['featured_image'] ?? 'placeholder.png') ?>" 
                                                 alt="<?= htmlspecialchars($product['name']) ?>" 
                                                 class="img-thumbnail" 
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                        </td>
                                        <td><?= htmlspecialchars($product['name']) ?></td>
                                        <td><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></td>
                                        <td>
                                            <?php
                                            if (isset($product['min_price'])) {
                                                if ($product['min_price'] == $product['max_price']) {
                                                    echo '৳' . number_format($product['min_price']);
                                                } else {
                                                    echo '৳' . number_format($product['min_price']) . ' - ৳' . number_format($product['max_price']);
                                                }
                                            } else {
                                                echo '<span class="text-muted">N/A</span>';
                                            }
                                            ?>
                                        </td>
                                        <td><?= $product['total_stock'] ?? 0 ?></td>
                                        <td>
                                            <span class="badge bg-<?= $product['status'] === 'published' ? 'success' : 'secondary' ?>">
                                                <?= ucfirst($product['status']) ?>
                                            </span>
                                        </td>
                                        <td class="d-flex gap-2">
                                            <a href="/admin/products/edit/<?= $product['id'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                            <form action="/admin/products/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure? This action is permanent.');">
                                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>