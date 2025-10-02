<?php
// controllers/admin/AdminBannerController.php

// Security Check
if (!$user || !in_array($user['role'], ['admin', 'super_admin'])) {
    http_response_code(403);
    die("<h1>403 Forbidden</h1>");
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$upload_dir = ROOT_PATH . '/assets/images/banners/';

// Ensure the upload directory exists
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// --- HANDLE POST REQUESTS (Create, Update, Delete) ---
if ($method === 'POST') {
    switch ($path) {
        case '/admin/banners/store':
            $title = $_POST['title'];
            $link_url = $_POST['link_url'];
            $status = $_POST['status'];
            $image_path = '';

            if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === 0) {
                // --- Reusable WebP Conversion Logic ---
                $file = $_FILES['banner_image'];
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                if (in_array($file['type'], $allowed_types)) {
                    $original_path = $file['tmp_name'];
                    $image_info = getimagesize($original_path);
                    $original_mime = $image_info['mime'];
                    
                    switch ($original_mime) {
                        case 'image/jpeg': $source_image = imagecreatefromjpeg($original_path); break;
                        case 'image/png': $source_image = imagecreatefrompng($original_path); break;
                        case 'image/gif': $source_image = imagecreatefromgif($original_path); break;
                    }

                    if (isset($source_image)) {
                        $filename = 'banner-' . uniqid() . '.webp';
                        $destination_path = $upload_dir . $filename;
                        imagewebp($source_image, $destination_path, 80); // Convert to WebP with 80% quality
                        imagedestroy($source_image);
                        $image_path = $filename;
                    }
                }
            }
            
            if ($image_path) {
                $stmt = $pdo->prepare("INSERT INTO banners (title, image_path, link_url, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $image_path, $link_url, $status]);
            }

            header('Location: /admin/banners');
            exit();

        case '/admin/banners/update':
            // Similar logic for update, including file handling if a new image is uploaded.
            // ... (Implementation for update) ...
            header('Location: /admin/banners');
            exit();

        case '/admin/banners/delete':
            $id = $_POST['banner_id'];
            // First, get the filename to delete the file
            $stmt = $pdo->prepare("SELECT image_path FROM banners WHERE id = ?");
            $stmt->execute([$id]);
            $banner = $stmt->fetch();
            if ($banner && file_exists($upload_dir . $banner['image_path'])) {
                unlink($upload_dir . $banner['image_path']);
            }
            // Then, delete the record from DB
            $del_stmt = $pdo->prepare("DELETE FROM banners WHERE id = ?");
            $del_stmt->execute([$id]);

            header('Location: /admin/banners');
            exit();
    }
}

// --- HANDLE GET REQUESTS (Display Pages) ---

// Route for Create Form
if ($path === '/admin/banners/create') {
    $pageTitle = "Add New Banner";
    require 'views/admin/banners/create.view.php';
    exit();
}

// Route for Edit Form
// ... (Implementation for showing edit form) ...


// Default: Show Banner List (Index)
$pageTitle = "Manage Banners";
$banners = $pdo->query("SELECT * FROM banners ORDER BY sort_order ASC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
require 'views/admin/banners/index.view.php';