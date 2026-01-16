<?php
require_once '../includes/init.php';
header('Content-Type: application/json');

// 1. Authorization
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// 2. Get Target Season
$targetSeason = filter_var($_POST['target_season_number'] ?? 0, FILTER_VALIDATE_INT);

if ($targetSeason === false || $targetSeason <= 0) {
    http_response_code(422);
    echo json_encode(['error' => 'Please select a valid target season']);
    exit;
}

// 3. Validate Target Season Exists in Draft
$draftSeasons = $_SESSION['series_draft']['seasons'] ?? [];
$seasonExists = false;
foreach ($draftSeasons as $s) {
    if ($s['number'] == $targetSeason) {
        $seasonExists = true;
        break;
    }
}

if (!$seasonExists) {
    http_response_code(422);
    echo json_encode(['error' => "Season $targetSeason not found in draft"]);
    exit;
}

// 4. Get Episode Data
$epNumbers = $_POST['ep_numbers'] ?? [];
$epTitles = $_POST['ep_titles'] ?? [];
$epDurations = $_POST['ep_durations'] ?? [];
$epOverviews = $_POST['ep_overviews'] ?? [];
$epStills = $_POST['ep_stills'] ?? [];

if (empty($epNumbers)) {
    http_response_code(422);
    echo json_encode(['error' => 'Please add at least one episode']);
    exit;
}

$episodes = [];
$usedNumbers = [];

for ($i = 0; $i < count($epNumbers); $i++) {
    $num = filter_var($epNumbers[$i], FILTER_VALIDATE_INT);
    $title = trim($epTitles[$i] ?? '');
    $duration = filter_var($epDurations[$i] ?? 0, FILTER_VALIDATE_INT);
    $overview = trim($epOverviews[$i] ?? '');
    $still = trim($epStills[$i] ?? '');
    
    // Validate episode number
    if ($num === false || $num <= 0) {
        http_response_code(422);
        echo json_encode(['error' => "Invalid episode number at row " . ($i + 1)]);
        exit;
    }
    
    // Check for duplicate episode numbers
    if (in_array($num, $usedNumbers)) {
        http_response_code(422);
        echo json_encode(['error' => "Duplicate episode number: $num for Season $targetSeason"]);
        exit;
    }
    $usedNumbers[] = $num;
    
    // Validate title
    if (empty($title)) {
        http_response_code(422);
        echo json_encode(['error' => "Episode title required for Episode $num"]);
        exit;
    }
    
    // Validate duration
    if ($duration === false || $duration <= 0) {
        http_response_code(422);
        echo json_encode(['error' => "Invalid duration for Episode $num"]);
        exit;
    }
    
    $episodes[] = [
        'episode_number' => $num,
        'title' => $title,
        'duration' => $duration,
        'overview' => $overview,
        'still_path' => $still
    ];
}

// 5. Store in Session (organized by season number)
if (!isset($_SESSION['series_draft']['episodes'])) {
    $_SESSION['series_draft']['episodes'] = [];
}
$_SESSION['series_draft']['episodes'][$targetSeason] = $episodes;

echo json_encode([
    'status' => 'ok',
    'season' => $targetSeason,
    'count' => count($episodes)
]);
?>