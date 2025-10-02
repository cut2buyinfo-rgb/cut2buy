<?php require(ROOT_PATH . '/views/partials/header.php'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2">
            <?php require(ROOT_PATH . '/views/partials/admin_sidebar.php'); ?>
        </div>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Flash Sale</h1>
            </div>

            <!-- Add to Flash Sale Form -->
            <div class="card mb-4">
                <div class="card-header">Add Product to Flash Sale</div>
                <div class="card-body">
                    <form action="/admin/flash-sale/store" method="POST">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="product_id" class="form-label">Product</label>
                                <select name="product_id" id="product_id" class="form-select" required>
                                    <option value="">Select a product...</option>
                                    <?php foreach ($products_to_add as $product): ?>
                                        <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="flash_price" class="form-label">Flash Price (৳)</label>
                                <input type="number" step="0.01" name="flash_price" id="flash_price" class="form-control" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="datetime-local" name="start_time" id="start_time" class="form-control" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <input type="datetime-local" name="end_time" id="end_time" class="form-control" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end mb-3">
                                <button type="submit" class="btn btn-primary w-100">Add to Sale</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Current Flash Sale Items -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Flash Price</th>
                        <th>Starts</th>
                        <th>Ends</th>
                        <?php if ($user['role'] === 'super_admin'): ?><th>Created By</th><?php endif; ?>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($flash_sale_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td>৳<?= number_format($item['flash_price']) ?></td>
                        <td><?= date('g:i a, M d', strtotime($item['start_time'])) ?></td>
                        <td><?= date('g:i a, M d', strtotime($item['end_time'])) ?></td>
                        <?php if ($user['role'] === 'super_admin'): ?><td><?= htmlspecialchars($item['created_by_name']) ?></td><?php endif; ?>
                                                <td class="d-flex gap-2">
                            <a href="/admin/flash-sale/edit/<?= $item['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <form action="/admin/flash-sale/delete" method="POST" onsubmit="return confirm('Are you sure?');" class="d-inline">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</div>
<?php require(ROOT_PATH . '/views/partials/footer.php'); ?>