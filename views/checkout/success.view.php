<?php require(ROOT_PATH . '/views/partials/header.php'); ?>

<div class="container my-5">

    <?php
    // --- TEMPORARY DEBUG BLOCK FOR SMS ---
    if (isset($_SESSION['sms_debug_info'])) {
        $sms_status = $_SESSION['sms_debug_info'];
        if ($sms_status === 'Success') {
            echo '<div class="alert alert-success">DEBUG: SMS API reported success (Code 202).</div>';
        } else {
            echo '<div class="alert alert-danger">DEBUG: SMS API reported failure. Please check your hosting\'s `error_log` file for the full API response from BulkSMSBD. Common issues are insufficient balance or incorrect API Key/Sender ID.</div>';
        }
        unset($_SESSION['sms_debug_info']); // Clear after showing
    }
    ?>

    <div class="text-center p-5 bg-light rounded">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
        <h2 class="mt-3">Thank You!</h2>
        <p class="lead">Your order #<?= htmlspecialchars($last_order_id ?? '') ?> has been placed successfully.</p>
        <p>We will contact you shortly to confirm your order. You can track your order status in your dashboard.</p>
        <a href="/orders" class="btn btn-primary mt-3">View My Orders</a>
        <a href="/" class="btn btn-outline-secondary mt-3">Continue Shopping</a>
    </div>
</div>

<?php require(ROOT_PATH . '/views/partials/footer.php'); ?>