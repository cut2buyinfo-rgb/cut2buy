<?php
// File: /controllers/AuthController.php
// Version: Final - With Role-Based Redirection

// Function to redirect the user to a different page
function redirect($path) {
    header("Location: {$path}");
    exit();
}

// Immediately handle the logout action if the URL is /logout
if ($_SERVER['REQUEST_URI'] === '/logout') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = [];
    session_destroy();
    redirect('/');
}

// --- HANDLE FORM SUBMISSIONS (POST REQUESTS) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- LOGIN LOGIC ---
    if (isset($_POST['login_action'])) {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verify the user and password
        if ($user && password_verify($password, $user['password'])) {
            // Password is correct! Set session variables.
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // <<<<< START: THIS IS THE UPGRADED PART >>>>>
            // This switch statement redirects the user based on their role
            switch ($user['role']) {
                case 'super_admin':
                case 'admin':
                    redirect('/admin/dashboard');
                    break;

                case 'saller': // Note: Check your database for the correct spelling
                    redirect('/seller/dashboard');
                    break;

                default: // This handles the 'user' role
                    redirect('/dashboard');
                    break;
            }
            // <<<<< END: THIS IS THE UPGRADED PART >>>>>

        } else {
            // Login failed
            $error = "Invalid email or password.";
        }
    }
    // --- REGISTRATION LOGIC ---
    if (isset($_POST['register_action'])) {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($name) || empty($email) || empty($password)) {
            $error = "Please fill in all required fields.";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $error = "An account with this email already exists.";
            } else {
                // All checks passed, create the new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $role = 'user'; // New users are assigned the 'user' role by default
                
                $insert_stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                $insert_stmt->execute([$name, $email, $hashed_password, $role]);
                
                // Redirect to login page with a success message
                redirect('/login?registered=success');
            }
        }
    }
}

// --- DISPLAY VIEW FILES (GET REQUESTS) ---
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
switch ($path) {
    case '/login':
        $pageTitle = "Login to your Account";
        require 'views/login.view.php';
        break;
    
    case '/register':
        $pageTitle = "Create a New Account";
        require 'views/register.view.php';
        break;
        
    default:
        // This is a fallback and should not be reached if routing in index.php is correct
        redirect('/');
        break;
}