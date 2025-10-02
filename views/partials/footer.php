<?php
// File: /views/partials/footer.php
// Version: ULTIMATE FINAL & COMPLETE (Includes all features and necessary site-wide JS)
?>
</main> <!-- This closes the <main> tag that was opened in header.php -->

<!-- ======================= DESKTOP FOOTER ======================= -->
<footer class="d-none d-lg-block footer-section">
    <div class="container">
        <!-- Footer Top Section -->
        <div class="footer-top pt-5 pb-4 row">
            <div class="col-md-4 mb-4 mb-md-0">
                <a href="/"><img src="/assets/images/logo-white.webp" alt="Cut2Buy Logo" class="footer-logo mb-3" style="height:50px;width:auto;"></a>
                <p class="footer-motto">Your trusted partner for global shopping. Buy anything from anywhere, easily.</p>
                <div class="contact-info">
                    <p><i class="bi bi-geo-alt-fill me-2"></i>Dhaka, Bangladesh</p>
                    <p><i class="bi bi-envelope-fill me-2"></i>support@cut2buy.com</p>
                    <p><i class="bi bi-telephone-fill me-2"></i>+880 96XX XXXXXX</p>
                </div>
            </div>
            <div class="col-md-2 col-6 mb-4 mb-md-0">
                <h5 class="footer-title">Customer Care</h5>
                <ul class="footer-links">
                    <?php if ($user): // $user is provided by bootstrap.php ?>
                        <li><a href="/dashboard">My Account</a></li>
                        <li><a href="/orders">My Orders</a></li>
                    <?php else: ?>
                        <li><a href="/login">Login</a></li>
                        <li><a href="/register">Register</a></li>
                    <?php endif; ?>
                    <li><a href="/faq">FAQ</a></li>
                </ul>
            </div>
            <div class="col-md-2 col-6 mb-4 mb-md-0">
                <h5 class="footer-title">Information</h5>
                <ul class="footer-links">
                    <li><a href="/about-us">About Us</a></li>
                    <li><a href="/contact-us">Contact Us</a></li>
                    <li><a href="/privacy-policy">Privacy Policy</a></li>
                    <li><a href="/terms-conditions">Terms & Conditions</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5 class="footer-title">Follow Us</h5>
                <div class="social-icons">
                    <a href="https://facebook.com/cut2buy.fb" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
        <!-- Footer Bottom Section -->
        <div class="footer-bottom d-flex justify-content-between align-items-center py-3">
            <p class="mb-0">&copy; <?= date('Y') ?> Cut2Buy. All Rights Reserved.</p>
            <div><img src="/assets/images/payment-methods.png" alt="Payment Methods" height="25"></div>
        </div>
    </div>
</footer>

<!-- ======================= MOBILE BOTTOM NAVIGATION ======================= -->
<nav class="d-lg-none mobile-bottom-nav">
    <a href="#mobileCategoryMenu" class="mobile-nav-item" data-bs-toggle="offcanvas" role="button" aria-controls="mobileCategoryMenu">
        <i class="bi bi-grid"></i><span>Category</span>
    </a>
    <a href="#mobileAccountMenu" class="mobile-nav-item" data-bs-toggle="offcanvas" role="button" aria-controls="mobileAccountMenu">
        <i class="bi bi-person"></i><span>Account</span>
    </a>
    <a href="/" class="mobile-nav-center-button" aria-label="Home">
        <img src="/assets/images/home-icon.svg" alt="Home">
    </a>
    <a href="tel:+8801892328077" class="mobile-nav-item">
        <i class="bi bi-telephone"></i><span>Call</span>
    </a>
    <a href="/contact-us" class="mobile-nav-item">
        <i class="bi bi-chat-dots"></i><span>Chat</span>
    </a>
</nav>

<!-- ======================= MOBILE CATEGORY OFFCANVAS MENU ======================= -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileCategoryMenu" aria-labelledby="mobileCategoryMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileCategoryMenuLabel"><i class="bi bi-grid-fill me-2"></i> All Categories</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <?php if (!empty($global_categories)): // $global_categories is from bootstrap.php ?>
            <div class="list-group list-group-flush">
                <?php foreach ($global_categories as $category): ?>
                    <a href="/category/<?= htmlspecialchars($category['slug']) ?>" class="list-group-item list-group-item-action"><?= htmlspecialchars($category['name']) ?></a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-muted p-3">No categories found.</p>
        <?php endif; ?>
    </div>
</div>

<!-- ======================= CORE JAVASCRIPT LIBRARIES ======================= -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- ======================= SITE-WIDE CUSTOM JAVASCRIPT ======================= -->
<script>
// Version: FINAL & GUARANTEED - Uses Standard Form Submission for Maximum Reliability
document.addEventListener('DOMContentLoaded', function() {
    function setupImageSearch(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        const cameraIcon = form.querySelector('.camera-icon');
        const imageInput = form.querySelector('.image-search-input');
        
        if (!cameraIcon || !imageInput) return;

        // When the camera icon is clicked, trigger the hidden file input
        cameraIcon.addEventListener('click', () => imageInput.click());
        
        // When a file is selected in the hidden input
        imageInput.addEventListener('change', function(event) {
            if (event.target.files.length > 0) {
                
                // --- THIS IS THE GUARANTEED FIX ---
                // We change the original form's properties and submit it directly.
                // This is the most reliable way to send a file.
                
                // 1. Set the action to the image search controller.
                form.action = '/image-search-upload';
                
                // 2. Set the method to POST.
                form.method = 'POST';
                
                // 3. Set the encoding type, which is CRITICAL for file uploads.
                form.enctype = 'multipart/form-data';
                
                // 4. Submit the form.
                form.submit();
            }
        });
    }

    // Setup the image search functionality for both desktop and mobile forms
    setupImageSearch('desktopSearchForm');
    setupImageSearch('mobileSearchForm');
});
</script>


<!-- Page-specific scripts (like for product-detail page) will be loaded here by their respective view files just before the body closes -->

</body>
</html>