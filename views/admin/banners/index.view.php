<?php require(ROOT_PATH . '/views/partials/header.php'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2"><?php require(ROOT_PATH . '/views/partials/admin_sidebar.php'); ?></div>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Banners</h1>
                <a href="/admin/banners/create" class="btn btn-primary btn-sm">Add New Banner</a>
            </div>
            <table class="table table-striped">
                <thead><tr><th>Preview</th><th>Title</th><th>Link URL</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($banners as $banner): ?>
                    <tr>
                        <td><img src="/assets/images/banners/<?= htmlspecialchars($banner['image_path']) ?>" alt="" style="height: 40px;"></td>
                        <td><?= htmlspecialchars($banner['title']) ?></td>
<td><?= htmlspecialchars($banner['link_url']) ?></td>
                        <td><span class="badge bg-<?= $banner['status'] === 'active' ? 'success' : 'secondary' ?>"><?= ucfirst($banner['status']) ?></span></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-secondary disabled">Edit</a>
                            <form action="/admin/banners/delete" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="banner_id" value="<?= $banner['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
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