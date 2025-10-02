<?php require('partials/header.php'); ?>

<main>
    <div class="container my-4">
  <nav aria-label="breadcrumb">



 

    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Home</a></li>
        <li class="breadcrumb-item"><a href="/categories">Categories</a></li>
        <li class="breadcrumb-item active" aria-current="page">
            <?= isset($category['name']) ? htmlspecialchars($category['name']) : 'Unknown Category' ?>
        </li>
    </ol>
</nav>


        <h1 class="mb-4"><?= htmlspecialchars($category['name']) ?></h1>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
            <?php if (empty($products)): ?>
                <div class="col-12">
                    <p class="text-center text-muted py-5">No products found in this category yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="card product-card h-100">
                             <a href="/product/<?= htmlspecialchars($product['slug']) ?>">
                                <img src="/assets/images/products/<?= htmlspecialchars($product['image'] ?? 'placeholder.png') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <?php $in_wishlist = isset($_SESSION['wishlist'][$product['id']]); ?>
                                <a href="/wishlist/add?id=<?= $product['id'] ?>" class="wishlist-btn" title="Add to Wishlist">
                                    <i class="bi <?= $in_wishlist ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i>
                                </a>
                                <h6 class="card-title flex-grow-1"><a href="/product/<?= htmlspecialchars($product['slug']) ?>"><?= htmlspecialchars($product['name']) ?></a></h6>
                                <div class="price mt-2">
                                    <span class="text-primary fw-bold">৳<?= htmlspecialchars(number_format($product['price'])) ?></span>
                                    <?php if (!empty($product['old_price']) && $product['old_price'] > $product['price']): ?>
                                        <small class="text-muted text-decoration-line-through">৳<?= htmlspecialchars(number_format($product['old_price'])) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-footer border-0 bg-transparent pt-0 pb-3">
                                <a href="/cart/add?id=<?= $product['id'] ?>" class="btn btn-primary w-100 btn-sm">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require('partials/footer.php'); ?>