<?php
// File: controllers/GoogleAuthController.php

// Instead of vendor/autoload.php
require_once __DIR__ . '/../google-client/Google/Client.php';
require_once __DIR__ . '/../google-client/Google/Service/Oauth2.php';


// --- Google Credentials ---
// Note: It's best practice to move these to a separate config file.
define('GOOGLE_CLIENT_ID', '1094130418417-5912ejud9es5gqkjq6310jq0o64jvvro.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', 'https://cut2buy.unaux.com/gmail-callback.php'); // আপনার ডোমেইন ও পাথ দিন

$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);
$client->addScope("email");
$client->addScope("profile");

$uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri_path === '/login/google') {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
    exit();

} elseif ($uri_path === '/gmail-callback') {
    if (isset($_GET['code'])) {
        try {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            if (isset($token['error'])) { throw new Exception($token['error_description']); }
            $client->setAccessToken($token['access_token']);

            $google_oauth = new Google_Service_Oauth2($client);
            $google_account_info = $google_oauth->userinfo->get();
            
            $email = $google_account_info->email;
            $name = $google_account_info->name;
            $google_id = $google_account_info->id;

            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (name, email, google_id, provider, status) VALUES (?, ?, ?, 'google', 'active')");
                $stmt->execute([$name, $email, $google_id]);
                $new_user_id = $pdo->lastInsertId();

                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_role'] = 'user';
            }
            
            header('Location: /dashboard');
            exit();

        } catch (Exception $e) {
            $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Google login failed. Please try again.'];
            header('Location: /login');
            exit();
        }
    }
    header('Location: /login');
    exit();
}