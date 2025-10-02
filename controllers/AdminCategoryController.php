<?php
// File: /controllers/AdminCategoryController.php
// Version: FINAL & SECURE (Prevents duplicate slug entries)

// Security Check
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'super_admin'])) {
    http_response_code(403);
    die("<h1>403 Forbidden</h1>");
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Handle POST requests for creating, updating, and deleting categories
if ($method === 'POST') {
    switch ($uri) {
        case '/admin/categories/store':
            $name = trim($_POST['name'] ?? '');
            $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            
            if (!empty($name)) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

                // --- THIS IS THE FIX ---
                // First, check if a category with this slug already exists.
                $check_stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
                $check_stmt->execute([$slug]);
                
                // Only insert if the slug is not found
                if ($check_stmt->fetch() === false) {
                    $stmt = $pdo->prepare("INSERT INTO categories (name, slug, parent_id) VALUES (?, ?, ?)");
                    $stmt->execute([$name, $slug, $parent_id]);
                } else {
                    // Optional: You can set a session flash message to inform the user
                    $_SESSION['error_message'] = "A category with a similar name already exists.";
                }
            }
            break;

        case '/admin/categories/update':
            $id = $_POST['id'] ?? null;
            $name = trim($_POST['name'] ?? '');
            $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

            if ($id && !empty($name) && $id != $parent_id) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));

                // --- THIS IS ALSO A FIX for UPDATE ---
                // Check if another category (not this one) already has the new slug
                $check_stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ? AND id != ?");
                $check_stmt->execute([$slug, $id]);

                if ($check_stmt->fetch() === false) {
                    $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, parent_id = ? WHERE id = ?");
                    $stmt->execute([$name, $slug, $parent_id, $id]);
                } else {
                    $_SESSION['error_message'] = "Another category with a similar name already exists.";
                }
            }
            break;
        
        case '/admin/categories/delete':
            $id = $_POST['id'] ?? null;
            if ($id) {
                $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                $stmt->execute([$id]);
            }
            break;
    }
    header('Location: /admin/categories');
    exit();
}

// --- Default GET request to show the category management page ---
$pageTitle = "Manage Categories";

$all_categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

function buildCategoryTree(array $elements, $parentId = null) {
    $branch = [];
    foreach ($elements as $element) {
        if ($element['parent_id'] == $parentId) {
            $children = buildCategoryTree($elements, $element['id']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[] = $element;
        }
    }
    return $branch;
}

$category_tree = buildCategoryTree($all_categories);

// Optional: Display the error message if it exists
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']); // Clear the message after displaying it once

require 'views/admin/categories/index.view.php';