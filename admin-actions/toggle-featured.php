<?php
require_once '../includes/init.php';
header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'error' => 'Invalid Movie ID']);
    exit;
}

$res = $Conn->query("SELECT is_featured FROM movies WHERE movie_id = $id");

if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $current = $row['is_featured'];

    $newState = $current ? 0 : 1;

    $stmt = $Conn->prepare("UPDATE movies SET is_featured = ? WHERE movie_id = ?");
    $stmt->bind_param('ii', $newState, $id);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success', 
            'new_state' => $newState
        ]);
    } else {
        echo json_encode(['status' => 'error', 'error' => 'Database update failed']);
    }
} else {
    echo json_encode(['status' => 'error', 'error' => 'Movie not found']);
}