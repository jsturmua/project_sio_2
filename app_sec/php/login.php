<?php

include("./config.php");

function validateAuthenticationToken($secretCode, $pin){
    $url =  "https://www.authenticatorApi.com/Validate.aspx?Pin=$pin&SecretCode=$secretCode";
    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options to retrieve the response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Execute cURL request
    $response = curl_exec($ch);

    // Check for errors and handle the response
    if ($response === false) {
        // Error occurred
        $error = curl_error($ch);
        echo "Error: $error";
    } else {
        // Successful response received, parse the HTML response
        $dom = new DOMDocument();
        $dom->loadHTML($response);
        // Extract body content
        $bodyContent = $dom->getElementsByTagName('body')->item(0)->nodeValue;
        // Interpret the response content (assuming it's 'True' or 'False')
        $validationResult = filter_var($bodyContent, FILTER_VALIDATE_BOOLEAN);
        // Output the interpretation
        if ($validationResult === true) {
            return true;
        } else {
            return false;
        }
    }

    // Close cURL session
    curl_close($ch);

}
function isPasswordBreached($password) {
    $hashedPassword = strtoupper(sha1((string)$password));
    $prefix = substr($hashedPassword, 0, 5);
    $suffix = substr($hashedPassword, 5);

    $ch = curl_init();
    $url = "https://api.pwnedpasswords.com/range/" . $prefix;

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if ($response !== false) {
        $matches = explode("\r\n", $response);
        foreach ($matches as $match) {
            list($hashSuffix, $count) = explode(":", $match);
            if ($suffix === $hashSuffix) {
                curl_close($ch);
                return true; // Password is breached
            }
        }
    }

    curl_close($ch);
    return false; // Password is not breached
}
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $myusername = mysqli_real_escape_string($db, $_POST['username']);
    $mypassword = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($db, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $myusername);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && $row = mysqli_fetch_array($result)) {
            // Verify the password using password_verify
            $hashedPassword = $row['password'];
            $totp_secret = $row['totp_secret'];
            $t = time();
            $timestamp = date("Y-m-d",$t);
            syslog(LOG_INFO, $timestamp . "Password check\n");
            if (password_verify($mypassword, $hashedPassword)) {
                if (validateAuthenticationToken($totp_secret, $_POST['2Ftoken'])){
                    // Generate a session token
                    $sessionToken = bin2hex(random_bytes(32)); // Generates a random 64-character token
                    // Store the token in a session variable
                    $_SESSION['session_token'] = $sessionToken;
                    // Set a cookie with the session token
                    setcookie('session_token', $sessionToken, time() + 3600, '/', '', true, true); // Cookie expires in 1 hour
                    $_SESSION['login_user'] = $myusername;
                    $_SESSION['login_role'] = $row['role'];
                    if (isPasswordBreached(trim($_POST["password"]))) {
                        echo '<script type="text/javascript">alert("Please change your password - it is breached!");</script>';
                    }
                    $_SESSION['last_activity'] = time();
                    header("location: welcome.php");
                    $t = time();
                    $timestamp = date("Y-m-d",$t);
                    syslog(LOG_INFO, $timestamp . " Valid user login\n");
                    exit; // Make sure to exit after a successful login to prevent further processing
                }
            } else {
                $login_err = "Your Login Name or Password 2FA Token is invalid";
                $t = time();
                $timestamp = date("Y-m-d",$t);
                error_log($timestamp + " Invalid user login\n");
            }
        } else {
            $login_err = "Your Login Name or Password is invalid";
            $t = time();
            $timestamp = date("Y-m-d",$t);
            error_log($timestamp + " Invalid user login\n");
        }
    } else {
        // Handle the query preparation error here, e.g., log the error or show an error message
        $t = time();
        $timestamp = date("Y-m-d",$t);
        die($timestamp + "[1] Query preparation failed: " . mysqli_error($db)); // 1 - Unexpected error ID
    }
}
?>

<!-- ... HTML code ... -->

<!DOCTYPE html>
<html>

<head>
    <title>Login Page</title>
    <link rel="stylesheet" type="text/css" href="../css/login.css">
</head>

<body>
    <div>
        <b>Login</b>
        <form action="" method="post">
            <label>UserName :</label><input type="text" name="username" class="box" /><br /><br />
            <label>Password :</label><input type="password" name="password" class="box" /><br /><br />
            <label>2-FA-Token :</label><input type="text" name="2Ftoken" class="box" /><br /><br />
            <input type="submit" value="Submit" /><br />
        </form>
        <p>Don't have an account? <a href="registration.php">Register here</a>.</p>

        <?php
        if (!empty($login_err)) {
            echo '<div class="alert">' . $login_err . '</div>';
        }
        ?>
    </div>
</body>

</html>
