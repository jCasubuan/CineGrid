<!-- PHP connection -->
<?php
require_once 'includes/init.php';

// 1. Get Series ID from URL
$series_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($series_id <= 0) {
    header('Location: index.php');
    exit;
}

// 2. Fetch Series Basic Info + Trailer
$stmt = $Conn->prepare("
    SELECT s.*, st.youtube_url as trailer_url
    FROM series s
    LEFT JOIN series_trailers st ON s.series_id = st.series_id
    WHERE s.series_id = ?
");
$stmt->bind_param('i', $series_id);
$stmt->execute();
$series = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$series) {
    header('Location: index.php?error=not_found');
    exit;
}

// 3. Update view count
$Conn->query("UPDATE series SET views_count = views_count + 1 WHERE series_id = $series_id");

// 4. Fetch Genres
$genres = [];
$stmt = $Conn->prepare("
    SELECT g.name 
    FROM series_genres sg 
    JOIN genres g ON sg.genre_id = g.genre_id 
    WHERE sg.series_id = ?
");
$stmt->bind_param('i', $series_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $genres[] = $row['name'];
}
$stmt->close();

// 5. Fetch Directors
$directors = [];
$stmt = $Conn->prepare("
    SELECT d.name 
    FROM series_directors sd 
    JOIN directors d ON sd.director_id = d.director_id 
    WHERE sd.series_id = ?
");
$stmt->bind_param('i', $series_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $directors[] = $row['name'];
}
$stmt->close();

// 6. Fetch Writers
$writers = [];
$stmt = $Conn->prepare("
    SELECT w.name 
    FROM series_writers sw 
    JOIN writers w ON sw.writer_id = w.writer_id 
    WHERE sw.series_id = ?
");
$stmt->bind_param('i', $series_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $writers[] = $row['name'];
}
$stmt->close();

// 7. Fetch Cast
$cast = [];
$stmt = $Conn->prepare("
    SELECT a.name as actor, a.image_path, sc.character_name, sc.cast_order
    FROM series_cast sc 
    JOIN actors a ON sc.actor_id = a.actor_id 
    WHERE sc.series_id = ? 
    ORDER BY sc.cast_order ASC 
    LIMIT 6
");
$stmt->bind_param('i', $series_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $cast[] = $row;
}
$stmt->close();

// 8. Fetch Seasons with Episodes
$seasons = [];
$stmt = $Conn->prepare("
    SELECT * 
    FROM seasons 
    WHERE series_id = ? 
    ORDER BY season_number ASC
");
$stmt->bind_param('i', $series_id);
$stmt->execute();
$result = $stmt->get_result();

while ($seasonRow = $result->fetch_assoc()) {
    $season_id = $seasonRow['season_id'];
    
    // Get episodes for this season
    $epStmt = $Conn->prepare("
        SELECT * 
        FROM episodes 
        WHERE season_id = ? 
        ORDER BY episode_number ASC
    ");
    $epStmt->bind_param('i', $season_id);
    $epStmt->execute();
    $epResult = $epStmt->get_result();
    
    $episodes = [];
    while ($epRow = $epResult->fetch_assoc()) {
        $episodes[] = $epRow;
    }
    $epStmt->close();
    
    $seasonRow['episodes'] = $episodes;
    $seasons[] = $seasonRow;
}
$stmt->close();

// 9. Calculate total episodes
$total_episodes = 0;
foreach ($seasons as $season) {
    $total_episodes += count($season['episodes']);
}

// 10. Get average episode duration (from first episode)
$avg_duration = 0;
if (!empty($seasons) && !empty($seasons[0]['episodes'])) {
    $durations = array_column($seasons[0]['episodes'], 'duration');
    $avg_duration = !empty($durations) ? (int)(array_sum($durations) / count($durations)) : 0;
}

// 11. Calculate year range
$year_range = $series['release_year'];
if ($series['status'] === 'Ended' || $series['status'] === 'Cancelled') {
    $last_season = end($seasons);
    if ($last_season && $last_season['release_year']) {
        $year_range .= '-' . $last_season['release_year'];
    }
}

// 12. Fetch Similar Series (same genres, exclude current)
$similar_series = [];
if (!empty($genres)) {
    $genre_list = "'" . implode("','", array_map([$Conn, 'real_escape_string'], $genres)) . "'";
    $similar_query = "
        SELECT DISTINCT s.*, GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') as genre_names
        FROM series s
        JOIN series_genres sg ON s.series_id = sg.series_id
        JOIN genres g ON sg.genre_id = g.genre_id
        WHERE g.name IN ($genre_list)
        AND s.series_id != $series_id
        GROUP BY s.series_id
        ORDER BY s.rating DESC
        LIMIT 4
    ";
    $similar_result = $Conn->query($similar_query);
    while ($row = $similar_result->fetch_assoc()) {
        $similar_series[] = $row;
    }
}

// 13. Format YouTube URL for embed
$youtube_embed = '';
if (!empty($series['trailer_url'])) {
    // Extract video ID from various YouTube URL formats
    $video_id = '';
    if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $series['trailer_url'], $id)) {
        $video_id = $id[1];
    } else if (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $series['trailer_url'], $id)) {
        $video_id = $id[1];
    } else if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $series['trailer_url'], $id)) {
        $video_id = $id[1];
    }
    
    if ($video_id) {
        $youtube_embed = "https://www.youtube.com/embed/$video_id";
    }
}

// 14. Calculate rating percentage for circular progress
$rating_percentage = ($series['rating'] / 10) * 100;

$seriesId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$seriesData = null;

$tmdbType = 'tv';

$fullCastUrl = !empty($series['tmdb_id']) 
    ? "https://www.themoviedb.org/" . $tmdbType . "/" . $series['tmdb_id'] . "/cast"
    : "#";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars(substr($series['overview'], 0, 155)); ?>">
    <title><?= htmlspecialchars($series['title']); ?> (<?= $year_range; ?>) | CineGrid</title>

    <!-- Site Icon / Logo -->
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" type="image/svg+xml" href="assets/img/logo.svg">

    <!-- BootStrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- Boostrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CineGrid base styles -->
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/details.css">

    <!-- Bootstrap overrides (modals, buttons, navbar, etc.) -->
    <link rel="stylesheet" href="assets/css/bootstrap-overrides.css">
</head>

<body data-type="series">
    <!-- Banner for admin browsing pages -->
    <?php include 'includes/admin-banner.php'; ?>

    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>

    <!-- HERO BANNER -->
    <section class="details-hero"
        style="background-image: url('<?= htmlspecialchars($series['backdrop_path']); ?>');">
        <div class="details-hero-overlay"></div>
    </section>

    <!-- MAIN CONTENT -->
    <main class="container">
        <div class="row">
            <!-- Series Poster (Floating) -->
            <div class="col-12 col-md-3">
                <img src="<?= htmlspecialchars($series['poster_path']); ?>"
                    class="img-fluid floating-poster" 
                    alt="<?= htmlspecialchars($series['title']); ?>"
                    onerror="this.src='assets/img/no-poster.jpg'">
            </div>

            <!-- Series Information -->
            <div class="col-12 col-md-9 mt-4 mt-md-0">
                <!-- Title & Meta -->
                <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                    <h1 class="display-5 fw-bold mb-0"><?= htmlspecialchars($series['title']); ?></h1>
                    <span class="badge bg-success fs-6"><i class="bi bi-tv"></i> TV Series</span>
                    <span class="badge bg-warning text-dark fs-6"><?= htmlspecialchars($series['content_rating']); ?></span>
                    <?php if ($series['status'] !== 'Ongoing'): ?>
                        <span class="badge bg-danger fs-6"><?= htmlspecialchars($series['status']); ?></span>
                    <?php endif; ?>
                </div>

                <p class="text-white mb-4">
                    <i class="bi bi-calendar3 me-1"></i> <?= $year_range; ?>
                    <span class="mx-2">•</span>
                    <i class="bi bi-collection-play me-1"></i> <?= count($seasons); ?> Season<?= count($seasons) != 1 ? 's' : ''; ?>, <?= $total_episodes; ?> Episodes
                    <?php if ($avg_duration > 0): ?>
                        <span class="mx-2">•</span>
                        <i class="bi bi-clock me-1"></i> <?= $avg_duration; ?>min per episode
                    <?php endif; ?>
                </p>

                <!-- Genres -->
                <?php if (!empty($genres)): ?>
                <div class="mb-4">
                    <?php foreach ($genres as $genre): ?>
                        <span class="tag"><i class="bi bi-tag-fill me-1"></i><?= htmlspecialchars($genre); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Rating & Actions -->
                <div class="row g-3 mb-4">
                    <div class="col-auto">
                        <div class="rating-circle" style="background: conic-gradient(#4caf50 0% <?= $rating_percentage; ?>%, #ddd <?= $rating_percentage; ?>% 100%);">
                            <span class="rating-value"><?= number_format($series['rating'], 1); ?></span>
                        </div>
                        <small class="d-block text-center mt-2 text-white">IMDB Rating</small>
                    </div>

                    <div class="col">
                        <div class="d-flex flex-wrap gap-2">
                            <?php if (!empty($youtube_embed)): ?>
                                <a href="#trailer-section" class="btn btn-primary action-btn">
                                    <i class="bi bi-play-fill me-2"></i>Watch Trailer
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($_SESSION['user_id'])): ?>
                                <button class="btn btn-outline-light">
                                    <i class="bi bi-bookmark me-2"></i>Add to Watchlist
                                </button>
                                <button class="btn btn-outline-light">
                                    <i class="bi bi-heart me-2"></i>Favorite
                                </button>
                                <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#ratingModal">
                                    <i class="bi bi-star me-2"></i>Rate
                                </button>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-outline-light">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login to Rate
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <div class="stat-badge">
                        <i class="bi bi-eye me-2"></i>
                        <strong><?= number_format($series['views_count']); ?></strong> <small class="text-white">Views</small>
                    </div>
                    <div class="stat-badge">
                        <i class="bi bi-translate me-2"></i>
                        <strong><?= htmlspecialchars($series['language']); ?></strong> <small class="text-white">Language</small>
                    </div>
                </div>

                <!-- Synopsis -->
                <div class="mb-4">
                    <h4 class="mb-3"><i class="bi bi-file-text me-2"></i>Synopsis</h4>
                    <p class="lead"><?= nl2br(htmlspecialchars($series['overview'])); ?></p>
                </div>

                <!-- Creator & Writers -->
                <div class="row mb-4">
                    <?php if (!empty($directors)): ?>
                    <div class="col-md-6">
                        <h5><i class="bi bi-megaphone me-2"></i>Directors</h5>
                        <p><?= htmlspecialchars(implode(', ', $directors)); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($writers)): ?>
                    <div class="col-md-6">
                        <h5><i class="bi bi-pen me-2"></i>Writers</h5>
                        <p><?= htmlspecialchars(implode(', ', $writers)); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <hr class="custom-hr mt-5 mb-4">

        <!-- Cast Section -->      
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-5"> 
                <h3 class="text-white"><i class="bi bi-people-fill me-2"></i>Main Cast</h3>
                
                <?php if ($fullCastUrl !== "#"): ?>
                    <a href="<?= $fullCastUrl ?>" class="btn btn-outline-light btn-sm" target="_blank" rel="noopener noreferrer">
                        <i class="bi bi-box-arrow-up-right me-1"></i> See Full Cast
                    </a>
                <?php endif; ?>
            </div>

            <div class="row row-cols-2 row-cols-md-6 g-4">
                <?php 
                $displayed = 0;
                foreach ($cast as $member): 
                    $displayed++;
                    
                    // Logic: Use database image if it exists, otherwise use the initials avatar
                    $initialsUrl = "https://ui-avatars.com/api/?name=" . urlencode($member['actor']) . "&background=random&color=fff&size=128";
                    $displayPhoto = !empty($member['image_path']) ? $member['image_path'] : $initialsUrl;
                ?>
                    <div class="col">
                        <div class="cast-card text-center">
                            <div class="position-relative d-inline-block">
                                <img src="<?= htmlspecialchars($displayPhoto) ?>" 
                                    class="cast-avatar rounded-circle mb-2" 
                                    alt="<?= htmlspecialchars($member['actor']); ?>"
                                    style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #333; transition: transform 0.3s ease;"
                                    onerror="this.src='<?= $initialsUrl ?>'">
                            </div>
                            <div class="mt-2 px-1">
                                <h6 class="mb-0 text-white text-truncate" title="<?= htmlspecialchars($member['actor']); ?>">
                                    <?= htmlspecialchars($member['actor']); ?>
                                </h6>
                                <small class="text-white-50 d-block text-truncate" title="<?= htmlspecialchars($member['character'] ?? 'Role TBA'); ?>">
                                    <?= htmlspecialchars($member['character_name'] ?? 'Role TBA'); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <hr class="my-5">


        <!-- Episodes Section -->
        <?php if (!empty($seasons)): ?>
        <section class="mb-5">
            <h3 class="mb-4"><i class="bi bi-collection-play me-2"></i>Episodes</h3>

            <!-- Season Tabs -->
            <ul class="nav nav-tabs season-tabs" id="seasonTabs" role="tablist">
                <?php foreach ($seasons as $index => $season): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $index === 0 ? 'active' : ''; ?>" 
                            id="season<?= $season['season_number']; ?>-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#season<?= $season['season_number']; ?>"
                            type="button" role="tab">
                        Season <?= $season['season_number']; ?>
                    </button>
                </li>
                <?php endforeach; ?>
            </ul>

            <!-- Season Content -->
            <div class="tab-content" id="seasonTabContent">
                <?php foreach ($seasons as $index => $season): ?>
                <div class="tab-pane fade <?= $index === 0 ? 'show active' : ''; ?>" 
                     id="season<?= $season['season_number']; ?>" 
                     role="tabpanel">
                    <div class="mt-4">
                        <p class="text-white mb-4">
                            <i class="bi bi-calendar3 me-2"></i><?= $season['release_year']; ?> • <?= count($season['episodes']); ?> Episodes
                        </p>

                        <?php if (!empty($season['episodes'])): ?>
                            <?php foreach ($season['episodes'] as $episode): ?>
                            <!-- Episode Card -->
                            <div class="episode-card">
                                <div class="row g-3">
                                    <div class="col-auto">
                                        <img src="<?= !empty($episode['still_path']) ? htmlspecialchars($episode['still_path']) : 'assets/img/no-episode.jpg'; ?>"
                                            class="episode-thumbnail" 
                                            alt="Episode <?= $episode['episode_number']; ?>"
                                            onerror="this.src='assets/img/no-episode.jpg'">
                                    </div>
                                    <div class="col">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="episode-number">E<?= $episode['episode_number']; ?></span>
                                                <h5 class="mt-2 mb-1"><?= htmlspecialchars($episode['title']); ?></h5>
                                            </div>
                                        </div>
                                        <p class="text-white mb-2">
                                            <i class="bi bi-clock me-1"></i><?= $episode['duration']; ?>min
                                            <?php if (!empty($episode['release_date'])): ?>
                                                <span class="mx-2">•</span>
                                                <i class="bi bi-calendar3 me-1"></i><?= date('M d, Y', strtotime($episode['release_date'])); ?>
                                            <?php endif; ?>
                                        </p>
                                        <?php if (!empty($episode['overview'])): ?>
                                            <p class="mb-0"><?= htmlspecialchars($episode['overview']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-collection-play display-1 text-white-50"></i>
                                <p class="text-white-50 mt-3">No episodes available for this season</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <hr class="my-5">
        <?php endif; ?>

        <!-- Trailer Section -->
        <?php if (!empty($youtube_embed)): ?>
        <section id="trailer-section" class="mb-5">
            <h3 class="mb-4"><i class="bi bi-play-circle me-2"></i>Official Trailer</h3>
            <div class="trailer-container">
                <iframe src="<?= $youtube_embed; ?>" 
                    title="<?= htmlspecialchars($series['title']); ?> Trailer" 
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>
        </section>
        <hr class="my-5">
        <?php endif; ?>

        <!-- Similar Series Section -->
        <?php if (!empty($similar_series)): ?>
        <section class="mb-5">
            <h3 class="mb-4"><i class="bi bi-tv me-2"></i>More Like This</h3>
            <div class="row row-cols-2 row-cols-md-4 g-4">
                <?php foreach ($similar_series as $similar): ?>
                <div class="col">
                    <a href="series-details.php?id=<?= $similar['series_id']; ?>" class="text-decoration-none">
                        <div class="card media-card bg-dark text-white position-relative">
                            <img src="<?= htmlspecialchars($similar['poster_path']); ?>"
                                class="card-img-top" 
                                alt="<?= htmlspecialchars($similar['title']); ?>"
                                onerror="this.src='assets/img/no-poster.jpg'">
                            <span class="position-absolute top-0 end-0 badge bg-warning text-dark m-2">
                                <i class="bi bi-star-fill"></i> <?= number_format($similar['rating'], 1); ?>
                            </span>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($similar['title']); ?></h5>
                                <p class="card-text text-white"><?= htmlspecialchars($similar['genre_names']); ?> • <?= $similar['release_year']; ?></p>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <?php include 'includes/details-rating-modals.php'; ?>
    
    <?php include 'includes/footer.php'; ?>