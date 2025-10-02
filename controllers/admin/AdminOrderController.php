<?php
// controllers/admin/AdminOrderController.php
// Version: FINAL & CORRECT - Based on your code, with variation details integrated.

// Security Check
if (!$user || !in_array($user['role'], ['admin', 'super_admin'])) {
    http_response_code(403);
    die("<h1>403 Forbidden</h1>");
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// --- Handle POST Requests (Status Updates) ---
if ($method === 'POST' && $path === '/admin/orders/update-status') {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    $tracking_number = trim($_POST['tracking_number'] ?? '');
    $tracking_url = trim($_POST['tracking_url'] ?? '');
    
    try {
        $sql = "UPDATE orders SET status = ?";
        $params = [$new_status];
        
        if ($new_status === 'shipped') {
            $sql .= ", tracking_number = ?, tracking_url = ?";
            $params[] = $tracking_number;
            $params[] = $tracking_url;
        }

        if ($new_status === 'delivered') {
            $sql .= ", payment_status = 'paid'";
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $order_id;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        header('Location: /admin/orders/view/' . $order_id . '?status=updated');
        exit();

    } catch (PDOException $e) {
        die("Error updating order status: " . $e->getMessage());
    }
}

// --- Handle GET Requests (Display Pages) ---

// Route for Viewing a Single Order
if (preg_match('/^\/admin\/orders\/view\/(\d+)$/', $path, $matches)) {
    $order_id = $matches[1];
    $pageTitle = "Order Details #" . $order_id;

    try {
        // Fetch order details (Unchanged)
        $order_stmt = $pdo->prepare("SELECT o.*, u.name as user_name, u.email as user_email, u.phone as user_phone FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
        $order_stmt->execute([$order_id]);
        $order = $order_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) { die('Order not found.'); }

        // --- [THE CRUCIAL FIX IS HERE] Fetch items for this order WITH their variation details ---
        $items_stmt = $pdo->prepare("
            SELECT 
                oi.*, 
                p.name as product_name, 
                p.slug as product_slug,
                (
                    SELECT GROUP_CONCAT(CONCAT(a.name, ': ', av.value) ORDER BY a.name SEPARATOR ', ')
                    FROM product_variation_attributes pva
                    JOIN attribute_values av ON pva.attribute_value_id = av.id
                    JOIN attributes a ON av.attribute_id = a.id
                    WHERE pva.variation_id = oi.variation_id
                ) as variation_details
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?
        ");
        $items_stmt->execute([$order_id]);
        $order_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require_once ROOT_PATH . '/views/admin/orders/view.view.php';
        exit();

    } catch (PDOException $e) {
        die("Error fetching order details: " . $e->getMessage());
    }
}


// Default: Show Order List (Index)
$pageTitle = "Manage Orders";
try {
    $orders = $pdo->query("SELECT o.id, o.total_amount, o.status, o.payment_status, o.created_at, u.name AS user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC")->fetchAll(PDO::FETCH_ASSOC);
    
    require_once ROOT_PATH . '/views/admin/orders/index.view.php';

} catch (PDOException $e) {
    die("Error fetching orders: ". $e->getMessage());
}