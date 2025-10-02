<?php
// File: /controllers/CategoryController.php
// Version: FINAL - Using a new view file to bypass stubborn server cache.

if (!empty($_GET['slug'])) {
    $category_slug = $_GET['slug'];
} elseif (isset($matches[1]) && !empty($matches[1])) {
    $category_slug = $matches[1];


    try {
        $stmt_cat = $pdo->prepare("SELECT id, name FROM categories WHERE slug = ? LIMIT 1");
        $stmt_cat->execute([$category_slug]); 
        $category = $stmt_cat->fetch(PDO::FETCH_ASSOC);

        if (!$category) {
            http_response_code(404);
            require ROOT_PATH . '/views/404.php';
            exit();
        }

        $pageTitle = "Products in " . htmlspecialchars($category['name']);
        $categoryId = $category['id'];

        $stmt_prod = $pdo->prepare("
            SELECT p.id, p.name, p.slug, MIN(pv.price) as price, MIN(pv.old_price) as old_price, img.image_path as image
            FROM products p
            LEFT JOIN product_variations pv ON p.id = pv.product_id
            LEFT JOIN product_images img ON p.id = img.product_id AND img.is_featured = 1
            WHERE p.status = 'published' AND p.category_id = ?
            GROUP BY p.id, p.name, p.slug, img.image_path
            ORDER BY p.created_at DESC
        ");
        $stmt_prod->execute([$categoryId]);
        $products = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);

        // --- FINAL SOLUTION: Load the new, un-cached view file ---
        require ROOT_PATH . '/views/category-display.view.php';

    } catch (PDOException $e) {
        error_log("Category page error: " . $e->getMessage());
        die("A database error occurred.");
    }
} else {
    $pageTitle = "All Categories";
    try {
        $stmt = $pdo->query("SELECT id, name, slug, image, description FROM categories ORDER BY name ASC");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // This view does not seem to have a caching issue, but we can change it too if needed.
        require ROOT_PATH . '/views/categories-all.view.php';
    } catch (PDOException $e) {
        error_log("All Categories page error: " . $e->getMessage());
        die("A database error occurred.");
    }
}