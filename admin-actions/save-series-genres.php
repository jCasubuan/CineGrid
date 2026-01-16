<?php
require_once '../includes/init.php';
header('Content-Type: application/json');

// 1. Authorization
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// 2. Get Genre Names from Form
$genreNames = $_POST['genres'] ?? [];

// 3. Validation
if (empty($genreNames)) {
    http_response_code(422);
    echo json_encode(['error' => 'Please select at least one genre']);
    exit;
}

// 4. Convert Genre Names to IDs
$genreIds = [];
$stmt = $Conn->prepare("SELECT genre_id, name FROM genres WHERE name = ?");

foreach ($genreNames as $name) {
    $name = trim($name);
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $genreIds[] = (int)$row['genre_id'];
    }
}
$stmt->close();

// 5. Validate that all genres were found
if (count($genreIds) !== count($genreNames)) {
    http_response_code(422);
    echo json_encode(['error' => 'One or more invalid genres selected']);
    exit;
}

// 6. Store in Session
$_SESSION['series_draft']['genres'] = $genreIds;

echo json_encode([
    'status' => 'ok',
    'count' => count($genreIds)
]);
?>