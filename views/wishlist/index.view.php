<?php require(ROOT_PATH . '/views/partials/header.php'); ?>
<main>
<div class="container my-5">
    <h2 class="mb-4">My Wishlist</h2>
    
    <?php if (empty($wishlist_items)): ?>
        <div class="text-center p-5 bg-light rounded">
            <h4>Your wishlist is empty.</h4>
            <p>Add items that you like to your wishlist. Review them anytime and easily move them to the cart.</p>
            <a href="/" class="btn btn-primary mt-3">Discover Products</a>
        </div>
    <?php else: ?>
        <div id="wishlist-product-grid" class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
            <?php foreach ($wishlist_items as $product): ?>
                <div class="col">
                    <div class="card product-card h-100">
                        <a href="/product/<?= htmlspecialchars($product['slug']) ?>">
                            <img src="/assets/images/products/<?= htmlspecialchars($product['image'] ?? 'placeholder.png') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title flex-grow-1"><a href="/product/<?= htmlspecialchars($product['slug']) ?>"><?= htmlspecialchars($product['name']) ?></a></h6>
                            <div class="price mt-2">
                                <span class="text-primary fw-bold">From ৳<?= number_format($product['price']) ?></span>
                            </div>
                        </div>
                        <div class="card-footer p-2 border-0 bg-transparent">
                            <button type="button" class="btn btn-primary w-100 btn-sm mb-1 move-to-cart-btn" data-product-id="<?= $product['id'] ?>">
                                Move to Cart
                            </button>
                            <a href="/wishlist/add?id=<?= $product['id'] ?>" class="btn btn-outline-danger w-100 btn-sm">Remove</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Quick View Modal (This is the pop-up for selecting variations) -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="quickViewModalLabel">Select Options</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="modal-loader" class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>
        <form id="quick-view-form" method="POST" style="display: none;">
            <input type="hidden" name="variation_id" id="modal-variation-id-input">
            <div id="modal-variations-container">
                <!-- Variation options will be loaded here by JavaScript -->
            </div>
            <div class="mt-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" class="form-control" value="1" min="1">
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="modal-add-to-cart-btn" disabled>Move to Cart</button>
      </div>
    </div>
  </div>
</div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- PART 1: Define all necessary variables for the modal ---
    const quickViewModal = new bootstrap.Modal(document.getElementById('quickViewModal'));
    const modalLoader = document.getElementById('modal-loader');
    const quickViewForm = document.getElementById('quick-view-form');
    const modalVariationsContainer = document.getElementById('modal-variations-container');
    const modalVariationIdInput = document.getElementById('modal-variation-id-input');
    const modalAddToCartBtn = document.getElementById('modal-add-to-cart-btn');
    const wishlistGrid = document.getElementById('wishlist-product-grid');

    let modalVariationsData = {};
    let modalSelectedOptions = {};
    let currentProductId = null;

    // --- PART 2: Function to open the modal and fetch product variations ---
    async function openQuickView(productId) {
        currentProductId = productId;
        
        // Reset modal to its initial state
        quickViewForm.style.display = 'none';
        modalLoader.style.display = 'block';
        modalVariationsContainer.innerHTML = '';
        modalAddToCartBtn.disabled = true;
        modalVariationIdInput.value = '';
        modalSelectedOptions = {};
        
        quickViewModal.show();

        try {
            // Fetch variation data from the API
            const response = await fetch(`/api/products?product_id=${productId}`);
            const data = await response.json();
            
            modalVariationsData = data;
            modalLoader.style.display = 'none';
            quickViewForm.style.display = 'block';
            
            // Handle products with only one variation vs multiple options
            if (data.variations.length === 1 && Object.keys(data.options).length === 0) {
                 modalVariationIdInput.value = data.variations[0].id;
                 modalAddToCartBtn.disabled = false;
                 modalVariationsContainer.innerHTML = '<p class="text-muted small">This product has one variation and will be added to your cart.</p>';
            } else {
                // Dynamically create the option buttons (e.g., for Color, Size)
                Object.keys(data.options).forEach(optionName => {
                    let buttonsHtml = data.options[optionName].map(value => 
                        `<button type="button" class="btn btn-sm btn-outline-secondary variation-option me-2 mb-2" data-option-name="${optionName}" data-option-value="${value}">${value}</button>`
                    ).join('');
                    modalVariationsContainer.innerHTML += `<div class="mb-3"><strong>${optionName}:</strong><br>${buttonsHtml}</div>`;
                });
            }
        } catch (error) {
            modalVariationsContainer.innerHTML = '<p class="text-danger">Could not load options. Please try again.</p>';
            modalLoader.style.display = 'none';
        }
    }
    
    // --- PART 3: Event listener for selecting options inside the modal ---
    modalVariationsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('variation-option')) {
            const { optionName, optionValue } = e.target.dataset;
            
            // Manage active state of buttons
            document.querySelectorAll(`#modal-variations-container .variation-option[data-option-name="${optionName}"]`).forEach(btn => btn.classList.remove('active'));
            e.target.classList.add('active');
            
            modalSelectedOptions[optionName] = optionValue;
            
            // Check if all options are selected to find the matching variation
            if (Object.keys(modalSelectedOptions).length === Object.keys(modalVariationsData.options).length) {
                const matchingVariation = modalVariationsData.variations.find(v => 
                    Object.keys(modalSelectedOptions).every(key => v.attributes[key] === modalSelectedOptions[key])
                );
                
                if (matchingVariation) {
                    modalVariationIdInput.value = matchingVariation.id;
                    modalAddToCartBtn.disabled = false;
                } else {
                    modalVariationIdInput.value = '';
                    modalAddToCartBtn.disabled = true;
                }
            }
        }
    });

    // --- PART 4: [CRUCIAL LOGIC] Event listener for the final "Move to Cart" button in the modal ---
    modalAddToCartBtn.addEventListener('click', function() {
        // Step 1: Set the form's action to submit to the CartController
        quickViewForm.action = '/cart/add';
        
        // Step 2: Create a hidden input to tell CartController to redirect to /cart
        const oldRedirectInput = quickViewForm.querySelector('input[name="redirect_to"]');
        if (oldRedirectInput) oldRedirectInput.remove(); // Remove if it exists from a previous click
        
        const redirectInput = document.createElement('input');
        redirectInput.type = 'hidden';
        redirectInput.name = 'redirect_to';
        redirectInput.value = '/cart'; // This is the destination URL
        quickViewForm.appendChild(redirectInput);
        
        // Step 3: Submit the form to add the item to the cart
        quickViewForm.submit();
        
        // Step 4 (Optional but good UX): Remove item from wishlist in the background.
        // This is a "fire and forget" request, we don't need to wait for it.
        // It makes sure that when the user is redirected, the item is gone from the wishlist.
        fetch(`/wishlist/add?id=${currentProductId}`);
    });

    // --- PART 5: Main event listener to open the modal ---
    if (wishlistGrid) {
        wishlistGrid.addEventListener('click', function(e) {
            const moveToCartButton = e.target.closest('.move-to-cart-btn');
            if (moveToCartButton) {
                e.preventDefault();
                openQuickView(moveToCartButton.dataset.productId);
            }
        });
    }
});
</script>

<?php require(ROOT_PATH . '/views/partials/footer.php'); ?>