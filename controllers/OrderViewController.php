<?php
// File: controllers/OrderViewController.php
// Version: COMPLETE AND FINAL - This file now correctly sets $current_user.

// Security Check: Ensures a user is logged in. The global $user is set by bootstrap.php.
if (!$user) {
    header('Location: /login');
    exit();
}

// ★★★ আপনার সমস্যার মূল সমাধানটি এখানেই ★★★
// This line makes the logged-in user's data available to the view and all its partials (like the sidebar).
$current_user = $user;

// Router থেকে অর্ডারের ID নেওয়া হচ্ছে
$order_id = $matches[1] ?? null;

if (!$order_id) {
    http_response_code(404);
    require ROOT_PATH . '/views/404.php';
    exit();
}

$user_id = $user['id']; // Get user ID from the user object for consistency

try {
    // Query 1: Fetch main order details and verify it belongs to the current user
    $order_stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $order_stmt->execute([$order_id, $user_id]);
    $order = $order_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        http_response_code(404);
        require ROOT_PATH . '/views/404.php';
        exit();
    }

    // Query 2: Fetch all items associated with this order
    $items_stmt = $pdo->prepare("
        SELECT oi.*, p.name as product_name, p.slug as product_slug,
               (SELECT image_path FROM product_images WHERE product_id = p.id AND is_featured = 1 LIMIT 1) as product_image
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $items_stmt->execute([$order_id]);
    $order_items_raw = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

    // This loop adds the review logic to each order item
    $order_items = [];
    foreach ($order_items_raw as $item) {
        $review_check_stmt = $pdo->prepare("SELECT id FROM product_reviews WHERE user_id = ? AND product_id = ? LIMIT 1");
        $review_check_stmt->execute([$user_id, $item['product_id']]);
        $item['is_reviewed'] = $review_check_stmt->fetch() ? true : false;
        $order_items[] = $item;
    }

} catch (PDOException $e) {
    error_log("Order View Error: " . $e->getMessage());
    die("Error fetching order details.");
}

$pageTitle = "Order Details #" . htmlspecialchars($order['id']);

// সব ডেটা ভিউ ফাইলে পাঠানো হচ্ছে
require ROOT_PATH . '/views/order-details.view.php';