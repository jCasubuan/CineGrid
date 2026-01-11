<!-- PHP connection -->
<?php
require_once 'includes/init.php';
global $Conn;

function formatDuration($mins) {
    if (!$mins) return "N/A";
    $hours = floor($mins / 60);
    $minutes = $mins % 60;
    if ($hours > 0) {
        return $hours . "h " . ($minutes > 0 ? $minutes . "m" : "");
    }
    return $minutes . "m";
}

// --- PAGINATION LOGIC ---
$limit = 8;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$count_query = "SELECT COUNT(*) as total FROM movies";
$count_result = mysqli_query($Conn, $count_query);
$total_movies = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_movies / $limit);

$query = "SELECT m.*, GROUP_CONCAT(g.name SEPARATOR ' • ') AS genre_list 
          FROM movies m
          LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
          LEFT JOIN genres g ON mg.genre_id = g.genre_id
          GROUP BY m.movie_id
          ORDER BY m.movie_id DESC
          LIMIT $limit OFFSET $offset";

$result = mysqli_query($Conn, $query);

    if (!$result) {
        die("Query Error: " . mysqli_error($Conn));
    }
$count = mysqli_num_rows($result);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse all movies on CineGrid - Filter by genre, year, and rating">
    <title><?php echo ucfirst($current_page); ?> | CineGrid</title> 

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
    <!-- Banner for admin browsing pagaes -->
    <?php include 'includes/admin-banner.php'; ?>

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
                                <span id="resultCount"><?php echo number_format($total_movies); ?></span> 
                                <?php echo ($total_movies === 1) ? 'movie' : 'movies'; ?> found
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
                    <?php if ($count > 0): ?>
                        <?php while($movie = mysqli_fetch_assoc($result)): ?>
                            <div class="col">
                                <a href="movie-details.php?id=<?php echo $movie['movie_id']; ?>" class="text-decoration-none">
                                    <div class="card media-card bg-dark text-white position-relative">
                                        <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2" style="z-index: 5;">
                                            <i class="bi bi-star-fill"></i> <?php echo number_format($movie['rating'], 1); ?>
                                        </span>
                                        
                                        <img src="<?php echo htmlspecialchars($movie['poster_path']); ?>" 
                                            class="card-img-top" 
                                            alt="<?php echo htmlspecialchars($movie['title']); ?>">
                                        
                                        <div class="card-body">
                                            <h5 class="card-title" title="<?php echo htmlspecialchars($movie['title']); ?>">
                                                <?php echo htmlspecialchars($movie['title']); ?>
                                            </h5>

                                            <div class="movie-stats">
                                                <span><?php echo $movie['release_year']; ?></span>
                                                <?php if (!empty($movie['duration'])): ?>
                                                    <span class="mx-2">•</span>
                                                    <span><i class="bi bi-clock me-1"></i><?= formatDuration($movie['duration']); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="movie-genres-list text-truncate">
                                                <?php 
                                                    $genres = explode(' • ', $movie['genre_list'] ?? ''); 
                                                    $short_genres = implode(' • ', array_slice($genres, 0, 3));
                                                    echo htmlspecialchars($short_genres ?: 'Movie'); 
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <?php endif; ?>
                </div>

                <!-- Pagination -->
                <div class="mt-5 d-flex flex-column align-items-center">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-md mb-2">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link bg-dark border-secondary text-white" href="?page=<?= $page - 1; ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : ''; ?>">
                                    <a class="page-link <?= ($page == $i) ? 'bg-primary border-primary' : 'bg-dark border-secondary text-white'; ?>" 
                                    href="?page=<?= $i; ?>"><?= $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link bg-dark border-secondary text-white" href="?page=<?= $page + 1; ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <small class="text-white-50">
                        Showing <?= ($total_movies > 0) ? ($offset + 1) : 0; ?> to <?= min($offset + $limit, $total_movies); ?> of <?= $total_movies; ?> movies
                    </small>
                </div>
            </div>
        </div>
    </main>

    <!-- Filter overlay for mobile -->
    <div class="filter-overlay" id="filterOverlay"></div>
    
    <?php include 'includes/footer.php'; ?>