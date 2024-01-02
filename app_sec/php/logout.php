<?php
// Initialize the session
session_start();
 
// Unset all of the session variables
$_SESSION = array();
 
// Destroy the session.
session_destroy();
$t = time();
$timestamp = date("Y-m-d",$t);
console.log($timestamp + " Session terminated\n");
 
// Redirect to login page
header("location: login.php");
exit;
?>