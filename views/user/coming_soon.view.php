<?php require(ROOT_PATH . '/views/partials/header.php'); ?>

<div class="container py-5">
    <div class="row">
        <!-- Column 1: Dashboard Sidebar -->
        <div class="col-lg-3">
            <?php require(ROOT_PATH . '/views/partials/dashboard_sidebar.php'); ?>
        </div>

        <!-- Column 2: Main Content -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <h4><?= htmlspecialchars($pageTitle) ?></h4>
                </div>
                <div class="card-body text-center p-5">
                    <i class="bi bi-tools" style="font-size: 4rem; color: #6c757d;"></i>
                    <h3 class="mt-4">Feature Coming Soon!</h3>
                    <p class="text-muted">We are working hard to bring you this feature. Please check back later.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require(ROOT_PATH . '/views/partials/footer.php'); ?>