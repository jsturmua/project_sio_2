<!DOCTYPE html>
<html>
<head>
    <title>Display QR Code</title>
</head>
<body>
    <h1>QR Code for your second factor authentification.</h1>
    <?php
    // Get the QR code URL from the URL parameter
    $qrCodeUrl = $_GET['qr_code_url'] ?? '';

    if ($qrCodeUrl) {
        echo '<iframe src="' . $qrCodeUrl . '" width="600" height="320" frameborder="0"></iframe>';
    } else {
        echo 'Something went wrong, please head to the administrator';
    }
    ?>
    <p>Please login in here after activating the second factor:<a href="login.php">Login</a>.</p>
</body>
</html>