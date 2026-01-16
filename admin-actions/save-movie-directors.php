<?php
require_once '../includes/init.php';
require_once '../includes/validate-people.php';

if (
    empty($_SESSION['user_id']) ||
    $_SESSION['user_role'] !== 'admin'
) {
    http_response_code(403);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

header('Content-Type: application/json');

// Ensure draft exists
if (empty($_SESSION['movie_draft'])) {
    http_response_code(409);
    echo json_encode(['error' => 'No active movie draft']);
    exit;
}

$directors = $_POST['directors'] ?? [];

if (!is_array($directors)) {
    http_response_code(422);
    echo json_encode(['error' => 'Invalid directors format']);
    exit;
}

// Clean + validate
$cleanDirectors = [];

foreach ($directors as $name) {
    $name = trim($name);

    if ($name === '') {
        continue;
    }

    $validation = isValidPersonName($name, 'Director');
    if (!$validation['valid']) {
        http_response_code(422);
        echo json_encode(['error' => $validation['error']]);
        exit;
    }

    if (strlen($name) > 255) {
        http_response_code(422);
        echo json_encode(['error' => 'Director name too long']);
        exit;
    }

    $cleanDirectors[] = $name;
}

if (empty($cleanDirectors)) {
    http_response_code(422);
    echo json_encode(['error' => 'At least one director is required']);
    exit;
}

// Save to session
$_SESSION['movie_draft']['directors'] = array_values(array_unique($cleanDirectors));

echo json_encode(['status' => 'ok']);
