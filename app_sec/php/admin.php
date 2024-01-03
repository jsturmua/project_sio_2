<?php
session_start();
include('reauthentication.php');
session_start();
$loggedIn = isset($_SESSION['login_user']);
if ($loggedIn){
    checkReauthentication();
}

// Check if the user is authenticated and has the role 'admin'
if (isset($_SESSION['login_role']) && $_SESSION['login_role'] == 'admin') {
    // User is an admin, display the admin page content
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Page</title>
        <link rel="stylesheet" type="text/css" href="../css/admin.css">
    </head>
    <body>
        <div>
            <b>Admin Page</b>
            <!-- Your admin page content goes here -->
            <p>This is the admin page content.</p>
        </div>
    </body>
    </html>
    <?php
} else {
    // User is not an admin, redirect to the login page
    header("Location: login.php");
    exit();
}
?>
