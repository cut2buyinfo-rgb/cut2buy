<?php
// File: /controllers/admin/AdminFlashSaleController.php
// Version: FINAL with full CRUD (Create, Read, Update, Delete)

if (!$user || !in_array($user['role'], ['admin', 'super_admin', 'saller'])) {
    http_response_code(403);
    die("<h1>403 Forbidden</h1>");
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Handle POST requests
if ($method === 'POST') {
    switch ($uri) {
        case '/admin/flash-sale/store':
            // ... (store logic remains the same) ...
            $product_id = (int)$_POST['product_id'];
            $flash_price = (float)$_POST['flash_price'];
            $start_time = $_POST['start_time'];
            $end_time = $_POST['end_time'];
            if ($product_id && $flash_price > 0 && $start_time && $end_time && $end_time > $start_time) {
                $stmt = $pdo->prepare("INSERT INTO flash_sales (product_id, flash_price, start_time, end_time, created_by) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$product_id, $flash_price, $start_time, $end_time, $user['id']]);
            }
            break;

        // --- NEW CASE TO HANDLE UPDATE ---
        case '/admin/flash-sale/update':
            $id = (int)$_POST['id'];
            $flash_price = (float)$_POST['flash_price'];
            $start_time = $_POST['start_time'];
            $end_time = $_POST['end_time'];

            $check_stmt = $pdo->prepare("SELECT created_by FROM flash_sales WHERE id = ?");
            $check_stmt->execute([$id]);
            $sale = $check_stmt->fetch();

            // Security check: Super admin can edit any sale, others can only edit their own.
            if ($id && $sale && ($user['role'] === 'super_admin' || $sale['created_by'] == $user['id'])) {
                if ($flash_price > 0 && $start_time && $end_time && $end_time > $start_time) {
                    $stmt = $pdo->prepare("UPDATE flash_sales SET flash_price = ?, start_time = ?, end_time = ? WHERE id = ?");
                    $stmt->execute([$flash_price, $start_time, $end_time, $id]);
                }
            }
            break;

        case '/admin/flash-sale/delete':
            // ... (delete logic remains the same) ...
            $id = (int)$_POST['id'];
            $check_stmt = $pdo->prepare("SELECT created_by FROM flash_sales WHERE id = ?");
            $check_stmt->execute([$id]);
            $sale = $check_stmt->fetch();
            if ($id && $sale && ($user['role'] === 'super_admin' || $sale['created_by'] == $user['id'])) {
                $stmt = $pdo->prepare("DELETE FROM flash_sales WHERE id = ?");
                $stmt->execute([$id]);
            }
            break;
    }
    header('Location: /admin/flash-sale');
    exit();
}

// --- HANDLE GET REQUESTS ---

// --- NEW LOGIC TO SHOW EDIT PAGE ---
if (preg_match('/^\/admin\/flash-sale\/edit\/(\d+)$/', $uri, $matches)) {
    $pageTitle = "Edit Flash Sale Item";
    $id = (int)$matches[1];

    $sql = "SELECT fs.*, p.name as product_name FROM flash_sales fs JOIN products p ON fs.product_id = p.id WHERE fs.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $sale_item = $stmt->fetch(PDO::FETCH_ASSOC);

    // Security check: can't edit if it doesn't exist or doesn't belong to the user (if not super_admin)
    if (!$sale_item || ($user['role'] !== 'super_admin' && $sale_item['created_by'] != $user['id'])) {
        http_response_code(404);
        die("<h1>404 Not Found or Access Denied</h1>");
    }

    require ROOT_PATH . '/views/admin/flash_sale/edit.view.php';
    exit(); // Stop here
}

// --- LOGIC TO SHOW THE MAIN LIST PAGE (remains the same) ---
$pageTitle = "Manage Flash Sale";
$sql = "SELECT fs.id, fs.flash_price, fs.start_time, fs.end_time, p.name as product_name, u.name as created_by_name
        FROM flash_sales fs
        JOIN products p ON fs.product_id = p.id
        JOIN users u ON fs.created_by = u.id";

if ($user['role'] !== 'super_admin') {
    $sql .= " WHERE fs.created_by = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user['id']]);
} else {
    $stmt = $pdo->query($sql);
}
$flash_sale_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($user['role'] === 'super_admin') {
    $products_to_add = $pdo->query("SELECT id, name FROM products WHERE status = 'published'")->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt_prod = $pdo->prepare("SELECT id, name FROM products WHERE status = 'published' AND vendor_id = ?");
    $stmt_prod->execute([$user['id']]);
    $products_to_add = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);
}

require ROOT_PATH . '/views/admin/flash_sale/index.view.php';