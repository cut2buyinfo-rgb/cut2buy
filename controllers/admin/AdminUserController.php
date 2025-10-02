<?php
// controllers/admin/AdminUserController.php
// Version: FINAL - Simplified to match existing architecture.

// The bootstrap.php file has created the global $user variable.
// The db.php file has created the global $pdo variable.

// --- Security Check using global $user variable ---
if (!$user || !in_array($user['role'], ['admin', 'super_admin'])) {
    http_response_code(403);
    die("<h1>403 Forbidden</h1><p>You do not have permission to access this page.</p>");
}

try {
    // Directly use the global $pdo variable
    $stmt = $pdo->query("SELECT id, name, email, phone, role, status, created_at FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Set the page title
    $pageTitle = "Manage Users";
    
    // Load the view
    require_once ROOT_PATH . '/views/admin/users/index.php';

} catch (PDOException $e) {
    // Error handling
    die("Error fetching users: ". $e->getMessage());
}