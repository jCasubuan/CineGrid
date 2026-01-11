<?php
require_once '../includes/init.php';

// Check if user is admin
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_id = isset($_POST['movie_id']) ? intval($_POST['movie_id']) : 0;
    
    if ($movie_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid movie ID']);
        exit;
    }
    
    // Start transaction
    $Conn->begin_transaction();
    
    try {
        // Delete movie_genres relationships first
        $stmt1 = $Conn->prepare("DELETE FROM movie_genres WHERE movie_id = ?");
        $stmt1->bind_param('i', $movie_id);
        $stmt1->execute();
        $stmt1->close();
        
        // Delete the movie
        $stmt2 = $Conn->prepare("DELETE FROM movies WHERE movie_id = ?");
        $stmt2->bind_param('i', $movie_id);
        $stmt2->execute();
        
        if ($stmt2->affected_rows > 0) {
            $Conn->commit();
            echo json_encode(['success' => true, 'message' => 'Movie deleted successfully']);
        } else {
            $Conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Movie not found']);
        }
        
        $stmt2->close();
        
    } catch (Exception $e) {
        $Conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error deleting movie: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>