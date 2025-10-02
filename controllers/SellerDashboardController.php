<?php
// File: /controllers/SellerDashboardController.php


// --- SECURITY CHECK: Must have the 'saller' role ---
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'saller') {
    http_response_code(403);
    echo "<h1>403 Forbidden</h1><p>You do not have permission to access this page.</p>";
    exit();
}

// Fetch data specific to this seller
$seller_id = $_SESSION['user_id'];
$my_products_count = $pdo->query("SELECT count(*) FROM products WHERE vendor_id = $seller_id")->fetchColumn();

$pageTitle = "Seller Dashboard";

require 'views/seller/dashboard.view.php';