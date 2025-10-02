<?php
// File: controllers/OtpController.php
require_once ROOT_PATH . '/includes/notifications.php'; // আপনার SMS পাঠানোর ফাংশন

$uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Route 1: Show phone number entry form
if ($uri_path === '/login/phone' && $method === 'GET') {
    require 'views/phone_login.view.php';
    exit();
}

// Route 2: Send the OTP
if ($uri_path === '/login/phone/send-otp' && $method === 'POST') {
    $phone = $_POST['phone'] ?? '';
    if (preg_match('/^01[3-9]\d{8}$/', $phone)) {
        $otp = rand(100000, 999999);
        
        $_SESSION['otp_code'] = $otp;
        $_SESSION['otp_phone'] = $phone;
        $_SESSION['otp_expiry'] = time() + 300; // 5 minute validity

        $message = "Your Cut2Buy login OTP is: {$otp}. It is valid for 5 minutes.";
        sendOrderConfirmationSMS($phone, $message); 

        header('Location: /login/phone/verify-otp');
        exit();
    } else {
        $error = "Invalid phone number. Please enter a valid 11-digit number.";
        require 'views/phone_login.view.php';
        exit();
    }
}

// Route 3: Verify the OTP
if ($uri_path === '/login/phone/verify-otp') {
    if (!isset($_SESSION['otp_phone'])) {
        header('Location: /login/phone');
        exit();
    }

    if ($method === 'POST') {
        $submitted_otp = $_POST['otp'] ?? '';
        
        if (isset($_SESSION['otp_code']) && $_SESSION['otp_code'] == $submitted_otp && time() < $_SESSION['otp_expiry']) {
            $phone = $_SESSION['otp_phone'];
            
            $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
            $stmt->execute([$phone]);
            $user = $stmt->fetch();

            unset($_SESSION['otp_code'], $_SESSION['otp_phone'], $_SESSION['otp_expiry']);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                header('Location: /dashboard');
                exit();
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'No account is associated with this phone number.'];
                header('Location: /login');
                exit();
            }
        } else {
            $error = "Invalid or expired OTP. Please try again.";
        }
    }
    
    require 'views/otp_verify.view.php';
    exit();
}