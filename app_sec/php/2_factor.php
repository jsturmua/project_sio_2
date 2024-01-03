<!DOCTYPE html>
<html>
<head>
    <title>Display QR Code</title>
</head>
<body>
    <h1>QR Code for TOTP Secret. Login after 2-FA Authentification</h1>
    <?php
    // Get the QR code URL from the URL parameter
    $qrCodeUrl = $_GET['qr_code_url'] ?? '';

    if ($qrCodeUrl) {
        echo '<img src="' . htmlspecialchars($qrCodeUrl) . '" alt="QR Code for TOTP Secret">';
    } else {
        echo 'Something went wrong, please head to the administrator';
    }
    ?>
    <p>You can <a href="login.php">login here</a>.</p>
</body>
</html>
