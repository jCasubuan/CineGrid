<?php
require_once '../includes/init.php';
header('Content-Type: application/json');

// 1. Authorization
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// 2. Get and Validate Trailer URL
$trailerUrl = trim($_POST['trailer_url'] ?? '');

if (empty($trailerUrl)) {
    http_response_code(422);
    echo json_encode(['error' => 'Trailer URL is required']);
    exit;
}

// Basic URL validation
if (!filter_var($trailerUrl, FILTER_VALIDATE_URL)) {
    http_response_code(422);
    echo json_encode(['error' => 'Invalid URL format']);
    exit;
}

// Validate it's a YouTube URL
if (!str_contains($trailerUrl, 'youtube.com') && !str_contains($trailerUrl, 'youtu.be')) {
    http_response_code(422);
    echo json_encode(['error' => 'Please provide a valid YouTube URL']);
    exit;
}

// 3. Store in Session
$_SESSION['series_draft']['trailer_url'] = $trailerUrl;

echo json_encode(['status' => 'ok']);
?>