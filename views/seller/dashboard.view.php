<?php require __DIR__ . '/../partials/header.php'; ?>

<div class="dashboard-container">
    <div class="row">
        <!-- Column 1: SELLER Sidebar Navigation -->
        <div class="col-lg-3">
            <div class="dashboard-sidebar card">
                <div class="card-header bg-info text-white">
                    Seller Panel
                </div>
                <div class="list-group list-group-flush">
                    <a href="/seller/dashboard" class="list-group-item list-group-item-action active">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a href="/seller/products" class="list-group-item list-group-item-action">
                        <i class="bi bi-box-seam me-2"></i>My Products
                    </a>
                    <a href="/seller/orders" class="list-group-item list-group-item-action">
                        <i class="bi bi-receipt me-2"></i>My Orders
                    </a>
                    <a href="/seller/profile" class="list-group-item list-group-item-action">
                        <i class="bi bi-shop me-2"></i>Store Profile
                    </a>
                    <a href="/logout" class="list-group-item list-group-item-action text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Column 2: Main Seller Content -->
        <div class="col-lg-9">
            <h1>Seller Dashboard</h1>
            <p>Manage your products and orders from here.</p>
             <div class="row">
                <div class="col-md-6"><div class="card text-center p-3"><h3 class="display-6"><?= $my_products_count ?></h3><p>Your Products</p></div></div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>