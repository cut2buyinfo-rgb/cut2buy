<?php
// File: /controllers/ProductDetailController.php
// Version: FINAL with "Can Review" Logic Included.

// Define constants needed by the view file.
define('SHIPPING_FEE', 65.00);

// The router in index.php guarantees that $matches[1] contains the slug.
$slug = $matches[1];

try {
    // === QUERY 1: Fetch main product details ===
    $stmt_product = $pdo->prepare("
        SELECT p.*, c.name as category_name, b.name as brand_name, u.name as vendor_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN brands b ON p.brand_id = b.id
        LEFT JOIN users u ON p.vendor_id = u.id
        WHERE p.slug = ? AND p.status = 'published'
    ");
    $stmt_product->execute([$slug]);
    $product = $stmt_product->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        http_response_code(404);
        require 'views/404.php';
        exit();
    }

    $product_id = $product['id'];
    $pageTitle = htmlspecialchars($product['name']);

$metaDescription = "Buy " . htmlspecialchars($product['name']) . " at the best price in Bangladesh. Check out specs, reviews, and enjoy fast delivery from Cut2Buy.";
    $canonicalUrl = "https://cut2buy.unaux.com/product/" . htmlspecialchars($product['slug']);


    // === QUERY 2: Fetch all images ===
    $stmt_images = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ? ORDER BY is_featured DESC, id ASC");
    $stmt_images->execute([$product_id]);
    $product_images = $stmt_images->fetchAll(PDO::FETCH_COLUMN);

    // === QUERY 3: Fetch all variations and process for JS ===
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
    
    $variations = [];
    $available_options = []; 
    foreach ($variations_raw as $var) {
        $attributes_array = [];
        if (!empty($var['attributes'])) {
            $attrs = explode(';', $var['attributes']);
            foreach ($attrs as $attr) {
                list($key, $value) = explode(':', $attr, 2);
                $attributes_array[$key] = $value;
                if (!isset($available_options[$key])) { $available_options[$key] = []; }
                if (!in_array($value, $available_options[$key])) { $available_options[$key][] = $value; }
            }
        }
        $var['attributes'] = $attributes_array;
        $variations[] = $var;
    }

    // === QUERY 4: Fetch related products ===
    $stmt_related = $pdo->prepare("
        SELECT p.id, p.name, p.slug, 
               (SELECT image_path FROM product_images WHERE product_id = p.id AND is_featured = 1 LIMIT 1) as image,
               (SELECT MIN(price) FROM product_variations WHERE product_id = p.id) as price
        FROM products p
        WHERE p.category_id = ? AND p.id != ? AND p.status = 'published' LIMIT 5
    ");
    $stmt_related->execute([$product['category_id'], $product_id]);
    $related_products = $stmt_related->fetchAll(PDO::FETCH_ASSOC);

    // === QUERY 5: Fetch Questions and Answers ===
    $qna_stmt = $pdo->prepare("
        SELECT q.*, u_asker.name as asker_name, u_answerer.name as answerer_name
        FROM product_qna q
        JOIN users u_asker ON q.user_id = u_asker.id
        LEFT JOIN users u_answerer ON q.answered_by = u_answerer.id
        WHERE q.product_id = ? ORDER BY q.created_at DESC
    ");
    $qna_stmt->execute([$product_id]);
    $qna = $qna_stmt->fetchAll(PDO::FETCH_ASSOC);

    // === QUERY 6: Fetch Reviews ===
    $reviews_stmt = $pdo->prepare("
        SELECT r.*, u.name as user_name
        FROM product_reviews r
        JOIN users u ON r.user_id = u.id
        WHERE r.product_id = ? ORDER BY r.created_at DESC
    ");
    $reviews_stmt->execute([$product_id]);
    $reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);

   // === QUERY 7: Check if the current user is eligible to review this product ===
$can_review = false;
if ($user) { // à¦¶à§à¦§à§à¦®à¦¾à¦¤à§à¦° à¦²à¦à¦à¦¨ à¦à¦°à¦¾ à¦¬à§à¦¯à¦¬à¦¹à¦¾à¦°à¦à¦¾à¦°à§à¦° à¦à¦¨à§à¦¯ à¦à§à¦ à¦à¦°à§à¦¨

    // à¦§à¦¾à¦ª à§§: à¦¬à§à¦¯à¦¬à¦¹à¦¾à¦°à¦à¦¾à¦°à§ à¦ªà¦£à§à¦¯à¦à¦¿ à¦à¦¿à¦¨à§à¦à§ à¦à¦¬à¦ à¦¡à§à¦²à¦¿à¦­à¦¾à¦°à¦¿ à¦ªà§à¦¯à¦¼à§à¦à§ à¦à¦¿à¦¨à¦¾ à¦¤à¦¾ à¦ªà¦°à§à¦à§à¦·à¦¾ à¦à¦°à§à¦¨
    $order_check_stmt = $pdo->prepare("
        SELECT o.id
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        WHERE o.user_id = ? 
          AND oi.product_id = ?
          AND o.status = 'delivered'
        LIMIT 1
    ");
    $order_check_stmt->execute([$user['id'], $product_id]);
    
    // à¦¯à¦¦à¦¿ à¦¤à¦¿à¦¨à¦¿ à¦ªà¦£à§à¦¯à¦à¦¿ à¦à¦¿à¦¨à§ à¦¥à¦¾à¦à§à¦¨
    if ($order_check_stmt->fetch()) {
        
        // à¦§à¦¾à¦ª à§¨: à¦¬à§à¦¯à¦¬à¦¹à¦¾à¦°à¦à¦¾à¦°à§ à¦à¦¤à¦¿à¦®à¦§à§à¦¯à§ à¦à¦ à¦ªà¦£à§à¦¯à§à¦° à¦à¦¨à§à¦¯ à¦°à¦¿à¦­à¦¿à¦ à¦¦à¦¿à¦¯à¦¼à§à¦à§à¦¨ à¦à¦¿à¦¨à¦¾ à¦¤à¦¾ à¦ªà¦°à§à¦à§à¦·à¦¾ à¦à¦°à§à¦¨
        $existing_review_stmt = $pdo->prepare("
            SELECT id FROM product_reviews WHERE user_id = ? AND product_id = ? LIMIT 1
        ");
        $existing_review_stmt->execute([$user['id'], $product_id]);

        // à¦¯à¦¦à¦¿ à¦à§à¦¨à§ à¦°à¦¿à¦­à¦¿à¦ à¦¨à¦¾ à¦ªà¦¾à¦à¦¯à¦¼à¦¾ à¦¯à¦¾à¦¯à¦¼, à¦¤à¦¬à§à¦ à¦¤à¦¿à¦¨à¦¿ à¦°à¦¿à¦­à¦¿à¦ à¦¦à¦¿à¦¤à§ à¦ªà¦¾à¦°à¦¬à§à¦¨
        if (!$existing_review_stmt->fetch()) {
            $can_review = true;
        }
    }
}
    // Finally, load the view. All variables are now ready for it.
    require 'views/product-detail.view.php';

} catch (PDOException $e) {
    error_log("Product Detail Page Error: " . $e->getMessage());
    http_response_code(500);
    die("A database error occurred. Please try again later.");
}