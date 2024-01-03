<<<<<<< Updated upstream
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
    checkReauthentication();
}

try {
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
        error_log($timestamp . " Payment completed\n", 0);
        $_SESSION['payment_time'] = $t;

        // Clear or unset sensitive data
        unset($last_payment);
    }
} catch (Exception $ex) {
    $t = time();
    $timestamp = date("Y-m-d", $t);
    error_log($timestamp . " Payment completed\n", 0);
    $_SESSION['payment_time'] = $t;
    header("Refresh:0; url=./checkout.html");

    // Clear or unset sensitive data
    unset($last_payment);
}
?>
=======
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
    checkReauthentication();
}

try {
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
        error_log($timestamp . " Payment completed\n", 0);
        $_SESSION['payment_time'] = $t;

        // Clear or unset sensitive data
        unset($last_payment);
    }
} catch (Exception $ex) {
    $t = time();
    $timestamp = date("Y-m-d", $t);
    error_log($timestamp . " Payment completed\n", 0);
    $_SESSION['payment_time'] = $t;
    header("Refresh:0; url=./checkout.html");

    // Clear or unset sensitive data
    unset($last_payment);
}
?>
>>>>>>> Stashed changes
