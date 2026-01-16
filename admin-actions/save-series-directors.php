<?php
require_once '../includes/init.php';
header('Content-Type: application/json');

// 1. Authorization
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// 2. Get Directors Array
$directors = $_POST['directors'] ?? [];

// 3. Clean and Validate
$directors = array_filter(array_map('trim', $directors), function($value) {
    return !empty($value);
});

if (empty($directors)) {
    http_response_code(422);
    echo json_encode(['error' => 'Please provide at least one director name']);
    exit;
}

// 4. Validate name lengths
foreach ($directors as $name) {
    if (strlen($name) > 255) {
        http_response_code(422);
        echo json_encode(['error' => 'Director name too long (max 255 characters)']);
        exit;
    }
}

// 5. Store in Session
$_SESSION['series_draft']['directors'] = array_values($directors);

echo json_encode([
    'status' => 'ok',
    'count' => count($directors)
]);
?>