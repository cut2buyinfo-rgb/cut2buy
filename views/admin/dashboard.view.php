<?php require __DIR__ . '/../partials/header.php'; // Go one directory up to find partials ?>

<div class="dashboard-container">
    <div class="row">
        <!-- Column 1: ADMIN Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="dashboard-sidebar card">
                
                <div class="list-group list-group-flush">

    <!-- Sidebar -->
     
            <?php require(ROOT_PATH . '/views/partials/admin_sidebar.php'); ?>
      


                </div>
            </div>
        </div>

        <!-- Column 2: Main Admin Content -->
        <div class="col-lg-9">
            <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
            <p>This is the central place to manage your e-commerce site.</p>
            <div class="row">
                <div class="col-md-6"><div class="card text-center p-3"><h3 class="display-6"><?= $total_users ?></h3><p>Total Users</p></div></div>
                <div class="col-md-6"><div class="card text-center p-3"><h3 class="display-6"><?= $total_orders ?></h3><p>Total Orders</p></div></div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>