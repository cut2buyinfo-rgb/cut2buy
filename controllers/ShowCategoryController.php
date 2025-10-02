

<?php
// File: /controllers/ShowCategoryController.php

// --- Scenario 1: For showing a specific category ---
// This block runs if the URL is in the format /category/some-slug
if (isset($matches[1]) && !empty($matches[1])) {
    
    // Clean the slug obtained from the URL to remove any extra spaces
    $category_slug = trim($matches[1]);

    try {
        // Find the specific category from the database using the slug
        $stmt_cat = $pdo->prepare("SELECT id, name, slug FROM categories WHERE slug = ? LIMIT 1");
        $stmt_cat->execute([$category_slug]); 
        $category = $stmt_cat->fetch(PDO::FETCH_ASSOC);

        // If no category is found with that slug, show a 404 page
        if (!$category) {
            http_response_code(404);
            require ROOT_PATH . '/views/404.php';
            exit();
        }

        // --- SEO OPTIMIZATION START ---
        // Set the page title, meta description, and canonical URL
        $pageTitle  = htmlspecialchars($category['name']) . " - Shop Online in Bangladesh";
        $metaDescription = "Explore our collection of " . htmlspecialchars($category['name']) . ". Find the best prices and deals on Cut2Buy, your trusted online store in Bangladesh.";
        $canonicalUrl = "https://cut2buy.unaux.com/category/" . htmlspecialchars($category['slug']);
        // --- SEO OPTIMIZATION END ---
        
        $categoryId = $category['id'];

        // Find all products that belong to this category
        $stmt_prod = $pdo->prepare("
            SELECT 
                p.id, 
                p.name, 
                p.slug, 
                MIN(pv.price)      AS price, 
                MIN(pv.old_price)  AS old_price, 
                img.image_path     AS image
            FROM products p
            LEFT JOIN product_variations pv ON p.id = pv.product_id
            LEFT JOIN (
                SELECT product_id, image_path 
                FROM product_images 
                WHERE is_featured = 1 
                GROUP BY product_id
            ) img ON p.id = img.product_id
            WHERE p.status = 'published' AND p.category_id = ?
            GROUP BY p.id, p.name, p.slug, img.image_path
            ORDER BY p.created_at DESC
        ");
        $stmt_prod->execute([$categoryId]);
        $products = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);

        // Load the view file to display the products
        require ROOT_PATH . '/views/category-products.view.php';

    } catch (PDOException $e) {
        // Handle any database errors
        error_log("Category page error: " . $e->getMessage());
        http_response_code(500);
        die("A database error occurred.");
    }

} else {
    // --- Scenario 2: For showing all categories ---
    // This block runs if the URL is just /categories
    
    // --- SEO OPTIMIZATION START ---
    $pageTitle = "All Product Categories - Cut2Buy Bangladesh";
    $metaDescription = "Browse all product categories available at Cut2Buy. From electronics to fashion, find everything you need in one place. Shop online in Bangladesh.";
    $canonicalUrl = "https://cut2buy.unaux.com/categories";
    // --- SEO OPTIMIZATION END ---

    try {
        // Fetch all categories from the database
        $stmt = $pdo->query("SELECT id, name, slug, image, description FROM categories ORDER BY name ASC");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Load the view file for showing all categories
        require ROOT_PATH . '/views/categories-all.view.php';

    } catch (PDOException $e) {
        // Handle any database errors
        error_log("All Categories page error: " . $e->getMessage());
        http_response_code(500);
        die("A database error occurred.");
    }
}



