<?php require('partials/header.php'); ?>

<main>
    <div class="container my-4">
        <div class="text-center mb-4">
            <h1 class="display-4 fw-bold text-danger">Flash Sale</h1>
            <p class="lead">Hurry up! These offers are for a limited time only.</p>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
            <?php if (empty($sale_products)): ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No products are currently on flash sale. Check back soon!</p>
                </div>
            <?php else: ?>
                <?php foreach ($sale_products as $product): ?>
                    <div class="col">
                        <div class="card product-card h-100">
                             <a href="/product/<?= htmlspecialchars($product['slug']) ?>">
                                <img src="/assets/images/products/<?= htmlspecialchars($product['image'] ?? 'placeholder.png') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title flex-grow-1"><a href="/product/<?= htmlspecialchars($product['slug']) ?>"><?= htmlspecialchars($product['name']) ?></a></h6>
                                <div class="price mt-2">
                                    <span class="text-danger fw-bold fs-5">৳<?= number_format($product['flash_price']) ?></span>
                                    
                                    <!-- === THIS IS THE FIX === -->
                                    <?php if (!empty($product['old_price']) && $product['old_price'] > $product['flash_price']): ?>
                                        <small class="text-muted text-decoration-line-through">৳<?= number_format($product['old_price']) ?></small>
                                    <?php endif; ?>
                                    
                                </div>
                                <div class="text-center text-muted small mt-2" data-countdown="<?= $product['end_time'] ?>">
                                    <!-- Countdown timer will be inserted here by JS -->
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Simple Countdown Timer Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const countdownElements = document.querySelectorAll('[data-countdown]');
    countdownElements.forEach(el => {
        const endTime = new Date(el.dataset.countdown.replace(' ', 'T')).getTime();
        
        const interval = setInterval(function() {
            const now = new Date().getTime();
            const distance = endTime - now;
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            el.innerHTML = `Ends in: ${days}d ${hours}h ${minutes}m ${seconds}s`;
            
            if (distance < 0) {
                clearInterval(interval);
                el.innerHTML = "Sale Ended";
            }
        }, 1000);
    });
});
</script>

<?php require('partials/footer.php'); ?>