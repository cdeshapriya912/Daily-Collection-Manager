<?php
/**
 * Send email with image attachment via SMTP (Mailpit)
 * 
 * SMTP configuration is loaded from config/smtp.php
 */

// Set JSON content type
header('Content-Type: application/json');

// Load SMTP configuration
$smtp_config = require __DIR__ . '/config/smtp.php';

// Extract SMTP settings from config
$smtp_host = $smtp_config['host'];
$smtp_port = $smtp_config['port'];
$smtp_encryption = $smtp_config['encryption'];
$smtp_username = $smtp_config['username'];
$smtp_password = $smtp_config['password'];

// Get POST data if available, otherwise use defaults
$input = json_decode(file_get_contents('php://input'), true);

// Email settings (use config defaults if not provided)
$from_email = $input['from'] ?? $smtp_config['from_email'];
$from_name = $input['from_name'] ?? $smtp_config['from_name'];
$to_email = $input['to'] ?? 'recipient@example.com';
$subject = 'Test Email with Image Attachment';
$message = 'This is a test email sent via Mailpit SMTP with an image attachment.';

// Image file path (relative to this script)
$image_path = __DIR__ . '/sample-image.jpg';

// Check if image exists, if not create a simple placeholder
if (!file_exists($image_path)) {
    // Create a simple test image
    $img = imagecreatetruecolor(400, 300);
    $bg_color = imagecolorallocate($img, 70, 130, 180);
    $text_color = imagecolorallocate($img, 255, 255, 255);
    imagefilledrectangle($img, 0, 0, 400, 300, $bg_color);
    imagestring($img, 5, 100, 140, 'Sample Test Image', $text_color);
    imagejpeg($img, $image_path, 90);
    imagedestroy($img);
}

// Generate boundaries
$main_boundary = md5(time());
$related_boundary = md5(time() . 'related');
$alt_boundary = md5(time() . 'alt');

// Read image file
$image_data = file_get_contents($image_path);
$image_base64 = base64_encode($image_data);
$image_name = basename($image_path);

// Build email message structure:
// multipart/mixed
//   - multipart/related
//     - multipart/alternative
//       - text/plain
//       - text/html (with embedded image reference)
//     - image/jpeg (inline/embedded)
//   - image/jpeg (attachment)

$email_body = "--{$main_boundary}\r\n";
$email_body .= "Content-Type: multipart/related; boundary=\"{$related_boundary}\"\r\n\r\n";

// Alternative part (text and HTML)
$email_body .= "--{$related_boundary}\r\n";
$email_body .= "Content-Type: multipart/alternative; boundary=\"{$alt_boundary}\"\r\n\r\n";

// Plain text version
$email_body .= "--{$alt_boundary}\r\n";
$email_body .= "Content-Type: text/plain; charset=UTF-8\r\n";
$email_body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$email_body .= $message . "\r\n";
$email_body .= "\r\nImage is attached to this email.\r\n\r\n";

// HTML version
$email_body .= "--{$alt_boundary}\r\n";
$email_body .= "Content-Type: text/html; charset=UTF-8\r\n";
$email_body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$email_body .= "<html><body>";
$email_body .= "<p>" . htmlspecialchars($message) . "</p>";
$email_body .= "<p><strong>Embedded Image:</strong></p>";
$email_body .= "<img src='cid:embedded-image' alt='Sample Image' style='max-width: 400px; border: 2px solid #ccc; padding: 10px; display: block;' />";
$email_body .= "<p><small>Image is also attached to this email.</small></p>";
$email_body .= "</body></html>\r\n\r\n";
$email_body .= "--{$alt_boundary}--\r\n\r\n";

// Embedded inline image
$email_body .= "--{$related_boundary}\r\n";
$email_body .= "Content-Type: image/jpeg; name=\"{$image_name}\"\r\n";
$email_body .= "Content-Disposition: inline; filename=\"{$image_name}\"\r\n";
$email_body .= "Content-ID: <embedded-image>\r\n";
$email_body .= "Content-Transfer-Encoding: base64\r\n\r\n";
$email_body .= chunk_split($image_base64) . "\r\n";
$email_body .= "--{$related_boundary}--\r\n\r\n";

// Attached image
$email_body .= "--{$main_boundary}\r\n";
$email_body .= "Content-Type: image/jpeg; name=\"{$image_name}\"\r\n";
$email_body .= "Content-Disposition: attachment; filename=\"{$image_name}\"\r\n";
$email_body .= "Content-Transfer-Encoding: base64\r\n\r\n";
$email_body .= chunk_split($image_base64) . "\r\n";

$email_body .= "--{$main_boundary}--\r\n";

// Email headers
$headers = [];
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: multipart/mixed; boundary=\"{$main_boundary}\"";
$headers[] = "From: {$from_name} <{$from_email}>";
$headers[] = "Reply-To: {$from_email}";
$headers[] = "X-Mailer: PHP/" . phpversion();

// Send email using SMTP
try {
    $result = sendSMTPEmail($smtp_host, $smtp_port, $from_email, $to_email, $subject, $email_body, $headers, $smtp_username, $smtp_password, $smtp_config['timeout']);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => 'Email sent successfully!',
            'details' => $result['message']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to send email',
            'error' => $result['error']
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

/**
 * Read SMTP response (handle multi-line responses)
 */
function readSMTPResponse($fp) {
    $response = '';
    while ($line = fgets($fp, 515)) {
        $response .= $line;
        // SMTP response lines ending with space continue, ending with dash are continuation
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
    
    // Set timeout
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
        // Try HELO if EHLO fails
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
    
    // Authentication (not needed for Mailpit, but handle if provided)
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
    $response = readSMTPResponse($fp);
    fclose($fp);
    
    return [
        'success' => true,
        'message' => 'Email sent successfully to Mailpit'
    ];
}
?>

