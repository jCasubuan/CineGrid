<?php
require_once '../includes/init.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$movie_id = isset($_POST['movie_id']) ? intval($_POST['movie_id']) : 0;

if ($movie_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid movie ID']);
    exit;
}

$Conn->begin_transaction();

try {
    // Update basic movie info
    $stmt = $Conn->prepare("
        UPDATE movies SET
            title = ?,
            overview = ?,
            release_year = ?,
            duration = ?,
            rating = ?,
            content_rating = ?,
            language = ?,
            tmdb_id = ?,
            poster_path = ?,
            backdrop_path = ?,
            status = ?
        WHERE movie_id = ?
    ");
    
    $stmt->bind_param(
        'ssiidssisssi',
        $_POST['title'],
        $_POST['overview'],
        $_POST['release_year'],
        $_POST['duration'],
        $_POST['rating'],
        $_POST['content_rating'],
        $_POST['language'],
        $_POST['tmdb_id'],
        $_POST['poster_path'],
        $_POST['backdrop_path'],
        $_POST['status'],
        $movie_id
    );
    
    $stmt->execute();
    
    // Update genres
    if (isset($_POST['genres'])) {
        // Delete existing genres
        $Conn->query("DELETE FROM movie_genres WHERE movie_id = $movie_id");
        
        // Insert new genres
        foreach ($_POST['genres'] as $genre_name) {
            // Get or create genre
            $genre_stmt = $Conn->prepare("
                INSERT INTO genres (name) VALUES (?)
                ON DUPLICATE KEY UPDATE genre_id = LAST_INSERT_ID(genre_id)
            ");
            $genre_stmt->bind_param('s', $genre_name);
            $genre_stmt->execute();
            $genre_id = $Conn->insert_id;
            
            // Link genre to movie
            $link_stmt = $Conn->prepare("INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)");
            $link_stmt->bind_param('ii', $movie_id, $genre_id);
            $link_stmt->execute();
        }
    }
    
    // Update trailer
    if (isset($_POST['trailer_url'])) {
        // Delete existing trailer
        $Conn->query("DELETE FROM movie_trailers WHERE movie_id = $movie_id");
        
        // Insert new trailer if URL is not empty
        if (!empty($_POST['trailer_url'])) {
            $trailer_stmt = $Conn->prepare("INSERT INTO movie_trailers (movie_id, youtube_url) VALUES (?, ?)");
            $trailer_stmt->bind_param('is', $movie_id, $_POST['trailer_url']);
            $trailer_stmt->execute();
        }
    }

    // Update directors
    if (isset($_POST['directors'])) {
        // Delete existing directors
        $Conn->query("DELETE FROM movie_directors WHERE movie_id = $movie_id");
        
        // Insert new directors
        foreach ($_POST['directors'] as $director_name) {
            if (!empty($director_name)) {
                $dir_stmt = $Conn->prepare("INSERT INTO directors (name) VALUES (?) ON DUPLICATE KEY UPDATE director_id = LAST_INSERT_ID(director_id)");
                $dir_stmt->bind_param('s', $director_name);
                $dir_stmt->execute();
                $director_id = $Conn->insert_id;
                
                $link_stmt = $Conn->prepare("INSERT INTO movie_directors (movie_id, director_id) VALUES (?, ?)");
                $link_stmt->bind_param('ii', $movie_id, $director_id);
                $link_stmt->execute();
            }
        }
    }

    // Update writers
    if (isset($_POST['writers'])) {
        $Conn->query("DELETE FROM movie_writers WHERE movie_id = $movie_id");
        
        foreach ($_POST['writers'] as $writer_name) {
            if (!empty($writer_name)) {
                $wr_stmt = $Conn->prepare("INSERT INTO writers (name) VALUES (?) ON DUPLICATE KEY UPDATE writer_id = LAST_INSERT_ID(writer_id)");
                $wr_stmt->bind_param('s', $writer_name);
                $wr_stmt->execute();
                $writer_id = $Conn->insert_id;
                
                $link_stmt = $Conn->prepare("INSERT INTO movie_writers (movie_id, writer_id) VALUES (?, ?)");
                $link_stmt->bind_param('ii', $movie_id, $writer_id);
                $link_stmt->execute();
            }
        }
    }

    // Update cast
    if (isset($_POST['actors']) && isset($_POST['characters'])) {
        $Conn->query("DELETE FROM movie_cast WHERE movie_id = $movie_id");
        
        $actors = $_POST['actors'];
        $characters = $_POST['characters'];
        $images = $_POST['actor_images'] ?? [];
        
        for ($i = 0; $i < count($actors); $i++) {
            if (!empty($actors[$i]) && !empty($characters[$i])) {
                $image = $images[$i] ?? 'assets/img/actors/default.jpg';
                
                $act_stmt = $Conn->prepare("INSERT INTO actors (name, image_path) VALUES (?, ?) ON DUPLICATE KEY UPDATE actor_id = LAST_INSERT_ID(actor_id), image_path = VALUES(image_path)");
                $act_stmt->bind_param('ss', $actors[$i], $image);
                $act_stmt->execute();
                $actor_id = $Conn->insert_id;
                
                $cast_stmt = $Conn->prepare("INSERT INTO movie_cast (movie_id, actor_id, character_name) VALUES (?, ?, ?)");
                $cast_stmt->bind_param('iis', $movie_id, $actor_id, $characters[$i]);
                $cast_stmt->execute();
            }
        }
    }
    
    $Conn->commit();

    // Log activity
    require_once '../includes/activity-logger.php';
    logActivity($Conn, 'Updated', 'Movie', $_POST['title'], $_SESSION['user_id']);

    echo json_encode(['success' => true, 'message' => 'Movie updated successfully']);
    
} catch (Exception $e) {
    $Conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error updating movie: ' . $e->getMessage()]);
}
?>