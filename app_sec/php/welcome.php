<?php
session_start();
include('reauthentication.php');
$loggedIn = isset($_SESSION['login_user']);
if ($loggedIn){
    checkReauthentication();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/main.css">
    <title>Webshop</title>
</head>
<body>
    <header>
        <h1>Welcome to the webshop</h1>
        <?php if ($loggedIn) { ?>
            <a href="logout.php">Logout</a>
            <a href="reset_password.php">Reset Password</a>
        <?php } ?>
    </header>

    <nav>
        <ul>
            <?php if ($loggedIn) { ?>
                <li><a href="../products/products.html">Products</a></li>
                <li><a href="../cart/cart.html">Cart</a></li>
                <li><a href="../delete_data/delete_data.html">Delete Account</a></li>
                <li><a href="privacy_policy/privacy.html">Privacy Policy</a></li>
            <?php } else { ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="../products/products.html">Products</a></li>
                <li><a href="privacy_policy/privacy.html">Privacy Policy</a></li>
            <?php } ?>
        </ul>
    </nav>

    <main>
        <p>Explore our online store and find the best DETI products!</p>
    </main>

    <footer>
        <p>&copy; 2023 Webshop</p>
    </footer>
    <script src="../js/script.js"></script>
</body>
</html>