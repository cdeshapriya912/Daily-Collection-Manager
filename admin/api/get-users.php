<?php
/**
 * Get Users API
 * Returns list of users (staff) with optional search and filtering
 */

session_start();

// Prevent caching - force fresh data always
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Get database connection
require_once __DIR__ . '/../config/db.php';

try {
    // Get query parameters
    $search = $_GET['search'] ?? '';
    $roleId = $_GET['role_id'] ?? '';
    $status = $_GET['status'] ?? '';
    
    // Log parameters for debugging
    error_log('Get Users API called with: search=' . $search . ', role_id=' . $roleId . ', status=' . $status);
    
    // Build query with joins to get role name
    $sql = "
        SELECT 
            u.id,
            u.username,
            u.full_name,
            u.email,
            u.mobile,
            u.role_id,
            r.name as role_name,
            u.status,
            u.last_login,
            u.created_at
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.id
        WHERE 1=1
    ";
    
    $params = [];
    
    // Add search filter
    if (!empty($search)) {
        $sql .= " AND (
            u.full_name LIKE ? OR 
            u.username LIKE ? OR 
            u.email LIKE ? OR
            u.id LIKE ?
        )";
        $searchTerm = "%{$search}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        error_log('Adding search filter: ' . $search);
    }
    
    // Add role filter - ensure empty string is treated as "no filter"
    if (!empty($roleId) && is_numeric($roleId)) {
        $sql .= " AND u.role_id = ?";
        $params[] = (int)$roleId;
        error_log('Adding role filter: ' . $roleId);
    } else {
        error_log('No role filter applied (showing all roles)');
    }
    
    // Add status filter
    if (!empty($status) && in_array($status, ['active', 'disabled', 'suspended'])) {
        $sql .= " AND u.status = ?";
        $params[] = $status;
    }
    
    // Order by name
    $sql .= " ORDER BY u.full_name ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log('Query returned ' . count($users) . ' users from database');
    
    // Format response
    $formattedUsers = [];
    foreach ($users as $user) {
        $formattedUsers[] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'email' => $user['email'] ?? '',
            'mobile' => $user['mobile'] ?? '',
            'role_id' => (int)$user['role_id'],
            'role_name' => $user['role_name'] ?? 'Unknown',
            'status' => $user['status'],
            'last_login' => $user['last_login'],
            'created_at' => $user['created_at']
        ];
    }
    
    error_log('Returning ' . count($formattedUsers) . ' formatted users to frontend');
    
    echo json_encode([
        'success' => true,
        'users' => $formattedUsers,
        'count' => count($formattedUsers)
    ]);
    
} catch (Exception $e) {
    error_log('Get users error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch users: ' . $e->getMessage()
    ]);
}
?>


