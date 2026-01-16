<?php
require_once '../includes/init.php';
header('Content-Type: application/json');

// 1. Authorization Check
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// 2. Clear Series Draft
if (isset($_SESSION['series_draft'])) {
    unset($_SESSION['series_draft']);
    $cleared = true;
} else {
    $cleared = false;
}

// 3. Success Response
echo json_encode([
    'status' => 'ok',
    'message' => $cleared ? 'Draft cleared successfully' : 'No draft to clear',
    'cleared' => $cleared
]);
?>