<?php
require_once '../includes/init.php';
header('Content-Type: application/json');

// 1. Authorization Check
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// 2. Validate Series ID
$series_id = filter_var($_GET['series_id'] ?? 0, FILTER_VALIDATE_INT);

if ($series_id === false || $series_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid series ID']);
    exit;
}

try {
    // 3. Get Series Basic Info + Trailer
    $stmt = $Conn->prepare("
        SELECT s.*, st.youtube_url as trailer_url
        FROM series s
        LEFT JOIN series_trailers st ON s.series_id = st.series_id
        WHERE s.series_id = ?
    ");
    
    $stmt->bind_param('i', $series_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to fetch series data');
    }
    
    $series = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$series) {
        echo json_encode(['success' => false, 'message' => 'Series not found']);
        exit;
    }

    // 4. Get Genres
    $genres = [];
    $stmt = $Conn->prepare("
        SELECT g.name 
        FROM series_genres sg 
        JOIN genres g ON sg.genre_id = g.genre_id 
        WHERE sg.series_id = ?
    ");
    $stmt->bind_param('i', $series_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $genres[] = $row['name'];
    }
    $stmt->close();

    // 5. Get Directors
    $directors = [];
    $stmt = $Conn->prepare("
        SELECT d.name 
        FROM series_directors sd 
        JOIN directors d ON sd.director_id = d.director_id 
        WHERE sd.series_id = ?
    ");
    $stmt->bind_param('i', $series_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $directors[] = $row['name'];
    }
    $stmt->close();

    // 6. Get Writers
    $writers = [];
    $stmt = $Conn->prepare("
        SELECT w.name 
        FROM series_writers sw 
        JOIN writers w ON sw.writer_id = w.writer_id 
        WHERE sw.series_id = ?
    ");
    $stmt->bind_param('i', $series_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $writers[] = $row['name'];
    }
    $stmt->close();

    // 7. Get Cast (with order)
    $cast = [];
    $stmt = $Conn->prepare("
        SELECT a.name as actor, sc.character_name, a.image_path as image, sc.cast_order
        FROM series_cast sc 
        JOIN actors a ON sc.actor_id = a.actor_id 
        WHERE sc.series_id = ? 
        ORDER BY sc.cast_order ASC
    ");
    $stmt->bind_param('i', $series_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $cast[] = [
            'actor' => $row['actor'],
            'character' => $row['character_name'],
            'image' => $row['image'] ?? ''
        ];
    }
    $stmt->close();

    // 8. Get Seasons with Episodes (Nested structure)
    $seasons = [];
    $stmt = $Conn->prepare("
        SELECT * 
        FROM seasons 
        WHERE series_id = ? 
        ORDER BY season_number ASC
    ");
    $stmt->bind_param('i', $series_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($seasonRow = $result->fetch_assoc()) {
        $season_id = $seasonRow['season_id'];
        
        // Get episodes for this season
        $epStmt = $Conn->prepare("
            SELECT * 
            FROM episodes 
            WHERE season_id = ? 
            ORDER BY episode_number ASC
        ");
        $epStmt->bind_param('i', $season_id);
        $epStmt->execute();
        $epResult = $epStmt->get_result();
        
        $episodes = [];
        while ($epRow = $epResult->fetch_assoc()) {
            $episodes[] = $epRow;
        }
        $epStmt->close();
        
        // Add episodes to season
        $seasonRow['episodes'] = $episodes;
        $seasons[] = $seasonRow;
    }
    $stmt->close();

    // 9. Build Complete Response
    $response = [
        'success' => true,
        'series' => [
            // Basic Info
            'series_id' => $series['series_id'],
            'title' => $series['title'],
            'overview' => $series['overview'],
            'release_year' => $series['release_year'],
            'rating' => $series['rating'],
            'content_rating' => $series['content_rating'],
            'language' => $series['language'],
            'status' => $series['status'],
            'tmdb_id' => $series['tmdb_id'],
            'poster_path' => $series['poster_path'],
            'backdrop_path' => $series['backdrop_path'],
            'is_featured' => $series['is_featured'],
            'views_count' => $series['views_count'],
            'created_at' => $series['created_at'],
            
            // Relationships
            'trailer_url' => $series['trailer_url'] ?? '',
            'genres' => $genres,
            'directors' => $directors,
            'writers' => $writers,
            'cast' => $cast,
            'seasons' => $seasons
        ]
    ];
    
    echo json_encode($response);

} catch (Exception $e) {
    error_log("Get Series Data Error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>