<?php
require_once '../includes/init.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$movie_id = isset($_GET['movie_id']) ? intval($_GET['movie_id']) : 0;

if ($movie_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid movie ID']);
    exit;
}

try {
    // Get movie basic info
    $stmt = $Conn->prepare("
        SELECT m.*, 
               GROUP_CONCAT(DISTINCT g.name) as genres,
               GROUP_CONCAT(DISTINCT d.name) as directors,
               GROUP_CONCAT(DISTINCT w.name) as writers,
               mt.youtube_url as trailer_url
        FROM movies m
        LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
        LEFT JOIN genres g ON mg.genre_id = g.genre_id
        LEFT JOIN movie_directors md ON m.movie_id = md.movie_id
        LEFT JOIN directors d ON md.director_id = d.director_id
        LEFT JOIN movie_writers mw ON m.movie_id = mw.movie_id
        LEFT JOIN writers w ON mw.writer_id = w.writer_id
        LEFT JOIN movie_trailers mt ON m.movie_id = mt.movie_id
        WHERE m.movie_id = ?
        GROUP BY m.movie_id
    ");
    
    $stmt->bind_param('i', $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Movie not found']);
        exit;
    }
    
    $movie = $result->fetch_assoc();
    
    // Get cast
    $cast_stmt = $Conn->prepare("
        SELECT a.name as actor, mc.character_name as character, a.image_path as image
        FROM movie_cast mc
        JOIN actors a ON mc.actor_id = a.actor_id
        WHERE mc.movie_id = ?
    ");
    $cast_stmt->bind_param('i', $movie_id);
    $cast_stmt->execute();
    $cast_result = $cast_stmt->get_result();
    
    $cast = [];
    while ($row = $cast_result->fetch_assoc()) {
        $cast[] = $row;
    }
    
    // Format response
    $response = [
        'success' => true,
        'movie' => [
            'movie_id' => $movie['movie_id'],
            'title' => $movie['title'],
            'overview' => $movie['overview'],
            'release_year' => $movie['release_year'],
            'duration' => $movie['duration'],
            'rating' => $movie['rating'],
            'content_rating' => $movie['content_rating'],
            'language' => $movie['language'],
            'tmdb_id' => $movie['tmdb_id'],
            'poster_path' => $movie['poster_path'],
            'backdrop_path' => $movie['backdrop_path'],
            'status' => $movie['status'],
            'genres' => $movie['genres'] ? explode(',', $movie['genres']) : [],
            'directors' => $movie['directors'] ? explode(',', $movie['directors']) : [],
            'writers' => $movie['writers'] ? explode(',', $movie['writers']) : [],
            'trailer_url' => $movie['trailer_url'],
            'cast' => $cast
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>