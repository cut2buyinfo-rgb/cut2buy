<?php
// File: /controllers/DashboardController.php
// Manages data for the user dashboard


// --- SECURITY CHECK: User must be logged in to access the dashboard ---
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header('Location: /login');
    exit();
}

// Get the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

try {
    // Query 1: Fetch the logged-in user's details
    $user_stmt = $pdo->prepare("SELECT id, name, email, phone, address FROM users WHERE id = ?");
    $user_stmt->execute([$user_id]);
    $current_user = $user_stmt->fetch();

    // If for some reason the user is not found in DB, log them out
    if (!$current_user) {
        header('Location: /logout');
        exit();
    }

    // Query 2: Fetch the user's recent orders
    $orders_stmt = $pdo->prepare(
        "SELECT id, total_amount, status, created_at 
         FROM orders 
         WHERE user_id = ? 
         ORDER BY created_at DESC 
         LIMIT 10"
    );
    $orders_stmt->execute([$user_id]);
    $orders = $orders_stmt->fetchAll();

} catch (PDOException $e) {
    // In a real application, you would log this error, not show it to the user
    die("Error fetching dashboard data: " . $e->getMessage());
}

// Set the page title for the dashboard
$pageTitle = "My Dashboard";

// Load the dashboard view and pass the fetched data to it
require 'views/dashboard.view.php';