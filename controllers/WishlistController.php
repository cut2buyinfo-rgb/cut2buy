<?php
// controllers/WishlistController.php
// Version: FINAL - Now correctly handles custom redirection.


// Ensure session variables are initialized to avoid errors.
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($path) {
    
    // --- ACTION: ADD or REMOVE an item from the wishlist ---
    case '/wishlist/add':
        $product_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($product_id) {
            if (isset($_SESSION['wishlist'][$product_id])) {
                // Item exists, so remove it.
                unset($_SESSION['wishlist'][$product_id]);
                $_SESSION['flash_message'] = ['type' => 'info', 'text' => 'Item removed from your wishlist.'];
            } else {
                // Item does not exist, so add it.
                $_SESSION['wishlist'][$product_id] = true;
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Item added to your wishlist!'];
            }
        }

        // --- [THE CRUCIAL FIX IS HERE] ---
        // Check if a specific redirect URL was provided in the query string (e.g., from homepage).
        // If not, fall back to the previous page (HTTP_REFERER), or finally to the homepage.
        $redirect_url = $_GET['redirect_to'] ?? $_SERVER['HTTP_REFERER'] ?? '/';
        
        header('Location: ' . $redirect_url);
        exit();

    // --- [IMPORTANT] The '/wishlist/move-to-cart' route is intentionally removed. ---
    // This logic is now handled by the JavaScript modal on the wishlist view page,
    // which submits the selected variation directly to the CartController.

    // --- ACTION: Display the Wishlist page ---
    case '/wishlist':
    default:
        $pageTitle = "My Wishlist";
        $wishlist_items = [];

        if (!empty($_SESSION['wishlist'])) {
            $product_ids = array_keys($_SESSION['wishlist']);
            $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
            
            // Fetch full details for all products in the wishlist for display.
            $stmt = $pdo->prepare("
                SELECT 
                    p.id, p.name, p.slug,
                    (SELECT image_path FROM product_images WHERE product_id = p.id AND is_featured = 1 LIMIT 1) as image,
                    (SELECT MIN(price) FROM product_variations WHERE product_id = p.id) as price
                FROM products p
                WHERE p.id IN ($placeholders)
            ");
            $stmt->execute($product_ids);
            $wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Load the view file to display the wishlist items.
        require 'views/wishlist/index.view.php';
        break;
}