<?php
// This check is to prevent direct access to the file
if (!isset($current_user)) {
    // A simple way to prevent direct access.
    // In a real app, you might redirect or show a 403 error.
    die('Access Denied');
}
?>
<div class="dashboard-sidebar card">
    <div class="card-header bg-light">
        <strong>Hello, <?= htmlspecialchars($current_user['name']) ?></strong>
    </div>
    <div class="list-group list-group-flush">
        <a href="/dashboard" class="list-group-item list-group-item-action <?= ($pageTitle === 'My Dashboard') ? 'active' : '' ?>">
            <i class="bi bi-person-circle me-2"></i>My Account
        </a>
        <a href="/orders" class="list-group-item list-group-item-action <?= ($pageTitle === 'My Orders') ? 'active' : '' ?>">
            <i class="bi bi-box-seam me-2"></i>My Orders
        </a>
        <a href="/reviews" class="list-group-item list-group-item-action <?= ($pageTitle === 'My Reviews') ? 'active' : '' ?>">
            <i class="bi bi-chat-square-text me-2"></i>My Reviews
        </a>
        <a href="/wishlist" class="list-group-item list-group-item-action <?= ($pageTitle === 'My Wishlist') ? 'active' : '' ?>">
            <i class="bi bi-heart me-2"></i>My Wishlist
        </a>
        <a href="/profile" class="list-group-item list-group-item-action <?= ($pageTitle === 'Address Book') ? 'active' : '' ?>">
            <i class="bi bi-person-vcard me-2"></i>Address Book
        </a>
        <a href="/logout" class="list-group-item list-group-item-action text-danger">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
        </a>
    </div>
</div>