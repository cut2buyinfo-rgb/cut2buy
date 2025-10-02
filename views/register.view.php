<?php require('partials/header.php'); ?>

<style>
    /* Modern Registration Page Styles */
    .register-container {
        max-width: 500px;
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
    .password-wrapper {
        position: relative;
    }
    .toggle-password-icon {
        position: absolute;
        top: 50%;
        right: 15px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        transition: color 0.2s;
    }
    .toggle-password-icon:hover {
        color: #212529;
    }
    #password-strength-status {
        margin-top: 5px;
        height: 5px;
        background: #e9ecef;
        border-radius: 5px;
        overflow: hidden;
    }
    #password-strength-bar {
        height: 100%;
        width: 0;
        background: #dc3545; /* Weak */
        transition: width 0.3s ease-in-out, background-color 0.3s ease-in-out;
    }
    #password-strength-bar.medium { background-color: #ffc107; }
    #password-strength-bar.strong { background-color: #198754; }
    #password-strength-bar.very-strong { background-color: #0d6efd; }

    #password-match-message {
        font-size: 0.875em;
        margin-top: 5px;
    }
    #password-match-message.match { color: #198754; }
    #password-match-message.no-match { color: #dc3545; }
</style>

<div class="container">
    <div class="register-container">
        <h2 class="card-title text-center">Create Your Account</h2>
        <p class="text-center text-muted mb-4">Join the Cut2Buy community today!</p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/register" method="POST" id="registrationForm">
            <input type="hidden" name="register_action" value="1">
            
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="e.g. John Doe" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="password-wrapper">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <i class="bi bi-eye-slash toggle-password-icon" id="togglePassword"></i>
                </div>
                <div id="password-strength-status"><div id="password-strength-bar"></div></div>
            </div>
            
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <div class="password-wrapper">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    <i class="bi bi-eye-slash toggle-password-icon" id="toggleConfirmPassword"></i>
                </div>
                <div id="password-match-message"></div>
            </div>
            
            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary fw-bold">Create Account</button>
            </div>
        </form>
        
        <div class="text-center mt-3">
            <p class="text-muted">Already have an account? <a href="/login">Login here</a></p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const togglePasswordIcon = document.getElementById('togglePassword');
    const toggleConfirmPasswordIcon = document.getElementById('toggleConfirmPassword');
    const strengthBar = document.getElementById('password-strength-bar');
    const matchMessage = document.getElementById('password-match-message');

    // Function to toggle password visibility
    const toggleVisibility = (input, icon) => {
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
    };

    togglePasswordIcon.addEventListener('click', () => toggleVisibility(passwordInput, togglePasswordIcon));
    toggleConfirmPasswordIcon.addEventListener('click', () => toggleVisibility(confirmPasswordInput, toggleConfirmPasswordIcon));

    // Function to check password strength
    passwordInput.addEventListener('input', function() {
        const val = passwordInput.value;
        let strength = 0;
        if (val.length >= 8) strength++; // Length
        if (val.match(/[a-z]/)) strength++; // Lowercase
        if (val.match(/[A-Z]/)) strength++; // Uppercase
        if (val.match(/[0-9]/)) strength++; // Numbers
        if (val.match(/[^A-Za-z0-9]/)) strength++; // Special characters
        
        strengthBar.className = '';
        switch (strength) {
            case 1: strengthBar.style.width = '20%'; strengthBar.classList.add('weak'); break;
            case 2: strengthBar.style.width = '40%'; strengthBar.classList.add('weak'); break;
            case 3: strengthBar.style.width = '60%'; strengthBar.classList.add('medium'); break;
            case 4: strengthBar.style.width = '80%'; strengthBar.classList.add('strong'); break;
            case 5: strengthBar.style.width = '100%'; strengthBar.classList.add('very-strong'); break;
            default: strengthBar.style.width = '0%';
        }
        checkPasswordMatch();
    });

    // Function to check if passwords match
    const checkPasswordMatch = () => {
        const pass1 = passwordInput.value;
        const pass2 = confirmPasswordInput.value;

        if (pass2.length === 0) {
            matchMessage.textContent = '';
            return;
        }

        if (pass1 === pass2) {
            matchMessage.textContent = '✅ Passwords match!';
            matchMessage.className = 'match';
        } else {
            matchMessage.textContent = '❌ Passwords do not match!';
            matchMessage.className = 'no-match';
        }
    };

    confirmPasswordInput.addEventListener('input', checkPasswordMatch);
});
</script>

<?php require('partials/footer.php'); ?>