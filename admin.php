<!-- PHP connection -->
<?php
require_once 'includes/init.php';

// validation for unauthorized access of admin.php from the url
if (
    empty($_SESSION['user_id']) ||
    empty($_SESSION['user_role']) ||
    $_SESSION['user_role'] !== 'admin'
) {
    header('Location: index.php?error=unauthorized');
    exit;
}

// Fetch movie count
$movieCount = $Conn->query("SELECT COUNT(*) as total FROM movies")->fetch_assoc()['total'];

// Fetch series count
// $seriesCount = $Conn->query("SELECT COUNT(*) as total FROM series")->fetch_assoc()['total'];

// Fetch User Count
$userCount = $Conn->query("SELECT COUNT(*) as total FROM users WHERE role != 'admin'")->fetch_assoc()['total'];

// Fetch review count
// $reviewCount = $Conn->query("SELECT COUNT(*) as total FROM reviews")->fetch_assoc()['total']; -->

// Define genres array (matching your movie setup)
$genres = ['Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Drama', 'Fantasy', 'Horror', 'Sci-Fi', 'Thriller'];

// Get filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$genre_filter = isset($_GET['genre']) ? trim($_GET['genre']) : '';
$year_filter = isset($_GET['year']) ? trim($_GET['year']) : '';

// Pagination Logic
$limit = 5; 
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Build dynamic WHERE clause for filtering
$where_conditions = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where_conditions[] = "m.title LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}

if (!empty($genre_filter)) {
    $where_conditions[] = "g.name = ?";
    $params[] = $genre_filter;
    $types .= 's';
}

