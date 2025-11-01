<?php
/**
 * Complete login after OTP verification
 * Finalizes the login process by setting session variables
 */

session_start();
header('Content-Type: application/json');

// Check if OTP was verified
if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
    echo json_encode([
        'success' => false,
        'message' => 'OTP verification required'
    ]);
    exit;
}

// Check if pending user data exists
if (!isset($_SESSION['pending_user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Session expired. Please login again.'
    ]);
    exit;
}

// Get database connection
require_once __DIR__ . '/../admin/config/db.php';

try {
    // Get pending user data
    $user_id = $_SESSION['pending_user_id'];
    $username = $_SESSION['pending_username'];
    $full_name = $_SESSION['pending_full_name'];
    $email = $_SESSION['pending_email'];
    $role_id = $_SESSION['pending_role_id'];
    $remember_me = $_SESSION['pending_remember_me'] ?? false;
    
    // Update last login timestamp
    try {
        $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $updateStmt->execute([$user_id]);
    } catch (PDOException $e) {
        error_log('Failed to update last_login: ' . $e->getMessage());
    }
    
    // Set session variables for authenticated user
    $_SESSION['user_id'] = (int)$user_id;
    $_SESSION['username'] = $username;
    $_SESSION['full_name'] = $full_name;
    $_SESSION['email'] = $email;
    $_SESSION['role_id'] = (int)$role_id;
    $_SESSION['logged_in'] = true;
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    // Set remember me cookie if requested (30 days)
    if ($remember_me) {
        setcookie('remember_user', $username, time() + (30 * 24 * 60 * 60), '/', '', false, true);
        setcookie('remember_me_active', '1', time() + (30 * 24 * 60 * 60), '/', '', false, true);
    } else {
        // Clear remember me cookie if not checked
        setcookie('remember_me_active', '', time() - 3600, '/', '', false, true);
    }
    
    // Clean up temporary session data
    unset($_SESSION['pending_user_id']);
    unset($_SESSION['pending_username']);
    unset($_SESSION['pending_full_name']);
    unset($_SESSION['pending_email']);
    unset($_SESSION['pending_role_id']);
    unset($_SESSION['pending_remember_me']);
    unset($_SESSION['otp_verified']);
    unset($_SESSION['otp_code']);
    unset($_SESSION['otp_email']);
    unset($_SESSION['otp_expires']);
    unset($_SESSION['otp_user_id']);
    
    // Check role and redirect accordingly
    // role_id: 1 = admin, 2 = staff
    $redirectUrl = 'admin/index.php';
    $showSelection = false;
    
    if ((int)$role_id === 2) {
        // Staff role - redirect directly to collection panel
        $redirectUrl = 'collection.php';
        $showSelection = false;
    } else {
        // Admin role - show panel selection
        $showSelection = true;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Login completed successfully',
        'redirect' => $redirectUrl,
        'show_selection' => $showSelection,
        'role_id' => (int)$role_id
    ]);
    
} catch (Exception $e) {
    error_log('Complete login error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to complete login. Please try again.'
    ]);
}
?>

