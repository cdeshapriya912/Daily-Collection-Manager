<?php

declare(strict_types=1);

// Security headers
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Enable CORS for testing (remove in production or restrict to your domain)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed. Only POST requests are accepted.'
    ]);
    exit;
}

// Include SMS configuration
require_once __DIR__ . '/../config/sms.php';

/**
 * Send SMS using Text.lk API
 */
function sendSMS(string $recipient, string $message): array {
    $url = SMSConfig::getApiUrl();
    $apiKey = SMSConfig::getApiKey();
    $senderId = SMSConfig::getSenderId();
    
    // Prepare request data
    $data = [
        'recipient' => $recipient,
        'sender_id' => $senderId,
        'type' => 'plain',
        'message' => $message
    ];
    
    // Prepare headers
    $headers = [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, SMSConfig::shouldVerifySSL());
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    
    // Execute request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    // Handle cURL errors
    if ($error) {
        error_log("SMS cURL Error: " . $error);
        return [
            'success' => false,
            'error' => 'Failed to connect to SMS service. Please try again later.',
            'http_code' => 0,
            'curl_error' => $error
        ];
    }
    
    // Decode response
    $responseData = json_decode($response, true);
    
    $success = $httpCode === 200;
    $errorMsg = null;
    if (!$success && is_array($responseData)) {
        $errorMsg = $responseData['message'] ?? $responseData['error'] ?? null;
    }
    return [
        'success' => $success,
        'http_code' => $httpCode,
        'response' => $responseData,
        'raw' => $response,
        'gateway_error' => $errorMsg
    ];
}

// Get and validate input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate required fields
if (!isset($data['customer_mobile']) || !isset($data['payment_amount']) || !isset($data['remaining_balance'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Missing required fields: customer_mobile, payment_amount, remaining_balance'
    ]);
    exit;
}

// Sanitize and validate input
$customerMobile = trim($data['customer_mobile']);
$paymentAmount = filter_var($data['payment_amount'], FILTER_VALIDATE_FLOAT);
$remainingBalance = filter_var($data['remaining_balance'], FILTER_VALIDATE_FLOAT);
$customerName = isset($data['customer_name']) ? trim($data['customer_name']) : 'Customer';

// Validate payment amount
if ($paymentAmount === false || $paymentAmount < 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid payment amount'
    ]);
    exit;
}

// Validate remaining balance
if ($remainingBalance === false || $remainingBalance < 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid remaining balance'
    ]);
    exit;
}

// Format phone number and get recipient (test number if in testing mode)
$recipient = SMSConfig::getRecipientNumber($customerMobile);

// Create SMS message
$date = date('Y-m-d H:i');
$message = "Payment Confirmation\n\n";
$message .= "Date: {$date}\n";
$message .= "Customer: {$customerName}\n";
$message .= "Paid Amount: Rs. " . number_format($paymentAmount, 2) . "\n";
$message .= "Remaining Balance: Rs. " . number_format($remainingBalance, 2) . "\n\n";
$message .= "Thank you for your payment!";

// Send SMS
$result = sendSMS($recipient, $message);

// Log SMS attempt (for audit trail)
$logData = [
    'timestamp' => date('Y-m-d H:i:s'),
    'customer_mobile' => $customerMobile,
    'recipient' => $recipient,
    'payment_amount' => $paymentAmount,
    'remaining_balance' => $remainingBalance,
    'success' => $result['success'],
    'http_code' => $result['http_code']
];
error_log("SMS Send Attempt: " . json_encode($logData));

// Return response
if ($result['success']) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'SMS sent successfully',
        'recipient' => SMSConfig::isTestingMode() ? 'Test number: ' . SMSConfig::getTestMobile() : $customerMobile,
        'response' => $result['response']
    ]);
} else {
    http_response_code(500);
    $payload = [
        'success' => false,
        'error' => $result['gateway_error'] ?? $result['error'] ?? 'Failed to send SMS',
        'http_code' => $result['http_code'],
        'response' => $result['response'] ?? null
    ];
    // In testing mode, expose raw response to help diagnose issues
    if (SMSConfig::isTestingMode()) {
        $payload['raw'] = $result['raw'] ?? null;
        $payload['curl_error'] = $result['curl_error'] ?? null;
    }
    echo json_encode($payload);
}

