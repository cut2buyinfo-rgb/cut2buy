<?php
// File: /index.php (Front Controller)
// Version: FINAL with .htaccess Fallback Handler

// --- 1. Basic Configuration ---
ini_set("display_errors", 1);
error_reporting(E_ALL);
define('ROOT_PATH', __DIR__);
date_default_timezone_set("Asia/Dhaka");

// --- 2. Core Includes ---
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/sitemap-updater.php';
// =================================================================
// --- 3. HTACCESS FALLBACK HANDLER ---
// This block handles specific POST actions if .htaccess URL rewriting fails.
// It allows forms to post directly to index.php.
// =================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    // Define which actions are handled by this fallback mechanism.
    $actionRoutes = [
        'ask-question' => 'controllers/ProductActionController.php',
        'submit-review' => 'controllers/ProductActionController.php', // For the future
    ];

    if (array_key_exists($action, $actionRoutes)) {
        require $actionRoutes[$action];
        exit(); // Stop execution after the action is handled.
    }
}


// --- 4. Standard URI Routing ---
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$routes = [
    // --- Core Public Routes ---
    '/^\/$/'                                  => 'controllers/HomeController.php',
    '/^\/product\/([a-zA-Z0-9-]+)\/?$/'        => 'controllers/ProductDetailController.php',


    '/^\/products$/'                          => 'controllers/SearchController.php',


  '/^\/categories$/'                        => 'controllers/ShowCategoryController.php',
'/^\/category\/([a-zA-Z0-9-]+)\/?$/'       => 'controllers/ShowCategoryController.php',



    '/^\/api\/products\/?$/'             => 'controllers/ApiProductController.php',
    '/^\/search$/'                       => 'controllers/SearchController.php',
    '/^\/image-search-upload$/'         => 'controllers/ImageSearchController.php', // For image search form submission

   // --- FLASH SALE ROUTES (UPDATED) ---
    '/^\/campaigns\/flash-sale$/'     => 'controllers/FlashSaleController.php',
    '/^\/admin\/flash-sale$/'         => 'controllers/admin/AdminFlashSaleController.php',
    '/^\/admin\/flash-sale\/store$/'  => 'controllers/admin/AdminFlashSaleController.php',
    '/^\/admin\/flash-sale\/delete$/' => 'controllers/admin/AdminFlashSaleController.php',
    // === NEW EDIT ROUTES ADDED HERE ===
    '/^\/admin\/flash-sale\/edit\/(\d+)$/' => 'controllers/admin/AdminFlashSaleController.php', // To show the edit form
    '/^\/admin\/flash-sale\/update$/'      => 'controllers/admin/AdminFlashSaleController.php', // To process the update
    
    // --- Authentication Routes ---
    '/^\/login$/'                             => 'controllers/AuthController.php',
    '/^\/register$/'                          => 'controllers/AuthController.php',
    '/^\/logout$/'                            => 'controllers/AuthController.php',
    
 // === NEW AUTHENTICATION ROUTES (START) ===
    // Google Login Routes
    '/^\/login\/google$/'             => 'controllers/GoogleAuthController.php', // Initiates Google Login
    '/^\/gmail-callback$/'            => 'controllers/GoogleAuthController.php', // Handles Google's response
    
    // Phone OTP Login Routes
    '/^\/login\/phone$/'              => 'controllers/OtpController.php', // Shows phone number form
    '/^\/login\/phone\/send-otp$/'    => 'controllers/OtpController.php', // Handles sending OTP
    '/^\/login\/phone\/verify-otp$/'   => 'controllers/OtpController.php', // Shows OTP verification form
    // === NEW AUTHENTICATION ROUTES (END) ===



    // --- User Account Routes ---
    '/^\/dashboard$/'                         => 'controllers/DashboardController.php',
    '/^\/orders$/'                            => 'controllers/UserAccountController.php',
   '/^\/orders\/view\/(\d+)$/' => 'controllers/OrderViewController.php',
    '/^\/profile$/'                           => 'controllers/UserAccountController.php',
      '/^\/reviews$/' => 'controllers/ReviewController.php',
    // --- Cart & Wishlist Routes ---
    '/^\/cart$/'                              => 'controllers/CartController.php',
    '/^\/cart\/add$/'                         => 'controllers/CartController.php',
    '/^\/cart\/update$/'                      => 'controllers/CartController.php',
    '/^\/cart\/remove$/'                      => 'controllers/CartController.php',
    '/^\/cart\/add-and-checkout$/'            => 'controllers/CartController.php',
    '/^\/wishlist$/'                          => 'controllers/WishlistController.php',
    '/^\/wishlist\/add$/'                     => 'controllers/WishlistController.php',
    '/^\/wishlist\/move-to-cart$/'            => 'controllers/WishlistController.php',

    // --- Product Interaction Routes (Handled by .htaccess or Fallback) ---
    '/^\/product\/ask-question$/'             => 'controllers/ProductActionController.php',
     '/^\/product\/submit-review$/'            => 'controllers/ProductActionController.php', // This line is crucial
     '/^\/contact-us$/'                        => 'controllers/ContactController.php', 
'/^\/about-us$/'                          => 'controllers/AboutController.php', 
'/^\/faq$/'                          => 'controllers/FaqController.php',
'/^\/privacy-policy$/'                    => 'controllers/PrivacyPolicyController.php', 
'/^\/terms-conditions$/'                  => 'controllers/TermsController.php', 


    // --- Checkout Process Routes ---
    '/^\/checkout$/'                          => 'controllers/CheckoutController.php',
    '/^\/place-order$/'                       => 'controllers/CheckoutController.php',
    '/^\/order-success$/'                     => 'controllers/CheckoutController.php',
    
    // --- Admin Panel Routes ---
    '/^\/admin\/dashboard$/'                  => 'controllers/AdminDashboardController.php',
    '/^\/admin\/products$/'                   => 'controllers/AdminProductController.php',
    '/^\/admin\/products\/create$/'            => 'controllers/AdminProductController.php',
    '/^\/admin\/products\/edit\/(\d+)$/'      => 'controllers/AdminProductController.php',
    '/^\/admin\/products\/store$/'            => 'controllers/AdminProductController.php',
    '/^\/admin\/products\/update$/'           => 'controllers/AdminProductController.php',
    '/^\/admin\/products\/delete$/'           => 'controllers/AdminProductController.php',
    '/^\/admin\/orders$/'                     => 'controllers/admin/AdminOrderController.php',
    '/^\/admin\/orders\/view\/(\d+)$/'        => 'controllers/admin/AdminOrderController.php',
    '/^\/admin\/orders\/update-status$/'      => 'controllers/admin/AdminOrderController.php',
    '/^\/admin\/users$/'                      => 'controllers/admin/AdminUserController.php',
    '/^\/admin\/banners$/'                    => 'controllers/admin/AdminBannerController.php',
    '/^\/admin\/banners\/create$/'             => 'controllers/admin/AdminBannerController.php',
    '/^\/admin\/banners\/store$/'              => 'controllers/admin/AdminBannerController.php',
    '/^\/admin\/banners\/edit\/(\d+)$/'       => 'controllers/admin/AdminBannerController.php',
    '/^\/admin\/banners\/update$/'            => 'controllers/admin/AdminBannerController.php',
    '/^\/admin\/banners\/delete$/'             => 'controllers/admin/AdminBannerController.php',




// ---  NEW ROUTES FOR BRAND MANAGEMENT ---
    '/^\/admin\/brands$/'          => 'controllers/AdminBrandController.php',
    '/^\/admin\/brands\/store$/'    => 'controllers/AdminBrandController.php',
    '/^\/admin\/brands\/delete$/'    => 'controllers/AdminBrandController.php',

    // --- NEW ROUTES FOR CATEGORY MANAGEMENT ---
    '/^\/admin\/categories$/'          => 'controllers/AdminCategoryController.php',
    '/^\/admin\/categories\/store$/'    => 'controllers/AdminCategoryController.php',
    '/^\/admin\/categories\/update$/'  => 'controllers/AdminCategoryController.php',
    '/^\/admin\/categories\/delete$/'    => 'controllers/AdminCategoryController.php',

            // ---  FOR Q&A MANAGEMENT ---
    '/^\/admin\/qna$/'                        => 'controllers/admin/AdminQnaController.php', // To list all questions
    '/^\/admin\/qna\/answer$/'                => 'controllers/admin/AdminQnaController.php', // To submit an answer
    '/^\/admin\/qna\/delete$/'                => 'controllers/admin/AdminQnaController.php', // To delete a question


 // ---  ROUTES FOR USER MANAGEMENT ---
    '/^\/admin\/users$/'               => 'controllers/AdminUserController.php', // Show user list & forms
    '/^\/admin\/users\/store$/'         => 'controllers/AdminUserController.php', // Handle new user creation
    '/^\/admin\/users\/update$/'       => 'controllers/AdminUserController.php', // Handle user update
    '/^\/admin\/users\/delete$/'         => 'controllers/AdminUserController.php', 


];

// --- 5. Routing Logic ---
$routeFound = false;
foreach ($routes as $pattern => $controllerPath) {
    if (preg_match($pattern, $uri, $matches)) {
        $routeFound = true;
        require $controllerPath;
        break;
    }
}

// --- 6. Handle 404 Not Found ---
if (!$routeFound) {
    http_response_code(404);
    require 'views/404.php';
    exit();
}