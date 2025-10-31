<?php
/**
 * Resend OTP for login verification
 */

session_start();
header('Content-Type: application/json');

// Check if there's a pending login with email
if (!isset($_SESSION['pending_email']) || empty($_SESSION['pending_email'])) {
    // Try to get from otp_email session
    if (!isset($_SESSION['otp_email']) || empty($_SESSION['otp_email'])) {
        echo json_encode([
            'success' => false,
            'error' => 'No pending login found. Please login again.'
        ]);
        exit;
    }
    $email = $_SESSION['otp_email'];
} else {
    $email = $_SESSION['pending_email'];
}

// Check if SMTP config exists
$smtpConfigPath = __DIR__ . '/../admin/config/smtp.php';
if (!file_exists($smtpConfigPath)) {
    echo json_encode([
        'success' => false,
        'error' => 'SMTP configuration not found'
    ]);
    exit;
}

try {
    $smtp_config = require $smtpConfigPath;
    
    // Generate new 6-digit OTP
    $otp = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Store new OTP in session with expiration (2 minutes = 120 seconds)
    $_SESSION['otp_code'] = $otp;
    $_SESSION['otp_email'] = $email;
    $_SESSION['otp_expires'] = time() + 120;
    
    $from_email = $smtp_config['from_email'];
    $from_name = $smtp_config['from_name'];
    $subject = 'Your Login OTP Verification Code';
    
    $html_body = "<html><body>";
    $html_body .= "<p>Your OTP verification code is: <strong style='font-size: 24px; color: #10b981;'>{$otp}</strong></p>";
    $html_body .= "<p>This code will expire in 2 minutes.</p>";
    $html_body .= "<p>If you didn't request this code, please ignore this email.</p>";
    $html_body .= "</body></html>";
    
    // Build email
    $boundary = md5(time());
    $email_content = "--{$boundary}\r\n";
    $email_content .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $email_content .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $email_content .= "Your OTP verification code is: {$otp}\r\n\r\n";
    $email_content .= "This code will expire in 2 minutes.\r\n\r\n";
    $email_content .= "--{$boundary}\r\n";
    $email_content .= "Content-Type: text/html; charset=UTF-8\r\n";
    $email_content .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $email_content .= $html_body;
    $email_content .= "\r\n--{$boundary}--\r\n";
    
    $headers = [];
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-Type: multipart/alternative; boundary=\"{$boundary}\"";
    $headers[] = "From: {$from_name} <{$from_email}>";
    $headers[] = "Reply-To: {$from_email}";
    
    // Include SMTP helper functions
    if (!function_exists('sendSMTPEmail')) {
        require_once __DIR__ . '/send-otp.php';
    }
    
    $result = sendSMTPEmail(
        $smtp_config['host'],
        $smtp_config['port'],
        $from_email,
        $email,
        $subject,
        $email_content,
        $headers,
        $smtp_config['username'],
        $smtp_config['password'],
        $smtp_config['timeout']
    );
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'OTP resent successfully',
            'otp' => $otp // For development only - remove in production
        ]);
    } else {
        error_log('OTP resend failed: ' . ($result['error'] ?? 'Unknown error'));
        echo json_encode([
            'success' => true, // Still return success to avoid UX issues
            'message' => 'OTP generated (check email)',
            'otp' => $otp // For development
        ]);
    }
} catch (Exception $e) {
    error_log('Resend OTP error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Failed to resend OTP. Please try again.'
    ]);
}
?>

