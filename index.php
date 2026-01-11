<!-- PHP connection -->
<?php
require_once 'includes/init.php';

function formatDuration($mins) {
    if (!$mins) return "N/A";
    $hours = floor($mins / 60);
    $minutes = $mins % 60;
    if ($hours > 0) {
        return $hours . "h " . ($minutes > 0 ? $minutes . "m" : "");
    }
    return $minutes . "m";
}

// Fetch top 4 highest rated active movies
$heroQuery = "
    SELECT m.*, GROUP_CONCAT(g.name SEPARATOR ' • ') as genre_list
    FROM movies m
    LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
    LEFT JOIN genres g ON mg.genre_id = g.genre_id
    WHERE m.status = 'active'
    GROUP BY m.movie_id
    ORDER BY m.rating DESC
    LIMIT 1
";
$heroResult = $Conn->query($heroQuery);
$hero = $heroResult->fetch_assoc();

$popularQuery = "
    SELECT m.movie_id, m.title, m.poster_path, m.rating, m.release_year, m.duration, 
           GROUP_CONCAT(g.name SEPARATOR ' • ') as genre_list
    FROM movies m
    LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
    LEFT JOIN genres g ON mg.genre_id = g.genre_id
    WHERE m.status = 'active'
    GROUP BY m.movie_id
    ORDER BY m.rating DESC
    LIMIT 4 
";
$featuredResult = $Conn->query($popularQuery);

// for movie being fetured
$featured_sql = "SELECT m.*, GROUP_CONCAT(g.name SEPARATOR ' • ') as genre_list 
                 FROM movies m 
                 LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
                 LEFT JOIN genres g ON mg.genre_id = g.genre_id
                 WHERE m.is_featured = 1 AND m.status = 'active'
                 GROUP BY m.movie_id 
                 ORDER BY m.created_at DESC 
                 LIMIT 5";
$featured_res = mysqli_query($Conn, $featured_sql);

$featured_movies = [];
while ($row = mysqli_fetch_assoc($featured_res)) {
    $featured_movies[] = $row;
}
$total_featured = count($featured_movies);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CineGrid - Your ultimate destination for movies, series, and entertainment">

    <title>CineGrid</title>

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

    <!-- Bootstrap overrides (modals, buttons, navbar, etc.) -->
    <link rel="stylesheet" href="assets/css/bootstrap-overrides.css">
</head>

