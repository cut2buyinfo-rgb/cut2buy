<?php
// File: /controllers/AdminUserController.php


// Security Check: Only super_admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'super_admin') {
    http_response_code(403);
    die("<h1>403 Forbidden</h1><p>You do not have permission to access this page.</p>");
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Handle all POST requests for user management
if ($method === 'POST') {
    switch ($uri) {
        case '/admin/users/store':
            // Logic to create a new user
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $password = $_POST['password'];
            $role = $_POST['role'];
            $status = $_POST['status'];

            // Basic validation
            if (!empty($name) && !empty($email) && !empty($password) && !empty($role)) {
                // Check if email already exists
                $check_stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $check_stmt->execute([$email]);
                if (!$check_stmt->fetch()) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, status) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $email, $phone, $hashed_password, $role, $status]);
                    $_SESSION['success_message'] = "User created successfully!";
                } else {
                    $_SESSION['error_message'] = "A user with this email already exists.";
                }
            } else {
                 $_SESSION['error_message'] = "Please fill all required fields.";
            }
            break;
        
        case '/admin/users/update':
            // Logic to update an existing user
            $id = $_POST['id'];
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $role = $_POST['role'];
            $status = $_POST['status'];

            if (!empty($id) && !empty($name) && !empty($email) && !empty($role)) {
                // Check if another user has the new email
                $check_stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $check_stmt->execute([$email, $id]);
                 if (!$check_stmt->fetch()) {
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, role = ?, status = ? WHERE id = ?");
                    $stmt->execute([$name, $email, $phone, $role, $status, $id]);
                    $_SESSION['success_message'] = "User updated successfully!";
                } else {
                    $_SESSION['error_message'] = "Another user with this email already exists.";
                }
            }
             // Optional: Update password if a new one is provided
            if (!empty($_POST['password'])) {
                $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $id]);
            }
            break;

        case '/admin/users/delete':
            // Logic to delete a user
            $id = $_POST['id'] ?? null;
            // Prevent super_admin from deleting themselves
            if ($id && $id != $_SESSION['user_id']) {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$id]);
                $_SESSION['success_message'] = "User deleted successfully!";
            } else {
                $_SESSION['error_message'] = "You cannot delete your own account.";
            }
            break;
    }
    header('Location: /admin/users');
    exit();
}

// --- Default GET request to show the user management page ---
$pageTitle = "Manage Users";

// Fetch all users except the current super_admin to prevent self-editing in the list
$stmt = $pdo->prepare("SELECT * FROM users WHERE id != ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Optional: Display success/error messages
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);

require 'views/admin/users/index.view.php';