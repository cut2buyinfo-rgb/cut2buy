<?php
// File: controllers/UserAccountController.php
// Version: FINAL - Handles Profile, Order List, and Single Order View.

// Security Check: The bootstrap.php file has already created the global $user variable.
if (!$user) {
    header('Location: /login');
    exit();
}

// Global variables for this controller
$user_id = $user['id'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$message = null; // For success/error messages on profile update

// --- 1. HANDLE POST REQUESTS (Profile Update) ---
if ($method === 'POST' && $path === '/profile') {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $email = $_POST['email'] ?? '';

    // Validation and DB update logic...
    if (empty($name) || empty($email)) {
        $message = ['type' => 'danger', 'text' => 'Name and Email are required.'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = ['type' => 'danger', 'text' => 'Invalid email format.'];
    } else {
        try {
            $check_stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $check_stmt->execute([$email, $user_id]);
            if ($check_stmt->fetch()) {
                $message = ['type' => 'danger', 'text' => 'This email is already used by another account.'];
            } else {
                $update_stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
                $update_stmt->execute([$name, $email, $phone, $address, $user_id]);
                $message = ['type' => 'success', 'text' => 'Profile updated successfully!'];
            }
        } catch (PDOException $e) {
            $message = ['type' => 'danger', 'text' => 'Database error. Could not update profile.'];
        }
    }
}

// --- 2. HANDLE GET REQUESTS (Page Loading) ---
try {
    // This variable is for view compatibility, so you don't have to change $current_user to $user in your views.
    $current_user = $user;

    // --- NEW: Route for viewing a single order ---
    if (preg_match('/^\/orders\/view\/(\d+)$/', $path, $matches)) {
        $order_id = $matches[1];
        $pageTitle = "Order Details #" . $order_id;
        
        // Fetch order details, ensuring it belongs to the logged-in user for security.
        $order_stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
        $order_stmt->execute([$order_id, $user_id]);
        $order = $order_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            http_response_code(404);
            require 'views/404.php'; // Show a proper 404 page
            exit();
        }

        // Fetch items for this order.
        $items_stmt = $pdo->prepare("
            SELECT oi.*, p.name as product_name, p.slug as product_slug,
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_featured = 1 LIMIT 1) as product_image
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?
        ");
        $items_stmt->execute([$order_id]);
        $order_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

        require 'views/user/order-details.view.php';
        exit(); // Stop further execution
    }

    // --- Existing routes for the user dashboard ---
    switch ($path) {
        case '/orders':
            $pageTitle = "My Orders";
            
            // Fetch ALL orders for the user
            $orders_stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
            $orders_stmt->execute([$user_id]);
            $orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);

            require 'views/user/orders.view.php';
            break;

        case '/profile':
            $pageTitle = "Address Book";
            
            // If the profile was just updated successfully, re-fetch the user data
            // to show the latest changes immediately in the form.
            if ($method === 'POST' && isset($message['type']) && $message['type'] === 'success') {
                $user_stmt = $pdo->prepare("SELECT id, name, email, phone, address FROM users WHERE id = ?");
                $user_stmt->execute([$user_id]);
                $current_user = $user_stmt->fetch(PDO::FETCH_ASSOC);
            }
            require 'views/user/profile.view.php';
            break;

        case '/reviews':
        case '/wishlist':
            $pageTitle = "Coming Soon";
            require 'views/user/coming_soon.view.php';
            break;
            
        // If no specific user route matches, you might want to redirect to the dashboard
        // or handle it as a 404, depending on your application's logic.
    }

} catch (PDOException $e) {
    die("Error fetching account data: " . $e->getMessage());
}