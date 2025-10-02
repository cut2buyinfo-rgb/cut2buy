<?php
// File: /controllers/AdminBrandController.php


// Security Check
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'super_admin'])) {
    http_response_code(403);
    die("<h1>403 Forbidden</h1><p>You do not have permission to access this page.</p>");
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Handle POST requests for creating and deleting brands
if ($method === 'POST') {
    switch ($uri) {
        case '/admin/brands/store':
            $name = trim($_POST['name'] ?? '');
            if (!empty($name)) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
                // Check if brand already exists
                $check_stmt = $pdo->prepare("SELECT id FROM brands WHERE slug = ?");
                $check_stmt->execute([$slug]);
                if (!$check_stmt->fetch()) {
                    $stmt = $pdo->prepare("INSERT INTO brands (name, slug) VALUES (?, ?)");
                    $stmt->execute([$name, $slug]);
                }
            }
            break;
        
        case '/admin/brands/delete':
            $id = $_POST['id'] ?? null;
            if ($id) {
                $stmt = $pdo->prepare("DELETE FROM brands WHERE id = ?");
                $stmt->execute([$id]);
            }
            break;
    }
    // Redirect back to the brand management page after the action
    header('Location: /admin/brands');
    exit();
}

// Default GET request to show the brand management page
$pageTitle = "Manage Brands";
$brands = $pdo->query("SELECT * FROM brands ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
require 'views/admin/brands/index.view.php';