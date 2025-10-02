<?php require('partials/header.php'); ?>
<style>
/* Modern Login Page Styles */
.login-container {
max-width: 450px;
margin: 50px auto;
padding: 2rem;
background: #fff;
border-radius: 12px;
box-shadow: 0 5px 25px rgba(0,0,0,0.1);
}
.form-control:focus {
border-color: #86b7fe;
box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
.btn-google { background-color: #DB4437; color: white; }
.btn-google:hover { background-color: #c33d2e; color: white; }
.btn-phone { background-color: #25D366; color: white; }
.btn-phone:hover { background-color: #1ebe59; color: white; }
.divider { display: flex; align-items: center; text-align: center; color: #aaa; margin: 1.5rem 0; }
.divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid #e0e0e0; }
.divider:not(:empty)::before { margin-right: .75em; }
.divider:not(:empty)::after { margin-left: .75em; }
.password-wrapper { position: relative; }
#togglePassword {
position: absolute;
top: 50%;
right: 15px;
transform: translateY(-50%);
cursor: pointer;
color: #6c757d;
transition: color 0.2s;
}
#togglePassword:hover { color: #212529; }
</style>
<div class="container">
<div class="login-container">
<h2 class="text-center mb-2">Welcome Back!</h2>
<p class="text-center text-muted mb-4">Login to access your Cut2Buy account</p>

<?php if (isset($_SESSION['flash_message']) && $_SESSION['flash_message']['type'] === 'error'): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($_SESSION['flash_message']['text']) ?></div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <!-- Social & OTP Login Buttons -->
    <div class="d-grid gap-2 mb-3">
        <a href="/login/google" style="display:none;" class="btn btn-google fw-bold"><i class="bi bi-google me-2"></i>Continue with Google</a>
        <a href="/login/phone" class="btn btn-phone fw-bold"><i class="bi bi-telephone-fill me-2"></i>Continue with Phone</a>
    </div>

    <div class="divider">OR</div>
    
    <!-- Email/Password Form -->
    <form action="/login" method="POST">
        <input type="hidden" name="login_action" value="1">

        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="password-wrapper">
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                <i class="bi bi-eye-slash" id="togglePassword"></i>
            </div>
        </div>
        
        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary fw-bold">Login with Email</button>
        </div>
    </form>
    
    <div class="text-center mt-3">
        <p class="text-muted">Don't have an account? <a href="/register">Sign Up</a></p>
    </div>
</div>
</div>
<!-- JavaScript for Password Toggle -->
<script>
document.addEventListener('DOMContentLoaded', function() {
const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#password');

togglePassword.addEventListener('click', function () {
const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
password.setAttribute('type', type);
this.classList.toggle('bi-eye');
this.classList.toggle('bi-eye-slash');
});
});
</script>
<?php require('partials/footer.php'); ?>