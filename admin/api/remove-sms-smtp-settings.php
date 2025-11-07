<?php
/**
 * Remove SMS and SMTP settings from database
 * This script removes all SMS and SMTP related settings from the settings table
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    die('Unauthorized - Please login first');
}

// Get database connection
require_once __DIR__ . '/../config/db.php';

try {
    // List of SMS and SMTP setting keys to remove
    $settingsToRemove = [
        // SMS settings
        'sms_gateway',
        'sms_sender_id',
        'sms_enabled',
        'sms_test_mode',
        
        // SMTP settings
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
        'smtp_username',
        'smtp_password',
        'smtp_from_email',
        'smtp_from_name',
        'smtp_timeout'
    ];
    
    $deleted = [];
    $errors = [];
    
    foreach ($settingsToRemove as $settingKey) {
        try {
            $stmt = $pdo->prepare("DELETE FROM settings WHERE setting_key = ?");
            $stmt->execute([$settingKey]);
            
            if ($stmt->rowCount() > 0) {
                $deleted[] = $settingKey;
            }
        } catch (PDOException $e) {
            error_log("Failed to delete setting {$settingKey}: " . $e->getMessage());
            $errors[] = $settingKey;
        }
    }
    
    // Show results
    echo "<!doctype html>";
    echo "<html><head><title>Remove SMS/SMTP Settings</title>";
    echo "<style>body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; } ";
    echo ".success { color: green; } .error { color: red; } .info { background: #f0f0f0; padding: 10px; border-radius: 5px; }</style>";
    echo "</head><body>";
    echo "<h1>Remove SMS/SMTP Settings</h1>";
    
    if (count($deleted) > 0) {
        echo "<div class='success'><h2>✓ Deleted Settings (" . count($deleted) . "):</h2><ul>";
        foreach ($deleted as $key) {
            echo "<li>{$key}</li>";
        }
        echo "</ul></div>";
    }
    
    if (count($errors) > 0) {
        echo "<div class='error'><h2>✗ Errors:</h2><ul>";
        foreach ($errors as $key) {
            echo "<li>Failed to delete: {$key}</li>";
        }
        echo "</ul></div>";
    }
    
    if (count($deleted) > 0 && count($errors) === 0) {
        echo "<div class='success'><p><strong>All SMS and SMTP settings have been removed successfully!</strong></p></div>";
    }
    
    echo "<div class='info'><p><a href='../settings.php'>← Back to Settings</a></p></div>";
    echo "</body></html>";
    
} catch (Exception $e) {
    error_log('Remove settings error: ' . $e->getMessage());
    http_response_code(500);
    echo "<!doctype html>";
    echo "<html><head><title>Error</title></head><body>";
    echo "<h1>Error</h1>";
    echo "<p>Failed to remove settings: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><a href='../settings.php'>← Back to Settings</a></p>";
    echo "</body></html>";
}
?>









