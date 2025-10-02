<?php
// File: /controllers/HomeController.php
// Version: FINAL - Now fetches Flash Sale products for the homepage.

// Fetch active banners (existing logic)
$banners_stmt = $pdo->query("SELECT * FROM banners WHERE status = 'active' ORDER BY sort_order ASC, created_at DESC");
$banners = $banners_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories for the filter (existing logic)
$categories_stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// --- NEW LOGIC: Fetch active Flash Sale products ---
try {
    $now = date('Y-m-d H:i:s');
    $flash_sale_stmt = $pdo->prepare("
        SELECT 
            p.id, p.name, p.slug,
            fs.flash_price,
            IFNULL(
                (SELECT MIN(old_price) FROM product_variations WHERE product_id = p.id AND old_price IS NOT NULL),
                (SELECT MIN(price) FROM product_variations WHERE product_id = p.id)
            ) as old_price,
            (SELECT image_path FROM product_images WHERE product_id = p.id AND is_featured = 1 LIMIT 1) as image
        FROM flash_sales fs
        JOIN products p ON fs.product_id = p.id
        WHERE fs.status = 'active' AND ? BETWEEN fs.start_time AND fs.end_time
        ORDER BY fs.end_time ASC
        LIMIT 5 -- Show a maximum of 5 flash sale products on the homepage
    ");
    $flash_sale_stmt->execute([$now]);
    $flash_sale_products = $flash_sale_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Homepage Flash Sale Error: " . $e->getMessage());
    $flash_sale_products = []; // Ensure variable exists
}

$pageTitle = "Online Shopping in Bangladesh - Best Deals at Cut2Buy";

$metaDescription = "Find the best deals on a wide range of products at Cut2Buy, the leading online shopping destination in Bangladesh. Enjoy fast delivery and secure payment options. Shop now!";

$canonicalUrl = "https://cut2buy.unaux.com/";

require 'views/home.view.php';