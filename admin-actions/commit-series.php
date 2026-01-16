<?php
require_once '../includes/init.php';
header('Content-Type: application/json');

// 1. Authorization Check
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// 2. Validate Draft Exists
if (empty($_SESSION['series_draft'])) {
    http_response_code(409);
    echo json_encode(['error' => 'No series draft found']);
    exit;
}

$draft = $_SESSION['series_draft'];

// 3. Validate Required Fields
$requiredFields = ['title', 'overview', 'release_year', 'tmdb_id', 'poster_path', 'backdrop_path'];
foreach ($requiredFields as $field) {
    if (empty($draft[$field])) {
        http_response_code(422);
        echo json_encode(['error' => "Required field missing: $field"]);
        exit;
    }
}

// 4. Start Database Transaction
$Conn->begin_transaction();

try {
    // 5. Insert Main Series Entry
    $stmt = $Conn->prepare("
        INSERT INTO series (
            title, overview, release_year, rating, content_rating, 
            language, status, tmdb_id, poster_path, backdrop_path
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param(
        'ssidsssiss',
        $draft['title'],
        $draft['overview'],
        $draft['release_year'],
        $draft['rating'],
        $draft['content_rating'],
        $draft['language'],
        $draft['status'],
        $draft['tmdb_id'],
        $draft['poster_path'],
        $draft['backdrop_path']
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to insert series: ' . $stmt->error);
    }
    
    $series_id = $Conn->insert_id;
    $stmt->close();

    // 6. Insert Genres (Using IDs, not names)
    if (!empty($draft['genres'])) {
        $genreStmt = $Conn->prepare("INSERT IGNORE INTO series_genres (series_id, genre_id) VALUES (?, ?)");
        
        foreach ($draft['genres'] as $genre_id) {
            $genreStmt->bind_param('ii', $series_id, $genre_id);
            if (!$genreStmt->execute()) {
                throw new Exception('Failed to insert genre');
            }
        }
        $genreStmt->close();
    }

    // 7. Insert Directors (Create if not exists, then link)
    if (!empty($draft['directors'])) {
        $insertDirStmt = $Conn->prepare("
            INSERT INTO directors (name) 
            VALUES (?) 
            ON DUPLICATE KEY UPDATE director_id = LAST_INSERT_ID(director_id)
        ");
        
        $linkDirStmt = $Conn->prepare("
            INSERT IGNORE INTO series_directors (series_id, director_id) 
            VALUES (?, ?)
        ");
        
        foreach ($draft['directors'] as $directorName) {
            $insertDirStmt->bind_param('s', $directorName);
            if (!$insertDirStmt->execute()) {
                throw new Exception('Failed to insert director: ' . $directorName);
            }
            
            $director_id = $Conn->insert_id;
            
            $linkDirStmt->bind_param('ii', $series_id, $director_id);
            if (!$linkDirStmt->execute()) {
                throw new Exception('Failed to link director');
            }
        }
        
        $insertDirStmt->close();
        $linkDirStmt->close();
    }

    // 8. Insert Writers (Create if not exists, then link)
    if (!empty($draft['writers'])) {
        $insertWriterStmt = $Conn->prepare("
            INSERT INTO writers (name) 
            VALUES (?) 
            ON DUPLICATE KEY UPDATE writer_id = LAST_INSERT_ID(writer_id)
        ");
        
        $linkWriterStmt = $Conn->prepare("
            INSERT IGNORE INTO series_writers (series_id, writer_id) 
            VALUES (?, ?)
        ");
        
        foreach ($draft['writers'] as $writerName) {
            $insertWriterStmt->bind_param('s', $writerName);
            if (!$insertWriterStmt->execute()) {
                throw new Exception('Failed to insert writer: ' . $writerName);
            }
            
            $writer_id = $Conn->insert_id;
            
            $linkWriterStmt->bind_param('ii', $series_id, $writer_id);
            if (!$linkWriterStmt->execute()) {
                throw new Exception('Failed to link writer');
            }
        }
        
        $insertWriterStmt->close();
        $linkWriterStmt->close();
    }

    // 9. Insert Cast (Create actors if not exists, then link with character names)
    if (!empty($draft['cast'])) {
        $insertActorStmt = $Conn->prepare("
            INSERT INTO actors (name, image_path) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE 
                actor_id = LAST_INSERT_ID(actor_id),
                image_path = IF(VALUES(image_path) != '', VALUES(image_path), image_path)
        ");
        
        $linkCastStmt = $Conn->prepare("
            INSERT INTO series_cast (series_id, actor_id, character_name, cast_order) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($draft['cast'] as $castMember) {
            $actorName = $castMember['name'];
            $actorImage = $castMember['image'] ?? '';
            $characterName = $castMember['character'];
            $castOrder = $castMember['order'];
            
            // Insert/update actor
            $insertActorStmt->bind_param('ss', $actorName, $actorImage);
            if (!$insertActorStmt->execute()) {
                throw new Exception('Failed to insert actor: ' . $actorName);
            }
            
            $actor_id = $Conn->insert_id;
            
            // Link to series with character name
            $linkCastStmt->bind_param('iisi', $series_id, $actor_id, $characterName, $castOrder);
            if (!$linkCastStmt->execute()) {
                throw new Exception('Failed to link cast member');
            }
        }
        
        $insertActorStmt->close();
        $linkCastStmt->close();
    }

    // 10. Insert Seasons and Episodes (Nested structure)
    if (!empty($draft['seasons'])) {
        $seasonStmt = $Conn->prepare("
            INSERT INTO seasons (series_id, season_number, title, release_year, poster_path) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $episodeStmt = $Conn->prepare("
            INSERT INTO episodes (season_id, episode_number, title, overview, duration, still_path) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $updateCountStmt = $Conn->prepare("
            UPDATE seasons SET episode_count = ? WHERE season_id = ?
        ");
        
        foreach ($draft['seasons'] as $season) {
            // Insert Season
            $seasonStmt->bind_param(
                'iisis',
                $series_id,
                $season['number'],
                $season['title'],
                $season['year'],
                $season['poster']
            );
            
            if (!$seasonStmt->execute()) {
                throw new Exception('Failed to insert season ' . $season['number']);
            }
            
            $season_id = $Conn->insert_id;
            
            // Get episodes for this season
            $episodes = $draft['episodes'][$season['number']] ?? [];
            $episodeCount = count($episodes);
            
            // Insert episodes
            foreach ($episodes as $episode) {
                $episodeStmt->bind_param(
                    'iissis',
                    $season_id,
                    $episode['episode_number'],
                    $episode['title'],
                    $episode['overview'],
                    $episode['duration'],
                    $episode['still_path']
                );
                
                if (!$episodeStmt->execute()) {
                    throw new Exception('Failed to insert episode ' . $episode['episode_number']);
                }
            }
            
            // Update episode count
            $updateCountStmt->bind_param('ii', $episodeCount, $season_id);
            if (!$updateCountStmt->execute()) {
                throw new Exception('Failed to update episode count');
            }
        }
        
        $seasonStmt->close();
        $episodeStmt->close();
        $updateCountStmt->close();
    }

    // 11. Insert Trailer (if provided)
    if (!empty($draft['trailer_url'])) {
        $trailerStmt = $Conn->prepare("
            INSERT INTO series_trailers (series_id, youtube_url) 
            VALUES (?, ?)
        ");
        
        $trailerStmt->bind_param('is', $series_id, $draft['trailer_url']);
        
        if (!$trailerStmt->execute()) {
            throw new Exception('Failed to insert trailer');
        }
        
        $trailerStmt->close();
    }

    // 12. Log Activity (if activity logger exists)
    if (file_exists('../includes/activity-logger.php')) {
        require_once '../includes/activity-logger.php';
        logActivity($Conn, 'Added', 'Series', $draft['title'], $_SESSION['user_id']);
    }

    // 13. Commit Transaction
    $Conn->commit();
    
    // 14. Clear Draft
    unset($_SESSION['series_draft']);
    
    // 15. Success Response
    echo json_encode([
        'status' => 'success',
        'series_id' => $series_id,
        'message' => 'Series published successfully'
    ]);

} catch (Exception $e) {
    // Rollback on any error
    $Conn->rollback();
    
    // Log error for debugging
    error_log("Series Commit Error: " . $e->getMessage());
    error_log("Draft data: " . print_r($draft, true));
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'error' => 'Failed to publish series: ' . $e->getMessage()
    ]);
}
?>