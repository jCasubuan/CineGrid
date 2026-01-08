<?php
require_once '../includes/init.php';

// 1. Security Check: Ensure user is logged in and is an admin
if (
    empty($_SESSION['user_id']) ||
    $_SESSION['user_role'] !== 'admin'
) {
    http_response_code(403);
    exit;
}

// 2. Method Check: Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

header('Content-Type: application/json');

// 3. Draft State Check: Ensure the previous steps were completed
if (empty($_SESSION['movie_draft'])) {
    http_response_code(409);
    echo json_encode(['error' => 'No active movie draft']);
    exit;
}

$writers = $_POST['writers'] ?? [];

// 4. Input Validation: Ensure it's an array
if (!is_array($writers)) {
    http_response_code(422);
    echo json_encode(['error' => 'Invalid writers format']);
    exit;
}

// 5. Clean + Validate individual names
$cleanWriters = [];

foreach ($writers as $name) {
    $name = trim($name);

    if ($name === '') {
        continue;
    }

    // Match the 255 character limit from your database schema
    if (strlen($name) > 255) {
        http_response_code(422);
        echo json_encode(['error' => 'Writer name too long']);
        exit;
    }

    $cleanWriters[] = $name;
}

// 6. Minimum Requirement Check
if (empty($cleanWriters)) {
    http_response_code(422);
    echo json_encode(['error' => 'At least one writer is required']);
    exit;
}

// 7. Save to Session (using unique to prevent duplicates)
$_SESSION['movie_draft']['writers'] = array_values(array_unique($cleanWriters));

echo json_encode(['status' => 'ok']);