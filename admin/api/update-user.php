<?php
/**
 * Update User API
 * Updates an existing staff user
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $userId = isset($data['id']) ? (int)$data['id'] : 0;
    
    if ($userId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
        exit;
    }
    
    // Check if user exists
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $checkStmt->execute([$userId]);
    if (!$checkStmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'User not found']);
        exit;
    }
    
    // Get fields to update
    $fullName = trim($data['full_name'] ?? '');
    $email = trim($data['email'] ?? '');
    $mobile = trim($data['mobile'] ?? '');
    $roleId = isset($data['role_id']) ? (int)$data['role_id'] : null;
    $status = $data['status'] ?? null;
    $password = $data['password'] ?? '';
    
    // Validation
    if (empty($fullName)) {
        echo json_encode(['success' => false, 'error' => 'Full name is required']);
        exit;
    }
    
    // Validate email format if provided
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Invalid email format']);
        exit;
    }
    
    // Validate status
    if ($status !== null && !in_array($status, ['active', 'disabled', 'suspended'])) {
        $status = null;
    }
    
    // Validate password if provided
    if (!empty($password)) {
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters']);
            exit;
        }
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    }
    
    // Build update query dynamically
    $updateFields = ['full_name = ?'];
    $params = [$fullName];
    
    // Always update email (allows clearing)
    $updateFields[] = 'email = ?';
    $params[] = $email ?: null;
    
    // Always update mobile (allows clearing and updating)
    $updateFields[] = 'mobile = ?';
    $params[] = $mobile ?: null;
    
    if ($roleId !== null) {
        $updateFields[] = 'role_id = ?';
        $params[] = $roleId;
    }
    
    if ($status !== null) {
        $updateFields[] = 'status = ?';
        $params[] = $status;
    }
    
    if (!empty($password)) {
        $updateFields[] = 'password_hash = ?';
        $params[] = $passwordHash;
    }
    
    $params[] = $userId;
    
    $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $updateStmt = $pdo->prepare($sql);
    $updateStmt->execute($params);
    
    // Get updated user with role name
    $userStmt = $pdo->prepare("
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
        WHERE u.id = ?
    ");
    $userStmt->execute([$userId]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'message' => 'Staff member updated successfully',
        'user' => [
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
        ]
    ]);
    
} catch (PDOException $e) {
    error_log('Update user error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to update user: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log('Update user error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to update user: ' . $e->getMessage()
    ]);
}
?>


