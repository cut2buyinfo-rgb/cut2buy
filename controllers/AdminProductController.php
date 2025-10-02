<?php
// File: /controllers/AdminProductController.php
// Version: FINAL - Corrected "Invalid parameter number" bug.

require_once __DIR__ . '/../includes/bootstrap.php';
require_once ROOT_PATH . '/includes/ImageHasher.php';

// Security Check
if (!$user || !in_array($user['role'], ['admin', 'super_admin'])) {
    http_response_code(403);
    die("<h1>403 Forbidden</h1>");
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Helper function (remains unchanged)
function getFormDataForAdmin($pdo, $product_id = null) {
    global $user; $data = []; $data['categories'] = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC); $data['brands'] = $pdo->query("SELECT id, name FROM brands ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC); $data['vendors'] = ($user['role'] === 'super_admin') ? $pdo->query("SELECT id, name FROM users WHERE role IN ('saller', 'admin')")->fetchAll(PDO::FETCH_ASSOC) : []; if ($product_id) { $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?"); $stmt->execute([$product_id]); $data['product'] = $stmt->fetch(PDO::FETCH_ASSOC); if (!$data['product']) return false; $var_stmt = $pdo->prepare("SELECT pv.*, GROUP_CONCAT(CONCAT(a.id, ':', av.value) SEPARATOR ';') as attributes_str FROM product_variations pv LEFT JOIN product_variation_attributes pva ON pv.id = pva.variation_id LEFT JOIN attribute_values av ON pva.attribute_value_id = av.id LEFT JOIN attributes a ON av.attribute_id = a.id WHERE pv.product_id = ? GROUP BY pv.id ORDER BY pv.id ASC"); $var_stmt->execute([$product_id]); $variations_raw = $var_stmt->fetchAll(PDO::FETCH_ASSOC); $grouped_variations = []; $color_map = []; $COLOR_ATTRIBUTE_ID = 1; $SIZE_ATTRIBUTE_ID = 2; foreach ($variations_raw as $variation) { $attributes = []; if (!empty($variation['attributes_str'])) { foreach (explode(';', $variation['attributes_str']) as $attr_pair) { list($id, $value) = explode(':', $attr_pair, 2); $attributes[(int)$id] = $value; } } $color_name = $attributes[$COLOR_ATTRIBUTE_ID] ?? 'N/A'; $size_name = $attributes[$SIZE_ATTRIBUTE_ID] ?? 'N/A'; if (!isset($color_map[$color_name])) { $grouped_variations[] = ['color' => $color_name, 'sizes' => []]; $color_map[$color_name] = count($grouped_variations) - 1; } $group_index = $color_map[$color_name]; $grouped_variations[$group_index]['sizes'][] = ['name' => $size_name, 'details' => $variation]; } $data['variations'] = $grouped_variations; $img_stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY is_featured DESC, id ASC"); $img_stmt->execute([$product_id]); $data['images'] = $img_stmt->fetchAll(PDO::FETCH_ASSOC); } return $data;
}

