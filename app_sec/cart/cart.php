<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $user = $_SESSION['login_user'];
        $t = time();
        $timestamp = date("Y-m-d",$t);
        console.log($t + "Redirecting user to checkout page\n");
        header("Location: ./checkout.html"); 
    } catch (Exception $ex) {
        $t = time();
        $timestamp = date("Y-m-d",$t);
        alert("Login required");
        console.log($t + "Checkout attempt failed - user not logged in\n");
        exit;
    }
}
?>