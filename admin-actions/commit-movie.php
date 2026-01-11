<?php
require_once '../includes/init.php';

header('Content-Type: application/json');

if (
    empty($_SESSION['user_id']) ||
    $_SESSION['user_role'] !== 'admin'
) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (empty($_SESSION['movie_draft'])) {
    http_response_code(409);
    echo json_encode(['error' => 'No movie draft found']);
    exit;
}

$draft = $_SESSION['movie_draft'];
$Conn->begin_transaction();

try {

    /* 1. Insert movie */
    $stmt = $Conn->prepare("
        INSERT INTO movies (title, overview, release_year, duration, rating, content_rating, language, tmdb_id, poster_path, backdrop_path)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        'ssiidssiss', 
        $draft['basic']['title'],
        $draft['basic']['overview'],
        $draft['basic']['release_year'],
        $draft['basic']['duration'],
        $draft['basic']['rating'],
        $draft['basic']['content_rating'],
        $draft['basic']['language'],
        $draft['basic']['tmdb_id'],
        $draft['basic']['poster_path'],
        $draft['basic']['backdrop_path']
    );

    $stmt->execute();
    $movie_id = $Conn->insert_id;

    /* 2. Trailer (1:1) */
    if (!empty($draft['trailer']['url'])) {
        $stmt = $Conn->prepare("
            INSERT INTO movie_trailers (movie_id, youtube_url)
            VALUES (?, ?)
        ");
        $stmt->bind_param('is', $movie_id, $draft['trailer']['url']);
        $stmt->execute();
    }

    /* 2.5 Genres (Many-to-Many) */
    if (!empty($draft['genres'])) {
        foreach ($draft['genres'] as $genre_name) {
            // Insert genre if it doesn't exist, get ID if it does
            $stmt = $Conn->prepare("
                INSERT INTO genres (name)
                VALUES (?)
                ON DUPLICATE KEY UPDATE genre_id = LAST_INSERT_ID(genre_id)
            ");
            $stmt->bind_param('s', $genre_name);
            $stmt->execute();
            
            $genre_id = $Conn->insert_id;

            // Link movie to genre
            $stmt = $Conn->prepare("
                INSERT IGNORE INTO movie_genres (movie_id, genre_id)
                VALUES (?, ?)
            ");
            $stmt->bind_param('ii', $movie_id, $genre_id);
            $stmt->execute();
        }
    }

    /* 3. Directors */
    foreach ($draft['directors'] as $name) {

        $stmt = $Conn->prepare("
            INSERT INTO directors (name)
            VALUES (?)
            ON DUPLICATE KEY UPDATE director_id = LAST_INSERT_ID(director_id)
        ");
        $stmt->bind_param('s', $name);
        $stmt->execute();

        $director_id = $Conn->insert_id; 

        $stmt = $Conn->prepare("
            INSERT IGNORE INTO movie_directors (movie_id, director_id) 
            VALUES (?, ?)"
        );
        $stmt->bind_param('ii', $movie_id, $director_id);
        $stmt->execute();
    }

    // for writers
    if (!empty($draft['writers'])) {
        foreach ($draft['writers'] as $name) {
            $stmt = $Conn->prepare("INSERT INTO writers (name) VALUES (?) ON DUPLICATE KEY UPDATE writer_id = LAST_INSERT_ID(writer_id)");
            $stmt->bind_param('s', $name);
            $stmt->execute();
            $writer_id = $Conn->insert_id;

            $stmt = $Conn->prepare("INSERT IGNORE INTO movie_writers (movie_id, writer_id) VALUES (?, ?)");
            $stmt->bind_param('ii', $movie_id, $writer_id);
            $stmt->execute();
        }
    }

    /* 4. Cast */
    foreach ($draft['cast'] as $c) {
        // We update the image path every time we encounter the actor
        $stmt = $Conn->prepare("
            INSERT INTO actors (name, image_path)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE 
                actor_id = LAST_INSERT_ID(actor_id),
                image_path = VALUES(image_path)
        ");
        $stmt->bind_param('ss', $c['actor'], $c['image']);
        $stmt->execute();
        $actor_id = $Conn->insert_id;

        $stmt = $Conn->prepare("INSERT INTO movie_cast (movie_id, actor_id, character_name) VALUES (?, ?, ?)");
        $stmt->bind_param('iis', $movie_id, $actor_id, $c['character']);
        $stmt->execute();
    }

    $Conn->commit();
    //unset($_SESSION['movie_draft']);
    echo json_encode(['status' => 'success']);

} catch (Throwable $e) {

    if ($Conn && $Conn->connect_errno === 0) {
        $Conn->rollback();
    }
    
    error_log($e->getMessage()); 
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save movie: ' . $e->getMessage()]);
}
