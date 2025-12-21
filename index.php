<!-- PHP connection -->
<?php
require_once 'includes/init.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CineGrid - Your ultimate destination for movies, series, and entertainment">

    <title>CineGrid | Home</title>

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

    <!-- navbar.php connection -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Carousel Section -->
    <section id="featuredSlider" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
        <!-- Indicators / Bullets -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#featuredSlider" data-bs-slide-to="0" class="active" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#featuredSlider" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#featuredSlider" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>

        <div class="carousel-inner">
            <!-- Slide 1 -->
            <div class="carousel-item active hero-slide" style="background-image: url('banner1.jpg');">
                <div class="hero-overlay">
                    <div class="container">
                        <span class="badge bg-warning text-dark mb-3"><i class="bi bi-star-fill"></i> 9.0 IMDb</span>
                        <h1 class="display-3 fw-bold mb-3">The Dark Knight</h1>
                        <p class="lead mb-4 text-white-50">Action • Crime • Drama • 2008 • 2h 32m</p>
                        <p class="lead mb-4" style="max-width: 800px; margin: 0 auto;">
                            When the menace known as the Joker wreaks havoc on Gotham, Batman must accept one of the greatest tests.
                        </p>
                        <div class="d-flex gap-3 justify-content-center">
                            <a href="movie-details.php" class="btn btn-primary btn-lg px-4">
                                <i class="bi bi-play-fill me-2"></i>View Details
                            </a>
                            <a href="#" class="btn btn-outline-light btn-lg px-4">
                                <i class="bi bi-plus-lg me-2"></i>Add to Watchlist
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="carousel-item hero-slide" style="background-image: url('banner2.jpg');">
                <div class="hero-overlay">
                    <div class="container">
                        <span class="badge bg-success mb-3"><i class="bi bi-tv"></i> Series</span>
                        <h1 class="display-3 fw-bold mb-3">Breaking Bad</h1>
                        <p class="lead mb-4 text-white-50">Crime • Drama • Thriller • 2008-2013 • 5 Seasons</p>
                        <p class="lead mb-4" style="max-width: 800px; margin: 0 auto;">
                            A chemistry teacher diagnosed with cancer turns to cooking meth to secure his family's future.
                        </p>
                        <div class="d-flex gap-3 justify-content-center">
                            <a href="series-details.php" class="btn btn-primary btn-lg px-4">
                                <i class="bi bi-info-circle me-2"></i>Learn More
                            </a>
                            <a href="#" class="btn btn-outline-light btn-lg px-4">
                                <i class="bi bi-bookmark me-2"></i>Save
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="carousel-item hero-slide" style="background-image: url('banner3.jpg');">
                <div class="hero-overlay">
                    <div class="container">
                        <span class="badge bg-danger mb-3"><i class="bi bi-fire"></i> New Release</span>
                        <h1 class="display-3 fw-bold mb-3">Dune: Part Two</h1>
                        <p class="lead mb-4 text-white-50">Sci-Fi • Adventure • Action • 2024 • 2h 46m</p>
                        <p class="lead mb-4" style="max-width: 800px; margin: 0 auto;">
                            Paul Atreides unites with Chani and the Fremen while seeking revenge against those who destroyed his family.
                        </p>
                        <div class="d-flex gap-3 justify-content-center">
                            <a href="movie-details.php" class="btn btn-primary btn-lg px-4">
                                <i class="bi bi-eye me-2"></i>Explore
                            </a>
                            <a href="#" class="btn btn-outline-light btn-lg px-4">
                                <i class="bi bi-share me-2"></i>Share
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Arrows -->
        <button class="carousel-control-prev" type="button" data-bs-target="#featuredSlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#featuredSlider" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </section>

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

                <!-- Movie Card 1 -->
                <div class="col">
                    <a href="movie-details.php" class="text-decoration-none">
                        <div class="card media-card bg-dark text-white position-relative">
                            <img src="https://via.placeholder.com/300x450/667eea/ffffff?text=Inception" class="card-img-top" alt="Inception">
                            <span class="rating-badge">
                                <i class="bi bi-star-fill text-warning"></i> 8.8
                            </span>
                            <div class="card-body">
                                <h5 class="card-title">Inception</h5>
                                <p class="card-text text-white">Sci-Fi • 2010</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Movie Card 2 -->
                <div class="col">
                    <a href="movie-details.php" class="text-decoration-none">
                        <div class="card media-card bg-dark text-white position-relative">
                            <img src="https://via.placeholder.com/300x450/764ba2/ffffff?text=Interstellar" class="card-img-top" alt="Interstellar">
                            <span class="rating-badge">
                                <i class="bi bi-star-fill text-warning"></i> 8.6
                            </span>
                            <div class="card-body">
                                <h5 class="card-title">Interstellar</h5>
                                <p class="card-text text-white">Sci-Fi • 2014</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Movie Card 3 -->
                <div class="col">
                    <a href="movie-details.php" class="text-decoration-none">
                        <div class="card media-card bg-dark text-white position-relative">
                            <img src="https://via.placeholder.com/300x450/4ecdc4/ffffff?text=The+Matrix" class="card-img-top" alt="The Matrix">
                            <span class="rating-badge">
                                <i class="bi bi-star-fill text-warning"></i> 8.7
                            </span>
                            <div class="card-body">
                                <h5 class="card-title">The Matrix</h5>
                                <p class="card-text text-white">Sci-Fi • 1999</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Movie Card 4 -->
                <div class="col">
                    <a href="movie-details.php" class="text-decoration-none">
                        <div class="card media-card bg-dark text-white position-relative">
                            <img src="https://via.placeholder.com/300x450/ff6b6b/ffffff?text=Parasite" class="card-img-top" alt="Parasite">
                            <span class="rating-badge">
                                <i class="bi bi-star-fill text-warning"></i> 8.5
                            </span>
                            <div class="card-body">
                                <h5 class="card-title">Parasite</h5>
                                <p class="card-text text-white">Thriller • 2019</p>
                            </div>
                        </div>
                    </a>
                </div>
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