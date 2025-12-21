<!-- PHP connection -->
<?php
require_once 'includes/init.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse all movies on CineGrid - Filter by genre, year, and rating">
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
    <link rel="stylesheet" href="assets/css/listings.css"> 

    <!-- Bootstrap overrides (modals, buttons, navbar, etc.) -->
    <link rel="stylesheet" href="assets/css/bootstrap-overrides.css">
</head>

<body>

    <!-- for PHP -->
    <?php include 'includes/navbar.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="container mt-0 pt-3 mb-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">All Movies</li>
            </ol>
        </nav>

        <div class="row">
            <!-- FILTER SIDEBAR -->
            <div class="col-lg-3">
                <!-- Mobile Filter Toggle -->
                <button class="btn btn-primary w-100 mb-3 d-lg-none" id="filterToggle">
                    <i class="bi bi-funnel me-2"></i>Filters
                </button>

                <div class="filter-sidebar" id="filterSidebar">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filters</h5>
                        <button class="btn btn-sm btn-outline-light d-lg-none" id="closeFilter">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <!-- Genre Filter -->
                    <div class="filter-section">
                        <h6 class="mb-3"><i class="bi bi-tags me-2"></i>Genre</h6>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="action" class="form-check-input">
                            <label for="action">Action</label>
                        </div>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="comedy" class="form-check-input">
                            <label for="comedy">Comedy</label>
                        </div>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="drama" class="form-check-input">
                            <label for="drama">Drama</label>
                        </div>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="horror" class="form-check-input">
                            <label for="horror">Horror</label>
                        </div>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="scifi" class="form-check-input">
                            <label for="scifi">Sci-Fi</label>
                        </div>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="thriller" class="form-check-input">
                            <label for="thriller">Thriller</label>
                        </div>
                    </div>

                    <!-- Year Filter -->
                    <div class="filter-section">
                        <h6 class="mb-3"><i class="bi bi-calendar3 me-2"></i>Release Year</h6>
                        <select class="form-select mb-2" id="yearFrom">
                            <option selected>From Year</option>
                            <option>2024</option>
                            <option>2023</option>
                            <option>2022</option>
                            <option>2021</option>
                            <option>2020</option>
                            <option>2010s</option>
                            <option>2000s</option>
                            <option>1990s</option>
                        </select>
                        <select class="form-select" id="yearTo">
                            <option selected>To Year</option>
                            <option>2024</option>
                            <option>2023</option>
                            <option>2022</option>
                            <option>2021</option>
                            <option>2020</option>
                        </select>
                    </div>

                    <!-- Rating Filter -->
                    <div class="filter-section">
                        <h6 class="mb-3"><i class="bi bi-star me-2"></i>Rating</h6>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="rating9" class="form-check-input">
                            <label for="rating9">
                                <i class="bi bi-star-fill text-warning"></i> 9+ Excellent
                            </label>
                        </div>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="rating8" class="form-check-input">
                            <label for="rating8">
                                <i class="bi bi-star-fill text-warning"></i> 8+ Very Good
                            </label>
                        </div>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="rating7" class="form-check-input">
                            <label for="rating7">
                                <i class="bi bi-star-fill text-warning"></i> 7+ Good
                            </label>
                        </div>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="rating6" class="form-check-input">
                            <label for="rating6">
                                <i class="bi bi-star-fill text-warning"></i> 6+ Average
                            </label>
                        </div>
                    </div>

                    <!-- Apply/Clear Buttons -->
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary flex-fill" id="applyFilters">
                            <i class="bi bi-check2 me-2"></i>Apply
                        </button>
                        <button class="btn btn-outline-light flex-fill" id="clearFilters">
                            <i class="bi bi-x-lg me-2"></i>Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- MOVIE GRID -->
            <div class="col-lg-9">
                <!-- Results Header -->
                <div class="results-header ui-panel">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h2 class="mb-1">All Movies</h2>
                            <p class="mb-0 text-white">
                                <span id="resultCount">248</span> movies found
                            </p>
                        </div>

                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <!-- Sort Dropdown -->
                            <select class="form-select form-select-sm" id="sortBy" style="width: auto;">
                                <option value="popular">Most Popular</option>
                                <option value="rating">Highest Rated</option>
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="az">A-Z</option>
                                <option value="za">Z-A</option>
                            </select>

                            <!-- View Toggle -->
                            <div class="view-toggle btn-group" role="group">
                                <button type="button" class="btn btn-outline-light active" id="gridView">
                                    <i class="bi bi-grid-3x3-gap"></i>
                                </button>
                                <button type="button" class="btn btn-outline-light" id="listView">
                                    <i class="bi bi-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Movie Cards Grid -->
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 movie-grid" id="movieGrid">
                    <!-- Movie Card 1 -->
                    <div class="col">
                        <a href="movie-details.php" class="text-decoration-none">
                            <div class="card media-card bg-dark text-white position-relative">
                                <img src="https://via.placeholder.com/300x450/667eea/ffffff?text=Inception" 
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

                    <!-- Movie Card 2 -->
                    <div class="col">
                        <a href="movie-details.php" class="text-decoration-none">
                            <div class="card media-card bg-dark text-white position-relative">
                                <img src="https://via.placeholder.com/300x450/764ba2/ffffff?text=The+Dark+Knight" 
                                     class="card-img-top" alt="The Dark Knight">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 9.0
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">The Dark Knight</h5>
                                    <p class="card-text text-white">Action • 2008</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Movie Card 3 -->
                    <div class="col">
                        <a href="movie-details.php" class="text-decoration-none">
                            <div class="card media-card bg-dark text-white position-relative">
                                <img src="https://via.placeholder.com/300x450/4ecdc4/ffffff?text=Interstellar" 
                                     class="card-img-top" alt="Interstellar">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 8.6
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">Interstellar</h5>
                                    <p class="card-text text-white">Sci-Fi • 2014</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Movie Card 4 -->
                    <div class="col">
                        <a href="movie-details.php" class="text-decoration-none">
                            <div class="card media-card bg-dark text-white position-relative">
                                <img src="https://via.placeholder.com/300x450/ff6b6b/ffffff?text=Parasite" 
                                     class="card-img-top" alt="Parasite">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 8.5
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">Parasite</h5>
                                    <p class="card-text text-white">Thriller • 2019</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Movie Card 5 -->
                    <div class="col">
                        <a href="movie-details.php" class="text-decoration-none">
                            <div class="card media-card bg-dark text-white position-relative">
                                <img src="https://via.placeholder.com/300x450/f39c12/ffffff?text=The+Matrix" 
                                     class="card-img-top" alt="The Matrix">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 8.7
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">The Matrix</h5>
                                    <p class="card-text text-white">Sci-Fi • 1999</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Movie Card 6 -->
                    <div class="col">
                        <a href="movie-details.php" class="text-decoration-none">
                            <div class="card media-card bg-dark text-white position-relative">
                                <img src="https://via.placeholder.com/300x450/9b59b6/ffffff?text=Pulp+Fiction" 
                                     class="card-img-top" alt="Pulp Fiction">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 8.9
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">Pulp Fiction</h5>
                                    <p class="card-text text-white">Crime • 1994</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Movie Card 7 -->
                    <div class="col">
                        <a href="movie-details.php" class="text-decoration-none">
                            <div class="card media-card bg-dark text-white position-relative">
                                <img src="https://via.placeholder.com/300x450/e74c3c/ffffff?text=Fight+Club" 
                                     class="card-img-top" alt="Fight Club">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 8.8
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">Fight Club</h5>
                                    <p class="card-text text-wthie">Drama • 1999</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Movie Card 8 -->
                    <div class="col">
                        <a href="movie-details.php" class="text-decoration-none">
                            <div class="card media-card bg-dark text-white position-relative">
                                <img src="https://via.placeholder.com/300x450/16a085/ffffff?text=Forrest+Gump" 
                                     class="card-img-top" alt="Forrest Gump">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 8.8
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">Forrest Gump</h5>
                                    <p class="card-text text-white">Drama • 1994</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Continue with more movie cards... -->
                </div>

                <!-- Pagination -->
                <nav aria-label="Movie pagination" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">4</a></li>
                        <li class="page-item"><a class="page-link" href="#">5</a></li>
                        <li class="page-item">
                            <span class="page-link">...</span>
                        </li>
                        <li class="page-item"><a class="page-link" href="#">21</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </main>

    <!-- Filter overlay for mobile -->
    <div class="filter-overlay" id="filterOverlay"></div>
    
    <?php include 'includes/footer.php'; ?>