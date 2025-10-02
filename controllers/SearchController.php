<?php
// File: /controllers/SearchController.php
// Version: ULTIMATE FINAL v13 (Handles both "All Products" and specific searches)

// --- Step 1: Include dependencies ---
require_once '/home/vol10_3/ezyro.com/ezyro_39909385/htdocs/includes/bootstrap.php';
require_once '/home/vol10_3/ezyro.com/ezyro_39909385/htdocs/includes/db.php';

// --- Step 2: Get all input from the URL ---
$search_query = trim($_GET['q'] ?? '');
$sort_order = $_GET['sort'] ?? 'relevance';
$min_price = !empty($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$max_price = !empty($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : null;

$sanitized_query = htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8');
$search_results = [];
$params = [];

// --- NEW (Step 3): Set Page Title and handle sort fallback based on context ---
if (!empty($search_query)) {
    $pageTitle = "Search results for '" . $sanitized_query . "'";
} else {
    $pageTitle = "All Products";
    // 'relevance' sort doesn't make sense without a search query, so fallback to 'newest'
    if ($sort_order === 'relevance') {
        $sort_order = 'newest';
    }
}

// --- Step 4: Build the SQL query dynamically ---
try {
    // Base query that joins products and variations
    $sql = "
        SELECT 
            p.id, p.name, p.slug, p.created_at,
            MIN(pv.price) as price,
            (SELECT image_path FROM product_images WHERE product_id = p.id AND is_featured = 1 LIMIT 1) as image
        FROM 
            products p
        LEFT JOIN 
            product_variations pv ON p.id = pv.product_id
    ";

    $where_clauses = [];
    
    // Always filter by published status
    $where_clauses[] = "p.status = 'published'";

    // **MODIFIED**: Conditionally add search keyword filters
    if (!empty($search_query)) {
        $keywords = array_filter(explode(' ', $search_query));
        $keyword_clauses = [];
        
        foreach ($keywords as $index => $word) {
            $param_name1 = ":keyword_name_" . $index;
            $param_name2 = ":keyword_desc_" . $index;
            
            $keyword_clauses[] = "(p.name LIKE {$param_name1} OR p.description LIKE {$param_name2})";
            
            $params[$param_name1] = '%' . $word . '%';
            $params[$param_name2] = '%' . $word . '%';
        }

        if (!empty($keyword_clauses)) {
            $where_clauses[] = "(" . implode(' AND ', $keyword_clauses) . ")";
        }
    }

    // Combine all WHERE conditions
    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(' AND ', $where_clauses);
    }
    
    // GROUP BY must come AFTER WHERE and is always needed
    $sql .= " GROUP BY p.id, p.name, p.slug, p.created_at";

    // Add Price Filtering using HAVING
    $having_clauses = [];
    if ($min_price !== null) {
        $having_clauses[] = "price >= :min_price";
        $params[':min_price'] = $min_price;
    }
    if ($max_price !== null) {
        $having_clauses[] = "price <= :max_price";
        $params[':max_price'] = $max_price;
    }
    if (!empty($having_clauses)) {
        $sql .= " HAVING " . implode(' AND ', $having_clauses);
    }
    
    // Add Sorting Logic
    switch ($sort_order) {
        case 'price_asc': 
            $sql .= " ORDER BY price ASC, p.name ASC"; 
            break;
        case 'price_desc': 
            $sql .= " ORDER BY price DESC, p.name ASC"; 
            break;
        case 'newest': 
            $sql .= " ORDER BY p.created_at DESC"; 
            break;
        default: // 'relevance' or any other value
            // Natural order from LIKE search is often good enough for relevance.
            // If no search query, this will default to the database's natural order.
            // Let's add a fallback sort for consistency.
            if (empty($search_query)) {
                $sql .= " ORDER BY p.created_at DESC";
            }
            // If there IS a search query, we don't add an ORDER BY here, letting the DB's text search ranking work.
            break;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // For production, log the error instead of displaying it
    error_log("Search Controller Error: " . $e->getMessage());
    die("<h1>Error</h1><p>Could not retrieve product data. Please try again later.</p>");
}

// --- Step 5: Load the view ---
require '/home/vol10_3/ezyro.com/ezyro_39909385/htdocs/views/search-results.view.php';