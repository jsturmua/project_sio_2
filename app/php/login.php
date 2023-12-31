<?php
include("./config.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $myusername = $_POST['username'];
    $mypassword = $_POST['password'];

    // WARNING: This is an example of a vulnerable SQL query
    $sql = "SELECT * FROM users WHERE username = '$myusername' AND password = '$mypassword'";
    $result = mysqli_query($db, $sql);

    if ($result && $row = mysqli_fetch_array($result)) {
        $_SESSION['login_user'] = $myusername;
        $_SESSION['login_role'] = $row['role'];
        header("location: welcome.php");
    } else {
        $login_err = "Your Login Name or Password is invalid";
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
                <p>Don´t have an account? <a href="registration.php">Register here</a>.</p>
            
            <?php
            if (!empty($login_err)) {
                echo '<div class="alert">' . $login_err . '</div>';
            }
            ?>
    </div>
</body>

</html>