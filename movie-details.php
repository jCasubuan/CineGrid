<!-- PHP connection -->
<?php
require_once 'includes/init.php';

if (!function_exists('formatDuration')) {
    function formatDuration($minutes) {
        $hours = floor($minutes / 60);
        $min = $minutes % 60;
        return $hours . "h " . $min . "m";
    }
}

if (!function_exists('formatNumber')) {
    function formatNumber($num) {
        if ($num >= 1000000) {
            return round($num / 1000000, 1) . 'M';
        }
        if ($num >= 1000) {
            return round($num / 1000, 1) . 'K';
        }
        return $num;
    }
}

function getYoutubeEmbedUrl($url) {
    $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
    $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';

    if (preg_match($shortUrlRegex, $url, $matches)) {
        $youtube_id = $matches[1];
    } else if (preg_match($longUrlRegex, $url, $matches)) {
        $youtube_id = $matches[3];
    } else {
        return $url; // Fallback
    }
    return "https://www.youtube.com/embed/" . $youtube_id;
}

// Get movie ID from URL (e.g., movie-details.php?id=12)
$movieId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$movieData = null;

// Fetch movie data if valid ID
if ($movieId > 0) {
    $query = "SELECT m.*, 
              GROUP_CONCAT(DISTINCT g.name SEPARATOR ' • ') as genre_list,
              GROUP_CONCAT(DISTINCT d.name SEPARATOR ', ') as director_list,
              GROUP_CONCAT(DISTINCT w.name SEPARATOR ', ') as writer_list,
              t.youtube_url
              FROM movies m 
              LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id 
              LEFT JOIN genres g ON mg.genre_id = g.genre_id 
              LEFT JOIN movie_directors md ON m.movie_id = md.movie_id
              LEFT JOIN directors d ON md.director_id = d.director_id
              LEFT JOIN movie_writers mw ON m.movie_id = mw.movie_id
              LEFT JOIN writers w ON mw.writer_id = w.writer_id
              LEFT JOIN movie_trailers t ON m.movie_id = t.movie_id
              WHERE m.movie_id = ?
              GROUP BY m.movie_id";
    
    $stmt = $Conn->prepare($query);
    $stmt->bind_param("i", $movieId);
    $stmt->execute();
    $result = $stmt->get_result();
    $movieData = $result->fetch_assoc();
    $stmt->close();
}

// Redirect if movie not found
if (!$movieData) {
    header("Location: index.php");
    exit;
}

// dummy data
$fakeViews = ($movieData['movie_id'] * 12500) + 500000;   
$fakeRatings = ($movieData['movie_id'] * 5200) + 100000; 
$fakeReviews = ($movieData['movie_id'] * 150) + 2000;     

// Calculate percentage for the circle
$rating = isset($movieData['rating']) ? $movieData['rating'] : 0;
$percentage = $rating * 10;

// Set page title
$pageTitle = htmlspecialchars($movieData['title']);

// Prepare the backdrop URL
$backdrop = !empty($movieData['backdrop_path']) 
            ? $movieData['backdrop_path'] 
            : 'assets/img/default-backdrop.jpg'; // Fallback image

// 1. Get the Cast (Joining movie_cast and actors)
// $castQuery = "SELECT a.name, a.image_path, mc.character_name 
//               FROM movie_cast mc 
//               JOIN actors a ON mc.actor_id = a.actor_id 
//               WHERE mc.movie_id = ? 
//               ORDER BY a.name ASC 
//               LIMIT 6";

$castQuery = "SELECT a.name, a.image_path, mc.character_name 
              FROM movie_cast mc 
              JOIN actors a ON mc.actor_id = a.actor_id 
              WHERE mc.movie_id = ? 
              ORDER BY mc.movie_id, a.actor_id ASC 
              LIMIT 6";

$stmtCast = $Conn->prepare($castQuery); 
$stmtCast->bind_param("i", $movieData['movie_id']);
$stmtCast->execute();
$castResult = $stmtCast->get_result();

// 2. Full Cast URL (using tmdb_id from your movies table)
$tmdbType = 'movie';

$fullCastUrl = !empty($movieData['tmdb_id']) 
    ? "https://www.themoviedb.org/" . $tmdbType . "/" . $movieData['tmdb_id'] . "/cast"
    : "#";

