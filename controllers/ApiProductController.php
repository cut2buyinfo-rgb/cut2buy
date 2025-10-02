<?php
// File: /controllers/ApiProductController.php
// Version: COMPLETE - Now integrates Flash Sale prices.

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if a specific product ID is requested for the Quick View Modal
if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
    
    // --- PART 1: SERVE SINGLE PRODUCT DETAILS FOR QUICK VIEW ---
    $product_id = (int)$_GET['product_id'];
    
    try {
        // --- NEW: Check for an active flash sale for this specific product ---
        $now = date('Y-m-d H:i:s');
        $flash_sale_stmt = $pdo->prepare(
            "SELECT flash_price FROM flash_sales 
             WHERE product_id = ? AND status = 'active' AND ? BETWEEN start_time AND end_time 
             LIMIT 1"
        );
        $flash_sale_stmt->execute([$product_id, $now]);
        $flash_sale = $flash_sale_stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch variations (original query is fine)
        $stmt_variations = $pdo->prepare("
            SELECT pv.id, pv.price, pv.old_price, pv.sku, pv.stock,
                   GROUP_CONCAT(CONCAT_WS(':', a.name, av.value) SEPARATOR ';') AS attributes
            FROM product_variations pv
            LEFT JOIN product_variation_attributes pva ON pv.id = pva.variation_id
            LEFT JOIN attribute_values av ON pva.attribute_value_id = av.id
            LEFT JOIN attributes a ON av.attribute_id = a.id
            WHERE pv.product_id = ? GROUP BY pv.id ORDER BY pv.id
        ");
        $stmt_variations->execute([$product_id]);
        $variations_raw = $stmt_variations->fetchAll(PDO::FETCH_ASSOC);
        
        $response = [
            'variations' => [],
            'options' => [],
            'flash_sale_price' => $flash_sale ? (float)$flash_sale['flash_price'] : null // Add flash price to response
        ];

        foreach ($variations_raw as $var) {
            $attributes_array = [];
            if (!empty($var['attributes'])) {
                $attrs = explode(';', $var['attributes']);
                foreach ($attrs as $attr) {
                    list($key, $value) = explode(':', $attr, 2);
                    $attributes_array[$key] = $value;
                    if (!isset($response['options'][$key])) { $response['options'][$key] = []; }
                    if (!in_array($value, $response['options'][$key])) { $response['options'][$key][] = $value; }
                }
            }
            $var['attributes'] = $attributes_array;
            $response['variations'][] = $var;
        }
        
        echo json_encode($response);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error while fetching product details.']);
    }
    exit();

} else {
    
    // --- PART 2: SERVE PRODUCT LIST FOR HOMEPAGE ---
    $categoryId = isset($_GET['category_id']) && is_numeric($_GET['category_id']) ? (int)$_GET['category_id'] : 'all';
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 9;
    $offset = ($page - 1) * $perPage;

    try {
        $now = date('Y-m-d H:i:s');
        // --- REVISED QUERY WITH FLASH SALE LOGIC ---
        // We use IFNULL() and a LEFT JOIN to prioritize the flash sale price.
        // If a flash sale price exists and is active, use it. Otherwise, use the regular price.
        $sql = "
            SELECT
                p.id, p.name, p.slug,
                (SELECT MIN(pv.old_price) FROM product_variations pv WHERE pv.product_id = p.id) as old_price,
                (SELECT pi.image_path FROM product_images pi WHERE pi.product_id = p.id AND pi.is_featured = 1 LIMIT 1) as image,
                -- Main Price Logic:
                IFNULL(fs.flash_price, (SELECT MIN(pv.price) FROM product_variations pv WHERE pv.product_id = p.id)) as price,
                -- Flag to indicate if the product is on sale
                CASE WHEN fs.flash_price IS NOT NULL THEN 1 ELSE 0 END as is_on_flash_sale
            FROM products p
            LEFT JOIN flash_sales fs ON p.id = fs.product_id 
                AND fs.status = 'active' 
                AND :now BETWEEN fs.start_time AND fs.end_time
            WHERE p.status = 'published'
        ";

        if ($categoryId !== 'all') {
            $sql .= " AND p.category_id = :category_id";
        }
        $sql .= " GROUP BY p.id ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':now', $now, PDO::PARAM_STR);
        if ($categoryId !== 'all') {
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        }
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($products) && isset($_SESSION['wishlist'])) {
            foreach ($products as $key => $product) {
                $products[$key]['in_wishlist'] = isset($_SESSION['wishlist'][$product['id']]);
            }
        }

        echo json_encode($products);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error while fetching product list.']);
    }
    exit();
}