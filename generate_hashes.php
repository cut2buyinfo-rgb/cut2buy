<?php
// File: /generate_hashes.php
// Version: FINAL - Uses the new self-contained ImageHasher

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/ImageHasher.php';

echo "<h1>Starting Image Hash Generation...</h1>";

try {
    $stmt = $pdo->prepare("SELECT id, image_path FROM product_images WHERE image_hash IS NULL OR image_hash = ''");
    $stmt->execute();
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($images)) {
        echo "<p style='color:green; font-weight:bold;'>No new images to hash. All are up to date.</p>";
        exit;
    }

    $hasher = new ImageHasher();
    $update_stmt = $pdo->prepare("UPDATE product_images SET image_hash = :hash WHERE id = :id");
    $image_dir = __DIR__ . '/assets/images/products/';
    $count = 0;

    foreach ($images as $image) {
        $image_path = $image_dir . $image['image_path'];
        if (file_exists($image_path)) {
            $hash = $hasher->hash($image_path);
            if ($hash) {
                $update_stmt->execute(['hash' => $hash, 'id' => $image['id']]);
                echo "<p style='color:green;'>SUCCESS: Hashed image ID {$image['id']} -> {$hash}</p>";
                $count++;
            } else {
                 echo "<p style='color:red;'>ERROR: Could not hash image ID {$image['id']}. File might be corrupt.</p>";
            }
        }
    }
    echo "<h2>Hashing Complete! {$count} images indexed.</h2>";
} catch (\PDOException $e) {
    die("Database Error: " . $e->getMessage());
}