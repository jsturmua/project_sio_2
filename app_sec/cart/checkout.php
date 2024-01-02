<?php
include('reauthentication.php');
session_start();
$loggedIn = isset($_SESSION['login_user']);
if ($loggedIn){
    checkReauthentication();
}
try {
    $last_payment = $_SESSION['payment_time'];
    $t = time();
    $timestamp = date("Y-m-d",$t);
    if (($last_payment - $t) < 60) {
        console.log($timestamp + "Error - consecutive payment attempts\n");
        header("Refresh:0; url=./checkout.html");
    } else {
        console.log($timestamp "Payment completed\n");
        $_SESSION['payment_time'] = $t;
    }
} catch (Exception $ex) {
    $t = time();
    $timestamp = date("Y-m-d",$t);
    console.log($timestamp "Payment completed\n");
    $_SESSION['payment_time'] = $t;
    header("Refresh:0; url=./checkout.html");
}
?>
