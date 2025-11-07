<?php
/**
 * Get Settings API
 * Retrieves settings from the database
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

try {
    $category = $_GET['category'] ?? 'all';
    
    if ($category === 'all') {
        // Get all settings
        $stmt = $pdo->query("SELECT setting_key, setting_value, setting_type, category FROM settings");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $settings = [];
        foreach ($results as $row) {
            // Convert value based on type
            $value = $row['setting_value'];
            if ($row['setting_type'] === 'boolean') {
                $value = ($value === '1' || $value === 'true');
            } elseif ($row['setting_type'] === 'number') {
                $value = (int)$value;
            } elseif ($row['setting_type'] === 'json') {
                $value = json_decode($value, true);
            }
            
            $settings[$row['setting_key']] = $value;
        }
        
        echo json_encode([
            'success' => true,
            'settings' => $settings
        ]);
    } else {
        // Get settings by category
        $stmt = $pdo->prepare("SELECT setting_key, setting_value, setting_type FROM settings WHERE category = ?");
        $stmt->execute([$category]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $settings = [];
        foreach ($results as $row) {
            // Convert value based on type
            $value = $row['setting_value'];
            if ($row['setting_type'] === 'boolean') {
                $value = ($value === '1' || $value === 'true');
            } elseif ($row['setting_type'] === 'number') {
                $value = (int)$value;
            } elseif ($row['setting_type'] === 'json') {
                $value = json_decode($value, true);
            }
            
            $settings[$row['setting_key']] = $value;
        }
        
        echo json_encode([
            'success' => true,
            'category' => $category,
            'settings' => $settings
        ]);
    }
    
} catch (Exception $e) {
    error_log('Get settings error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load settings: ' . $e->getMessage()
    ]);
}
?>









