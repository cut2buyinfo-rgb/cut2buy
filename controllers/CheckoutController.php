<?php
// File: controllers/CheckoutController.php
// Version: FINAL & CORRECT - Based on your advanced code, with variation logic fully integrated.

// Include notifications if the file exists.
if (file_exists(ROOT_PATH . '/includes/notifications.php')) {
    require_once ROOT_PATH . '/includes/notifications.php';
}

// Bootstrap.php handles sessions and the $user variable.
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Define constants for easy management.
define('SHIPPING_FEE', 65.00);
define('PREPAYMENT_PERCENTAGE', 0.25); // 25%

// Security Check: All actions in this controller require a logged-in user.
if (!$user) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('Location: /login');
    exit();
}

switch ($path) {

    case '/place-order':
        if ($method === 'POST') {
            
            // Prerequisite: Ensure the cart is not empty.
            if (empty($_SESSION['cart'])) {
                header('Location: /cart');
                exit();
            }

            $pdo->beginTransaction();
            try {
                // --- A. Collect & Sanitize Form Data ---
                $shipping_address = trim($_POST['address'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $transaction_id = trim($_POST['transaction_id'] ?? '');
                $payment_screenshot_path = null;

                if (empty($shipping_address) || empty($phone)) {
                    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Address and Phone number are required.'];
                    header('Location: /checkout');
                    exit();
                }
                
                // --- B. Handle Screenshot Upload (with WebP Conversion) ---
                if (isset($_FILES['payment_screenshot']) && $_FILES['payment_screenshot']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = ROOT_PATH . '/assets/images/screenshots/';
                    if (!is_dir($upload_dir)) { @mkdir($upload_dir, 0777, true); }
                    
                    $file = $_FILES['payment_screenshot'];
                    $allowed_types = ['image/jpeg', 'image/png'];
                    
                    if (in_array($file['type'], $allowed_types)) {
                        $source_image = ($file['type'] === 'image/jpeg') ? @imagecreatefromjpeg($file['tmp_name']) : @imagecreatefrompng($file['tmp_name']);
                        if ($source_image) {
                            $filename = 'ss-' . $user['id'] . '-' . uniqid() . '.webp';
                            imagewebp($source_image, $upload_dir . $filename, 80);
                            imagedestroy($source_image);
                            $payment_screenshot_path = $filename;
                        }
                    }
                }

                if (empty($transaction_id) && empty($payment_screenshot_path)) {
                    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'You must provide either a Transaction ID or a Screenshot as payment proof.'];
                    header('Location: /checkout');
                    exit();
                }
                
                // --- C. Calculate Totals from Session ---
                $cart_items_for_order = $_SESSION['cart'];
                $subtotal = array_reduce($cart_items_for_order, fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);
                $total_amount = $subtotal + SHIPPING_FEE;
                $prepayment_amount = $total_amount * PREPAYMENT_PERCENTAGE;

                // --- D. Insert Order into Database ---
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_fee, prepayment_amount, shipping_address, payment_method, payment_status, transaction_id, payment_screenshot, status) VALUES (?, ?, ?, ?, ?, 'bKash Prepayment', 'unpaid', ?, ?, 'processing')");
                $stmt->execute([$user['id'], $total_amount, SHIPPING_FEE, $prepayment_amount, $shipping_address, $transaction_id, $payment_screenshot_path]);
                $order_id = $pdo->lastInsertId();

                // --- E. [CRUCIAL FIX] Insert Order Items with variation_id ---
                $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, variation_id, quantity, price) VALUES (?, ?, ?, ?, ?)");
                $stock_stmt = $pdo->prepare("UPDATE product_variations SET stock = stock - ? WHERE id = ?");

                foreach ($cart_items_for_order as $variation_id => $item) {
                    $item_stmt->execute([$order_id, $item['product_id'], $variation_id, $item['quantity'], $item['price']]);
                    $stock_stmt->execute([$item['quantity'], $variation_id]);
                }
                
                // --- F. Commit Transaction ---
                $pdo->commit();
                
                // --- G. Send Notifications (ONLY AFTER SUCCESSFUL COMMIT) ---
                if (function_exists('sendOrderConfirmationSMS') && function_exists('sendTelegramNotification')) {
                    // 1. Send SMS to customer.
                    $customerMessage = "Dear Customer, your order #{$order_id} has been received. We will contact you for confirmation shortly. Thank you for choosing Cut2Buy.";
                    sendOrderConfirmationSMS($phone, $customerMessage);
                    
                    // 2. Prepare item list with variation details for Telegram.
                    $item_list_str = implode("\n  ", array_map(function($item) {
                        $details = $item['name'];
                        if (!empty($item['attributes'])) {
                            $details .= " ({$item['attributes']})";
                        }
                        $details .= " (Qty: {$item['quantity']})";
                        return "- " . $details;
                    }, $cart_items_for_order));
                    
                    $orderInfoString = "✅ New Order: #{$order_id}\n"
                                     . "👤 Name: {$user['name']}\n"
                                     . "📞 Phone: {$phone}\n"
                                     . "💰 Total: " . number_format($total_amount, 0) . "\n"
                                     . "💵 Prepaid: " . number_format($prepayment_amount, 0) . "\n  "
                                     . $item_list_str;
                    $trxInfoString = "TrxID: " . ($transaction_id ?: ($payment_screenshot_path ? 'Receipt Uploaded' : 'N/A'));
                    sendTelegramNotification($orderInfoString, $trxInfoString);
                }
                
                // --- H. Update User's Profile & Clear Session Data ---
                $update_user_stmt = $pdo->prepare("UPDATE users SET address = ?, phone = ? WHERE id = ?");
                $update_user_stmt->execute([$shipping_address, $phone, $user['id']]);

                unset($_SESSION['cart']);
                $_SESSION['last_order_id'] = $order_id;
                header('Location: /order-success');
                exit();

            } catch (Exception $e) {
                $pdo->rollBack();
                error_log("Order Placement Error: " . $e->getMessage());
                die('Sorry, there was an error placing your order. Please try again.');
            }
        }
        break;

    case '/order-success':
        $pageTitle = "Order Successful";
        $last_order_id = $_SESSION['last_order_id'] ?? null;
        if (!$last_order_id) {
            header('Location: /orders'); // Redirect to user's orders page if ID is lost
            exit();
        }
        unset($_SESSION['last_order_id']); // Clear it after use
        require 'views/checkout/success.view.php';
        break;

    case '/checkout':
    default:
        // If cart is empty, redirect to cart page which shows a user-friendly message.
        if (empty($_SESSION['cart'])) {
            header('Location: /cart');
            exit();
        }

        $pageTitle = "Checkout";
        // The cart session already contains all necessary details (including attributes)
        // thanks to the updated CartController. No extra query needed here.
        $cart_items = $_SESSION['cart'];
        
        // Calculate totals based on the reliable cart data in session.
        $subtotal = array_reduce($cart_items, fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);
        $total_amount = $subtotal + SHIPPING_FEE;
        $prepayment_amount = $total_amount * PREPAYMENT_PERCENTAGE;
        
        require 'views/checkout/index.view.php';
        break;
}