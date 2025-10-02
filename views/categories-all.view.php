<?php require('partials/header.php'); ?>

<main>
    <div class="container my-4">
        <h1 class="mb-4">All Categories</h1>
        
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3">
            <?php if (empty($categories)): ?>
                <div class="col-12">
                    <p class="text-center text-muted py-5">No categories available.</p>
                </div>
            <?php else: ?>
                <?php foreach ($categories as $category): ?>
                    <div class="col">
                        <a href="/category/<?= htmlspecialchars($category['slug']) ?>" class="card text-center text-decoration-none h-100">
                            <div class="card-body d-flex flex-column justify-content-center">
                                <!-- You can add category images here in the future -->
                                <!-- <img src="/assets/images/categories/<?= $category['image'] ?? 'placeholder.png' ?>" class="mb-2" style="height: 60px; object-fit: contain;"> -->
                                <h6 class="card-title mb-0"><?= htmlspecialchars($category['name']) ?></h6>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require('partials/footer.php'); ?>