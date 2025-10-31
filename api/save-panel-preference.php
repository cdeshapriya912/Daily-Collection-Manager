<?php
/**
 * Save panel preference after login
 */

session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$panel = $data['panel'] ?? null;

// Validate panel
$allowedPanels = ['admin/index.php', 'collection.php'];
if (!in_array($panel, $allowedPanels)) {
    echo json_encode(['success' => false, 'error' => 'Invalid panel']);
    exit;
}

// Save in session
$_SESSION['last_panel'] = $panel;

// Save in cookie for 30 days (persists across sessions)
setcookie('last_panel', $panel, time() + (30 * 24 * 60 * 60), '/', '', false, true);

echo json_encode([
    'success' => true,
    'message' => 'Panel preference saved',
    'panel' => $panel
]);
?>

