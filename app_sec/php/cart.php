<?php
include('reauthentication.php');
session_start();
$loggedIn = isset($_SESSION['login_user']);
if ($loggedIn){
    checkReauthentication();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $user = $_SESSION['login_user'];
        $t = time();
        $timestamp = date("Y-m-d",$t);
        syslog(LOG_INFO, $t + " Redirecting user to checkout page\n");
        header("Location: ./checkout.html"); 
    } catch (Exception $ex) {
        $t = time();
        $timestamp = date("Y-m-d",$t);
        alert("Login required");
        error_log($t + " Checkout attempt failed - user not logged in\n");
        exit;
    }
}
?>