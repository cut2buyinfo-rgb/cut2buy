<?php
// File: /controllers/FlashSaleController.php
// Version: FINAL - Corrected to show the proper "old price".

$pageTitle = "Flash Sale";

try {
    // Get current time in the server's timezone, which is set to "Asia/Dhaka" in index.php.
    $now = date('Y-m-d H:i:s');

    // Fetch active flash sale products
    $stmt = $pdo->prepare("
        SELECT 
            p.id, p.name, p.slug,
            fs.flash_price,
            fs.end_time,
            
            -- THIS IS THE FIX --
            -- We now fetch the lowest 'old_price' from variations to show as the crossed-out price.
            -- If no 'old_price' exists, we fall back to the lowest regular price.
            IFNULL(
                (SELECT MIN(old_price) FROM product_variations WHERE product_id = p.id AND old_price IS NOT NULL),
                (SELECT MIN(price) FROM product_variations WHERE product_id = p.id)
            ) as old_price,
            
            (SELECT image_path FROM product_images WHERE product_id = p.id AND is_featured = 1 LIMIT 1) as image
        FROM flash_sales fs
        JOIN products p ON fs.product_id = p.id
        WHERE 
            fs.status = 'active' AND ? BETWEEN fs.start_time AND fs.end_time
        ORDER BY fs.end_time ASC
    ");
    $stmt->execute([$now]);
    $sale_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Flash Sale page error: " . $e->getMessage());
    $sale_products = []; // Ensure the variable exists even if the query fails
}

require ROOT_PATH . '/views/flash-sale.view.php';