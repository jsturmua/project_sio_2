<?php
session_start();
function checkReauthentication() {
    if (isset($_SESSION['login_user'])) {
        $lastActivityTime = isset($_SESSION['last_activity']) ? $_SESSION['last_activity'] : 0;
        $currentTime = time();
        $inactiveDuration = 1800; // 30 minutes in seconds

        if (($currentTime - $lastActivityTime) > $inactiveDuration) {
            // Redirect to logout page
            header("Location: logout.php");
            exit();
        }

        // Update last activity time
        $_SESSION['last_activity'] = time();
    }
}
?>
