<?php
/**
 * SMTP Email Functions
 * Utility functions for sending emails via SMTP
 */

if (!function_exists('readSMTPResponse')) {
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
}

if (!function_exists('sendSMTPEmail')) {
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
}
?>

