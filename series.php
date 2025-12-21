<!-- PHP connection -->
<?php
require_once 'includes/init.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse all TV series on CineGrid - Filter by genre, year, and rating">
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
                <li class="breadcrumb-item"><a href="home.html" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page" >All Series</li>
            </ol>
        </nav>

        <div class="row">
            <!-- FILTER SIDEBAR -->
            <div class="col-lg-3">
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
                        <div class="filter-option hover-lift ">
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
                            <input type="checkbox" id="crime" class="form-check-input">
                            <label for="crime">Crime</label>
                        </div>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="scifi" class="form-check-input">
                            <label for="scifi">Sci-Fi</label>
                        </div>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="fantasy" class="form-check-input">
                            <label for="fantasy">Fantasy</label>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="filter-section">
                        <h6 class="mb-3"><i class="bi bi-broadcast me-2"></i>Status</h6>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="ongoing" class="form-check-input">
                            <label for="ongoing">
                                <i class="bi bi-play-circle text-success"></i> Ongoing
                            </label>
                        </div>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="completed" class="form-check-input">
                            <label for="completed">
                                <i class="bi bi-check-circle text-info"></i> Completed
                            </label>
                        </div>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="upcoming" class="form-check-input">
                            <label for="upcoming">
                                <i class="bi bi-clock-history text-warning"></i> Upcoming
                            </label>
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

                    <!-- Seasons Filter -->
                    <div class="filter-section">
                        <h6 class="mb-3"><i class="bi bi-collection-play me-2"></i>Seasons</h6>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="season1" class="form-check-input">
                            <label for="season1">1 Season</label>
                        </div>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="seasons2-3" class="form-check-input">
                            <label for="seasons2-3">2-3 Seasons</label>
                        </div>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="seasons4-6" class="form-check-input">
                            <label for="seasons4-6">4-6 Seasons</label>
                        </div>
                        <div class="filter-option hover-lift">
                            <input type="checkbox" id="seasons7plus" class="form-check-input">
                            <label for="seasons7plus">7+ Seasons</label>
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

            <!-- SERIES GRID -->
            <div class="col-lg-9">
                <!-- Results Header -->
                <div class="results-header ui-panel">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <h2 class="mb-1">All Series</h2>
                            <p class="mb-0 text-white">
                                <span id="resultCount">186</span> series found
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

                <!-- Series Cards Grid -->
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4" id="seriesGrid">
                    <!-- Series Card 1 -->
                    <div class="col">
                        <a href="series-details.html" class="text-decoration-none">
                            <div class="card media-card bg-dark text-white position-relative">
                                <span class="series-badge">
                                    <i class="bi bi-tv"></i> TV
                                </span>
                                <img src="https://via.placeholder.com/300x450/667eea/ffffff?text=Breaking+Bad" 
                                     class="card-img-top" alt="Breaking Bad">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 9.5
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">Breaking Bad</h5>
                                    <p class="card-text text-white mb-1">Crime • 2008-2013</p>
                                    <p class="seasons-info">
                                        <i class="bi bi-collection-play me-1"></i>5 Seasons
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Series Card 2 -->
                    <div class="col">
                        <a href="series-details.html" class="text-decoration-none">
                            <div class="card media-card bg-dark text-white position-relative">
                                <span class="series-badge">
                                    <i class="bi bi-tv"></i> TV
                                </span>
                                <img src="https://via.placeholder.com/300x450/764ba2/ffffff?text=Game+of+Thrones" 
                                     class="card-img-top" alt="Game of Thrones">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 9.2
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">Game of Thrones</h5>
                                    <p class="card-text text-white mb-1">Fantasy • 2011-2019</p>
                                    <p class="seasons-info">
                                        <i class="bi bi-collection-play me-1"></i>8 Seasons
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Series Card 3 -->
                    <div class="col">
                        <a href="series-details.html" class="text-decoration-none">
                            <div class="card media-card bg-dark text-white position-relative">
                                <span class="series-badge">
                                    <i class="bi bi-tv"></i> TV
                                </span>
                                <img src="https://via.placeholder.com/300x450/4ecdc4/ffffff?text=Stranger+Things" 
                                     class="card-img-top" alt="Stranger Things">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 8.7
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">Stranger Things</h5>
                                    <p class="card-text text-white mb-1">Sci-Fi • 2016-Present</p>
                                    <p class="seasons-info">
                                        <i class="bi bi-collection-play me-1"></i>4 Seasons
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Series Card 4 -->
                    <div class="col">
                        <a href="series-details.html" class="text-decoration-none">
                            <div class="card media-card bg-dark text-white position-relative">
                                <span class="series-badge">
                                    <i class="bi bi-tv"></i> TV
                                </span>
                                <img src="https://via.placeholder.com/300x450/ff6b6b/ffffff?text=The+Crown" 
                                     class="card-img-top" alt="The Crown">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 8.6
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">The Crown</h5>
                                    <p class="card-text text-white mb-1">Drama • 2016-2023</p>
                                    <p class="seasons-info">
                                        <i class="bi bi-collection-play me-1"></i>6 Seasons
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Series Card 5 -->
                    <div class="col">
                        <a href="series-details.html" class="text-decoration-none">
                            <div class="card media-card bg-dark text-white position-relative">
                                <span class="series-badge">
                                    <i class="bi bi-tv"></i> TV
                                </span>
                                <img src="https://via.placeholder.com/300x450/f39c12/ffffff?text=The+Wire" 
                                     class="card-img-top" alt="The Wire">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 9.3
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">The Wire</h5>
                                    <p class="card-text text-white mb-1">Crime • 2002-2008</p>
                                    <p class="seasons-info">
                                        <i class="bi bi-collection-play me-1"></i>5 Seasons
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Series Card 6 -->
                    <div class="col">
                        <a href="series-details.html" class="text-decoration-none">
                            <div class="card media-card bg-dark text-white position-relative">
                                <span class="series-badge">
                                    <i class="bi bi-tv"></i> TV
                                </span>
                                <img src="https://via.placeholder.com/300x450/9b59b6/ffffff?text=The+Sopranos" 
                                     class="card-img-top" alt="The Sopranos">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 9.2
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">The Sopranos</h5>
                                    <p class="card-text text-white mb-1">Crime • 1999-2007</p>
                                    <p class="seasons-info">
                                        <i class="bi bi-collection-play me-1"></i>6 Seasons
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Series Card 7 -->
                    <div class="col">
                        <a href="series-details.html" class="text-decoration-none">
                            <div class="card media-card bg-dark text-white position-relative">
                                <span class="series-badge">
                                    <i class="bi bi-tv"></i> TV
                                </span>
                                <img src="https://via.placeholder.com/300x450/e74c3c/ffffff?text=The+Last+of+Us" 
                                     class="card-img-top" alt="The Last of Us">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 8.8
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">The Last of Us</h5>
                                    <p class="card-text text-white mb-1">Drama • 2023-Present</p>
                                    <p class="seasons-info">
                                        <i class="bi bi-collection-play me-1"></i>2 Seasons
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Series Card 8 -->
                    <div class="col">
                        <a href="series-details.html" class="text-decoration-none">
                            <div class="card media-card bg-dark text-white position-relative">
                                <span class="series-badge">
                                    <i class="bi bi-tv"></i> TV
                                </span>
                                <img src="https://via.placeholder.com/300x450/16a085/ffffff?text=Friends" 
                                     class="card-img-top" alt="Friends">
                                <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                    <i class="bi bi-star-fill"></i> 8.9
                                </span>
                                <div class="card-body">
                                    <h5 class="card-title">Friends</h5>
                                    <p class="card-text text-white mb-1">Comedy • 1994-2004</p>
                                    <p class="seasons-info">
                                        <i class="bi bi-collection-play me-1"></i>10 Seasons
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Pagination -->
                <nav aria-label="Series pagination" class="mt-5">
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
                        <li class="page-item"><a class="page-link" href="#">16</a></li>
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

    <!-- Filter Overlay for Mobile -->
    <div class="filter-overlay" id="filterOverlay"></div>

    <?php include 'includes/footer.php'; ?>
