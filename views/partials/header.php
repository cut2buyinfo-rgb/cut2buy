<?php
// File: /views/partials/header.php
// Version: ULTIMATE FINAL & COMPLETE (With Full Admin Links in Header)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="google-site-verification" content="fZdQFBdaahTRUF9aLtdCCKDgk353W0fIuYuxnNh93jg" />

<meta name="description" content="<?= isset($metaDescription) ? htmlspecialchars($metaDescription) : 'Cut2Buy is your trusted online shop in Bangladesh for quality products, great deals, and fast delivery.' ?>">

    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - Cut2Buy' : 'Cut2Buy' ?></title>
    
    <!-- CSS Files -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
<link rel="stylesheet" href="/assets/css/style-v2.css?v=<?= filemtime(ROOT_PATH . '/assets/css/style-v2.css') ?>">

    <link rel="icon" href="/assets/images/favicon.png" type="image/png">

<link rel="canonical" href="<?= isset($canonicalUrl) ? htmlspecialchars($canonicalUrl) : 'https://cut2buy.unaux.com' . htmlspecialchars($_SERVER['REQUEST_URI']) ?>">


    <style>
    .header-desktop {
        top: 0;
        z-index: 1030;
    }
    .desktop-category-nav {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .all-categories-dropdown .dropdown-toggle {
        font-weight: 600;
        color: var(--text-dark);
    }
    .all-categories-dropdown .dropdown-menu {
        width: 250px;
        max-height: 75vh;
        overflow-y: auto;
        border-radius: var(--border-radius);
        border: 1px solid var(--border-color);
        box-shadow: var(--box-shadow);
    }
     .all-categories-dropdown .dropdown-item {
        padding: 0.5rem 1rem;
        transition: background-color 0.2s ease, color 0.2s ease;
     }
     .all-categories-dropdown .dropdown-item:hover {
        background-color: var(--brand-primary-color);
        color: white;
     }
</style>
</head>
<body>

<!-- ======================= MOBILE HEADER ======================== -->
<header class="d-lg-none sticky-top header-section">
    <div class="header-mobile">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="/"><img src="/assets/images/logo-white.webp" alt="Cut2Buy Logo" class="cut2buy-logo"></a>
            <div class="d-flex align-items-center">
                <a href="/cart" class="icon-link position-relative"><i class="bi bi-cart3"></i><?php if ($cartItemCount > 0): ?><span class="badge header-badge rounded-pill"><?= $cartItemCount ?></span><?php endif; ?></a>
                <a href="/wishlist" class="icon-link position-relative mx-2"><i class="bi bi-heart"></i><?php if ($wishlistItemCount > 0): ?><span class="badge header-badge rounded-pill"><?= $wishlistItemCount ?></span><?php endif; ?></a>
                <a class="icon-link" data-bs-toggle="offcanvas" href="#mobileAccountMenu"><i class="bi <?= $user ? 'bi-person-check-fill' : 'bi-person' ?>"></i></a>
            </div>
        </div>
    </div>
    <div class="mobile-search-bar">
        <div class="container">
          <form action="/search" method="get" id="mobileSearchForm">
               <div class="input-group">
                   <span class="input-group-text camera-icon" style="cursor: pointer;"><i class="bi bi-camera"></i></span>
                   <input type="file" name="search_image" class="d-none image-search-input" accept="image/*">
                   <input type="search" name="q" class="form-control" placeholder="Search...">
                   <button class="btn btn-search px-3" type="submit"><i class="bi bi-search"></i></button>
               </div>
          </form>
        </div>
    </div>
</header>

<!-- ======================= DESKTOP HEADER ======================= -->
<header class="d-none d-lg-block sticky-top header-section">
    <!-- Top Bar -->
    <div class="header-desktop-top bg-white py-3 border-bottom">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="/"><img src="/assets/images/logo-color.webp" alt="Cut2Buy Logo" style="height:45px;"></a>
                <div class="w-50">
                  <form action="/search" method="get" id="desktopSearchForm">
                    <div class="input-group">
                        <span class="input-group-text camera-icon" style="cursor: pointer;"><i class="bi bi-camera"></i></span>
                        <input type="file" name="search_image" class="d-none image-search-input" accept="image/*">
                        <input type="search" name="q" class="form-control" placeholder="Search by Product Link or Keyword...">
                        <button class="btn btn-search px-4" type="submit"><i class="bi bi-search me-1"></i> Search</button>
                    </div>
                  </form>
                </div>
                <div class="d-flex align-items-center">
                    <a href="/wishlist" class="header-icon-link me-4 position-relative">
                        <i class="bi bi-heart m-2"></i><span>Wishlist</span>
                        <?php if ($wishlistItemCount > 0): ?><span class="badge header-badge-desktop rounded-pill bg-danger"><?= $wishlistItemCount ?></span><?php endif; ?>
                    </a>
                    <a href="/cart" class="header-icon-link me-4 position-relative">
                        <i class="bi bi-cart3 m-2"></i><span>Cart</span>
                        <?php if ($cartItemCount > 0): ?><span class="badge header-badge-desktop rounded-pill bg-danger"><?= $cartItemCount ?></span><?php endif; ?>
                    </a>
                    <div class="dropdown">
                        <a href="#" class="header-icon-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                            <span><?= $user ? htmlspecialchars($user['name']) : 'Account'; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg">
                            <?php if ($user): ?>
                                <?php switch ($user['role']):
                                    case 'super_admin':
                                    case 'admin': ?>
                                        <!-- === START: ADDED FULL ADMIN LINKS FOR DESKTOP === -->
                                        <li><h6 class="dropdown-header">Store Management</h6></li>
                                        <li><a class="dropdown-item" href="/admin/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                                        <li><a class="dropdown-item" href="/admin/products"><i class="bi bi-box-seam me-2"></i>Products</a></li>
                                        <li><a class="dropdown-item" href="/admin/orders"><i class="bi bi-receipt me-2"></i>Orders</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><h6 class="dropdown-header">Site Content</h6></li>
                                        <li><a class="dropdown-item" href="/admin/categories"><i class="bi bi-grid-fill me-2"></i>Categories</a></li>
                                        <li><a class="dropdown-item" href="/admin/brands"><i class="bi bi-tag-fill me-2"></i>Brands</a></li>
                                        <li><a class="dropdown-item" href="/admin/banners"><i class="bi bi-images me-2"></i>Banners</a></li>
                                        <li><a class="dropdown-item" href="/admin/qna"><i class="bi bi-patch-question-fill me-2"></i>Q&A</a></li>
                                        
                                        <?php if ($user['role'] === 'super_admin'): ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><h6 class="dropdown-header">Administration</h6></li>
                                            <li><a class="dropdown-item" href="/admin/users"><i class="bi bi-people me-2"></i>Users</a></li>
                                        <?php endif; ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <!-- === END: ADDED FULL ADMIN LINKS FOR DESKTOP === -->
                                        <?php break; ?>
                                    <?php case 'saller': ?>
                                        <li><a class="dropdown-item" href="/seller/dashboard">Seller Dashboard</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <?php break; ?>
                                    <?php default: ?>
                                        <li><a class="dropdown-item" href="/dashboard">My Dashboard</a></li>
                                        <li><a class="dropdown-item" href="/orders">My Orders</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                <?php endswitch; ?>
                                <li><a class="dropdown-item text-danger" href="/logout">Logout</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="/login">Login</a></li>
                                <li><a class="dropdown-item" href="/register">Register</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Bar with Category Navigation -->
    <nav class="desktop-category-nav py-2 bg-white border-bottom">
        <div class="container">
            <div class="d-flex align-items-center">
                <div class="dropdown all-categories-dropdown">
                    <a class="nav-link dropdown-toggle fw-bold" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-list me-2"></i>All Categories
                    </a>
                    <?php if (!empty($global_categories)): ?>
    <ul class="dropdown-menu shadow-lg">
        <?php foreach ($global_categories as $nav_cat): ?>
            <li><a class="dropdown-item" href="/category/<?= htmlspecialchars($nav_cat['slug']) ?>"><?= htmlspecialchars($nav_cat['name']) ?></a></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
                </div>
                <div class="ms-4 d-flex gap-4">
                    <a class="nav-link" href="/">Home</a>
                    <a class="nav-link" href="/products">All Products</a>
                    <a class="nav-link text-danger fw-bold" href="/campaigns/flash-sale">Flash Sale</a>
                </div>
            </div>
        </div>
    </nav>
</header>

<!-- ======================= MOBILE OFFCANVAS MENU ======================= -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="mobileAccountMenu" aria-labelledby="mobileAccountMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileAccountMenuLabel">
            <i class="bi bi-person-circle me-2"></i>
            <span><?= $user ? htmlspecialchars($user['name']) : 'My Account'; ?></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="list-group list-group-flush">
            <?php if ($user): ?>
                <?php switch ($user['role']):
                    case 'super_admin':
                    case 'admin': ?>
                        <!-- === START: ADDED FULL ADMIN LINKS FOR MOBILE === -->
                        <li class="list-group-item disabled text-muted">Store Management</li>
                        <a href="/admin/dashboard" class="list-group-item list-group-item-action"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                        <a href="/admin/products" class="list-group-item list-group-item-action"><i class="bi bi-box-seam me-2"></i>Products</a>
                        <a href="/admin/orders" class="list-group-item list-group-item-action"><i class="bi bi-receipt me-2"></i>Orders</a>
                        
                        <li class="list-group-item disabled text-muted mt-3">Site Content</li>
                        <a href="/admin/categories" class="list-group-item list-group-item-action"><i class="bi bi-grid-fill me-2"></i>Categories</a>
                        <a href="/admin/brands" class="list-group-item list-group-item-action"><i class="bi bi-tag-fill me-2"></i>Brands</a>
                        <a href="/admin/banners" class="list-group-item list-group-item-action"><i class="bi bi-images me-2"></i>Banners</a>
                        <a href="/admin/qna" class="list-group-item list-group-item-action"><i class="bi bi-patch-question-fill me-2"></i>Q&A</a>

                        <?php if ($user['role'] === 'super_admin'): ?>
                            <li class="list-group-item disabled text-muted mt-3">Administration</li>
                            <a href="/admin/users" class="list-group-item list-group-item-action"><i class="bi bi-people me-2"></i>Users</a>
                        <?php endif; ?>
                        <!-- === END: ADDED FULL ADMIN LINKS FOR MOBILE === -->
                        <?php break; ?>
                    <?php default: ?>
                        <a href="/dashboard" class="list-group-item list-group-item-action">My Dashboard</a>
                        <a href="/orders" class="list-group-item list-group-item-action">My Orders</a>
                        <a href="/profile" class="list-group-item list-group-item-action">My Profile</a>
                <?php endswitch; ?>
                <a href="/logout" class="list-group-item list-group-item-action text-danger mt-3">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
            <?php else: ?>
                <a href="/login" class="list-group-item list-group-item-action"><i class="bi bi-box-arrow-in-right me-2"></i>Login</a>
                <a href="/register" class="list-group-item list-group-item-action"><i class="bi bi-person-plus-fill me-2"></i>Register</a>
            <?php endif; ?>
        </ul>
    </div>
</div>
<!-- This will display flash messages from any controller -->
<?php if (isset($_SESSION['flash_message'])): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= $_SESSION['flash_message']['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash_message']['text']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <?php unset($_SESSION['flash_message']); ?>
<?php endif; ?>

<main class="container my-3 my-lg-4">