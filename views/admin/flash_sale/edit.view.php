<?php
// File: /views/admin/flash_sale/edit.view.php
require(ROOT_PATH . '/views/partials/header.php'); 
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2">
            <?php require(ROOT_PATH . '/views/partials/admin_sidebar.php'); ?>
        </div>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Edit Flash Sale Item</h1>
                <a href="/admin/flash-sale" class="btn btn-secondary btn-sm">Back to List</a>
            </div>

            <div class="card">
                <div class="card-header">Editing: <?= htmlspecialchars($sale_item['product_name']) ?></div>
                <div class="card-body">
                    <form action="/admin/flash-sale/update" method="POST">
                        <input type="hidden" name="id" value="<?= $sale_item['id'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Product</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($sale_item['product_name']) ?>" disabled>
                            <div class="form-text">The product cannot be changed. To change the product, delete this entry and create a new one.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="flash_price" class="form-label">Flash Price (৳)</label>
                                <input type="number" step="0.01" name="flash_price" id="flash_price" class="form-control" value="<?= htmlspecialchars($sale_item['flash_price']) ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="datetime-local" name="start_time" id="start_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($sale_item['start_time'])) ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <input type="datetime-local" name="end_time" id="end_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($sale_item['end_time'])) ?>" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Sale Item</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require(ROOT_PATH . '/views/partials/footer.php'); ?>