<?php
/**
 * Logout handler
 * Destroys session and redirects to login page
 */

session_start();

// Log the logout action
if (isset($_SESSION['username'])) {
    error_log("User logout: " . $_SESSION['username']);
}

// Destroy all session data
$_SESSION = array();

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Clear remember me cookie if it exists
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/', '', false, true);
}

// Redirect to login page
header('Location: ../login.php');
exit;
?>

