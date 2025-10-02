<?php require('partials/header.php'); ?>

<main class="homepage">
<!-- ======================= HERO SECTION ======================= -->
<section class="hero-section">
    <div class="container pt-2 card">
        <?php if (!empty($banners)): ?>
            <div id="hero-carousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner" style="border-radius: var(--border-radius); overflow: hidden;">
                    <?php foreach ($banners as $key => $banner): ?>
                        <div class="carousel-item <?= $key === 0 ? 'active' : '' ?>">
                            <a href="<?= htmlspecialchars($banner['link_url'] ?? '#') ?>" class="banner-link">
                                <img src="/assets/images/banners/<?= htmlspecialchars($banner['image_path']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($banner['title']) ?>">
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ======================= PAGE HEADING ======================= -->
    <div class="container text-center pt-4">
        <h1 class="display-5 fw-bold">Online Shopping in Bangladesh</h1>
        <p class="lead text-muted">Discover the best deals on electronics, fashion, and more at Cut2Buy.</p>
    </div>


<!-- ======================= MAIN CONTENT SECTION ======================= -->
<section class="main-content-section pt-4">
    <div class="container">
       
        <!-- 1. CATEGORY FILTER (TOP) -->
        <?php if (!empty($categories)): ?>
            <div class="category-filter-wrapper card-like-section mb-4">
                
                <div class="category-selector">
                    <button class="category-selector-item active" data-category-id="all">
                        <i class="bi bi-grid-fill"></i><span>All Products</span>
                    </button>
                    <?php foreach ($categories as $category): ?>
                        <button class="category-selector-item" data-category-id="<?= $category['id'] ?>">
                            <i class="bi bi-tag"></i><span><?= htmlspecialchars($category['name']) ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- 2. FLASH SALE SECTION (MIDDLE) -->
        <?php if (!empty($flash_sale_products)): ?>
        <div class="flash-sale-wrapper card-like-section mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3 px-3 pt-3">
                <h2 class="section-title mb-0">⚡ Flash Sale</h2>
                <a href="/campaigns/flash-sale" class="btn btn-sm btn-outline-danger">View All</a>
            </div>
            <div class="p-3 pt-0">
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
                    <?php foreach ($flash_sale_products as $product): ?>
                        <div class="col">
                            <div class="card product-card h-100">
                                <a href="/product/<?= htmlspecialchars($product['slug']) ?>">
                                    <img src="/assets/images/products/<?= htmlspecialchars($product['image'] ?? 'placeholder.png') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                                </a>
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title flex-grow-1"><a href="/product/<?= htmlspecialchars($product['slug']) ?>"><?= htmlspecialchars($product['name']) ?></a></h6>
                                    <div class="price mt-2">
                                        <span class="text-danger fw-bold">৳<?= number_format($product['flash_price']) ?></span>
                                        <?php if (!empty($product['old_price']) && $product['old_price'] > $product['flash_price']): ?>
                                            <small class="text-muted text-decoration-line-through">৳<?= number_format($product['old_price']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- 3. ALL PRODUCTS SECTION (BOTTOM) -->
        <div id="product-grid-container" class="card-like-section">
             <h2 class="section-title px-3 pt-3">All Products</h2>
             <div class="p-3">
                <div class="loader-wrapper text-center my-5"><div class="loader"></div></div>
                <div id="product-grid" class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3"></div>
                 <!-- LOAD MORE BUTTON -->
                <div id="load-more-container" class="text-center mt-4">
                    <button id="load-more-btn" class="btn btn-outline-primary">Load More Products</button>
                </div>
             </div>
        </div>
    </div>
</section>

<!-- ======================= QUICK VIEW MODAL ======================= -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="quickViewModalLabel">Select Options</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body quick-view-modal">
        <div id="modal-loader" class="text-center">
            <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
        </div>
        <form id="quick-view-form" method="POST" style="display: none;">
            <input type="hidden" name="variation_id" id="modal-variation-id-input">
            <div id="modal-variations-container"></div>
            <div class="mt-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" class="form-control" value="1" min="1">
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="modal-add-to-cart-btn" disabled>Add to Cart</button>
      </div>
    </div>
  </div>
</div>

</main>

<!-- ======================= JAVASCRIPT SECTION ======================= -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- PART 1: HOMEPAGE PRODUCT LOADING LOGIC (FOR "ALL PRODUCTS" SECTION) ---
    let currentPage = 1;
    let currentCategoryId = 'all';
    let isLoading = false;
    const productGrid = document.getElementById('product-grid');
    const loadMoreBtn = document.getElementById('load-more-btn');
    const categorySelectors = document.querySelectorAll('.category-selector-item');
    const loader = document.querySelector('.loader-wrapper');

    function createProductCard(product) {
        const image = product.image ? `/assets/images/products/${product.image}` : '/assets/images/products/placeholder.png';
        let oldPriceHtml = '';
        if (product.old_price && parseFloat(product.old_price) > parseFloat(product.price)) {
            oldPriceHtml = `<small class="text-muted text-decoration-line-through">৳${parseInt(product.old_price).toLocaleString('en-IN')}</small>`;
        }
        const wishlistIconClass = product.in_wishlist ? 'bi-heart-fill text-danger' : 'bi-heart';
        const wishlistLink = `/wishlist/add?id=${product.id}&redirect_to=/`;
        const addToCartButtonHtml = `<button type="button" class="btn btn-primary w-100 btn-sm add-to-cart-btn" data-product-id="${product.id}">Add to Cart</button>`;
        
        let priceClass = product.is_on_flash_sale ? 'text-danger' : 'text-primary';
        let priceHtml = `<span class="${priceClass} fw-bold">৳${parseInt(product.price).toLocaleString('en-IN')}</span> ${oldPriceHtml}`;
        
        return `
            <div class="col">
                <div class="card product-card h-100">
                    <a href="/product/${product.slug}"><img src="${image}" class="card-img-top" alt="${product.name}"></a>
                    <div class="card-body d-flex flex-column">
                        <a href="${wishlistLink}" class="wishlist-btn" title="Add to Wishlist"><i class="bi ${wishlistIconClass}"></i></a>
                        <h6 class="card-title flex-grow-1"><a href="/product/${product.slug}">${product.name}</a></h6>
                        <div class="price mt-2">${priceHtml}</div>
                    </div>
                    <div class="card-footer border-0 bg-transparent pt-0 pb-3">${addToCartButtonHtml}</div>
                </div>
            </div>`;
    }

    async function fetchProducts(categoryId, page, append = false) {
        if (isLoading) return;
        isLoading = true;
        loader.style.display = 'block';
        if (!append) productGrid.innerHTML = '';
        
        try {
            const response = await fetch(`/api/products?category_id=${categoryId}&page=${page}`);
            const products = await response.json();
            
            if (products.length > 0) {
                products.forEach(p => productGrid.innerHTML += createProductCard(p));
            } else if (!append) {
                productGrid.innerHTML = '<div class="col-12 text-center py-5"><p class="text-muted">No products found in this category.</p></div>';
            }
            loadMoreBtn.style.display = (products.length < 9) ? 'none' : 'block';
        } catch (error) {
            console.error('Fetch error:', error);
            productGrid.innerHTML = '<div class="col-12 text-center py-5"><p class="text-danger">Failed to load products.</p></div>';
        } finally {
            isLoading = false;
            loader.style.display = 'none';
        }
    }

    categorySelectors.forEach(button => {
        button.addEventListener('click', function() {
            categorySelectors.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            currentCategoryId = this.dataset.categoryId;
            currentPage = 1;
            fetchProducts(currentCategoryId, currentPage, false);
        });
    });

    loadMoreBtn.addEventListener('click', () => fetchProducts(currentCategoryId, ++currentPage, true));
    
    // Initial load
    fetchProducts(currentCategoryId, currentPage);

    // --- PART 2: QUICK VIEW MODAL LOGIC ---
    const quickViewModal = new bootstrap.Modal(document.getElementById('quickViewModal'));
    const modalLoader = document.getElementById('modal-loader');
    const quickViewForm = document.getElementById('quick-view-form');
    const modalVariationsContainer = document.getElementById('modal-variations-container');
    const modalVariationIdInput = document.getElementById('modal-variation-id-input');
    const modalAddToCartBtn = document.getElementById('modal-add-to-cart-btn');
    let modalVariationsData = {};
    let modalSelectedOptions = {};

    async function openQuickView(productId) {
        quickViewForm.style.display = 'none';
        modalLoader.style.display = 'block';
        modalVariationsContainer.innerHTML = '';
        modalAddToCartBtn.disabled = true;
        modalVariationIdInput.value = '';
        modalSelectedOptions = {};
        quickViewModal.show();
        
        try {
            const response = await fetch(`/api/products?product_id=${productId}`);
            const data = await response.json();
            
            modalVariationsData = data;
            modalLoader.style.display = 'none';
            quickViewForm.style.display = 'block';

            if (data.flash_sale_price) {
                modalVariationsContainer.innerHTML += `<div class="alert alert-danger">Flash Sale Price: <strong>৳${parseInt(data.flash_sale_price)}</strong></div>`;
            }

            if (data.variations.length === 1 && Object.keys(data.options).length === 0) {
                modalVariationIdInput.value = data.variations[0].id;
                modalAddToCartBtn.disabled = false;
                if (!data.flash_sale_price) {
                    modalVariationsContainer.innerHTML += '<p class="text-muted small">This product has one variation and will be added to your cart.</p>';
                }
            } else {
                Object.keys(data.options).forEach(optionName => {
                    let buttonsHtml = data.options[optionName].map(value => `<button type="button" class="btn btn-sm btn-outline-secondary variation-option me-2 mb-2" data-option-name="${optionName}" data-option-value="${value}">${value}</button>`).join('');
                    modalVariationsContainer.innerHTML += `<div class="mb-3"><strong>${optionName}:</strong><br>${buttonsHtml}</div>`;
                });
            }
        } catch (error) {
            modalVariationsContainer.innerHTML = '<p class="text-danger">Could not load options.</p>';
            modalLoader.style.display = 'none';
        }
    }
    
    modalVariationsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('variation-option')) {
            const { optionName, optionValue } = e.target.dataset;
            document.querySelectorAll(`#modal-variations-container .variation-option[data-option-name="${optionName}"]`).forEach(btn => btn.classList.remove('active'));
            e.target.classList.add('active');
            modalSelectedOptions[optionName] = optionValue;
            if (Object.keys(modalSelectedOptions).length === Object.keys(modalVariationsData.options).length) {
                const matchingVariation = modalVariationsData.variations.find(v => Object.keys(modalSelectedOptions).every(key => v.attributes[key] === modalSelectedOptions[key]));
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

    modalAddToCartBtn.addEventListener('click', function() {
        quickViewForm.action = '/cart/add';
        const oldRedirectInput = quickViewForm.querySelector('input[name="redirect_to"]');
        if (oldRedirectInput) oldRedirectInput.remove();
        const redirectInput = document.createElement('input');
        redirectInput.type = 'hidden';
        redirectInput.name = 'redirect_to';
        redirectInput.value = '/cart';
        quickViewForm.appendChild(redirectInput);
        quickViewForm.submit();
    });

    productGrid.addEventListener('click', function(e) {
        const addToCartButton = e.target.closest('.add-to-cart-btn');
        if (addToCartButton) {
            e.preventDefault();
            openQuickView(addToCartButton.dataset.productId);
        }
    });
});
</script>
<?php require('partials/footer.php'); ?>