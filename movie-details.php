<!-- PHP connection -->
<?php
require_once 'includes/init.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Watch and review Movie Title on CineGrid">
    <title>The Dark Knight (2008) | CineGrid</title>

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
    <!-- for PHP -->
    <?php include 'includes/navbar.php'; ?>

    <!-- HERO BANNER -->
    <section class="details-hero" style="background-image: url('https://via.placeholder.com/1920x1080/1a1a2e/667eea?text=The+Dark+Knight+Banner');">
        <div class="details-hero-overlay"></div>
    </section>

    <!-- MAIN CONTENT -->
    <main class="container">
        <div class="row">
            <!-- Movie Poster (Floating) -->
            <div class="col-12 col-md-3">
                <img src="https://via.placeholder.com/300x450/667eea/ffffff?text=The+Dark+Knight" 
                     class="img-fluid floating-poster" 
                     alt="The Dark Knight">
            </div>

            <!-- Movie Information -->
            <div class="col-12 col-md-9 mt-4 mt-md-0">
                <!-- Title & Meta -->
                <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                    <h1 class="display-5 fw-bold mb-0">The Dark Knight</h1>
                    <span class="badge bg-warning text-dark fs-6">PG-13</span>
                </div>

                <p class="text-white mb-4">
                    <i class="bi bi-calendar3 me-1"></i> 2008
                    <span class="mx-2">•</span>
                    <i class="bi bi-clock me-1"></i> 2h 32m
                    <span class="mx-2">•</span>
                    <i class="bi bi-translate me-1"></i> English
                </p>

                <!-- Genres -->
                <div class="mb-4">
                    <span class="tag"><i class="bi bi-tag-fill me-1"></i>Action</span>
                    <span class="tag"><i class="bi bi-tag-fill me-1"></i>Crime</span>
                    <span class="tag"><i class="bi bi-tag-fill me-1"></i>Drama</span>
                    <span class="tag"><i class="bi bi-tag-fill me-1"></i>Thriller</span>
                </div>

                <!-- Rating & Actions -->
                <div class="row g-3 mb-4">
                    <div class="col-auto">
                        <div class="rating-circle" style="background: conic-gradient(#4caf50 0% 87%, #ddd 87% 100%);">
                            <span class="rating-value">8.7</span>
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
                        <strong>2.5M</strong> <small class="text-white">Views</small>
                    </div>
                    <div class="stat-badge">
                        <i class="bi bi-star-fill text-warning me-2"></i>
                        <strong>1.2M</strong> <small class="text-white">Ratings</small>
                    </div>
                    <div class="stat-badge">
                        <i class="bi bi-chat-dots me-2"></i>
                        <strong>45K</strong> <small class="text-white">Reviews</small>
                    </div>
                </div>

                <!-- Synopsis -->
                <div class="mb-4">
                    <h4 class="mb-3"><i class="bi bi-file-text me-2"></i>Synopsis</h4>
                    <p class="lead">
                        When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, 
                        Batman must accept one of the greatest psychological and physical tests of his ability 
                        to fight injustice. With the help of Lt. Jim Gordon and District Attorney Harvey Dent, 
                        Batman sets out to dismantle the remaining criminal organizations.
                    </p>
                </div>

                <!-- Director & Writers -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5><i class="bi bi-megaphone me-2"></i>Director</h5>
                        <p>
                            <a href="https://www.imdb.com/name/nm0634240/" 
                            class="text-decoration-none" 
                            target="_blank">Christopher Nolan
                            </a>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5><i class="bi bi-pen me-2"></i>Writers</h5>
                        <p>
                            <a href="https://www.imdb.com/name/nm0634240/" 
                            class="text-decoration-none" 
                            target="_blank">Christopher Nolan
                            </a>, 
                            <a href="https://www.imdb.com/name/nm0634240/" 
                            class="text-decoration-none" 
                            target="_blank">Christopher Nolan
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5">

        <!-- Cast Section -->
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-people-fill me-2"></i>Top Cast</h3>
                <a href="https://www.imdb.com/title/tt0468569/fullcredits/" class="btn btn-outline-light btn-sm" target="_blank">See Full Cast</a>
            </div>

            <div class="row row-cols-2 row-cols-md-6 g-3">
                <!-- Cast Member 1 -->
                <div class="col">
                    <div class="cast-card">
                        <img src="https://via.placeholder.com/150x200/667eea/ffffff?text=Christian+Bale" 
                             class="cast-avatar" alt="Christian Bale">
                        <div class="mt-2">
                            <h6 class="mb-0">Christian Bale</h6>
                            <small class="text-white">Bruce Wayne</small>
                        </div>
                    </div>
                </div>

                <!-- Cast Member 2 -->
                <div class="col">
                    <div class="cast-card">
                        <img src="https://via.placeholder.com/150x200/764ba2/ffffff?text=Heath+Ledger" 
                             class="cast-avatar" alt="Heath Ledger">
                        <div class="mt-2">
                            <h6 class="mb-0">Heath Ledger</h6>
                            <small class="text-white">The Joker</small>
                        </div>
                    </div>
                </div>

                <!-- Cast Member 3 -->
                <div class="col">
                    <div class="cast-card">
                        <img src="https://via.placeholder.com/150x200/4ecdc4/ffffff?text=Aaron+Eckhart" 
                             class="cast-avatar" alt="Aaron Eckhart">
                        <div class="mt-2">
                            <h6 class="mb-0">Aaron Eckhart</h6>
                            <small class="text-white">Harvey Dent</small>
                        </div>
                    </div>
                </div>

                <!-- Cast Member 4 -->
                <div class="col">
                    <div class="cast-card">
                        <img src="https://via.placeholder.com/150x200/ff6b6b/ffffff?text=Michael+Caine" 
                             class="cast-avatar" alt="Michael Caine">
                        <div class="mt-2">
                            <h6 class="mb-0">Michael Caine</h6>
                            <small class="text-white">Alfred</small>
                        </div>
                    </div>
                </div>

                <!-- Cast Member 5 -->
                <div class="col">
                    <div class="cast-card">
                        <img src="https://via.placeholder.com/150x200/f39c12/ffffff?text=Maggie+Gyllenhaal" 
                             class="cast-avatar" alt="Maggie Gyllenhaal">
                        <div class="mt-2">
                            <h6 class="mb-0">Maggie Gyllenhaal</h6>
                            <small class="text-white">Rachel Dawes</small>
                        </div>
                    </div>
                </div>

                <!-- Cast Member 6 -->
                <div class="col">
                    <div class="cast-card">
                        <img src="https://via.placeholder.com/150x200/9b59b6/ffffff?text=Gary+Oldman" 
                             class="cast-avatar" alt="Gary Oldman">
                        <div class="mt-2">
                            <h6 class="mb-0">Gary Oldman</h6>
                            <small class="text-white">Jim Gordon</small>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <hr class="my-5">

        <!-- Trailer Section -->
        <section id="trailer-section" class="mb-5">
            <h3 class="mb-4"><i class="bi bi-play-circle me-2"></i>Official Trailer</h3>
            <div class="trailer-container">
                <iframe 
                    src="https://www.youtube.com/embed/EXeTwQWrcwY" 
                    title="The Dark Knight Trailer" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>
            </div>
        </section>

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
            <div class="row row-cols-2 row-cols-md-4 g-4">
                <!-- Similar Movie 1 -->
                <div class="col">
                    <a href="movie-details.php" class="text-decoration-none">
                        <div class="card media-card bg-dark text-white position-relative">
                            <img src="https://via.placeholder.com/300x450/667eea/ffffff?text=Batman+Begins" 
                                 class="card-img-top" alt="Batman Begins">
                            <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                <i class="bi bi-star-fill"></i> 8.2
                            </span>
                            <div class="card-body">
                                <h5 class="card-title">Batman Begins</h5>
                                <p class="card-text text-white">Action • 2005</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Similar Movie 2 -->
                <div class="col">
                    <a href="movie-details.php" class="text-decoration-none">
                        <div class="card media-card bg-dark text-white position-relative">
                            <img src="https://via.placeholder.com/300x450/764ba2/ffffff?text=Inception" 
                                 class="card-img-top" alt="Inception">
                            <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                <i class="bi bi-star-fill"></i> 8.8
                            </span>
                            <div class="card-body">
                                <h5 class="card-title">Inception</h5>
                                <p class="card-text text-white">Sci-Fi • 2010</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Similar Movie 3 -->
                <div class="col">
                    <a href="movie-details.php" class="text-decoration-none">
                        <div class="card media-card bg-dark text-white position-relative">
                            <img src="https://via.placeholder.com/300x450/4ecdc4/ffffff?text=The+Prestige" 
                                 class="card-img-top" alt="The Prestige">
                            <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                <i class="bi bi-star-fill"></i> 8.5
                            </span>
                            <div class="card-body">
                                <h5 class="card-title">The Prestige</h5>
                                <p class="card-text text-white">Mystery • 2006</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Similar Movie 4 -->
                <div class="col">
                    <a href="movie-details.php" class="text-decoration-none">
                        <div class="card media-card bg-dark text-white position-relative">
                            <img src="https://via.placeholder.com/300x450/ff6b6b/ffffff?text=Joker" 
                                 class="card-img-top" alt="Joker">
                            <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                <i class="bi bi-star-fill"></i> 8.4
                            </span>
                            <div class="card-body">
                                <h5 class="card-title">Joker</h5>
                                <p class="card-text text-white">Crime • 2019</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </main>


    <?php include 'includes/movie-details-modals.php'?>
    
    <?php include 'includes/footer.php'; ?>

    

