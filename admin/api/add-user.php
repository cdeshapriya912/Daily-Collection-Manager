<?php
/**
 * Add User API
 * Creates a new staff user
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
    
    // Validate required fields
    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';
    $fullName = trim($data['full_name'] ?? '');
    $email = trim($data['email'] ?? '');
    $mobile = trim($data['mobile'] ?? '');
    $roleId = isset($data['role_id']) ? (int)$data['role_id'] : 2; // Default to staff
    $status = $data['status'] ?? 'active';
    
    // Validation
    if (empty($username)) {
        echo json_encode(['success' => false, 'error' => 'Username is required']);
        exit;
    }
    
    if (empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Password is required']);
        exit;
    }
    
    if (empty($fullName)) {
        echo json_encode(['success' => false, 'error' => 'Full name is required']);
        exit;
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters']);
        exit;
    }
    
    // Validate email format if provided
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Invalid email format']);
        exit;
    }
    
    // Validate status
    if (!in_array($status, ['active', 'disabled', 'suspended'])) {
        $status = 'active';
    }
    
    // Check if username already exists
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $checkStmt->execute([$username]);
    if ($checkStmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Username already exists']);
        exit;
    }
    
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Get current user ID for created_by
    $createdBy = $_SESSION['user_id'] ?? null;
    
    // Insert user
    $insertStmt = $pdo->prepare("
        INSERT INTO users (username, password_hash, full_name, email, mobile, role_id, status, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $insertStmt->execute([
        $username,
        $passwordHash,
        $fullName,
        $email ?: null,
        $mobile ?: null,
        $roleId,
        $status,
        $createdBy
    ]);
    
    $userId = $pdo->lastInsertId();
    
    // Get the created user with role name
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
        'message' => 'Staff member added successfully',
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
    error_log('Add user error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to add user: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log('Add user error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to add user: ' . $e->getMessage()
    ]);
}
?>


