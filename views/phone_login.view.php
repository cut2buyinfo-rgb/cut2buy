<?php require('partials/header.php'); ?>
<div class="container">
    <div class="login-container" style="max-width: 450px; margin-top: 50px;">
        <h2 class="text-center mb-2">Login with Phone</h2>
        <p class="text-center text-muted mb-4">Enter your phone number to receive an OTP.</p>
        <?php if (isset($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form action="/login/phone/send-otp" method="POST">
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="01xxxxxxxxx" required>
            </div>
            <div class="d-grid mt-4"><button type="submit" class="btn btn-primary fw-bold">Send OTP</button></div>
        </form>
        <div class="text-center mt-3"><a href="/login">« Back to Login</a></div>
    </div>
</div>
<?php require('partials/footer.php'); ?>