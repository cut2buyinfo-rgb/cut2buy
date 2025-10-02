<?php
// File: /includes/bootstrap.php
// Version: OPTIMIZED WITH CACHING

// --- 1. Basic Setup ---
ini_set("display_errors", 1);
error_reporting(E_ALL);
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
date_default_timezone_set("Asia/Dhaka");

// --- 2. Start Session ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- 3. Database Connection ---
require_once ROOT_PATH . '/includes/db.php';

// --- 4. Define Global Variables from Session ---
$user = null;
if (isset($_SESSION['user_id'])) {
    $user = [
        'id'   => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? 'User',
        'role' => $_SESSION['user_role'] ?? 'user'
    ];
}

$cartItemCount = count($_SESSION['cart'] ?? []);
$wishlistItemCount = count($_SESSION['wishlist'] ?? []);

// --- 5. [OPTIMIZED] Function to get categories with file-based caching ---
function get_global_categories_cached($pdo, $cache_duration = 3600) { // Cache for 1 hour
    $cache_file = ROOT_PATH . '/cache/global_categories.cache';

    // Check if a valid cache file exists
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_duration) {
        // If yes, read from the cache file
        return json_decode(file_get_contents($cache_file), true);
    } else {
        // If not, fetch from the database
        try {
            $stmt = $pdo->prepare("SELECT id, name, slug FROM categories WHERE parent_id IS NULL ORDER BY name ASC");
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Create a new cache file for future use
            // Ensure the 'cache' directory exists and is writable
            if (!is_dir(ROOT_PATH . '/cache')) {
                mkdir(ROOT_PATH . '/cache', 0755, true);
            }
            file_put_contents($cache_file, json_encode($categories));

            return $categories;
        } catch (PDOException $e) {
            error_log("Bootstrap global category fetch failed: " . $e->getMessage());
            return []; // Return empty on error
        }
    }
}

// --- 6. Load Global Categories using the Caching Function ---
$global_categories = get_global_categories_cached($pdo);

?>