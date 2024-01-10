<?php
// Initialize the session
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Expire the session cookie by setting its expiration in the past
setcookie('session_token', '', time() - 3600, '/', '', true, true);

$t = time();
$timestamp = date("Y-m-d", $t);

syslog(LOG_INFO, $timestamp . " Session terminated\n");

// Redirect to login page
header("location: /app_sec/index.html");
exit;
?>
