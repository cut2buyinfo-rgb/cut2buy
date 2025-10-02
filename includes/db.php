<?php
// File: /includes/db.php
// Version: DEBUG MODE - FORCES ERRORS TO SHOW

// --- 1. Force PHP to display all errors on the screen ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- 2. Database Connection Details ---
$db_host = 'sql303.ezyro.com';
$db_name = 'ezyro_39909385_cut2buy_db';
$db_user = 'ezyro_39909385';
$db_pass = 'obaydul2014b';
$charset = 'utf8mb4';

// --- 3. DSN and Options ---
$dsn = "mysql:host={$db_host};dbname={$db_name};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// --- 4. The Connection Test ---
try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
    // If the script continues past this line, the connection is successful.
    // To be absolutely sure, we can add a temporary success message.
    // echo "<h1>Database Connection Successful!</h1>"; 
    
} catch (PDOException $e) {
    // If the connection fails, this will STOP EVERYTHING and show the real error.
    // This is the most important part of the debug process.
    die("<h1>Database Connection Failed!</h1><p>The exact error is: <pre>" . htmlspecialchars($e->getMessage()) . "</pre></p>");
}

// After connecting, immediately set the character set for the connection.
// This is another critical step for character encoding issues.
$pdo->exec("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");