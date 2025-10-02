<?php require(ROOT_PATH . '/views/partials/header.php'); ?>

<div class="container my-4">
    <div class="row">
        <!-- User Dashboard Sidebar -->
        <div class="col-md-3">
            <?php require(ROOT_PATH . '/views/partials/dashboard_sidebar.php'); ?>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">My Reviews</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($reviews)): ?>
                        <div class="text-center p-4">
                            <p class="text-muted">You haven't written any reviews yet.</p>
                            <a href="/" class="btn btn-primary">Continue Shopping</a>
                        </div>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($reviews as $review): ?>
                                <li class="list-group-item p-3">
                                    <div class="d-flex flex-column flex-sm-row">
                                        <a href="/product/<?= htmlspecialchars($review['product_slug']) ?>">
                                            <img src="/assets/images/products/<?= htmlspecialchars($review['product_image'] ?? 'placeholder.png') ?>" 
                                                 alt="<?= htmlspecialchars($review['product_name']) ?>" 
                                                 style="width: 80px; height: 80px; object-fit: cover;" 
                                                 class="rounded me-sm-3 mb-2 mb-sm-0">
                                        </a>
                                        <div class="flex-grow-1">
                                            <a href="/product/<?= htmlspecialchars($review['product_slug']) ?>" class="fw-bold text-dark text-decoration-none">
                                                <?= htmlspecialchars($review['product_name']) ?>
                                            </a>
                                            <div class="my-1">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="bi <?= $i <= $review['rating'] ? 'bi-star-fill text-warning' : 'bi-star text-muted' ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                            
                                            <!-- Using the correct column name 'review_text' -->
                                            <?php if (!empty($review['review_text'])): ?>
                                                <p class="mb-1 fst-italic">"<?= nl2br(htmlspecialchars($review['review_text'])) ?>"</p>
                                            <?php endif; ?>

                                            <!-- Displaying the review image if it exists -->
                                            <?php if (!empty($review['review_image'])): ?>
                                                <a href="/assets/images/reviews/<?= htmlspecialchars($review['review_image']) ?>" target="_blank">
                                                    <img src="/assets/images/reviews/<?= htmlspecialchars($review['review_image']) ?>" 
                                                         alt="Review image" 
                                                         style="max-width: 100px; max-height: 100px; object-fit: cover; border-radius: 5px;" 
                                                         class="mt-2">
                                                </a>
                                            <?php endif; ?>

                                            <small class="text-muted d-block mt-2">Reviewed on: <?= date('d M, Y', strtotime($review['created_at'])) ?></small>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require(ROOT_PATH . '/views/partials/footer.php'); ?>