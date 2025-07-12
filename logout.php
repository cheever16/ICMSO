<?php
// Start output buffering to avoid header issues
ob_start();

// Start the session
session_start();

// Clear all session variables
$_SESSION = array();

// Delete the PHP session cookie (if it exists)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Clear custom login cookies
setcookie('user_id', '', time() - 3600, '/');
setcookie('username', '', time() - 3600, '/');
setcookie('role', '', time() - 3600, '/');

// Optional delay to ensure browser clears cookies (can help in rare cases)
sleep(1);

// Redirect to homepage
header('Location: index.php');
exit();

// End output buffering
ob_end_flush();
?>