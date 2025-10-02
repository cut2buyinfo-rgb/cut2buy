<?php require('partials/header.php'); ?>
<div class="container">
    <div class="login-container" style="max-width: 450px; margin-top: 50px;">
        <h2 class="text-center mb-2">Verify OTP</h2>
        <p class="text-center text-muted mb-4">Enter the 6-digit code sent to <strong><?= htmlspecialchars($_SESSION['otp_phone'] ?? '') ?></strong>.</p>
        <?php if (isset($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form action="/login/phone/verify-otp" method="POST">
            <div class="mb-3">
                <label for="otp" class="form-label">OTP Code</label>
                <input type="text" class="form-control text-center" id="otp" name="otp" inputmode="numeric" pattern="\d{6}" maxlength="6" required>
            </div>
            <div class="d-grid mt-4"><button type="submit" class="btn btn-primary fw-bold">Verify & Login</button></div>
        </form>
        <div class="text-center mt-3"><a href="/login/phone">Didn't receive code? Resend.</a></div>
    </div>
</div>
<?php require('partials/footer.php'); ?>