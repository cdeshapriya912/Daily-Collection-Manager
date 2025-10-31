<?php
/**
 * Verify OTP code
 */

session_start();
header('Content-Type: application/json');

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$otp = $input['otp'] ?? '';

if (empty($otp)) {
    echo json_encode([
        'success' => false,
        'error' => 'OTP code is required'
    ]);
    exit;
}

// Check if OTP exists in session
if (!isset($_SESSION['otp_code'])) {
    echo json_encode([
        'success' => false,
        'error' => 'OTP session expired. Please request a new code.'
    ]);
    exit;
}

// Check if OTP has expired
if (isset($_SESSION['otp_expires']) && time() > $_SESSION['otp_expires']) {
    unset($_SESSION['otp_code']);
    unset($_SESSION['otp_expires']);
    unset($_SESSION['otp_email']);
    echo json_encode([
        'success' => false,
        'error' => 'OTP code has expired. Please request a new code.'
    ]);
    exit;
}

// Verify OTP
if ($_SESSION['otp_code'] === $otp) {
    // OTP is correct
    $_SESSION['otp_verified'] = true;
    // Keep OTP code and expiration until login is completed
    // They will be cleared in complete-login.php
    
    echo json_encode([
        'success' => true,
        'message' => 'OTP verified successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid OTP code. Please try again.'
    ]);
}
?>

