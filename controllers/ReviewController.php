<?php
// File: /controllers/ReviewController.php
// Version: FINAL (Corrected according to your 'product_reviews' table structure)

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

$user_id = $_SESSION['user_id'];
$pageTitle = "My Reviews"; // For the active state in the sidebar

// --- Fetch all reviews using the CORRECT table and column names ---
try {
    $stmt = $pdo->prepare("
        SELECT 
            pr.id,
            pr.rating,
            pr.review_text,
            pr.review_image,
            pr.created_at,
            p.name AS product_name, 
            p.slug AS product_slug,
            (SELECT image_path FROM product_images WHERE product_id = pr.product_id ORDER BY is_featured DESC, id ASC LIMIT 1) AS product_image
        FROM 
            product_reviews pr -- Correct table name
        JOIN 
            products p ON pr.product_id = p.id
        WHERE 
            pr.user_id = ?
        ORDER BY 
            pr.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error fetching reviews: " . $e.getMessage());
}

// Data needed for the user dashboard layout
$current_user = [
    'id' => $_SESSION['user_id'],
    'name' => $_SESSION['user_name'] ?? 'User',
    'role' => $_SESSION['user_role'] ?? 'user'
];

// Load the view file
require 'views/reviews/index.view.php';