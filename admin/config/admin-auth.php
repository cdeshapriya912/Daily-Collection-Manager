<?php
/**
 * Admin Authentication & Authorization Check
 * Include this file at the top of admin-only pages to:
 * 1. Ensure user is logged in
 * 2. Ensure user has admin role (not staff)
 * 3. Redirect staff users to collection panel
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Not logged in - redirect to login page
    header('Location: ../login.php');
    exit;
}

// Check user role
$userRoleId = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : 0;

// Role ID: 1 = admin, 2 = staff
if ($userRoleId === 2) {
    // Staff role - not authorized for admin dashboard
    // Redirect to collection panel
    header('Location: ../collection.php');
    exit;
}

// If we got here, user is logged in and is an admin
// Continue with the page
?>



