<?php
/**
 * Send OTP code via email using SMTP
 */

session_start();
header('Content-Type: application/json');

// Load SMTP configuration
$smtp_config = require __DIR__ . '/../admin/config/smtp.php';

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$email = $input['email'] ?? '';

if (empty($email)) {
    echo json_encode([
        'success' => false,
        'error' => 'Email address is required'
    ]);
    exit;
}

// Generate 6-digit OTP
$otp = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);

// Store OTP in session with expiration (2 minutes = 120 seconds)
$_SESSION['otp_code'] = $otp;
$_SESSION['otp_email'] = $email;
$_SESSION['otp_expires'] = time() + 120; // 120 seconds from now

// Email content
$from_email = $smtp_config['from_email'];
$from_name = $smtp_config['from_name'];
$subject = 'Your OTP Verification Code';
$message = "Your OTP verification code is: <strong>{$otp}</strong><br><br>This code will expire in 2 minutes.<br><br>If you didn't request this code, please ignore this email.";

// Build email message (plain text and HTML)
$email_body = "Your OTP verification code is: {$otp}\r\n\r\n";
$email_body .= "This code will expire in 2 minutes.\r\n\r\n";
$email_body .= "If you didn't request this code, please ignore this email.";

// HTML version
$html_body = "<html><body>";
$html_body .= "<p>Your OTP verification code is: <strong style='font-size: 24px; color: #10b981;'>{$otp}</strong></p>";
$html_body .= "<p>This code will expire in 2 minutes.</p>";
$html_body .= "<p>If you didn't request this code, please ignore this email.</p>";
$html_body .= "</body></html>";

// Build multipart email
$boundary = md5(time());
$email_content = "--{$boundary}\r\n";
$email_content .= "Content-Type: text/plain; charset=UTF-8\r\n";
$email_content .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$email_content .= $email_body . "\r\n\r\n";

$email_content .= "--{$boundary}\r\n";
$email_content .= "Content-Type: text/html; charset=UTF-8\r\n";
$email_content .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$email_content .= $html_body . "\r\n";
$email_content .= "--{$boundary}--\r\n";

// Email headers
$headers = [];
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: multipart/alternative; boundary=\"{$boundary}\"";
$headers[] = "From: {$from_name} <{$from_email}>";
$headers[] = "Reply-To: {$from_email}";
$headers[] = "X-Mailer: PHP/" . phpversion();

// Send email using SMTP
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
        'message' => 'OTP sent successfully to ' . $email,
        'otp' => $otp // Only for development/testing - remove in production
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => $result['error'] ?? 'Failed to send OTP email'
    ]);
}

/**
 * Read SMTP response (handle multi-line responses)
 */
function readSMTPResponse($fp) {
    $response = '';
    while ($line = fgets($fp, 515)) {
        $response .= $line;
        if (substr(trim($line), 3, 1) === ' ') {
            break;
        }
    }
    return $response;
}

/**
 * Send email via SMTP
 */
function sendSMTPEmail($host, $port, $from, $to, $subject, $body, $headers, $username = '', $password = '', $timeout = 30) {
    $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
    
    if (!$fp) {
        return [
            'success' => false,
            'error' => "Cannot connect to SMTP server: {$errstr} ({$errno})"
        ];
    }
    
    stream_set_timeout($fp, $timeout);
    
    // Read server welcome response
    $response = readSMTPResponse($fp);
    if (substr($response, 0, 3) !== '220') {
        fclose($fp);
        return [
            'success' => false,
            'error' => 'SMTP server error: ' . trim($response)
        ];
    }
    
    // Send EHLO
    fputs($fp, "EHLO {$host}\r\n");
    $response = readSMTPResponse($fp);
    if (substr($response, 0, 3) !== '250') {
        fputs($fp, "HELO {$host}\r\n");
        $response = readSMTPResponse($fp);
        if (substr($response, 0, 3) !== '250') {
            fclose($fp);
            return [
                'success' => false,
                'error' => 'SMTP HELO/EHLO error: ' . trim($response)
            ];
        }
    }
    
    // Authentication
    if (!empty($username) && !empty($password)) {
        fputs($fp, "AUTH LOGIN\r\n");
        $response = readSMTPResponse($fp);
        
        if (substr($response, 0, 3) === '334') {
            fputs($fp, base64_encode($username) . "\r\n");
            $response = readSMTPResponse($fp);
            
            if (substr($response, 0, 3) === '334') {
                fputs($fp, base64_encode($password) . "\r\n");
                $response = readSMTPResponse($fp);
                
                if (substr($response, 0, 3) !== '235') {
                    fclose($fp);
                    return [
                        'success' => false,
                        'error' => 'SMTP authentication failed: ' . trim($response)
                    ];
                }
            }
        }
    }
    
    // Set FROM
    fputs($fp, "MAIL FROM: <{$from}>\r\n");
    $response = readSMTPResponse($fp);
    if (substr($response, 0, 3) !== '250') {
        fclose($fp);
        return [
            'success' => false,
            'error' => 'SMTP MAIL FROM error: ' . trim($response)
        ];
    }
    
    // Set TO
    fputs($fp, "RCPT TO: <{$to}>\r\n");
    $response = readSMTPResponse($fp);
    if (substr($response, 0, 3) !== '250') {
        fclose($fp);
        return [
            'success' => false,
            'error' => 'SMTP RCPT TO error: ' . trim($response)
        ];
    }
    
    // Send DATA
    fputs($fp, "DATA\r\n");
    $response = readSMTPResponse($fp);
    if (substr($response, 0, 3) !== '354') {
        fclose($fp);
        return [
            'success' => false,
            'error' => 'SMTP DATA error: ' . trim($response)
        ];
    }
    
    // Send headers and body
    $header_string = implode("\r\n", $headers);
    fputs($fp, "Subject: {$subject}\r\n");
    fputs($fp, "To: <{$to}>\r\n");
    fputs($fp, $header_string . "\r\n\r\n");
    fputs($fp, $body);
    fputs($fp, "\r\n.\r\n");
    $response = readSMTPResponse($fp);
    
    if (substr($response, 0, 3) !== '250') {
        fclose($fp);
        return [
            'success' => false,
            'error' => 'SMTP message send error: ' . trim($response)
        ];
    }
    
    // Quit
    fputs($fp, "QUIT\r\n");
    readSMTPResponse($fp);
    fclose($fp);
    
    return [
        'success' => true,
        'message' => 'Email sent successfully'
    ];
}
?>




