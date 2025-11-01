<?php
/**
 * Admin Authentication & Authorization Check
 * Include this file at the top of admin-only pages to:
 * 1. Ensure user is logged in
 * 2. Ensure user has admin role (not staff)
 * 3. Redirect staff users to collection panel
 * 4. Support developer mode for testing
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load developer mode configuration
require_once __DIR__ . '/developer-mode.php';

// Check for developer mode
if (isDeveloperMode()) {
    // Initialize developer session
    initDeveloperMode();
    
    // Developer mode is active - bypass authentication
    // Continue with the page
} else {
    // Normal authentication flow
    
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
}

// If we got here, user is logged in and is an admin (or developer mode is active)
// Continue with the page
?>



