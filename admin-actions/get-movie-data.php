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
               mt.youtube_url as trailer_url
        FROM movies m
        LEFT JOIN movie_trailers mt ON m.movie_id = mt.movie_id
        WHERE m.movie_id = ?
    ");
    
    $stmt->bind_param('i', $movie_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Movie not found']);
        exit;
    }
    
    $movie = $result->fetch_assoc();
    
    // Get genres
    $genres_stmt = $Conn->prepare("
        SELECT g.name
        FROM movie_genres mg
        JOIN genres g ON mg.genre_id = g.genre_id
        WHERE mg.movie_id = ?
    ");
    $genres_stmt->bind_param('i', $movie_id);
    $genres_stmt->execute();
    $genres_result = $genres_stmt->get_result();
    
    $genres = [];
    while ($row = $genres_result->fetch_assoc()) {
        $genres[] = $row['name'];
    }
    
    // Get directors
    $directors_stmt = $Conn->prepare("
        SELECT d.name
        FROM movie_directors md
        JOIN directors d ON md.director_id = d.director_id
        WHERE md.movie_id = ?
    ");
    $directors_stmt->bind_param('i', $movie_id);
    $directors_stmt->execute();
    $directors_result = $directors_stmt->get_result();
    
    $directors = [];
    while ($row = $directors_result->fetch_assoc()) {
        $directors[] = $row['name'];
    }
    
    // Get writers
    $writers_stmt = $Conn->prepare("
        SELECT w.name
        FROM movie_writers mw
        JOIN writers w ON mw.writer_id = w.writer_id
        WHERE mw.movie_id = ?
    ");
    $writers_stmt->bind_param('i', $movie_id);
    $writers_stmt->execute();
    $writers_result = $writers_stmt->get_result();
    
    $writers = [];
    while ($row = $writers_result->fetch_assoc()) {
        $writers[] = $row['name'];
    }
    
    // Get cast
    $cast_stmt = $Conn->prepare("
        SELECT a.name as actor, mc.character_name, a.image_path as image
        FROM movie_cast mc
        JOIN actors a ON mc.actor_id = a.actor_id
        WHERE mc.movie_id = ?
    ");
    $cast_stmt->bind_param('i', $movie_id);
    $cast_stmt->execute();
    $cast_result = $cast_stmt->get_result();
    
    $cast = [];
    while ($row = $cast_result->fetch_assoc()) {
        $cast[] = [
            'actor' => $row['actor'],
            'character' => $row['character_name'],
            'image' => $row['image']
        ];
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
            'genres' => $genres,
            'directors' => $directors,
            'writers' => $writers,
            'trailer_url' => $movie['trailer_url'],
            'cast' => $cast
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>