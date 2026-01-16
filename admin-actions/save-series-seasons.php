<?php
require_once '../includes/init.php';
header('Content-Type: application/json');

// 1. Authorization
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// 2. Get Arrays
$seasonNumbers = $_POST['season_numbers'] ?? [];
$seasonTitles = $_POST['season_titles'] ?? [];
$seasonYears = $_POST['season_years'] ?? [];
$seasonPosters = $_POST['season_posters'] ?? [];

// 3. Validation
if (empty($seasonNumbers)) {
    http_response_code(422);
    echo json_encode(['error' => 'Please add at least one season']);
    exit;
}

$seasons = [];
$usedNumbers = [];

for ($i = 0; $i < count($seasonNumbers); $i++) {
    $number = filter_var($seasonNumbers[$i], FILTER_VALIDATE_INT);
    $year = filter_var($seasonYears[$i] ?? null, FILTER_VALIDATE_INT);
    $title = trim($seasonTitles[$i] ?? '');
    $poster = trim($seasonPosters[$i] ?? '');
    
    // Validate season number
    if ($number === false || $number <= 0) {
        http_response_code(422);
        echo json_encode(['error' => "Invalid season number at row " . ($i + 1)]);
        exit;
    }
    
    // Check for duplicate season numbers
    if (in_array($number, $usedNumbers)) {
        http_response_code(422);
        echo json_encode(['error' => "Duplicate season number: $number"]);
        exit;
    }
    $usedNumbers[] = $number;
    
    // Validate year
    if ($year === false || $year < 1900 || $year > 2099) {
        http_response_code(422);
        echo json_encode(['error' => "Invalid year for Season $number"]);
        exit;
    }
    
    // Validate poster path
    if (empty($poster)) {
        http_response_code(422);
        echo json_encode(['error' => "Poster path required for Season $number"]);
        exit;
    }
    
    $seasons[] = [
        'number' => $number,
        'title' => !empty($title) ? $title : "Season $number",
        'year' => $year,
        'poster' => $poster
    ];
}

// 4. Store in Session
$_SESSION['series_draft']['seasons'] = $seasons;

echo json_encode([
    'status' => 'ok',
    'count' => count($seasons)
]);
?>