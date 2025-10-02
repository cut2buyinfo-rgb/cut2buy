<?php
// File: /views/search-results.view.php
// Version: FINAL (Handles All Products, Text Search, and Image Search)

require('partials/header.php');
?>
<main>
<div class="container my-4">
<div class="row">
<div class="col-12">

<!-- Heading Logic for All Scenarios -->
        <?php if ($pageTitle === 'All Products'): ?>
            <h1 class="mb-4">All Products</h1>
            <p class="text-muted"><?= count($search_results) ?> products available.</p>
        <?php elseif (!empty($sanitized_query)): ?>
            <h1 class="mb-4">Search Results for "<?= $sanitized_query ?>"</h1>
            <p class="text-muted"><?= count($search_results) ?> products found.</p>
        <?php else: ?>
             <h1 class="mb-4">Search Products</h1>
             <p class="text-muted">Enter a keyword or click the camera icon to search by image.</p>
        <?php endif; ?>

    </div>
</div>

<!-- Filter and Sort Bar (Show for both All Products and Search Results) -->
<?php if ($pageTitle === 'All Products' || !empty($sanitized_query)): ?>
<div class="card card-body mb-4">
    <form action="/search" method="GET">
        <!-- Hidden input for search query to persist it during filtering -->
        <input type="hidden" name="q" value="<?= $sanitized_query ?>">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <div>
                <label for="sort" class="form-label small mb-1">Sort By:</label>
                <select name="sort" id="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                    <?php if(!empty($sanitized_query)): // Show relevance only for actual searches ?>
                    <option value="relevance" <?= ($sort_order === 'relevance') ? 'selected' : '' ?>>Best Match</option>
                    <?php endif; ?>
                    <option value="newest" <?= ($sort_order === 'newest') ? 'selected' : '' ?>>Newest Arrivals</option>
                    <option value="price_asc" <?= ($sort_order === 'price_asc') ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= ($sort_order === 'price_desc') ? 'selected' : '' ?>>Price: High to Low</option>
                </select>
            </div>
            <div class="d-flex align-items-end gap-2">
                <div>
                    <label for="min_price" class="form-label small mb-1">Price Range:</label>
                    <input type="number" name="min_price" id="min_price" class="form-control form-control-sm" placeholder="Min" value="<?= htmlspecialchars($min_price ?? '') ?>" style="width: 100px;">
                </div>
                <span>-</span>
                <div>
                    <input type="number" name="max_price" id="max_price" class="form-control form-control-sm" placeholder="Max" value="<?= htmlspecialchars($max_price ?? '') ?>" style="width: 100px;">
                </div>
            </div>
            <div class="ms-auto">
                 <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
                 <a href="<?= !empty($sanitized_query) ? '/search?q=' . $sanitized_query : '/products' ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- Product Grid -->
<div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
    <?php if (!empty($search_results)): ?>
        <?php foreach ($search_results as $product): ?>
            <div class="col">
                <div class="card product-card h-100">
                     <a href="/product/<?= htmlspecialchars($product['slug']) ?>">
                        <img src="/assets/images/products/<?= htmlspecialchars($product['image'] ?? 'placeholder.png') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title flex-grow-1"><a href="/product/<?= htmlspecialchars($product['slug']) ?>"><?= htmlspecialchars($product['name']) ?></a></h6>
                        <div class="price mt-2">
                            <span class="text-primary fw-bold">৳<?= number_format($product['price'] ?? 0) ?></span>
                        </div>
                    </div>
                    <div class="card-footer border-0 bg-transparent pt-0 pb-3">
                        <a href="/product/<?= htmlspecialchars($product['slug']) ?>" class="btn btn-primary w-100 btn-sm">View Options</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php elseif (!empty($sanitized_query)): ?>
        <div class="col-12 text-center py-5 bg-light rounded">
            <h4>No products found</h4>
            <p class="text-muted">We couldn't find any products matching your search "<?= $sanitized_query ?>".<br>Try adjusting your filters or using a different keyword.</p>
             <a href="/search?q=<?= $sanitized_query ?>" class="btn btn-outline-secondary btn-sm mt-2">Reset Filters</a>
        </div>
    <?php endif; ?>
</div>
</div>
</main>
<?php require('partials/footer.php'); ?>