// Handle POST Requests
if ($method === 'POST') {
    $image_dir = ROOT_PATH . '/assets/images/products/';

    function generateSku($product_id) { return 'C2B-P' . $product_id . '-V' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4)); }

    // --- THIS IS THE CORRECTED FUNCTION ---
    function getAttributeValueId($pdo, $attribute_id, $value) {
        $trimmed_value = trim($value);
        $stmt = $pdo->prepare("SELECT id FROM attribute_values WHERE attribute_id = ? AND value = ?");
        // It now correctly passes both parameters to execute()
        $stmt->execute([$attribute_id, $trimmed_value]);
        $id = $stmt->fetchColumn();
        if (!$id) {
            $stmt = $pdo->prepare("INSERT INTO attribute_values (attribute_id, value) VALUES (?, ?)");
            $stmt->execute([$attribute_id, $trimmed_value]);
            $id = $pdo->lastInsertId();
        }
        return $id;
    }

    switch ($uri) {
        case '/admin/products/store':
            $pdo->beginTransaction();
            try {
                $vendor_id = ($user['role'] === 'super_admin' && !empty($_POST['vendor_id'])) ? $_POST['vendor_id'] : $user['id'];
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['name'])));
                
                $stmt = $pdo->prepare("INSERT INTO products (name, slug, description, category_id, brand_id, vendor_id, status, warranty_info) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$_POST['name'], $slug, $_POST['description'], $_POST['category_id'], ($_POST['brand_id'] ?: null), $vendor_id, $_POST['status'] ?? 'draft', trim($_POST['warranty_info'] ?? '')]);
                $product_id = $pdo->lastInsertId();

                if (!empty($_POST['variations'])) {
                    foreach ($_POST['variations'] as $group) {
                        if (empty($group['color']) || empty($group['sizes'])) continue;
                        foreach ($group['sizes'] as $sizeData) {
                            $sku = !empty(trim($sizeData['sku'])) ? trim($sizeData['sku']) : generateSku($product_id);
                            $var_stmt = $pdo->prepare("INSERT INTO product_variations (product_id, price, old_price, sku, stock) VALUES (?, ?, ?, ?, ?)");
                            $var_stmt->execute([$product_id, $sizeData['price'], $sizeData['old_price'] ?: null, $sku, $sizeData['stock']]);
                            $variation_id = $pdo->lastInsertId();
                            
                            $color_attr_id = getAttributeValueId($pdo, 1, $group['color']); // 1 = Color Attribute ID
                            $size_attr_id = getAttributeValueId($pdo, 2, $sizeData['name']); // 2 = Size Attribute ID
                            
                            $pdo->prepare("INSERT INTO product_variation_attributes (variation_id, attribute_value_id) VALUES (?, ?)")->execute([$variation_id, $color_attr_id]);
                            $pdo->prepare("INSERT INTO product_variation_attributes (variation_id, attribute_value_id) VALUES (?, ?)")->execute([$variation_id, $size_attr_id]);
                        }
                    }
                }
                
                if (isset($_POST['uploaded_images']) && is_array($_POST['uploaded_images'])) {
                    $hasher = new ImageHasher();
                    foreach ($_POST['uploaded_images'] as $key => $filename) {
                        if (!empty($filename)) {
                            $hash = $hasher->hash($image_dir . $filename);
                            $img_stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_path, is_featured, image_hash) VALUES (?, ?, ?, ?)");
                            $img_stmt->execute([$product_id, $filename, ($key == 0) ? 1 : 0, $hash]);
                        }
                    }
                }
                
                $pdo->commit();
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Product has been created successfully.'];
                header('Location: /admin/products');
                exit();
            } catch (Exception $e) {
                $pdo->rollBack();
                // Show the specific error message for debugging
                die("Failed to create product: " . $e->getMessage());
            }
            break;

        case '/admin/products/update':
            // Update logic will be added here later
            break;

        case '/admin/products/delete':
            $product_id = $_POST['product_id'] ?? null;
            if (!$product_id) {
                header('Location: /admin/products');
                exit();
            }
            $pdo->beginTransaction();
            try {
                $img_stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
                $img_stmt->execute([$product_id]);
                $images_to_delete = $img_stmt->fetchAll(PDO::FETCH_COLUMN);
                $delete_stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
                $delete_stmt->execute([$product_id]);
                foreach ($images_to_delete as $image_path) {
                    $file_path = $image_dir . $image_path;
                    if (file_exists($file_path)) {
                        @unlink($file_path);
                    }
                }
                $pdo->commit();
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Product has been deleted successfully.'];
            } catch (Exception $e) {
                $pdo->rollBack();
                $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Failed to delete product.'];
            }
            header('Location: /admin/products');
            exit();
            break;
    }
}

// Handle GET Requests
if ($method === 'GET') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($path === '/admin/products') {
        $pageTitle = "Manage Products";
        $products_query = "SELECT p.*, c.name AS category_name, b.name AS brand_name, u.name AS vendor_name, pv_summary.min_price, pv_summary.max_price, pv_summary.total_stock, (SELECT image_path FROM product_images WHERE product_id = p.id AND is_featured = 1 LIMIT 1) as featured_image FROM products p LEFT JOIN categories c ON p.category_id = c.id LEFT JOIN brands b ON p.brand_id = b.id LEFT JOIN users u ON p.vendor_id = u.id LEFT JOIN ( SELECT product_id, MIN(price) AS min_price, MAX(price) AS max_price, SUM(stock) AS total_stock FROM product_variations GROUP BY product_id ) AS pv_summary ON p.id = pv_summary.product_id ORDER BY p.created_at DESC";
        $products = $pdo->query($products_query)->fetchAll(PDO::FETCH_ASSOC);
        require ROOT_PATH . '/views/admin/products/index.view.php';
        exit();
    } 
    if ($path === '/admin/products/create') {
        $pageTitle = "Add New Product";
        $data = getFormDataForAdmin($pdo);
        extract($data);
        require ROOT_PATH . '/views/admin/products/create.view.php';
        exit();
    } 
    if (preg_match('/^\/admin\/products\/edit\/(\d+)\/?$/', $path, $matches)) {
        $pageTitle = "Edit Product";
        $product_id = $matches[1];
        $formData = getFormDataForAdmin($pdo, $product_id);
        if (!$formData) { 
            http_response_code(404); 
            die("Product not found."); 
        }
        extract($formData);
        require ROOT_PATH . '/views/admin/products/edit.view.php';
        exit();
    }
}