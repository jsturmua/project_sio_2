<?php

include("./config.php");

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
            if (password_verify($mypassword, $hashedPassword)) {
                $_SESSION['login_user'] = $myusername;
                $_SESSION['login_role'] = $row['role'];
                if (isPasswordBreached(trim($_POST["password"]))) {
                    echo '<script type="text/javascript">alert("Please change your password - it is breached!");</script>';
                }
                header("location: welcome.php");
                exit; // Make sure to exit after a successful login to prevent further processing
            } else {
                $login_err = "Your Login Name or Password is invalid";
            }
        } else {
            $login_err = "Your Login Name or Password is invalid";
        }
    } else {
        // Handle the query preparation error here, e.g., log the error or show an error message
        die("Query preparation failed: " . mysqli_error($db));
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
