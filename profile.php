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

   <title>CineGrid | <?php echo ucfirst($current_page); ?></title>

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

    <!-- Main content -->
    <main>
        <div class="container my-5 pt-5">
            <!-- Welcome Section -->
            <section class="welcome-section mb-5">
                <h1 class="display-5 fw-bold mb-3">Welcome Back, <span id="userName">User</span>!</h1>
                <p class="lead text-white mb-4">Track your progress, view your ratings and discover new titles.</p>

                <!-- Quick Stats -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card bg-dark text-white text-center p-3">
                            <h3 class="mb-0"><i class="bi bi-star-fill text-warning"></i> 0</h3>
                            <small class="text-white">Ratings</small>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="card bg-dark text-white text-center p-3">
                            <h3 class="mb-0"><i class="bi bi-bookmark-fill text-info"></i> 0</h3>
                            <small class="text-white">Watchlist</small>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="card bg-dark text-white text-center p-3">
                            <h3 class="mb-0"><i class="bi bi-heart-fill text-danger"></i> 0</h3>
                            <small class="text-white">Favorites</small>
                        </div>
                    </div>
                </div>
            </section>

            <hr class="text-white my-5">

            <!-- Recently Rated Section -->
            <section class="rated-section mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Recently Rated</h2>
                    <a href="#" class="btn btn-outline-light btn-sm">
                        See All Ratings <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="row row-cols-2 row-cols-md-4 g-4 mb-5">
                    <!-- Rated Card 1 -->
                    <div class="col">
                        <a href="movie-details.html" class="text-decoration-none">
                            <div class="card movie-card bg-dark text-white position-relative">
                                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Rated Movie">
                                <!-- Rating Badge -->
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 9.0
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">The Matrix</h5>
                                    <p class="card-text text-white">Sci-Fi • 1999</p>
                                    <small class="text-white-50">Rated 2 days ago</small>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Rated Card 2 -->
                    <div class="col">
                        <a href="movie-details.html" class="text-decoration-none">
                            <div class="card movie-card bg-dark text-white position-relative">
                                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Rated Movie">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 8.5
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">Inception</h5>
                                    <p class="card-text text-white">Sci-Fi • 2010</p>
                                    <small class="text-white-50">Rated 5 days ago</small>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Rated Card 3 -->
                    <div class="col">
                        <a href="movie-details.html" class="text-decoration-none">
                            <div class="card movie-card bg-dark text-white position-relative">
                                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Rated Movie">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 7.8
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">Interstellar</h5>
                                    <p class="card-text text-white">Sci-Fi • 2014</p>
                                    <small class="text-white-50">Rated 1 week ago</small>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Rated Card 4 -->
                    <div class="col">
                        <a href="movie-details.html" class="text-decoration-none">
                            <div class="card movie-card bg-dark text-white position-relative">
                                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Rated Movie">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 9.2
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">The Dark Knight</h5>
                                    <p class="card-text text-white">Action • 2008</p>
                                    <small class="text-white-50">Rated 2 weeks ago</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </section>

            <!-- Watchlist Section -->
            <section class="watchlist-section mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Your Watchlist</h2>
                    <a href="watchlist.html" class="btn btn-outline-light btn-sm">
                        See Full List <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="row row-cols-2 row-cols-md-4 g-4 mb-5">
                    <!-- Watchlist Card 1 -->
                    <div class="col">
                        <a href="movie-details.html" class="text-decoration-none">
                            <div class="card movie-card bg-dark text-white position-relative">
                                <img src="https://via.placeholder.com/300x450" class="card-img-top"
                                    alt="Watchlist Movie">
                                <!-- Watchlist Badge -->
                                <span class="position-absolute top-0 start-0 badge bg-info m-2">
                                    <i class="bi bi-bookmark-fill"></i>
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">Dune</h5>
                                    <p class="card-text text-white">Sci-Fi • 2021</p>
                                    <small class="text-white-50">Added 3 days ago</small>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Watchlist Card 2 -->
                    <div class="col">
                        <a href="movie-details.html" class="text-decoration-none">
                            <div class="card movie-card bg-dark text-white position-relative">
                                <img src="https://via.placeholder.com/300x450" class="card-img-top"
                                    alt="Watchlist Movie">
                                <span class="position-absolute top-0 start-0 badge bg-info m-2">
                                    <i class="bi bi-bookmark-fill"></i>
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">Avatar 2</h5>
                                    <p class="card-text text-white">Action • 2022</p>
                                    <small class="text-white-50">Added 1 week ago</small>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Watchlist Card 3 -->
                    <div class="col">
                        <a href="movie-details.html" class="text-decoration-none">
                            <div class="card movie-card bg-dark text-white position-relative">
                                <img src="https://via.placeholder.com/300x450" class="card-img-top"
                                    alt="Watchlist Movie">
                                <span class="position-absolute top-0 start-0 badge bg-info m-2">
                                    <i class="bi bi-bookmark-fill"></i>
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">Oppenheimer</h5>
                                    <p class="card-text text-white">Drama • 2023</p>
                                    <small class="text-white-50">Added 2 weeks ago</small>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Watchlist Card 4 -->
                    <div class="col">
                        <a href="movie-details.html" class="text-decoration-none">
                            <div class="card movie-card bg-dark text-white position-relative">
                                <img src="https://via.placeholder.com/300x450" class="card-img-top"
                                    alt="Watchlist Movie">
                                <span class="position-absolute top-0 start-0 badge bg-info m-2">
                                    <i class="bi bi-bookmark-fill"></i>
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">The Irishman</h5>
                                    <p class="card-text text-white">Crime • 2019</p>
                                    <small class="text-white-50">Added 1 month ago</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>