// Fetch Similar Movies based on genres (RANDOM)
$similarMovies = [];
if ($movieData && !empty($movieData['genre_list'])) {
    $similarQuery = "SELECT DISTINCT m.movie_id, m.title, m.poster_path, m.release_year, m.rating,
                     GROUP_CONCAT(DISTINCT g.name SEPARATOR ' • ') as genre_names
                     FROM movies m
                     LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
                     LEFT JOIN genres g ON mg.genre_id = g.genre_id
                     WHERE m.movie_id != ? 
                     AND g.name IN (SELECT g2.name 
                                    FROM movie_genres mg2 
                                    JOIN genres g2 ON mg2.genre_id = g2.genre_id 
                                    WHERE mg2.movie_id = ?)
                     GROUP BY m.movie_id
                     ORDER BY RAND()
                     LIMIT 4";
    
    $stmtSimilar = $Conn->prepare($similarQuery);
    $stmtSimilar->bind_param("ii", $movieId, $movieId);
    $stmtSimilar->execute();
    $similarResult = $stmtSimilar->get_result();
    
    while($row = $similarResult->fetch_assoc()) {
        $similarMovies[] = $row;
    }
    $stmtSimilar->close();
    
    // If we don't have enough similar movies (less than 4), fill with completely random movies
    if (count($similarMovies) < 4) {
        $remaining = 4 - count($similarMovies);
        $existingIds = array_column($similarMovies, 'movie_id');
        $existingIds[] = $movieId; // Exclude current movie
        
        $placeholders = str_repeat('?,', count($existingIds) - 1) . '?';
        
        $randomQuery = "SELECT m.movie_id, m.title, m.poster_path, m.release_year, m.rating,
                        GROUP_CONCAT(DISTINCT g.name SEPARATOR ' • ') as genre_names
                        FROM movies m
                        LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
                        LEFT JOIN genres g ON mg.genre_id = g.genre_id
                        WHERE m.movie_id NOT IN ($placeholders)
                        GROUP BY m.movie_id
                        ORDER BY RAND()
                        LIMIT ?";
        
        $stmtRandom = $Conn->prepare($randomQuery);
        $types = str_repeat('i', count($existingIds)) . 'i';
        $params = array_merge($existingIds, [$remaining]);
        $stmtRandom->bind_param($types, ...$params);
        $stmtRandom->execute();
        $randomResult = $stmtRandom->get_result();
        
        while($row = $randomResult->fetch_assoc()) {
            $similarMovies[] = $row;
        }
        $stmtRandom->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Watch and review Movie Title on CineGrid">
    <title><?= $pageTitle; ?> | CineGrid</title>

    <!-- Site Icon / Logo -->
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" type="image/svg+xml" href="assets/img/logo.svg">

    <!-- BootStrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- Boostrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
        rel="stylesheet">

    <!-- CineGrid base styles -->
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/details.css">

    <!-- Bootstrap overrides (modals, buttons, navbar, etc.) -->
    <link rel="stylesheet"   href="assets/css/bootstrap-overrides.css">
</head>

<body data-type="movie">
    <!-- Banner for admin browsing pagaes -->
    <?php include 'includes/admin-banner.php'; ?>

    <!-- for PHP -->
    <?php include 'includes/navbar.php'; ?>

    <!-- HERO BANNER -->
    <section class="details-hero" style="background-image: url('<?= htmlspecialchars($backdrop); ?>');">
        <div class="details-hero-overlay"></div>
    </section>

    <!-- MAIN CONTENT -->
    <main class="container">
        <div class="row">
            <!-- Movie Poster (Floating) -->
            <div class="col-12 col-md-3">
                <?php 
                    $poster = !empty($movieData['poster_path']) ? $movieData['poster_path'] : 'assets/img/default-poster.jpg';
                ?>
                <img src="<?= htmlspecialchars($poster); ?>" 
                    class="img-fluid floating-poster shadow-lg rounded" 
                    alt="<?= htmlspecialchars($movieData['title']); ?>">
            </div>

            <!-- Movie Information -->
            <div class="col-12 col-md-9 mt-4 mt-md-0">
                <!-- Title & Meta -->
                <div class="mb-3">
                    <h1 class="display-5 fw-bold text-white d-inline">
                        <?= htmlspecialchars($movieData['title']); ?>
                        <span class="badge bg-warning text-dark fs-6 ms-2 align-middle">
                            <?= htmlspecialchars($movieData['content_rating'] ?? 'NR'); ?>
                        </span>
                    </h1>
                </div>

                <p class="text-white mb-4">
                    <i class="bi bi-calendar3 me-1 text-primary"></i> <?= $movieData['release_year']; ?>
                    <span class="mx-2 text-secondary">•</span>
                    <i class="bi bi-clock me-1 text-primary"></i> <?= formatDuration($movieData['duration']); ?>
                    <span class="mx-2 text-secondary">•</span>
                    <i class="bi bi-translate me-1 text-primary"></i> <?= htmlspecialchars($movieData['language']); ?>
                </p>

                <!-- Genres -->
                <div class="mb-4">
                    <?php 
                    $genres = explode(' • ', $movieData['genre_list']);
                    foreach($genres as $genre): 
                    ?>
                        <span class="tag me-2 mb-2">
                            <i class="bi bi-tag-fill me-1"></i><?= htmlspecialchars($genre); ?>
                        </span>
                    <?php endforeach; ?>
                </div>

                <!-- Rating & Actions -->
                <div class="row g-3 mb-4">
                    <div class="col-auto">
                        <div class="rating-circle" style="background: conic-gradient(#4caf50 0% <?= $percentage ?>%, #333 <?= $percentage ?>% 100%);">
                            <span class="rating-value"><?= number_format($rating, 1); ?></span>
                        </div>
                        <small class="d-block text-center mt-2 text-white">IMDb Rating</small>
                    </div>

                    <div class="col">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="#trailer-section" class="btn btn-primary action-btn">
                                <i class="bi bi-play-fill me-2"></i>Watch Trailer
                            </a>
                            <button class="btn btn-outline-light action-btn">
                                <i class="bi bi-bookmark me-2"></i>Add to Watchlist
                            </button>
                            <button class="btn btn-outline-light action-btn">
                                <i class="bi bi-heart me-2"></i>Favorite
                            </button>
                            <button class="btn btn-outline-light action-btn" data-bs-toggle="modal" data-bs-target="#ratingModal">
                                <i class="bi bi-star me-2"></i>Rate
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <div class="stat-badge">
                        <i class="bi bi-eye me-2"></i>
                        <strong><?= formatNumber($fakeViews); ?></strong> 
                        <small class="text-white-50">Views</small>
                    </div>
                    
                    <div class="stat-badge">
                        <i class="bi bi-star-fill text-warning me-2"></i>
                        <strong><?= formatNumber($fakeRatings); ?></strong> 
                        <small class="text-white-50">Ratings</small>
                    </div>
                    
                    <div class="stat-badge">
                        <i class="bi bi-chat-dots me-2"></i>
                        <strong><?= formatNumber($fakeReviews); ?></strong> 
                        <small class="text-white-50">Reviews</small>
                    </div>
                </div>

                <!-- Synopsis -->
                <div class="mb-4">
                    <h4 class="mb-3 text-white"><i class="bi bi-file-text me-2"></i>Synopsis</h4>
                    <p class="lead text-white">
                        <?= nl2br(htmlspecialchars($movieData['overview'])); ?>
                    </p>
                </div>

                <!-- Director & Writers -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5><i class="bi bi-megaphone me-2"></i>Director</h5>
                        <p class="text-white">
                            <?= !empty($movieData['director_list']) ? htmlspecialchars($movieData['director_list']) : 'N/A'; ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="bi bi-pen me-2"></i>Writers</h5>
                        <p class="text-white">
                            <?= !empty($movieData['writer_list']) ? htmlspecialchars($movieData['writer_list']) : 'N/A'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <hr class="custom-hr mt-5 mb-4">

        <!-- Cast Section -->
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-5"> 
                <h3 class="text-white"><i class="bi bi-people-fill me-2"></i>Top Cast</h3>
                
                <?php if ($fullCastUrl !== "#"): ?>
                    <a href="<?= $fullCastUrl ?>" class="btn btn-outline-light btn-sm" target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-box-arrow-up-right me-1"></i> See Full Cast
                    </a>
                <?php endif; ?>
            </div>

    <div class="row row-cols-2 row-cols-md-6 g-4">
        <?php 
        // Reset displayed counter if needed
        $displayed = 0;

        while($actor = $castResult->fetch_assoc()): 
            $displayed++;
            
            // Logic: Use database image if it exists, otherwise use the initials avatar
            $initialsUrl = "https://ui-avatars.com/api/?name=" . urlencode($actor['name']) . "&background=random&color=fff&size=128";
            $displayPhoto = !empty($actor['image_path']) ? $actor['image_path'] : $initialsUrl;
        ?>
            <div class="col">
                <div class="cast-card text-center">
                    <div class="position-relative d-inline-block">
                        <img src="<?= htmlspecialchars($displayPhoto) ?>" 
                             class="cast-avatar rounded-circle mb-2" 
                             alt="<?= htmlspecialchars($actor['name']); ?>"
                             style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #333; transition: transform 0.3s ease;">
                    </div>
                    <div class="mt-2 px-1">
                        <h6 class="mb-0 text-white text-truncate" title="<?= htmlspecialchars($actor['name']); ?>">
                            <?= htmlspecialchars($actor['name']); ?>
                        </h6>
                        <small class="text-white-50 d-block text-truncate" title="<?= htmlspecialchars($actor['character_name'] ?? 'Role TBA'); ?>">
                            <?= htmlspecialchars($actor['character_name'] ?? 'Role TBA'); ?>
                        </small>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

        <hr class="custom-hr my-5">

        <!-- Trailer Section -->
        <?php if (!empty($movieData['youtube_url'])): ?>
        <section id="trailer-section" class="mb-5">
            <h3 class="mb-4"><i class="bi bi-play-circle me-2"></i>Official Trailer</h3>
            
            <div class="trailer-container shadow-lg rounded">
                <iframe 
                    src="<?= getYoutubeEmbedUrl($movieData['youtube_url']); ?>"
                    title="<?= htmlspecialchars($movieData['title']); ?> Official Trailer" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>
            </div>
        </section>
        <?php endif; ?>

        <hr class="my-5">

        <!-- Reviews Section -->
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-chat-square-text me-2"></i>User Reviews</h3>
                <button class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#reviewModal">
                    <i class="bi bi-pencil me-2"></i>Write Review
                </button>
            </div>

            <!-- Review 1 -->
            <div class="review-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="mb-1">john_doe_92</h5>
                        <div class="review-rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <span class="ms-2">10/10</span>
                        </div>
                    </div>
                    <small class="text-white">2 weeks ago</small>
                </div>
                <h6 class="mb-2">A Masterpiece of Modern Cinema</h6>
                <p class="mb-2">
                    The Dark Knight is not just a superhero movie; it's a crime epic that transcends 
                    the genre. Heath Ledger's performance as the Joker is haunting and unforgettable. 
                    Christopher Nolan delivers a complex, dark, and thrilling narrative that keeps you 
                    on the edge of your seat from start to finish.
                </p>
                <div class="d-flex gap-3">
                    <button class="btn btn-sm btn-outline-light">
                        <i class="bi bi-hand-thumbs-up me-1"></i>Helpful (245)
                    </button>
                    <button class="btn btn-sm btn-outline-light">
                        <i class="bi bi-flag me-1"></i>Report
                    </button>
                </div>
            </div>

            <!-- Review 2 -->
            <div class="review-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="mb-1">movie_buff_2024</h5>
                        <div class="review-rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                            <span class="ms-2">9/10</span>
                        </div>
                    </div>
                    <small class="text-white">1 month ago</small>
                </div>
                <h6 class="mb-2">Outstanding Performance and Direction</h6>
                <p class="mb-2">
                    Heath Ledger's Joker is legendary. The cinematography is stunning, and the story 
                    is gripping. This film set a new standard for superhero movies. A must-watch!
                </p>
                <div class="d-flex gap-3">
                    <button class="btn btn-sm btn-outline-light">
                        <i class="bi bi-hand-thumbs-up me-1"></i>Helpful (189)
                    </button>
                    <button class="btn btn-sm btn-outline-light">
                        <i class="bi bi-flag me-1"></i>Report
                    </button>
                </div>
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-outline-light">Load More Reviews</button>
            </div>
        </section>

        <hr class="my-5">

        <!-- Similar Movies Section -->
        <section class="mb-5">
            <h3 class="mb-4"><i class="bi bi-film me-2"></i>More Like This</h3>
            
            <?php if (!empty($similarMovies)): ?>
                <div class="row row-cols-2 row-cols-md-4 g-4">
                    <?php foreach($similarMovies as $similar): 
                        $posterPath = !empty($similar['poster_path']) ? $similar['poster_path'] : 'assets/img/no-poster.jpg';
                        $genres = !empty($similar['genre_names']) ? explode(' • ', $similar['genre_names'])[0] : 'N/A';
                    ?>
                        <div class="col">
                            <a href="movie-details.php?id=<?= $similar['movie_id']; ?>" class="text-decoration-none">
                                <div class="card media-card bg-dark text-white position-relative">
                                    <img src="<?= htmlspecialchars($posterPath); ?>" 
                                        class="card-img-top" 
                                        alt="<?= htmlspecialchars($similar['title']); ?>"
                                        style="height: 350px; object-fit: cover;">
                                    <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                        <i class="bi bi-star-fill"></i> <?= number_format($similar['rating'], 1); ?>
                                    </span>
                                    <div class="card-body">
                                        <h5 class="card-title text-truncate" title="<?= htmlspecialchars($similar['title']); ?>">
                                            <?= htmlspecialchars($similar['title']); ?>
                                        </h5>
                                        <p class="card-text text-white-50">
                                            <?= htmlspecialchars($genres); ?> • <?= $similar['release_year']; ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center text-white-50 py-5">
                    <i class="bi bi-film fs-1 d-block mb-3"></i>
                    <p>No similar movies found at the moment.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>


    <?php include 'includes/details-rating-modals.php'?>
    
    <?php include 'includes/footer.php'; ?>

    

