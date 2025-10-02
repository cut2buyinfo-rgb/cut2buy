<?php
// File: sitemap-updater.php
// Version: Final & Clean

// Define the path to the sitemap.xml file.
$sitemapFile = __DIR__ . '/sitemap.xml';

// Set the update interval to 24 hours (in seconds).
$updateInterval = 86400;

// The script will only run if the sitemap file does not exist or is older than 24 hours.
if (!file_exists($sitemapFile) || (time() - filemtime($sitemapFile)) > $updateInterval) {
    
    // Use the global database connection object from index.php.
    global $pdo; 
    if (!$pdo) {
        return; // Stop if the database connection is not available.
    }

    // Set the base URL for your website.
    $baseUrl = "https://cut2buy.unaux.com"; 

    // Start building the XML content.
    $xmlContent = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xmlContent .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // --- Section 1: Add Static Pages ---
    $staticPages = ['/', '/products', '/categories', '/contact-us', '/about-us', '/faq', '/privacy-policy', '/terms-conditions'];
    foreach ($staticPages as $page) {
        $xmlContent .= '  <url><loc>' . $baseUrl . $page . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><priority>0.8</priority></url>' . "\n";
    }

    // --- Section 2: Add Product Pages ---
    try {
        $stmt = $pdo->query("SELECT slug, updated_at FROM products WHERE status = 'published' ORDER BY id DESC");
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $productUrl = $baseUrl . '/product/' . htmlspecialchars($row['slug']);
                $lastMod = !empty($row['updated_at']) ? date('Y-m-d', strtotime($row['updated_at'])) : date('Y-m-d');
                $xmlContent .= '  <url><loc>' . $productUrl . '</loc><lastmod>' . $lastMod . '</lastmod><priority>1.0</priority></url>' . "\n";
            }
        }
    } catch (Exception $e) {
        // In case of a future error, it will be silently ignored.
    }

    // --- Section 3: Add Category Pages ---
    try {
        $stmt = $pdo->query("SELECT slug FROM categories ORDER BY id DESC");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categoryUrl = $baseUrl . '/category/' . htmlspecialchars($row['slug']);
            $xmlContent .= '  <url><loc>' . $categoryUrl . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><priority>0.9</priority></url>' . "\n";
        }
    } catch (Exception $e) {
        // Silently ignore errors.
    }

    // Close the XML structure.
    $xmlContent .= '</urlset>';

    // Write the final content to the sitemap.xml file.
    @file_put_contents($sitemapFile, $xmlContent);
}
?>