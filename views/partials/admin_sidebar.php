<?php
// File: /views/partials/admin_sidebar.php
// Version: FINAL - Added Flash Sale Management Link.
?>
<nav id="sidebarMenu" class="d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
            <span>Store Management</span>
        </h6>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= ($pageTitle === 'Admin Dashboard') ? 'active' : '' ?>" href="/admin/dashboard">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (strpos($pageTitle, 'Product') !== false) ? 'active' : '' ?>" href="/admin/products">
                    <i class="bi bi-box-seam me-2"></i>Manage Products
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (strpos($pageTitle, 'Order') !== false) ? 'active' : '' ?>" href="/admin/orders">
                    <i class="bi bi-receipt me-2"></i>Manage Orders
                </a>
            </li>
            <!-- === NEW LINK ADDED HERE === -->
            <li class="nav-item">
                <a class="nav-link <?= (strpos($pageTitle, 'Flash Sale') !== false) ? 'active' : '' ?>" href="/admin/flash-sale">
                    <i class="bi bi-lightning-charge-fill me-2"></i>Manage Flash Sale
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
            <span>Site Content</span>
        </h6>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= ($pageTitle === 'Manage Brands') ? 'active' : '' ?>" href="/admin/brands">
                    <i class="bi bi-tag-fill me-2"></i>Manage Brands
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= ($pageTitle === 'Manage Categories') ? 'active' : '' ?>" href="/admin/categories">
                    <i class="bi bi-grid-fill me-2"></i>Manage Categories
                </a>
            </li>

             <li class="nav-item">
                <a class="nav-link <?= (strpos($pageTitle, 'Banner') !== false) ? 'active' : '' ?>" href="/admin/banners">
                    <i class="bi bi-images me-2"></i>Manage Banners
                </a>
            </li>
             <li class="nav-item">
                <a class="nav-link <?= ($pageTitle === 'Manage Q&A') ? 'active' : '' ?>" href="/admin/qna">
                    <i class="bi bi-patch-question-fill me-2"></i>Manage Q&A
                </a>
            </li>
        </ul>
        
        <?php if (isset($user) && $user['role'] === 'super_admin'): ?>
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted text-uppercase">
            <span>Administration</span>
        </h6>
        <ul class="nav flex-column">
             <li class="nav-item">
                <a class="nav-link <?= ($pageTitle === 'Manage Users') ? 'active' : '' ?>" href="/admin/users">
                    <i class="bi bi-people me-2"></i>Manage Users
                </a>
            </li>
        </ul>
        <?php endif; ?>

        <hr>

        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link text-danger" href="/logout">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
            </li>
        </ul>
    </div>
</nav>