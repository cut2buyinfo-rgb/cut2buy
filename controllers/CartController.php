<?php
// File: controllers/CartController.php
// Version: FINAL - Now correctly handles custom redirection from the wishlist page.



// Initialize cart in session if it doesn't exist.
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// A reusable function to add/update an item in the cart using variation_id.
function manageCartItem($pdo, $variation_id, $quantity) {
    if (!$variation_id || $quantity <= 0) {
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Please select product options before adding to cart.'];
        return false;
    }

    try {
        // Fetch the necessary details for the specific variation.
        $stmt = $pdo->prepare("
            SELECT 
                p.id as product_id, p.name, p.slug,
                pv.id as variation_id, pv.price, pv.stock,
                (SELECT image_path FROM product_images WHERE product_id = p.id AND is_featured = 1 LIMIT 1) as image,
                GROUP_CONCAT(CONCAT(a.name, ': ', av.value) ORDER BY a.name SEPARATOR ', ') as attributes
            FROM product_variations pv
            JOIN products p ON pv.product_id = p.id
            LEFT JOIN product_variation_attributes pva ON pv.id = pva.variation_id
            LEFT JOIN attribute_values av ON pva.attribute_value_id = av.id
            LEFT JOIN attributes a ON av.attribute_id = a.id
            WHERE pv.id = ?
            GROUP BY pv.id
        ");
        $stmt->execute([$variation_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            $current_cart_qty = isset($_SESSION['cart'][$variation_id]) ? $_SESSION['cart'][$variation_id]['quantity'] : 0;
            $requested_total_qty = $current_cart_qty + $quantity;

            if ($requested_total_qty <= $item['stock']) {
                if (isset($_SESSION['cart'][$variation_id])) {
                    $_SESSION['cart'][$variation_id]['quantity'] = $requested_total_qty;
                } else {
                    $_SESSION['cart'][$variation_id] = [
                        'product_id' => $item['product_id'],
                        'variation_id' => $item['variation_id'],
                        'name' => $item['name'],
                        'attributes' => $item['attributes'],
                        'price' => $item['price'],
                        'slug' => $item['slug'],
                        'image' => $item['image'] ?? 'placeholder.png',
                        'quantity' => $quantity
                    ];
                }
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Item added to cart!'];
                return true;
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Not enough stock available. Only ' . $item['stock'] . ' items left.'];
                return false;
            }
        }
    } catch (PDOException $e) {
        error_log("Cart Management Error: " . $e->getMessage());
    }
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Could not find the selected product variation.'];
    return false;
}


switch ($path) {
    // --- ACTION: ADD ITEM TO CART (from Product Detail, Homepage, or Wishlist) ---
    case '/cart/add':
        if ($method === 'POST') {
            $variation_id = filter_input(INPUT_POST, 'variation_id', FILTER_VALIDATE_INT);
            $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT, ['options' => ['default' => 1]]);
            manageCartItem($pdo, $variation_id, $quantity);
        }
        
        // --- [THE CRUCIAL FIX IS HERE] ---
        // Check if a specific redirect URL was provided in the form (e.g., from wishlist).
        // If not, fall back to the previous page (HTTP_REFERER), or finally to the cart page.
        $redirect_url = $_POST['redirect_to'] ?? $_SERVER['HTTP_REFERER'] ?? '/cart';
        
        header('Location: ' . $redirect_url);
        exit();

    // --- ACTION: ADD & GO TO CHECKOUT (for "Buy Now") ---
    case '/cart/add-and-checkout':
        if ($method === 'POST') {
            $variation_id = filter_input(INPUT_POST, 'variation_id', FILTER_VALIDATE_INT);
            $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT, ['options' => ['default' => 1]]);
            if (manageCartItem($pdo, $variation_id, $quantity)) {
                header('Location: /checkout');
                exit();
            }
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/cart'));
        exit();

    // --- ACTION: UPDATE ITEM QUANTITIES (from Cart Page) ---
    case '/cart/update':
        if ($method === 'POST' && isset($_POST['quantities']) && is_array($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $variation_id => $quantity) {
                $variation_id = (int)$variation_id;
                $quantity = (int)$quantity;

                if (isset($_SESSION['cart'][$variation_id])) {
                    if ($quantity > 0) {
                        $_SESSION['cart'][$variation_id]['quantity'] = $quantity;
                    } else {
                        unset($_SESSION['cart'][$variation_id]);
                    }
                }
            }
            $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Cart updated successfully.'];
        }
        header('Location: /cart');
        exit();

    // --- ACTION: REMOVE ITEM FROM CART ---
    case '/cart/remove':
        $variation_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($variation_id && isset($_SESSION['cart'][$variation_id])) {
            unset($_SESSION['cart'][$variation_id]);
            $_SESSION['flash_message'] = ['type' => 'info', 'text' => 'Item removed from cart.'];
        }
        header('Location: /cart');
        exit();

    // --- ACTION: VIEW CART PAGE (DEFAULT) ---
    case '/cart':
    default:
        $pageTitle = "Shopping Cart";
        $cart_items = $_SESSION['cart'];
        
        $subtotal = 0;
        foreach ($cart_items as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
       require 'views/cart/index.view.php';
        break;
}