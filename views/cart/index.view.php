<?php require(ROOT_PATH . '/views/partials/header.php'); ?>
<main>
<div class="container my-4">
    <h2 class="mb-4">Your Shopping Cart</h2>
    
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?= $_SESSION['flash_message']['type'] ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash_message']['text']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <div class="text-center p-5 bg-light rounded">
            <h4>Your cart is empty.</h4>
            <p>Looks like you haven't added anything to your cart yet.</p>
            <a href="/" class="btn btn-primary mt-3">Continue Shopping</a>
        </div>
    <?php else: ?>
        <form action="/cart/update" method="POST">
            <div class="row g-4">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <?php 
                            $item_count = count($cart_items); 
                            $i = 0;
                            ?>
                            <?php foreach ($cart_items as $variation_id => $item): ?>
                                <?php $i++; ?>
                                <div class="row align-items-center mb-3">
                                    <!-- Product Image -->
                                    <div class="col-md-2 col-3">
                                        <img src="/assets/images/products/<?= htmlspecialchars($item['image']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($item['name']) ?>">
                                    </div>
                                    <!-- Product Details & Variation -->
                                    <div class="col-md-4 col-9">
                                        <a href="/product/<?= htmlspecialchars($item['slug']) ?>" class="fw-bold text-dark d-block"><?= htmlspecialchars($item['name']) ?></a>
                                        <small class="text-muted"><?= htmlspecialchars($item['attributes'] ?? 'Standard') ?></small>
                                        <p class="mb-0 small">Price: ৳<?= number_format($item['price']) ?></p>
                                    </div>
                                    <!-- Quantity Input -->
                                    <div class="col-md-3 col-6 mt-2 mt-md-0">
                                        <div class="input-group" style="max-width: 130px;">
                                            <input type="number" class="form-control text-center" name="quantities[<?= $variation_id ?>]" value="<?= $item['quantity'] ?>" min="1" aria-label="Quantity">
                                        </div>
                                    </div>
                                    <!-- Total Price & Remove Button -->
                                    <div class="col-md-3 col-6 mt-2 mt-md-0 text-end">
                                        <strong class="d-block">৳<?= number_format($item['price'] * $item['quantity']) ?></strong>
                                        <a href="/cart/remove?id=<?= $variation_id ?>" class="text-danger small" title="Remove">Remove</a>
                                    </div>
                                </div>
                                <?php 
                                // [FIXED] Simple logic to show <hr> only between items
                                if ($i < $item_count) {
                                    echo '<hr>';
                                }
                                ?>
                            <?php endforeach; ?>
                        </div>
                        <div class="card-footer bg-light d-flex justify-content-between">
                            <a href="/" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Continue Shopping
                            </a>
                            <button type="submit" class="btn btn-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Update Cart
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">Order Summary</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Subtotal</span>
                                    <strong>৳<?= number_format($subtotal) ?></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0">
                                    <span>Shipping Fee</span>
                                    <span class="text-muted">Calculated at next step</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between px-0 border-top pt-3">
                                    <strong class="h5">Estimated Total</strong>
                                    <strong class="h5">৳<?= number_format($subtotal) ?></strong>
                                </li>
                            </ul>
                            <a href="/checkout" class="btn btn-primary w-100 mt-3 btn-lg">
                                Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>
</main>
<?php require(ROOT_PATH . '/views/partials/footer.php'); ?>