<?php
// File: /controllers/AdminDashboardController.php


// --- SECURITY CHECK: Must be an admin or super_admin ---
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'super_admin')) {
    // If not, deny access by showing a 403 Forbidden page or redirecting
    http_response_code(403);
    echo "<h1>403 Forbidden</h1><p>You do not have permission to access this page.</p>";
    exit();
}

// Fetch data for the admin dashboard (e.g., total users, orders, products)
$total_users = $pdo->query("SELECT count(*) FROM users")->fetchColumn();
$total_orders = $pdo->query("SELECT count(*) FROM orders")->fetchColumn();

$pageTitle = "Admin Dashboard";

// Load the admin dashboard view
require 'views/admin/dashboard.view.php';