if (!empty($year_filter)) {
    $where_conditions[] = "m.release_year = ?";
    $params[] = $year_filter;
    $types .= 'i';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Count total movies with filters
$count_query = "SELECT COUNT(DISTINCT m.movie_id) as count 
                FROM movies m 
                LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id 
                LEFT JOIN genres g ON mg.genre_id = g.genre_id 
                $where_clause";

if (!empty($params)) {
    $count_stmt = $Conn->prepare($count_query);
    $count_stmt->bind_param($types, ...$params);
    $count_stmt->execute();
    $total_movies = $count_stmt->get_result()->fetch_assoc()['count'];
    $count_stmt->close();
} else {
    $total_movies = $Conn->query($count_query)->fetch_assoc()['count'];
}

$total_pages = ceil($total_movies / $limit);

// Fetch movies with filters
$movies_query = "SELECT m.*, GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') as genre_names 
                 FROM movies m 
                 LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id 
                 LEFT JOIN genres g ON mg.genre_id = g.genre_id 
                 $where_clause
                 GROUP BY m.movie_id 
                 ORDER BY m.movie_id DESC 
                 LIMIT ? OFFSET ?";

if (!empty($params)) {
    $movies_stmt = $Conn->prepare($movies_query);
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    $movies_stmt->bind_param($types, ...$params);
    $movies_stmt->execute();
    $movies = $movies_stmt->get_result();
    $movies_stmt->close();
} else {
    $movies_stmt = $Conn->prepare($movies_query);
    $movies_stmt->bind_param('ii', $limit, $offset);
    $movies_stmt->execute();
    $movies = $movies_stmt->get_result();
    $movies_stmt->close();
}

// Get unique years from database (for year filter dropdown)
$years_query = "SELECT DISTINCT release_year FROM movies WHERE release_year IS NOT NULL ORDER BY release_year DESC";
$years_result = $Conn->query($years_query);
$available_years = [];
while ($row = $years_result->fetch_assoc()) {
    $available_years[] = $row['release_year'];
}

// Get recent activity from activity log
$recentActivity = $Conn->query("
    SELECT action_type, 
            item_type, 
            item_name, 
            created_at, 
            user_id
    FROM activity_log 
    ORDER BY created_at DESC 
    LIMIT $limit OFFSET $offset
");

// FIX: Use different variable names for the Activity Log pagination
$log_res = $Conn->query("SELECT COUNT(*) as count FROM activity_log");
$total_logs = $log_res->fetch_assoc()['count']; 
$total_log_pages = ceil($total_logs / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CineGrid Admin Dashboard - Manage movies, series, and users">
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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/admin-dashboard.css">

</head>

<body>
    <!-- Sidebar -->
    <?php include 'includes/admin-sidebar.php'; ?>

    <!-- Mobile Toggle -->
    <button class="mobile-toggle" id="mobileToggle">
        <i class="bi bi-list"></i>
    </button>

    <!-- Main Content -->
    <main class="main-content">

        <!-- Top Bar -->
        <div class="top-bar">
            <div>
                <h4 class="mb-0">Welcome, Admin</h4>
                <small class="text-white">Manage your CineGrid content</small>
            </div>
            <div class="d-flex gap-3 align-items-center">
                <button class="btn btn-outline-light btn-sm">
                    <i class="bi bi-bell"></i>
                </button>

                <!-- Logout button -->
                <button class="btn btn-outline-danger btn-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#logoutConfirmModal">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </div>
        </div>

        <!-- Dashboard Section -->
        <section id="dashboardSection" class="content-section">
            <h2 class="mb-4">Dashboard Overview</h2>

            <!-- Stats Cards -->
            <!-- Movie Card -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(52, 152, 219, 0.2);">
                            <i class="bi bi-film" style="color: #3498db;"></i>
                        </div>
                        <div class="stat-value"><?php echo $movieCount; ?></div>
                        <div class="stat-label">Total Movies</div>
                    </div>
                </div>
                <!-- Series Card -->
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(46, 204, 113, 0.2);">
                            <i class="bi bi-tv" style="color: #2ecc71;"></i>
                        </div>
                        <div class="stat-value">0</div>
                        <div class="stat-label">Total Series</div>
                    </div>
                </div>
                <!-- Total Users Card -->
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(155, 89, 182, 0.2);">
                            <i class="bi bi-people" style="color: #9b59b6;"></i>
                        </div>
                        <div class="stat-value"><?php echo $userCount; ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
                <!-- Total Review Card -->
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(241, 196, 15, 0.2);">
                            <i class="bi bi-chat-square-text" style="color: #f1c40f;"></i>
                        </div>
                        <div class="stat-value">0</div>
                        <div class="stat-label">Total Reviews</div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="data-table">
                <div class="p-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Item</th>
                            <th>User</th>
                            <th>Date Added</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recentActivity->num_rows > 0): ?>
                            <?php while($row = $recentActivity->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php 
                                        $badgeClass = 'bg-success'; // Default for Added
                                        if ($row['action_type'] === 'Updated') {
                                            $badgeClass = 'bg-primary';
                                        } elseif ($row['action_type'] === 'Deleted') {
                                            $badgeClass = 'bg-danger';
                                        }
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($row['action_type']); ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-white"><?= htmlspecialchars($row['item_name']); ?></div>
                                        <small class="text-white-50"><?= htmlspecialchars($row['item_type']); ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        // Get user name from user_id
                                        if ($row['user_id']) {
                                            $user_stmt = $Conn->prepare("SELECT full_name FROM users WHERE id = ?");
                                            $user_stmt->bind_param('i', $row['user_id']);
                                            $user_stmt->execute();
                                            $user_result = $user_stmt->get_result();
                                            $user = $user_result->fetch_assoc();
                                            echo htmlspecialchars($user['full_name'] ?? 'Unknown User');
                                            $user_stmt->close();
                                        } else {
                                            echo 'System';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            echo date('M d, Y', strtotime($row['created_at'])); 
                                        ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-active">Success</span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-white">No recent activity found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div> 
            <div class="mt-4 d-flex flex-column align-items-center">
            <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-2">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link bg-dark border-secondary text-white" href="?page=<?= $page - 1; ?>">Previous</a>
                        </li>
                        
                        <?php for($i = 1; $i <= $total_log_pages; $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link <?= ($page == $i) ? 'bg-primary border-primary' : 'bg-dark border-secondary text-white'; ?>" href="?page=<?= $i; ?>"><?= $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($page >= $total_log_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link bg-dark border-secondary text-white" href="?page=<?= $page + 1; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <small class="text-white-50">
                    Showing <?= ($offset + 1); ?> to <?= min($offset + $limit, $total_logs); ?> of <?= $total_logs; ?> entries
                </small>
            </div>
        </section>

        <!-- Movies Section -->
        <section id="moviesSection" class="content-section" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Manage Movies</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMovieModal">
                    <i class="bi bi-plus-lg me-2"></i>Add New Movie
                </button>
            </div>

            <!-- Search and Filter -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" action="" id="searchForm">
                        <input type="hidden" name="section" value="movies">
                        <input type="hidden" name="page" value="1">
                        <input type="hidden" name="genre" value="<?= htmlspecialchars($genre_filter); ?>">
                        <input type="hidden" name="year" value="<?= htmlspecialchars($year_filter); ?>">
                        <div class="search-bar">
                            <input type="text" 
                                name="search" 
                                id="movieSearch" 
                                placeholder="Search movies..." 
                                class="form-control"
                                value="<?= htmlspecialchars($search); ?>"
                                autocomplete="off">
                            <i class="bi bi-search"></i>
                        </div>
                    </form>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="movieGenreFilter">
                        <option value="">All Genres</option>
                        <?php foreach($genres as $genre): ?>
                            <option value="<?= htmlspecialchars($genre); ?>" <?= ($genre_filter === $genre) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($genre); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="movieYearFilter">
                        <option value="">All Years</option>
                        <?php foreach($available_years as $year): ?>
                            <option value="<?= $year; ?>" <?= ($year_filter == $year) ? 'selected' : ''; ?>>
                                <?= $year; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Add Clear Filters Button if any filter is active -->
            <?php if (!empty($search) || !empty($genre_filter) || !empty($year_filter)): ?>
            <div class="mb-3">
                <a href="?section=movies&page=1" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>Clear Filters
                </a>
                <span class="text-white-50 ms-2">
                    <?php if (!empty($search)): ?>
                        Search: "<?= htmlspecialchars($search); ?>" 
                    <?php endif; ?>
                    <?php if (!empty($genre_filter)): ?>
                        | Genre: <?= htmlspecialchars($genre_filter); ?>
                    <?php endif; ?>
                    <?php if (!empty($year_filter)): ?>
                        | Year: <?= htmlspecialchars($year_filter); ?>
                    <?php endif; ?>
                </span>
            </div>
            <?php endif; ?>

            <!-- Movies Table -->
            <div class="data-table">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Poster</th>
                            <th>Title</th>
                            <th>Genre</th>
                            <th>Year</th>
                            <th>Ratings</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="moviesTableBody">
                        <?php if ($movies && $movies->num_rows > 0): ?>
                            <?php while($row = $movies->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= str_pad($row['movie_id'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td>
                                        <img src="<?= !empty($row['poster_path']) ? $row['poster_path'] : 'assets/img/no-poster.jpg'; ?>" 
                                            class="rounded border border-secondary" 
                                            alt="Poster" 
                                            style="width:45px; height:65px; object-fit:cover;">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-white"><?= htmlspecialchars($row['title']); ?></div>
                                        <small class="text-info">Standard Feature</small> </td>
                                    </td>
                                    <td><span class="small text-white-50"><?= htmlspecialchars($row['genre_names'] ?: 'Uncategorized'); ?></span></td>
                                    <td><?= $row['release_year']; ?></td>                              
                                    <td>
                                        <span class="badge bg-dark text-warning border border-warning">
                                            <i class="bi bi-star-fill me-1"></i><?= number_format($row['rating'], 1); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm <?= $row['is_featured'] ? 'btn-warning' : 'btn-outline-secondary' ?>"
                                                onclick="toggleFeatured(<?= $row['movie_id']; ?>)" 
                                                title="Toggle Hero Banner">
                                            <i class="bi <?= $row['is_featured'] ? 'bi-lightning-fill' : 'bi-lightning' ?>"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="movie-details.php?id=<?= $row['movie_id']; ?>" class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editMovieModal" onclick="loadEditData(<?= $row['movie_id']; ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteMovie(<?= $row['movie_id']; ?>, '<?= addslashes($row['title']); ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-white-50">
                                    <i class="bi bi-folder2-open d-block fs-2 mb-2"></i>
                                    No movies found in the database.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="data-table border-0 shadow-sm">
                <table class="table table-dark table-hover mb-0 align-middle">
                    </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4 d-flex flex-column align-items-center">
                <?php
                // Build filter query string for pagination
                $filter_params = '';
                if (!empty($search)) {
                    $filter_params .= '&search=' . urlencode($search);
                }
                if (!empty($genre_filter)) {
                    $filter_params .= '&genre=' . urlencode($genre_filter);
                }
                if (!empty($year_filter)) {
                    $filter_params .= '&year=' . urlencode($year_filter);
                }
                ?>
                
                <nav aria-label="Movie navigation">
                    <ul class="pagination pagination-sm mb-2">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link border-secondary text-white" 
                            href="?page=<?= $page - 1; ?>&section=movies<?= $filter_params; ?>" 
                            style="background-color: #1a1d20;">Previous</a>
                        </li>

                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link border-secondary <?= ($page == $i) ? '' : 'text-white'; ?>" 
                                href="?page=<?= $i; ?>&section=movies<?= $filter_params; ?>"
                                style="<?= ($page == $i) ? 'background-color: #0d6efd; border-color: #0d6efd;' : 'background-color: #1a1d20;'; ?>">
                                <?= $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link border-secondary text-white" 
                            href="?page=<?= $page + 1; ?>&section=movies<?= $filter_params; ?>" 
                            style="background-color: #1a1d20;">Next</a>
                        </li>
                    </ul>
                </nav>
                
                <div class="text-white-50 small">
                    Showing <?= ($total_movies > 0) ? ($offset + 1) : 0; ?> to <?= min($offset + $limit, $total_movies); ?> of <?= $total_movies; ?> movies
                </div>
            </div>
        </section>

        <!-- Series Section -->
        <section id="seriesSection" class="content-section" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Manage Series</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSeriesModal">
                    <i class="bi bi-plus-lg me-2"></i>Add New Series
                </button>
            </div>

            <!-- Search Bar -->
            <div class="search-bar mb-4">
                <input type="text" id="seriesSearch" placeholder="Search series..." class="form-control">
                <i class="bi bi-search"></i>
            </div>

            <!-- Series Table -->
            <div class="data-table">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Poster</th>
                            <th>Title</th>
                            <th>Genre</th>
                            <th>Seasons</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>001</td>
                            <td><img src="https://via.placeholder.com/50x75/667eea/ffffff?text=BB" class="thumbnail" alt="Series"></td>
                            <td>Breaking Bad</td>
                            <td>Crime</td>
                            <td>5</td>
                            <td><i class="bi bi-star-fill text-warning"></i> 9.5</td>
                            <td><span class="status-badge status-active">Active</span></td>
                            <td>
                                <button class="action-btn btn-view" onclick="viewSeries(1)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="action-btn btn-edit" data-bs-toggle="modal" data-bs-target="#editSeriesModal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="action-btn btn-delete" onclick="deleteSeries(1)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>002</td>
                            <td><img src="https://via.placeholder.com/50x75/764ba2/ffffff?text=GOT" class="thumbnail" alt="Series"></td>
                            <td>Game of Thrones</td>
                            <td>Fantasy</td>
                            <td>8</td>
                            <td><i class="bi bi-star-fill text-warning"></i> 9.2</td>
                            <td><span class="status-badge status-active">Active</span></td>
                            <td>
                                <button class="action-btn btn-view" onclick="viewSeries(2)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="action-btn btn-edit" data-bs-toggle="modal" data-bs-target="#editSeriesModal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="action-btn btn-delete" onclick="deleteSeries(2)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Users Section -->
        <section id="usersSection" class="content-section" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Manage Users</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-lg me-2"></i>Add New User
                </button>
            </div>

            <!-- Search Bar -->
             <div class="search-bar mb-4">
                <input type="text" id="userSearch" placeholder="Search users..." class="form-control">
                <i class="bi bi-search"></i>
            </div>

            <!-- Users Table -->
            <div class="data-table">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>001</td>
                            <td>John Doe</td>
                            <td>john@example.com</td>
                            <td><span class="badge bg-primary">User</span></td>
                            <td>Jan 15, 2024</td>
                            <td><span class="status-badge status-active">Active</span></td>
                            <td>
                                <button class="action-btn btn-view" onclick="viewUser(1)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="action-btn btn-edit" data-bs-toggle="modal" data-bs-target="#editUserModal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="action-btn btn-delete" onclick="deleteUser(1)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>002</td>
                            <td>Jane Smith</td>
                            <td>jane@example.com</td>
                            <td><span class="badge bg-success">Admin</span></td>
                            <td>Feb 20, 2024</td>
                            <td><span class="status-badge status-active">Active</span></td>
                            <td>
                                <button class="action-btn btn-view" onclick="viewUser(2)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="action-btn btn-edit" data-bs-toggle="modal" data-bs-target="#editUserModal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="action-btn btn-delete" onclick="deleteUser(2)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>003</td>
                            <td>Mike Wilson</td>
                            <td>mike@example.com</td>
                            <td><span class="badge bg-primary">User</span></td>
                            <td>Mar 10, 2024</td>
                            <td><span class="status-badge status-inactive">Inactive</span></td>
                            <td>
                                <button class="action-btn btn-view" onclick="viewUser(3)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="action-btn btn-edit" data-bs-toggle="modal" data-bs-target="#editUserModal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="action-btn btn-delete" onclick="deleteUser(3)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Reviews Section -->
         <section id="reviewsSection" class="content-section" style="display: none;">
            <h2 class="mb-4">Manage Reviews</h2>

            <!-- Search Bar -->
           <div class="search-bar mb-4">
                <input type="text" id="reviewSearch" placeholder="Search reviews..." class="form-control">
                <i class="bi bi-search"></i>
            </div>

            <!-- Reviews Table -->
            <div class="data-table">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Content</th>
                            <th>Rating</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>001</td>
                            <td>john_doe_92</td>
                            <td>The Dark Knight</td>
                            <td><i class="bi bi-star-fill text-warning"></i> 10/10</td>
                            <td>Dec 1, 2024</td>
                            <td><span class="status-badge status-active">Approved</span></td>
                            <td>
                                <button class="action-btn btn-view" onclick="viewReview(1)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="action-btn btn-delete" onclick="deleteReview(1)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>002</td>
                            <td>movie_buff</td>
                            <td>Breaking Bad</td>
                            <td><i class="bi bi-star-fill text-warning"></i> 9/10</td>
                            <td>Dec 5, 2024</td>
                            <td><span class="status-badge status-active">Approved</span></td>
                            <td>
                                <button class="action-btn btn-view" onclick="viewReview(2)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="action-btn btn-delete" onclick="deleteReview(2)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Analytics Section -->
        <section id="analyticsSection" class="content-section" style="display: none;">
            <h2 class="mb-4">Analytics</h2>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="data-table p-4">
                        <h5 class="mb-3">Most Viewed Content</h5>
                        <ol>
                            <li class="mb-2">The Dark Knight - 2.5M views</li>
                            <li class="mb-2">Breaking Bad - 5.2M views</li>
                            <li class="mb-2">Inception - 1.8M views</li>
                            <li class="mb-2">Game of Thrones - 4.1M views</li>
                            <li class="mb-2">Stranger Things - 3.3M views</li>
                        </ol>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="data-table p-4">
                        <h5 class="mb-3">Top Rated Content</h5>
                        <ol>
                            <li class="mb-2">Breaking Bad - 9.5/10</li>
                            <li class="mb-2">The Wire - 9.3/10</li>
                            <li class="mb-2">Game of Thrones - 9.2/10</li>
                            <li class="mb-2">The Dark Knight - 9.0/10</li>
                            <li class="mb-2">Inception - 8.8/10</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Settings Section -->
        <section id="settingsSection" class="content-section" style="display: none;">
            <h2 class="mb-4">Settings</h2>
            <div class="data-table p-4">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Site Name</label>
                        <input type="text" class="form-control" value="CineGrid">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Email</label>
                        <input type="email" class="form-control" value="admin@cinegrid.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Items Per Page</label>
                        <select class="form-select">
                            <option>10</option>
                            <option selected>20</option>
                            <option>50</option>
                            <option>100</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="maintenanceMode">
                            <label class="form-check-label" for="maintenanceMode">
                                Maintenance Mode
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </section>
    </main>
    
    <?php 
        include 'includes/admin-modals/movie-modals.php'; 
        include 'includes/admin-modals/series-modals.php';
        include 'includes/admin-modals/user-modals.php';
    ?>

    <?php include 'includes/footer.php'; ?>