<?php
// File: /controllers/ImageSearchController.php
// Version: ULTIMATE FINAL - With 50% Matching, WebP Conversion, and Auto-Cleanup

// --- Step 1: Include dependencies ---
// Using ROOT_PATH constant from bootstrap for better path management
require_once '/home/vol10_3/ezyro.com/ezyro_39909385/htdocs/includes/bootstrap.php';
require_once ROOT_PATH . '/includes/db.php';
require_once ROOT_PATH . '/includes/ImageHasher.php';

// --- NEW (Part 3): Auto-delete old search images (Poor Man's Cron Job) ---
function deleteOldSearchImages() {
    $search_image_dir = ROOT_PATH . '/assets/images/image-searches/';
    $max_age_seconds = 7 * 24 * 60 * 60; // 7 days in seconds

    if (!is_dir($search_image_dir)) {
        return; // Directory doesn't exist
    }

    foreach (glob($search_image_dir . '*') as $file) {
        if (is_file($file) && (time() - filemtime($file)) > $max_age_seconds) {
            @unlink($file); // Delete the old file
        }
    }
}

// Run the cleanup function at the start of every image search
deleteOldSearchImages();

// --- Step 2: Security & Pre-checks ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['search_image']) || $_FILES['search_image']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Image upload failed. Please try again.'];
    header('Location: /');
    exit();
}

// --- Step 3: File Upload Validation ---
$file = $_FILES['search_image'];
$allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']; // Added GIF for broader support
$file_info = getimagesize($file['tmp_name']);
if (!$file_info || !in_array($file_info['mime'], $allowed_mimes)) {
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Invalid file type. Please upload a JPG, PNG, or WebP file.'];
    header('Location: /');
    exit();
}

// --- NEW (Part 2): Convert uploaded image to compressed WebP and save it ---
$search_image_dir = ROOT_PATH . '/assets/images/image-searches/';
if (!is_dir($search_image_dir)) {
    mkdir($search_image_dir, 0755, true); // Create directory if it doesn't exist
}

// Create image resource from uploaded file
$source_image = null;
switch ($file_info['mime']) {
    case 'image/jpeg':
        $source_image = imagecreatefromjpeg($file['tmp_name']);
        break;
    case 'image/png':
        $source_image = imagecreatefrompng($file['tmp_name']);
        break;
    case 'image/gif':
        $source_image = imagecreatefromgif($file['tmp_name']);
        break;
    case 'image/webp':
        $source_image = imagecreatefromwebp($file['tmp_name']);
        break;
}

if (!$source_image) {
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Could not process the uploaded image.'];
    header('Location: /');
    exit();
}

// Define the path for the new WebP image
$unique_filename = 'img-search-' . uniqid() . '.webp';
$webp_image_path = $search_image_dir . $unique_filename;

// Save the image as WebP with 75% quality
imagewebp($source_image, $webp_image_path, 75);
imagedestroy($source_image);


// --- Step 4: Calculate Hash of the NEWLY CREATED WebP Image ---
$hasher = new ImageHasher();
// Use the path of the newly saved WebP image for hashing
$user_image_hash = $hasher->hash($webp_image_path);

if (!$user_image_hash) {
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Could not process the uploaded image. It might be invalid or corrupted.'];
    header('Location: /');
    exit();
}

// --- Step 5: Find Similar Images in the Database ---
$search_results = [];
$pageTitle = "Image Search Results";
$sanitized_query = "products similar to your uploaded image";

try {
    $stmt = $pdo->prepare("
        SELECT 
            pi.image_hash, p.id, p.name, p.slug, p.created_at,
            (SELECT MIN(price) FROM product_variations WHERE product_id = p.id) as price,
            pi.image_path as image
        FROM product_images pi
        JOIN products p ON pi.product_id = p.id
        WHERE pi.image_hash IS NOT NULL AND pi.image_hash != '' AND p.status = 'published'
    ");
    $stmt->execute();
    $all_db_images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- CHANGED (Part 1): Increased threshold for "50% matching" ---
    $threshold = 20; // Increased from 12. Adjust this value (18-25) to get desired results.
    $products_found = [];

    foreach ($all_db_images as $db_image) {
        $distance = ImageHasher::distance($user_image_hash, $db_image['image_hash']);

        if ($distance <= $threshold) {
            $product_id = $db_image['id'];
            if (!isset($products_found[$product_id]) || $distance < $products_found[$product_id]['distance']) {
                $products_found[$product_id] = [
                    'distance' => $distance,
                    'product_data' => $db_image
                ];
            }
        }
    }

    usort($products_found, function ($a, $b) {
        return $a['distance'] <=> $b['distance'];
    });

    foreach ($products_found as $match) {
        $search_results[] = $match['product_data'];
    }

} catch (\PDOException $e) {
    error_log("Image search DB error: " . $e->getMessage());
}

// --- Step 6: Load the search results view ---
$search_query = 'image search';
$sort_order = 'relevance';
$min_price = null;
$max_price = null;

require ROOT_PATH . '/views/search-results.view.php';