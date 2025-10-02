<?php require(ROOT_PATH . '/views/partials/header.php'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2"><?php require(ROOT_PATH . '/views/partials/admin_sidebar.php'); ?></div>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <h1 class="h2 pt-3 pb-2 mb-3 border-bottom">Add New Banner</h1>
            <form action="/admin/banners/store" method="POST" enctype="multipart/form-data">
                <div class="mb-3"><label class="form-label">Banner Title (for alt text)</label><input type="text" name="title" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Banner Image (Recommended size: 1200x400px)</label><input type="file" name="banner_image" class="form-control" required accept="image/jpeg,image/png,image/gif"></div>
                <div class="mb-3"><label class="form-label">Link URL (optional)</label><input type="text" name="link_url" class="form-control" placeholder="e.g., /product/my-product-slug"></div>
                <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                <button type="submit" class="btn btn-primary">Save Banner</button>
            </form>
        </main>
    </div>
</div>
<?php require(ROOT_PATH . '/views/partials/footer.php'); ?>


