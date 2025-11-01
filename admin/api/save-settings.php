<?php
/**
 * Save Settings API
 * Handles saving various settings to the database
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Get database connection
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['category'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid request data']);
        exit;
    }
    
    $category = $data['category'];
    $user_id = $_SESSION['user_id'] ?? null;
    $updated = [];
    $errors = [];
    
    // Map of categories and their settings
    $allowedCategories = ['general', 'sms', 'smtp', 'notifications', 'system'];
    
    if (!in_array($category, $allowedCategories)) {
        echo json_encode(['success' => false, 'error' => 'Invalid category']);
        exit;
    }
    
    // Handle SMTP settings
    if ($category === 'smtp') {
        // First, get existing SMTP password if password field is empty
        $existingPassword = '';
        if (empty($data['password'])) {
            try {
                $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'smtp_password'");
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $existingPassword = $result['setting_value'];
                }
            } catch (PDOException $e) {
                error_log("Failed to get existing password: " . $e->getMessage());
            }
        }
        
        $smtpSettings = [
            'smtp_host' => ['value' => $data['host'] ?? '', 'type' => 'string'],
            'smtp_port' => ['value' => (string)($data['port'] ?? 587), 'type' => 'number'],
            'smtp_encryption' => ['value' => $data['encryption'] ?? 'tls', 'type' => 'string'],
            'smtp_username' => ['value' => $data['username'] ?? '', 'type' => 'string'],
            'smtp_password' => ['value' => !empty($data['password']) ? $data['password'] : $existingPassword, 'type' => 'string'],
            'smtp_from_email' => ['value' => $data['fromEmail'] ?? '', 'type' => 'string'],
            'smtp_from_name' => ['value' => $data['fromName'] ?? '', 'type' => 'string'],
            'smtp_timeout' => ['value' => (string)($data['timeout'] ?? 30), 'type' => 'number']
        ];
        
        foreach ($smtpSettings as $key => $setting) {
            try {
                // Use INSERT ... ON DUPLICATE KEY UPDATE
                $stmt = $pdo->prepare("
                    INSERT INTO settings (setting_key, setting_value, setting_type, category, updated_by)
                    VALUES (?, ?, ?, 'smtp', ?)
                    ON DUPLICATE KEY UPDATE
                        setting_value = VALUES(setting_value),
                        updated_by = VALUES(updated_by),
                        updated_at = CURRENT_TIMESTAMP
                ");
                
                $stmt->execute([
                    $key,
                    $setting['value'],
                    $setting['type'],
                    $user_id
                ]);
                
                $updated[] = $key;
            } catch (PDOException $e) {
                error_log("Failed to save setting {$key}: " . $e->getMessage());
                $errors[] = $key;
            }
        }
    }
    // Handle General settings
    elseif ($category === 'general') {
        $generalSettings = [
            'company_name' => ['value' => $data['companyName'] ?? '', 'type' => 'string'],
            'company_email' => ['value' => $data['companyEmail'] ?? '', 'type' => 'string'],
            'company_phone' => ['value' => $data['companyPhone'] ?? '', 'type' => 'string'],
            'company_address' => ['value' => $data['companyAddress'] ?? '', 'type' => 'string']
        ];
        
        foreach ($generalSettings as $key => $setting) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO settings (setting_key, setting_value, setting_type, category, updated_by)
                    VALUES (?, ?, ?, 'general', ?)
                    ON DUPLICATE KEY UPDATE
                        setting_value = VALUES(setting_value),
                        updated_by = VALUES(updated_by),
                        updated_at = CURRENT_TIMESTAMP
                ");
                
                $stmt->execute([
                    $key,
                    $setting['value'],
                    $setting['type'],
                    $user_id
                ]);
                
                $updated[] = $key;
            } catch (PDOException $e) {
                error_log("Failed to save setting {$key}: " . $e->getMessage());
                $errors[] = $key;
            }
        }
    }
    // Handle SMS settings
    elseif ($category === 'sms') {
        $smsSettings = [
            'sms_gateway' => ['value' => $data['smsGateway'] ?? 'textlk', 'type' => 'string'],
            'sms_sender_id' => ['value' => $data['senderId'] ?? '', 'type' => 'string'],
            'sms_enabled' => ['value' => ($data['enableSMS'] ?? false) ? '1' : '0', 'type' => 'boolean'],
            'sms_test_mode' => ['value' => ($data['testMode'] ?? false) ? '1' : '0', 'type' => 'boolean']
        ];
        
        foreach ($smsSettings as $key => $setting) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO settings (setting_key, setting_value, setting_type, category, updated_by)
                    VALUES (?, ?, ?, 'sms', ?)
                    ON DUPLICATE KEY UPDATE
                        setting_value = VALUES(setting_value),
                        updated_by = VALUES(updated_by),
                        updated_at = CURRENT_TIMESTAMP
                ");
                
                $stmt->execute([
                    $key,
                    $setting['value'],
                    $setting['type'],
                    $user_id
                ]);
                
                $updated[] = $key;
            } catch (PDOException $e) {
                error_log("Failed to save setting {$key}: " . $e->getMessage());
                $errors[] = $key;
            }
        }
    }
    // Handle Notification settings
    elseif ($category === 'notifications') {
        $notificationSettings = [
            'email_notifications' => ['value' => ($data['emailNotifications'] ?? false) ? '1' : '0', 'type' => 'boolean'],
            'payment_reminders' => ['value' => ($data['paymentReminders'] ?? false) ? '1' : '0', 'type' => 'boolean'],
            'low_stock_alerts' => ['value' => ($data['lowStockAlerts'] ?? false) ? '1' : '0', 'type' => 'boolean']
        ];
        
        foreach ($notificationSettings as $key => $setting) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO settings (setting_key, setting_value, setting_type, category, updated_by)
                    VALUES (?, ?, ?, 'notifications', ?)
                    ON DUPLICATE KEY UPDATE
                        setting_value = VALUES(setting_value),
                        updated_by = VALUES(updated_by),
                        updated_at = CURRENT_TIMESTAMP
                ");
                
                $stmt->execute([
                    $key,
                    $setting['value'],
                    $setting['type'],
                    $user_id
                ]);
                
                $updated[] = $key;
            } catch (PDOException $e) {
                error_log("Failed to save setting {$key}: " . $e->getMessage());
                $errors[] = $key;
            }
        }
    }
    // Handle System settings
    elseif ($category === 'system') {
        $systemSettings = [
            'currency' => ['value' => $data['currency'] ?? 'LKR', 'type' => 'string'],
            'date_format' => ['value' => $data['dateFormat'] ?? 'Y-m-d', 'type' => 'string'],
            'timezone' => ['value' => $data['timezone'] ?? 'Asia/Colombo', 'type' => 'string']
        ];
        
        foreach ($systemSettings as $key => $setting) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO settings (setting_key, setting_value, setting_type, category, updated_by)
                    VALUES (?, ?, ?, 'system', ?)
                    ON DUPLICATE KEY UPDATE
                        setting_value = VALUES(setting_value),
                        updated_by = VALUES(updated_by),
                        updated_at = CURRENT_TIMESTAMP
                ");
                
                $stmt->execute([
                    $key,
                    $setting['value'],
                    $setting['type'],
                    $user_id
                ]);
                
                $updated[] = $key;
            } catch (PDOException $e) {
                error_log("Failed to save setting {$key}: " . $e->getMessage());
                $errors[] = $key;
            }
        }
    }
    
    if (count($errors) > 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Some settings could not be saved',
            'errors' => $errors,
            'updated' => $updated
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'Settings saved successfully',
            'updated' => $updated
        ]);
    }
    
} catch (Exception $e) {
    error_log('Save settings error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to save settings: ' . $e->getMessage()
    ]);
}
?>

