<?php
require_once 'includes/init.php';

header('Content-Type: application/json');

$search_term = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($search_term)) {
    echo json_encode(['results' => []]);
    exit;
}

// Search movies (and series when ready)
$search_query = "
    SELECT 
        m.movie_id,
        m.title,
        m.poster_path,
        m.rating,
        m.release_year,
        'movie' as type,
        GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') as genres
    FROM movies m
    LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
    LEFT JOIN genres g ON mg.genre_id = g.genre_id
    WHERE m.status = 'active' 
    AND m.title LIKE ?
    GROUP BY m.movie_id
    ORDER BY m.rating DESC
    LIMIT 10
";

$stmt = $Conn->prepare($search_query);
$search_param = "%$search_term%";
$stmt->bind_param('s', $search_param);
$stmt->execute();
$result = $stmt->get_result();

$results = [];
while ($row = $result->fetch_assoc()) {
    $results[] = $row;
}

echo json_encode(['results' => $results]);
$stmt->close();
?>