<?php
require_once '../includes/init.php';
header('Content-Type: application/json');

// 1. Authorization Check
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// 2. Method Check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// 3. Get and Sanitize Input
$title = trim($_POST['title'] ?? '');
$overview = trim($_POST['overview'] ?? '');
$releaseYear = filter_var($_POST['release_year'] ?? null, FILTER_VALIDATE_INT);
$rating = filter_var($_POST['rating'] ?? 0, FILTER_VALIDATE_FLOAT);
$status = $_POST['status'] ?? 'Ongoing';
$language = trim($_POST['language'] ?? 'English');
$contentRating = $_POST['content_rating'] ?? 'PG';
$tmdbId = filter_var($_POST['tmdb_id'] ?? null, FILTER_VALIDATE_INT);
$posterPath = trim($_POST['poster_path'] ?? '');
$backdropPath = trim($_POST['backdrop_path'] ?? '');

// 4. Validation Rules
$errors = [];

// Title validation
if (strlen($title) < 2) {
    $errors[] = 'Title must be at least 2 characters long';
}
if (strlen($title) > 255) {
    $errors[] = 'Title must not exceed 255 characters';
}

// Overview validation
if (strlen($overview) < 10) {
    $errors[] = 'Overview must be at least 10 characters long';
}

// Year validation
if ($releaseYear === false || $releaseYear < 1900 || $releaseYear > 2099) {
    $errors[] = 'Release year must be between 1900 and 2099';
}

// Rating validation
if ($rating === false || $rating < 0.0 || $rating > 10.0) {
    $errors[] = 'Rating must be between 0.0 and 10.0';
}

// Content Rating validation (must match ENUM)
if (!in_array($contentRating, ['G', 'PG', 'SPG'], true)) {
    $errors[] = 'Invalid content rating. Must be G, PG, or SPG';
}

// Status validation (must match ENUM)
if (!in_array($status, ['Ongoing', 'Ended', 'Cancelled'], true)) {
    $errors[] = 'Invalid status. Must be Ongoing, Ended, or Cancelled';
}

// TMDB ID validation
if ($tmdbId === false || $tmdbId <= 0) {
    $errors[] = 'Invalid TMDB ID';
}

// Image paths validation
if (empty($posterPath)) {
    $errors[] = 'Poster path is required';
}
if (empty($backdropPath)) {
    $errors[] = 'Backdrop path is required';
}

// Language validation
$allowedLanguages = ['English', 'Tagalog', 'Japanese', 'Korean', 'Mandarin', 'Spanish', 'French'];
if (!in_array($language, $allowedLanguages, true)) {
    $errors[] = 'Invalid language selection';
}

// Return errors if any
if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['error' => implode('. ', $errors)]);
    exit;
}

// 5. Initialize Draft Structure
$_SESSION['series_draft'] = [
    'title' => $title,
    'overview' => $overview,
    'release_year' => $releaseYear,
    'rating' => $rating,
    'status' => $status,
    'language' => $language,
    'content_rating' => $contentRating,
    'tmdb_id' => $tmdbId,
    'poster_path' => $posterPath,
    'backdrop_path' => $backdropPath,
    'trailer_url' => '',
    'genres' => [],
    'directors' => [],
    'writers' => [],
    'cast' => [],
    'seasons' => [],
    'episodes' => []
];

echo json_encode(['status' => 'ok']);
?>