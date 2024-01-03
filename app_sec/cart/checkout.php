<?php
session_start();

// Function to log requests
function logRequest($message) {
    // Implement your logging mechanism here
    error_log($message, 0);
}

// Function to check and rate limit requests
function checkRequestRateLimit($ip, $threshold) {
    $key = 'rate_limit_' . $ip;
    $requestCount = isset($_SESSION[$key]) ? $_SESSION[$key] : 0;

    // Increase request count
    $_SESSION[$key] = $requestCount + 1;

    // Log the request
    logRequest("Request from IP $ip - Count: $requestCount");

    // Check if the threshold is exceeded
    if ($requestCount > $threshold) {
        // Implement your alerting mechanism here
        logRequest("ALERT: Abnormal number of requests from IP $ip!");

        // You may also implement actions like blocking or throttling the IP
        header("Refresh:0; url=./error_page.html");
        exit; // Terminate script execution
    }
}

// Get the IP address of the user
$ip = $_SERVER['REMOTE_ADDR'];

$threshold = 50;

// Check and rate limit requests
checkRequestRateLimit($ip, $threshold);

// Include reauthentication logic
include('reauthentication.php');

$loggedIn = isset($_SESSION['login_user']);

if ($loggedIn) {
    // Log user access without logging sensitive data
    logRequest("User access: IP $ip - User logged in.");
    checkReauthentication();
}

try {
    // Log access to payment-related information without logging the sensitive data
    logRequest("Access to payment information: IP $ip");
    
    $last_payment = $_SESSION['payment_time'];
    $t = time();
    $timestamp = date("Y-m-d", $t);

    // Set cache control headers to prevent caching
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");

    if (($last_payment - $t) < 60) {
        error_log($timestamp . " Error - consecutive payment attempts\n", 0);
        header("Refresh:0; url=./checkout.html");
    } else {
        // Generate a random encryption key for each session
        $encryptionKey = bin2hex(random_bytes(32)); // 32 bytes (256 bits) for AES-256
        
        // Encrypt sensitive data
        $iv = openssl_random_pseudo_bytes(16); // Initialization Vector
        $encryptedPayment = openssl_encrypt($last_payment, 'aes-256-cbc', $encryptionKey, 0, $iv);

        // Your code to handle the encrypted data goes here

        error_log($timestamp . " Payment completed\n", 0);

        // Clear or unset sensitive data
        unset($last_payment);
        
        $_SESSION['payment_time'] = $t;

        // Automatically delete old session data (e.g., after 30 minutes)
        $maxSessionLifetime = 30 * 60; // 30 minutes in seconds
        if (isset($_SESSION['last_activity']) && ($t - $_SESSION['last_activity'] > $maxSessionLifetime)) {
            session_unset();
            session_destroy();
            logRequest("Old session data deleted for IP $ip");
        } else {
            $_SESSION['last_activity'] = $t;
        }
    }
} catch (Exception $ex) {
    // Log exceptions without logging sensitive data
    logRequest("Exception occurred: IP $ip - " . $ex->getMessage());
    
    $t = time();
    $timestamp = date("Y-m-d", $t);
    error_log($timestamp . " Payment completed\n", 0);

    // Generate a random encryption key for each session
    $encryptionKey = bin2hex(random_bytes(32)); // 32 bytes (256 bits) for AES-256
    
    // Encrypt sensitive data
    $iv = openssl_random_pseudo_bytes(16); // Initialization Vector
    $encryptedPayment = openssl_encrypt($last_payment, 'aes-256-cbc', $encryptionKey, 0, $iv);

    // Your code to handle the encrypted data goes here

    // Clear or unset sensitive data
    unset($last_payment);

    $_SESSION['payment_time'] = $t;

    // Automatically delete old session data (e.g., after 30 minutes)
    $maxSessionLifetime = 30 * 60; // 30 minutes in seconds
    if (isset($_SESSION['last_activity']) && ($t - $_SESSION['last_activity'] > $maxSessionLifetime)) {
        session_unset();
        session_destroy();
        logRequest("Old session data deleted for IP $ip");
    } else {
        $_SESSION['last_activity'] = $t;
    }

    header("Refresh:0; url=./checkout.html");
}
?>
