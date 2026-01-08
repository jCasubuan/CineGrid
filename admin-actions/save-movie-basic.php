<?php
require_once '../includes/init.php';

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

// Required fields
$title          = trim($_POST['title'] ?? '');
$overview       = trim($_POST['overview'] ?? '');
$releaseYear    = $_POST['release_year'] ?? null;
$duration       = $_POST['duration'] ?? 0;
$rating         = $_POST['rating'] ?? 0;
$language       = trim($_POST['language'] ?? 'English');
$contentRating  = trim($_POST['content_rating'] ?? 'NR'); 
$type           = $_POST['type'] ?? 'movie';

// Optional fields
$tmdbId = $_POST['tmdb_id'] ?? null;
$posterPath = $_POST['poster_path'] ?? null;
$backdropPath = $_POST['backdrop_path'] ?? null;

// Validation
if ($title === '' || $overview === '' || !$releaseYear) {
    http_response_code(422);
    echo json_encode([
        'error' => 'Required fields (Title, Overview, Year) are missing.'
    ]);
    exit;
}

// Store draft in session (STEP 1)
$_SESSION['movie_draft'] = [
    'basic' => [
        'title'          => $title,
        'overview'       => $overview,
        'release_year'   => (int)$releaseYear,
        'duration'       => (int)$duration,
        'rating'         => (float)$rating,
        'language'       => $language,
        'content_rating' => $contentRating,
        'type'           => $type,
        'tmdb_id'        => $tmdbId ?: null,
        'poster_path'    => $posterPath ?: null,
        'backdrop_path'  => $backdropPath ?: null
    ],
    // Pre-initialize other keys to prevent 'undefined index' notices in review modal
    'genres'    => [],
    'directors' => [],
    'writers'   => [],
    'cast'      => [],
    'trailer'   => ['url' => '']
];

echo json_encode([
    'status' => 'ok'
]);