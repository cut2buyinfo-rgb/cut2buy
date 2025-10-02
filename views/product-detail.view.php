<?php
// File: /views/product-detail.view.php
// Version: FINAL - Complete, interactive, and error-free.
require('partials/header.php'); 
?>

<style>
    .product-detail-page { background-color: #f7f7f7; }
    .thumb-item { cursor: pointer; margin: 0 5px; border: 2px solid #eee; padding: 2px; border-radius: 5px; transition: border-color 0.2s; }
    .thumb-item.active { border-color: #0d6efd; }
    .thumb-item img { width: 60px; height: 60px; object-fit: cover; }
    .main-image-container img { max-height: 450px; object-fit: contain; }
    .variation-group .variation-option { cursor: pointer; border: 1px solid #ddd; background-color: #fff; transition: all 0.2s; user-select: none; padding: 5px 12px; }
    .variation-group .variation-option:hover { border-color: #333; }
    .variation-group .variation-option.active { border-color: #0d6efd; background-color: #e7f1ff; color: #0d6efd; box-shadow: 0 0 5px rgba(13, 110, 253, 0.5); }
    .color-swatch { width: 24px; height: 24px; border-radius: 50%; border: 1px solid #ccc; display: inline-block; vertical-align: middle; }
    .price-old { text-decoration: line-through; }
    .stock-status { font-weight: bold; }
    .stock-status.in-stock { color: #198754; }
    .stock-status.out-of-stock { color: #dc3545; }
    .btn-add-to-cart:disabled, .btn-buy-now:disabled { cursor: not-allowed; opacity: 0.65; }
    .service-box { border: 1px solid #eee; border-radius: 8px; }
    .service-box .service-icon { font-size: 1.5rem; }
    .nav-tabs .nav-link { color: #6c757d; }
    .nav-tabs .nav-link.active { color: #000; font-weight: bold; border-color: #dee2e6 #dee2e6 #fff; }
    .qna-item, .review-item { border-bottom: 1px solid #eee; padding-bottom: 1rem; margin-bottom: 1rem; }
    .qna-item:last-child, .review-item:last-child { border-bottom: none; }
    .review-images img { width: 80px; height: 80px; object-fit: cover; border-radius: 5px; cursor: pointer; margin-right: 10px; }
    .rating-stars { color: #ffc107; }

/* রিভিউ ফর্মের স্টার রেটিং এর জন্য নতুন CSS */
.rating-stars-input {
    display: inline-block;
    direction: rtl; /* তারকাগুলোকে ডান থেকে বামে সাজায় */
    border: none;
}
.rating-stars-input input[type="radio"] {
    display: none; /* আসল রেডিও বাটন লুকিয়ে রাখা হয়েছে */
}
.rating-stars-input label {
    font-size: 1.5rem; /* স্টারের আকার */
    color: #ddd; /* নিষ্ক্রিয় স্টারের রঙ */
    cursor: pointer;
    transition: color 0.2s;
}

/* যখন মাউস একটি স্টারের উপর আনা হয় */
.rating-stars-input label:hover,
.rating-stars-input label:hover ~ label,
.rating-stars-input input[type="radio"]:checked ~ label {
    color: #ffc107; /* সক্রিয় বা হোভার করা স্টারের রঙ (হলুদ) */
}


</style>

<div class="container py-4 product-detail-page">
    <div class="bg-white p-3 p-md-4 rounded shadow-sm">
        <div class="row">
            <!-- Left Column: Image Gallery -->
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="product-gallery">
                    <div class="main-image-container mb-3 text-center">
                        <img src="<?= !empty($product_images) ? '/assets/images/products/' . htmlspecialchars($product_images[0]) : '/assets/images/placeholder.png' ?>" id="main-product-image" class="img-fluid rounded" alt="<?= htmlspecialchars($product['name']) ?>">
                    </div>
                    <?php if (count($product_images) > 1): ?>
                    <div class="thumbnail-images d-flex justify-content-center flex-wrap">
                        <?php foreach ($product_images as $image_path): ?>
                        <div class="thumb-item"><img src="/assets/images/products/<?= htmlspecialchars($image_path) ?>" alt="Product Thumbnail"></div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Middle Column: Product Information -->
            <div class="col-lg-5">
                <div class="product-info">
                    <h1 class="product-title h3 mb-2"><?= htmlspecialchars($product['name']) ?></h1>
                    <div class="product-meta mb-3 text-muted small">
                        <span>Brand: <a href="#"><?= htmlspecialchars($product['brand_name'] ?? 'N/A') ?></a></span> |
                        <span>Sold by: <a href="#"><?= htmlspecialchars($product['vendor_name'] ?? 'Admin') ?></a></span>
                    </div>
                    <div class="price-section bg-light p-3 rounded mb-3">
                        <span class="price-current h2 text-danger" id="current-price"></span>
                        <div class="price-old-wrapper d-inline-block" id="old-price-wrapper" style="display: none;">
                            <span class="price-old text-muted" id="old-price"></span>
                            <span class="discount-badge badge bg-danger ms-2" id="discount-badge"></span>
                        </div>
                    </div>
                    <form id="add-to-cart-form" action="/cart/add" method="POST">
                        <input type="hidden" name="variation_id" id="variation-id-input" value="">
                        <?php if (!empty($available_options)): ?>
                            <?php foreach ($available_options as $option_name => $values): ?>
                            <div class="variation-group mb-3">
                                <strong class="mb-2 d-block small"><?= htmlspecialchars($option_name) ?>:</strong>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($values as $value): ?>
                                    <button type="button" class="btn btn-sm variation-option" data-option-name="<?= htmlspecialchars($option_name) ?>" data-option-value="<?= htmlspecialchars($value) ?>">
                                        <?php if (strtolower($option_name) === 'color'): ?>
                                            <span class="color-swatch" style="background-color: <?= htmlspecialchars($value) ?>;" title="<?= htmlspecialchars($value) ?>"></span>
                                            <span class="ms-1"><?= htmlspecialchars($value) ?></span>
                                        <?php else: ?>
                                            <?= htmlspecialchars($value) ?>
                                        <?php endif; ?>
                                    </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="action-section d-flex align-items-center flex-wrap">
                            <div class="quantity-selector me-3 mb-2">
                                <label class="form-label d-block small">Quantity</label>
                                <div class="input-group" style="max-width: 120px;">
                                    <button class="btn btn-outline-secondary" type="button" id="btn-minus" disabled>-</button>
                                    <input type="text" class="form-control text-center" value="1" id="quantity-input" name="quantity" readonly>
                                    <button class="btn btn-outline-secondary" type="button" id="btn-plus" disabled>+</button>
                                </div>
                            </div>
                            <div id="stock-status" class="stock-status align-self-end mb-2 ms-3"></div>
                        </div>
                        <div class="buttons-wrapper mt-3 d-flex gap-2">
                            <button type="button" class="btn btn-primary btn-lg btn-buy-now" id="buy-now-btn" disabled>Buy Now</button>
                            <button type="submit" class="btn btn-danger btn-lg btn-add-to-cart" id="add-to-cart-btn" disabled>Add to Cart</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Delivery & Services (Dynamic) -->
            <div class="col-lg-3">
                <div class="service-box p-3">
                    <p class="fw-bold small text-muted mb-2">Delivery Options</p>
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-geo-alt-fill service-icon me-2 text-primary"></i>
                        <span class="small" id="delivery-location">
                            <?= htmlspecialchars(substr($user['address'] ?? 'Dhaka, Bangladesh', 0, 25)) . (strlen($user['address'] ?? '') > 25 ? '...' : '') ?>
                            <?php if ($user): ?><a href="/profile" class="ms-1 small" style="text-decoration: underline;">Change</a><?php endif; ?>
                        </span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-truck service-icon me-2 text-primary"></i>
                        <div>
                            <p class="mb-0 small fw-bold">Standard Delivery</p>
                            <p class="mb-0 small text-muted">Est. 3 - 5 days</p>
                        </div>
                        <p class="ms-auto fw-bold small">৳<?= number_format(SHIPPING_FEE, 0) ?></p>
                    </div>
                    <hr class="my-2">
                    <p class="fw-bold small text-muted mb-2">Services</p>

                   <!-- --- THIS IS THE DYNAMIC WARRANTY PART --- -->
                    <?php if (!empty($product['warranty_info'])): ?>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-shield-check service-icon me-2 text-primary"></i>
                        <span class="small"><?= htmlspecialchars($product['warranty_info']) ?></span>
                    </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Tabs for Details, Specs, Q&A, Reviews -->
    <div class="bg-white p-3 p-md-4 rounded shadow-sm mt-4">
        <ul class="nav nav-tabs" id="productTab" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#description" type="button">Description</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#specs" type="button">Specifications</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#qna" type="button">Questions (<?= count($qna) ?>)</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews" type="button">Reviews (<?= count($reviews) ?>)</button></li>
        </ul>
        <div class="tab-content pt-3" id="productTabContent">
            <div class="tab-pane fade show active" id="description" role="tabpanel"><?= nl2br(htmlspecialchars($product['description'])) ?></div>
            <div class="tab-pane fade" id="specs" role="tabpanel">
                <table class="table table-striped table-bordered">
                    <tbody>
                        <tr><th>Brand</th><td><?= htmlspecialchars($product['brand_name'] ?? 'N/A') ?></td></tr>
                        <tr><th>Category</th><td><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></td></tr>
                        <tr><th>SKU</th><td id="spec-sku">Select a variation</td></tr>
                    </tbody>
                </table>
            </div>
            
            
            
          <!-- Q&A Tab -->
            <div class="tab-pane fade" id="qna" role="tabpanel">
                <h5 class="mb-3">Questions about this product</h5>

                <?php
                // --- THIS BLOCK DISPLAYS SUCCESS/ERROR MESSAGES ---
                if (isset($_SESSION['form_success'])) {
                    echo '<div class="alert alert-success">' . $_SESSION['form_success'] . '</div>';
                    unset($_SESSION['form_success']); // Clear message after showing
                }
                if (isset($_SESSION['form_error'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['form_error'] . '</div>';
                    unset($_SESSION['form_error']); // Clear message after showing
                }
                ?>

                <?php if ($user): ?>
                    <form action="/index.php" method="POST" class="mb-4">
                        <input type="hidden" name="action" value="ask-question">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="redirect_url" value="<?= $_SERVER['REQUEST_URI'] ?>">
                        <textarea name="question" class="form-control" rows="2" placeholder="Have a question? Ask here..." required></textarea>
                        <button type="submit" class="btn btn-primary btn-sm mt-2">Ask Question</button>
                    </form>
                <?php else: ?>
                    <p><a href="/login?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">Login</a> to ask a question.</p>
                <?php endif; ?>
                <hr>
                <?php if (empty($qna)): ?>
                    <p>There are no questions yet for this product. Be the first to ask!</p>
                <?php else: ?>
                    <?php foreach ($qna as $item): ?>
                    <div class="qna-item">
                        <p class="mb-1"><strong>Q:</strong> <?= htmlspecialchars($item['question']) ?></p>
                        <small class="text-muted">by <?= htmlspecialchars($item['asker_name']) ?> - <?= date('d M Y', strtotime($item['created_at'])) ?></small>
                        
                        <?php if (!empty($item['answer'])): // Only show the answer block if an answer exists ?>
                        <div class="answer mt-2 ps-4 border-start">
                            <p class="mb-1"><strong>A:</strong> <?= htmlspecialchars($item['answer']) ?></p>
                            <small class="text-muted">by <?= htmlspecialchars($item['answerer_name']) ?> (Seller/Admin) - <?= date('d M Y', strtotime($item['answered_at'])) ?></small>
                        </div>
                        <?php else: ?>
                        <div class="answer mt-2 ps-4">
                             <p class="mb-1 text-muted"><em>Awaiting answer from seller...</em></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>



            
                          <!-- Reviews Tab -->
            <div class="tab-pane fade" id="reviews" role="tabpanel">
                <h5 class="mb-3">Customer Ratings & Reviews</h5>
                
                <?php
                // Display success/error messages from session after form submission
                if (isset($_SESSION['form_success'])) {
                    echo '<div class="alert alert-success">' . $_SESSION['form_success'] . '</div>';
                    unset($_SESSION['form_success']);
                }
                if (isset($_SESSION['form_error'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['form_error'] . '</div>';
                    unset($_SESSION['form_error']);
                }
                ?>

                <?php 
                // Check if the user is eligible to write a review.
                if (isset($can_review) && $can_review): 
                ?>
                    <div class="border rounded p-3 mb-4 bg-light">
                        <h6>Write Your Own Review</h6>
                        
                        <!-- THIS IS THE MODIFIED FORM FOR THE FALLBACK SYSTEM -->
                        <form action="/index.php" method="POST" enctype="multipart/form-data">
                            <!-- Hidden inputs for the fallback system -->
                            <input type="hidden" name="action" value="submit-review">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="redirect_url" value="<?= $_SERVER['REQUEST_URI'] ?>">
                            
                        <div class="mb-3">
    <label class="form-label d-block">Your Rating <span class="text-danger">*</span></label>
    <div class="rating-stars-input">
        <!-- ইনপুটগুলো লেবেলের আগে এবং উল্টো ক্রমে সাজানো হয়েছে -->
        <input type="radio" name="rating" id="rating5" value="5" required><label for="rating5" title="5 stars" class="bi bi-star-fill"></label>
        <input type="radio" name="rating" id="rating4" value="4"><label for="rating4" title="4 stars" class="bi bi-star-fill"></label>
        <input type="radio" name="rating" id="rating3" value="3"><label for="rating3" title="3 stars" class="bi bi-star-fill"></label>
        <input type="radio" name="rating" id="rating2" value="2"><label for="rating2" title="2 stars" class="bi bi-star-fill"></label>
        <input type="radio" name="rating" id="rating1" value="1"><label for="rating1" title="1 star" class="bi bi-star-fill"></label>
    </div>
</div>
                            
                            <div class="mb-3">
                                <label for="review_text" class="form-label">Your Review (optional):</label>
                                <textarea name="review_text" id="review_text" class="form-control" rows="3" placeholder="Share your experience with the product..."></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="review_image" class="form-label">Add a photo (optional):</label>
                                <input type="file" name="review_image" id="review_image" class="form-control form-control-sm" accept="image/jpeg,image/png">
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </form>
                    </div>
                <?php elseif ($user): ?>
                    <div class="border rounded p-3 mb-4 bg-light">
                        <p class="mb-0 small text-muted">You can write a review for this product after your order has been delivered.</p>
                    </div>
                <?php endif; ?>

                <hr>

                <?php if (empty($reviews)): ?>
                    <p>No reviews yet for this product.</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                    <div class="review-item">
                        <div class="d-flex">
                            <div class="flex-shrink-0"><i class="bi bi-person-circle fs-2 text-muted"></i></div>
                            <div class="flex-grow-1 ms-3">
                                <strong><?= htmlspecialchars($review['user_name']) ?></strong>
                                <div class="rating-stars mb-1">
                                    <?php for ($i = 1; $i <= 5; $i++): ?><i class="bi <?= $i <= $review['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i><?php endfor; ?>
                                </div>
                                <p class="mb-1"><?= nl2br(htmlspecialchars($review['review_text'])) ?></p>
                                <?php if ($review['review_image']): ?>
                                <div class="review-images mt-2">
                                    <a href="/assets/images/reviews/<?= htmlspecialchars($review['review_image']) ?>" target="_blank">
                                        <img src="/assets/images/reviews/<?= htmlspecialchars($review['review_image']) ?>" alt="Review Image">
                                    </a>
                                </div>
                                <?php endif; ?>
                                <small class="text-muted">Reviewed on: <?= date('d M Y', strtotime($review['created_at'])) ?></small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>


    <!-- Related Products Section -->
    <?php if (!empty($related_products)): ?>
    <section class="products-section mt-5">
        <div class="section-header">
            <h2 class="section-title">You Might Also Like</h2>
        </div>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
            <?php foreach ($related_products as $rel_product): ?>
                <div class="col">
                    <div class="card product-card h-100">
                        <a href="/product/<?= htmlspecialchars($rel_product['slug']) ?>">
                            <img src="/assets/images/products/<?= htmlspecialchars($rel_product['image'] ?? 'placeholder.png') ?>" class="card-img-top" alt="<?= htmlspecialchars($rel_product['name']) ?>">
                        </a>
                        <div class="card-body">
                            <h6 class="card-title"><a href="/product/<?= htmlspecialchars($rel_product['slug']) ?>"><?= htmlspecialchars($rel_product['name']) ?></a></h6>
                            <div class="price mt-2">
                                <span class="current-price">৳<?= number_format($rel_product['price']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<!-- ======================= JAVASCRIPT SECTION ======================= -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const variationsData = <?= json_encode($variations, JSON_HEX_TAG | JSON_HEX_APOS); ?>;
    const availableOptionNames = <?= json_encode(array_keys($available_options), JSON_HEX_TAG | JSON_HEX_APOS); ?>;
    
    const elements = {
        mainImage: document.getElementById('main-product-image'),
        thumbnails: document.querySelectorAll('.thumb-item img'),
        optionButtons: document.querySelectorAll('.variation-option'),
        currentPrice: document.getElementById('current-price'),
        oldPriceWrapper: document.getElementById('old-price-wrapper'),
        oldPrice: document.getElementById('old-price'),
        discountBadge: document.getElementById('discount-badge'),
        stockStatus: document.getElementById('stock-status'),
        buyNowBtn: document.getElementById('buy-now-btn'),
        addToCartBtn: document.getElementById('add-to-cart-btn'),
        variationIdInput: document.getElementById('variation-id-input'),
        qtyInput: document.getElementById('quantity-input'),
        btnPlus: document.getElementById('btn-plus'),
        btnMinus: document.getElementById('btn-minus'),
        specSku: document.getElementById('spec-sku'),
        mainForm: document.getElementById('add-to-cart-form')
    };
    
    let selectedOptions = {};

    function findMatchingVariation() {
        if (Object.keys(selectedOptions).length !== availableOptionNames.length) return null;
        return variationsData.find(v => 
            availableOptionNames.every(key => v.attributes[key] === selectedOptions[key])
        );
    }

    function updateUI(variation) {
        if (variation) {
            elements.currentPrice.textContent = '৳' + parseFloat(variation.price).toLocaleString('en-IN');
            if (variation.old_price && parseFloat(variation.old_price) > parseFloat(variation.price)) {
                elements.oldPrice.textContent = '৳' + parseFloat(variation.old_price).toLocaleString('en-IN');
                const discount = Math.round(100 - (variation.price / variation.old_price) * 100);
                elements.discountBadge.textContent = `-${discount}%`;
                elements.oldPriceWrapper.style.display = 'inline-block';
            } else {
                elements.oldPriceWrapper.style.display = 'none';
            }
            elements.specSku.textContent = variation.sku || 'N/A';
            
            if (variation.stock > 0) {
                elements.stockStatus.textContent = `In Stock (${variation.stock})`;
                elements.stockStatus.className = 'stock-status in-stock';
                elements.addToCartBtn.disabled = false;
                elements.buyNowBtn.disabled = false;
                elements.btnPlus.disabled = false;
                elements.btnMinus.disabled = false;
                elements.qtyInput.dataset.max = variation.stock;
                if (parseInt(elements.qtyInput.value) > variation.stock) elements.qtyInput.value = variation.stock;
            } else {
                elements.stockStatus.textContent = 'Out of Stock';
                elements.stockStatus.className = 'stock-status out-of-stock';
                elements.addToCartBtn.disabled = true;
                elements.buyNowBtn.disabled = true;
            }
            elements.variationIdInput.value = variation.id;
        } else {
            resetToDefaultState();
        }
    }

    function resetToDefaultState() {
        const defaultPriceText = variationsData.length > 0 ? 'Select options to see price' : 'Currently Unavailable';
        elements.currentPrice.textContent = defaultPriceText;
        elements.oldPriceWrapper.style.display = 'none';
        elements.specSku.textContent = 'Select a variation';
        elements.stockStatus.textContent = '';
        elements.addToCartBtn.disabled = true;
        elements.buyNowBtn.disabled = true;
        elements.btnPlus.disabled = true;
        elements.btnMinus.disabled = true;
        elements.variationIdInput.value = '';
    }

    elements.optionButtons.forEach(button => {
        button.addEventListener('click', () => {
            const { optionName, optionValue } = button.dataset;
            const isActive = button.classList.contains('active');
            document.querySelectorAll(`.variation-option[data-option-name="${optionName}"]`).forEach(btn => btn.classList.remove('active'));
            
            if (!isActive) {
                button.classList.add('active');
                selectedOptions[optionName] = optionValue;
            } else {
                delete selectedOptions[optionName];
            }
            updateUI(findMatchingVariation());
        });
    });

    elements.thumbnails.forEach((thumb, index) => {
        thumb.addEventListener('click', function() {
            elements.mainImage.src = this.src;
            document.querySelectorAll('.thumb-item').forEach(item => item.classList.remove('active'));
            this.parentElement.classList.add('active');
        });
        if (index === 0) thumb.parentElement.classList.add('active');
    });

    elements.btnPlus.addEventListener('click', () => {
        let max = parseInt(elements.qtyInput.dataset.max) || 1;
        let current = parseInt(elements.qtyInput.value);
        if (current < max) elements.qtyInput.value = current + 1;
    });
    
    elements.btnMinus.addEventListener('click', () => {
        let current = parseInt(elements.qtyInput.value);
        if (current > 1) elements.qtyInput.value = current - 1;
    });

    elements.buyNowBtn.addEventListener('click', function(event) {
        event.preventDefault();
        elements.mainForm.action = '/cart/add-and-checkout';
        elements.mainForm.submit();
    });

    // Initial state setup
    resetToDefaultState();
    if (variationsData.length === 1) {
        elements.optionButtons.forEach(btn => btn.click());
    }
});
</script>

<?php require('partials/footer.php'); ?>