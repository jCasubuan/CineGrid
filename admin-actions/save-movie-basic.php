<?php
require_once '../includes/init.php';
require_once '../includes/validate-movie.php';

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
$duration       = isset($_POST['duration']) ? (int)$_POST['duration'] : 0;
$rating         = isset($_POST['rating']) ? (float)$_POST['rating'] : 0.0;
$language       = trim($_POST['language'] ?? 'English');
$contentRating  = trim($_POST['content_rating'] ?? 'PG');
$tmdbId         = $_POST['tmdb_id'] ?? null;
$posterPath     = trim($_POST['poster_path'] ?? '');
$backdropPath   = trim($_POST['backdrop_path'] ?? '');

// Validations

// Validate movie title (prevent gibberish/random text)
   $titleValidation = isValidMovieTitle($title);
   if (!$titleValidation['valid']) {
       http_response_code(422);
       echo json_encode(['error' => $titleValidation['error']]);
       exit;
   }

// Validate year
$yearValidation = isValidReleaseYear($releaseYear);
if (!$yearValidation['valid']) {
    http_response_code(422);
    echo json_encode(['error' => $yearValidation['error']]);
    exit;
}

// all fields required
if ($title === '' || $overview === '' || !$releaseYear || !$tmdbId || $posterPath === '' || $backdropPath === '') {
    http_response_code(422);
    echo json_encode(['error' => 'Required fields (Title, Overview, Year, TMDB ID, and Images) are missing.']);
    exit;
}

// // invalid tmdb id
// if (!is_numeric($tmdbId) || $tmdbId <= 0) {
//     http_response_code(422);
//     echo json_encode(['error' => 'Invalid TMDB ID. It must be a positive number.']);
//     exit;
// }

// // tmdb length
// $tmdb_len = strlen((string)$tmdbId);
// if ($tmdb_len < 3 || $tmdb_len > 8) {
//     http_response_code(422);
//     echo json_encode(['error' => 'TMDB ID must be between 3 and 8 digits.']);
//     exit;
// }

$tmdbValidation = isValidTmdbId($tmdbId);
if (!$tmdbValidation['valid']) {
    http_response_code(422);
    echo json_encode(['error' => $tmdbValidation['error']]);
    exit;
}

$overviewValidation = isValidOverview($overview);
if (!$overviewValidation['valid']) {
    http_response_code(422);
    echo json_encode(['error' => $overviewValidation['error']]);
    exit;
}

$posterValidation = isValidImagePath($posterPath, 'Poster');
if (!$posterValidation['valid']) {
    http_response_code(422);
    echo json_encode(['error' => $posterValidation['error']]);
    exit;
}

$backdropValidation = isValidImagePath($backdropPath, 'Backdrop');
if (!$backdropValidation['valid']) {
    http_response_code(422);
    echo json_encode(['error' => $backdropValidation['error']]);
    exit;
}

$ratingValidation = isValidMovieRating($rating);
if (!$ratingValidation['valid']) {
    http_response_code(422);
    echo json_encode(['error' => $ratingValidation['error']]);
    exit;
}



// duration 
if ($duration <= 0 || $duration > 600) {
    http_response_code(422);
    echo json_encode(['error' => 'Duration must be between 1 and 600 minutes.']);
    exit;
}

// poster & backdrop required
if ($posterPath === '' || $backdropPath === '') {
    http_response_code(422);
    echo json_encode([
        'error' => 'Every movie must have both a Poster and a Backdrop image path.'
    ]);
    exit;
}

// empty poster & backdrop
if (!filter_var($posterPath, FILTER_VALIDATE_URL) && !str_contains($posterPath, 'assets/')) {
    http_response_code(422);
    echo json_encode(['error' => 'Invalid Poster path format. Use a URL or local assets path.']);
    exit;
}

// range validation
if ($rating < 0.0 || $rating > 10.0) {
    http_response_code(422);
    echo json_encode([
        'error' => 'Rating must be a value between 0.0 and 10.0.'
    ]);
    exit;
}

// invalidate random typing
if (!is_numeric($_POST['rating'])) {
    http_response_code(422);
    echo json_encode(['error' => 'Rating must be a numeric value.']);
    exit;
}

// rating for movies
$allowedMTRCB = ['G', 'PG', 'R-13', 'R-16', 'R-18'];
if (!in_array($contentRating, $allowedMTRCB)) {
    http_response_code(422);
    echo json_encode([
        'error' => 'Invalid Rating. Please select a valid MTRCB rating (G, PG, R-13, R-16, or R-18).'
    ]);
    exit;
}

// Language Validation
if (strlen($language) < 2 || is_numeric($language)) {
    http_response_code(422);
    echo json_encode(['error' => 'Please select a valid language.']);
    exit;
}

// Store draft in session (STEP 1)
$_SESSION['movie_draft'] = [
    'basic' => [
        'title'          => $title,
        'overview'       => $overview,
        'release_year'   => (int)$releaseYear,
        'duration'       => $duration,
        'rating'         => $rating,
        'language'       => $language,
        'content_rating' => $contentRating,
        'tmdb_id'        => (int)$tmdbId,
        'poster_path'    => $posterPath,
        'backdrop_path'  => $backdropPath
    ],
    
    'genres'    => [],
    'directors' => [],
    'writers'   => [],
    'cast'      => [],
    'trailer'   => ['url' => '']
];

echo json_encode([
    'status' => 'ok'
]);