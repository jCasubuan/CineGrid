<?php
require_once '../includes/init.php';
header('Content-Type: application/json');

// 1. Authorization
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// 2. Check if Draft Exists
if (empty($_SESSION['series_draft'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'No draft found'
    ]);
    exit;
}

// 3. Get Draft with ALL Necessary Defaults
$draft = $_SESSION['series_draft'];

$defaults = [
    // Basic Info
    'title' => 'Untitled',
    'overview' => '',
    'release_year' => date('Y'),
    'rating' => 0.0,
    'content_rating' => 'PG',
    'language' => 'English',
    'status' => 'Ongoing',
    'tmdb_id' => null,
    
    // Images (IMPORTANT: These were missing!)
    'poster_path' => 'assets/img/no-poster.jpg',
    'backdrop_path' => 'assets/img/no-backdrop.jpg',  // THIS WAS CAUSING THE ERROR
    
    // Related Data
    'trailer_url' => '',
    'genres' => [],
    'directors' => [],
    'writers' => [],
    'cast' => [],
    'seasons' => [],
    'episodes' => []
];

// Merge defaults with actual draft data
$finalDraft = array_merge($defaults, $draft);

echo json_encode([
    'status' => 'ok',
    'draft' => $finalDraft
]);
?>