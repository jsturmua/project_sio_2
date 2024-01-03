<?php
// Initialize the session
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

// Expire the session cookie by setting its expiration in the past
setcookie('session_token', '', time() - 3600, '/', '', true, true);

// Clear client-side data using JavaScript
echo '<script>';
echo 'localStorage.clear();'; // Clear local storage
echo 'sessionStorage.clear();'; // Clear session storage
echo '</script>';

$t = time();
$timestamp = date("Y-m-d", $t);

syslog(LOG_INFO, $timestamp + " Session terminated\n");

// Redirect to login page
header("location: login.php");
exit;
?>