<body>
    <!-- Banner for admin browsing pagaes -->
    <?php include 'includes/admin-banner.php'; ?>

    <!-- Alert message for new users only -->
    <?php if (!empty($_SESSION['welcome_new_user'])): ?>
        <div class="alert alert-success text-center rounded-0 mb-0" id="welcomeAlert">
            <strong>Welcome to CineGrid, <?= htmlspecialchars($_SESSION['welcome_name']); ?>!</strong>
            Your account has been successfully created.
        </div>

        <script>
            setTimeout(() => {
                const alert = document.getElementById('welcomeAlert');
                if (alert) {
                    alert.classList.add('fade');
                    setTimeout(() => alert.remove(), 300);
                }
            }, 3000);
        </script>

        <?php
            unset($_SESSION['welcome_new_user']);
            unset($_SESSION['welcome_name']);
        ?>
    <?php endif; ?>

    <!-- navbar.php connection -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Carousel Section -->
    <?php if ($total_featured > 0): ?>
    <section id="featuredSlider" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-indicators">
            <?php foreach ($featured_movies as $i => $movie): ?>
                <button type="button" data-bs-target="#featuredSlider" data-bs-slide-to="<?= $i ?>" 
                        class="<?= $i === 0 ? 'active' : '' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
            <?php endforeach; ?>
        </div>

        <div class="carousel-inner">
            <?php foreach ($featured_movies as $index => $movie): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                    <div class="hero-slide">
                        <div class="hero-overlay" style="background-image: url('<?= htmlspecialchars($movie['backdrop_path']) ?>');">
                            
                            <div class="hero-content-wrapper">
                                <span class="badge bg-warning text-dark mb-3">
                                    <i class="bi bi-star-fill"></i> <?= number_format($movie['rating'], 1) ?> Rating
                                </span>
                                
                                <h1 class="display-4 fw-bold text-white mb-2"><?= htmlspecialchars($movie['title']) ?></h1>
                                
                                <p class="text-white-50 mb-3">
                                    <?= htmlspecialchars($movie['genre_list']) ?> • <?= $movie['release_year'] ?>
                                </p>
                                
                                <p class="lead text-white mb-4 opacity-75" style="font-size: 0.95rem;">
                                    <?= htmlspecialchars(mb_strimwidth($movie['overview'], 0, 160, "...")) ?>
                                </p>
                                
                                <div class="d-flex gap-3">
                                    <a href="movie-details.php?id=<?= $movie['movie_id'] ?>" class="btn btn-primary px-4 py-2 fw-bold">
                                        <i class="bi bi-play-fill"></i> View Details
                                    </a>
                                    <a href="#" class="btn btn-outline-light px-4 py-2">
                                        <i class="bi bi-plus-lg"></i> Watchlist
                                    </a>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Previous/Next Navigation Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#featuredSlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#featuredSlider" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </section>
    <?php endif; ?>

    <!-- Main CONTENT -->
    <main>
        <!-- Movie List Section -->
       <section class="container my-5" id="popular-movies">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0 section-header">Popular Movies</h2>
                <a href="movies.php" class="btn btn-outline-light btn-sm">
                    See More <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <!-- 2 columns on mobile, 4 on desktop -->
            <div class="row row-cols-2 row-cols-md-4 g-4 mb-5">
                <?php if($featuredResult->num_rows > 0): ?>
                    <?php while($movie = $featuredResult->fetch_assoc()): ?>
                        <div class="col">
                            <a href="movie-details.php?id=<?= $movie['movie_id']; ?>" class="text-decoration-none">
                                <div class="card media-card bg-dark text-white shadow-sm">
                                    <img src="<?= htmlspecialchars($movie['poster_path']); ?>" 
                                    class="card-img-top" 
                                    alt="<?= htmlspecialchars($movie['title']); ?>">
                                    
                                    <span class="rating-badge">
                                        <i class="bi bi-star-fill text-warning"></i> <?= number_format($movie['rating'], 1); ?>
                                    </span>

                                    <div class="card-body p-3">
                                        <h5 class="card-title mb-2 fw-bold" style="font-size: 1.1rem;">
                                            <?= htmlspecialchars($movie['title']); ?>
                                            <span class="ms-1 fw-normal text-white-50" style="font-size: 0.9rem;">
                                                (<?= $movie['release_year']; ?>)
                                            </span>
                                        </h5>
                                        
                                        <p class="card-text text-white-50 mb-0" style="font-size: 0.85rem; line-height: 1.4;">
                                            <span class="text-truncate">
                                                <?php 
                                                    $genres = explode(' • ', $movie['genre_list']); 
                                                    // Changed 2 to 3 here to show more genres
                                                    $short_genres = implode(' • ', array_slice($genres, 0, 3));
                                                    echo htmlspecialchars($short_genres ?: 'General'); 
                                                ?>
                                            </span>
                                            <span class="mx-2">•</span>
                                            <span class="text-nowrap">
                                                <i class="bi bi-clock me-1"></i><?= formatDuration($movie['duration']); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center text-white py-5">
                        <p>No movies added yet. Check back later!</p>
                    </div>
                <?php endif; ?>
            </div>          
        </section>

        <!-- Popular Series Section -->
        <section class="container my-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Popular Series</h2>
                <a href="series.php" class="btn btn-outline-light btn-sm">
                    See More <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <!-- 2 columns on mobile, 4 on desktop -->
            <div class="row row-cols-2 row-cols-md-4 g-4 mb-5"> 

                <!-- Series Card 1 -->
                <div class="col">
                    <a href="series-details.php" class="text-decoration-none">
                        <div class="card media-card bg-dark text-white position-relative">
                            <img src="https://via.placeholder.com/300x450/667eea/ffffff?text=Stranger+Things" class="card-img-top" alt="Series">
                            <span class="rating-badge">
                                <i class="bi bi-star-fill text-warning"></i> 8.7
                            </span>
                            <div class="card-body">
                                <h5 class="card-title">Stranger Things</h5>
                                <p class="card-text text-white">Sci-Fi • 2016</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Series Card 2 -->
                <div class="col">
                    <a href="series-details.php" class="text-decoration-none">
                        <div class="card media-card bg-dark text-white position-relative">
                            <img src="https://via.placeholder.com/300x450/764ba2/ffffff?text=The+Crown" class="card-img-top" alt="Series">
                            <span class="rating-badge">
                                <i class="bi bi-star-fill text-warning"></i> 8.6
                            </span>
                            <div class="card-body">
                                <h5 class="card-title">The Crown</h5>
                                <p class="card-text text-white">Drama • 2016</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Series Card 3 -->
                <div class="col">
                    <a href="series-details.php" class="text-decoration-none">
                        <div class="card media-card bg-dark text-white position-relative">
                            <img src="https://via.placeholder.com/300x450/4ecdc4/ffffff?text=Game+of+Thrones" class="card-img-top" alt="Series">
                            <span class="rating-badge">
                                <i class="bi bi-star-fill text-warning"></i> 9.2
                            </span>
                            <div class="card-body">
                                <h5 class="card-title">Game of Thrones</h5>
                                <p class="card-text text-white">Fantasy • 2011</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Series Card 4 -->
                <div class="col">
                    <a href="series-details.php" class="text-decoration-none">
                        <div class="card media-card bg-dark text-white position-relative">
                            <img src="https://via.placeholder.com/300x450/ff6b6b/ffffff?text=The+Last+of+Us" class="card-img-top" alt="Series">
                            <span class="rating-badge">
                                <i class="bi bi-star-fill text-warning"></i> 8.8
                            </span>
                            <div class="card-body">
                                <h5 class="card-title">The Last of Us</h5>
                                <p class="card-text text-white">Drama • 2023</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>