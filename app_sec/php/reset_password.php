<?php
include('reauthentication.php');

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
// Initialize the session
session_start();

// Check if the user is logged in, otherwise redirect to the login page
if (!isset($_SESSION["login_user"])) {
    header("location: login.php");
    exit;
} else {
    checkReauthentication();
}

// Include config file
require_once "config.php";

// ... (previous code remains unchanged)

// Define variables and initialize with empty values
$current_password = $new_password = $confirm_password = "";
$current_password_err = $new_password_err = $confirm_password_err = "";

// Processing form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $t = time();
    $timestamp = date("Y-m-d",$t);
    syslog(LOG_INFO, $timestamp + " New password validation\n");
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";
    } else {
        // Remove leading/trailing spaces and trim consecutive multiple spaces into one
        $password = preg_replace('/\s+/', ' ', trim($_POST["password"]));

        if(strlen($password) < 12){
            $password_err = "Password must have at least 12 characters.";
        } elseif(strlen($password) > 128){
            $password_err = "Password must not have more than 128 characters.";
        } elseif(strlen(preg_replace('/\s+/', ' ', $password)) < 12){
            $password_err = "Password must be at least 12 characters after combining multiple spaces.";
        } elseif (isPasswordBreached($password)) {
            $password_err = "Password has been compromised in a data breach. Please choose a different password.";
        }
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm the password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Passwords did not match.";
        }
    }

    // Verify the current password before updating
    if (empty($current_password_err) && empty($new_password_err) && empty($confirm_password_err)) {
        // Check if the entered current password matches the stored password
        $sql = "SELECT password FROM users WHERE username = ?";
        if ($stmt = mysqli_prepare($db, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $_SESSION["login_user"];
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($current_password, $hashed_password)) {
                            // Current password is verified; proceed with updating the password
                            $update_sql = "UPDATE users SET password = ? WHERE username = ?";
                            if ($update_stmt = mysqli_prepare($db, $update_sql)) {
                                mysqli_stmt_bind_param($update_stmt, "ss", $param_password, $param_id);
                                $param_password = password_hash($new_password, PASSWORD_DEFAULT);
                                $param_id = $_SESSION["login_user"];
                                if (mysqli_stmt_execute($update_stmt)) {
                                    // Password updated successfully. Destroy the session and redirect to the login page
                                    session_destroy();
                                    header("location: login.php");
                                    exit();
                                } else {
                                    echo "Oops! Something went wrong. Please try again later.";
                                }
                                mysqli_stmt_close($update_stmt);
                            }
                        } else {
                            $current_password_err = "Incorrect password.";
                        }
                    }
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Close the connection
    mysqli_close($db);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
        .password-strength {
            width: 200px;
            height: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
    <script src="../js/password_strength.js"></script>
</head>
<body>
    <div class="wrapper">
        <h2>Reset Password</h2>
        <p>Please fill out this form to reset your password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
        <div class="form-group">
            <label>Current Password</label>
            <input type="password" name="current_password" class="form-control <?php echo (!empty($current_password_err)) ? 'is-invalid' : ''; ?>">
            <span class="invalid-feedback"><?php echo $current_password_err; ?></span>
        </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>" onkeyup="checkPasswordStrength(this.value)">
                <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
                <div id="password-strength" class="password-strength"></div>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link ml-2" href="welcome.php">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
