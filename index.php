<?php
/**
 * Index page - Redirect based on login status
 */
session_start();

// Check if user is logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // Redirect to admin dashboard
    header('Location: admin/index.php');
    exit;
} else {
    // Redirect to login page
    header('Location: login.php');
    exit;
}
?>

