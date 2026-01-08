<!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top mb-0">
        <div class="container">
            <!-- Logo with Icon -->
            <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
                <img src="assets/img/logo.svg" alt="CineGrid Logo" width="32" height="32" class="me-2">
                CineGrid
            </a>

            <!-- Hamburger Menu Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Navigation Links -->
                <ul class="navbar-nav ms-auto me-3">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'index') ? 'active' : ''; ?>" href="index.php">
                            <i class="bi bi-house-door me-1"></i>Home
                        </a>
                    </li>

                    <!-- Explore Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo ($current_page == 'movies' || $current_page == 'series') ? 'active' : ''; ?>" 
                            href="#" 
                            role="button" 
                            data-bs-toggle="dropdown"
                            aria-expanded="false" 
                            id="exploreDropdown">
                            <i class="bi bi-compass me-1"></i>Explore
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="exploreDropdown">
                            <li>
                                <a class="dropdown-item" href="#popular-movies">
                                    <i class="bi bi-fire me-2"></i>Trending
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'movies') ? 'active' : ''; ?>" href="movies.php">
                                    <i class="bi bi-film me-2"></i>Popular Movies
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'series') ? 'active' : ''; ?>" href="series.php">
                                    <i class="bi bi-tv me-2"></i>Popular Series
                                </a>
                            </li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-star me-2"></i>Top Actors</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-calendar-event me-2"></i>Upcoming Releases</a></li>
                        </ul>
                    </li>
                </ul>

                <!-- Action Buttons -->
                <div class="d-flex gap-2">
                    <!-- Search Button -->
                    <button class="btn btn-sm btn-outline-light d-flex align-items-center" 
                            type="button" 
                            data-bs-toggle="modal" 
                            data-bs-target="#searchModal"
                            aria-label="Search">
                        <i class="bi bi-search me-1"></i> Search
                    </button>

                    <?php if (isset($_SESSION['user_id'])): ?>

                    <!-- Profile Dropdown (LOGGED IN) -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-light d-flex align-items-center dropdown-toggle"
                            type="button"
                            id="userProfileDropdown"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-person-fill me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Profile'); ?>

                            <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                                <span class="badge bg-danger ms-2" style="font-size: 0.6rem;">ADMIN</span>
                            <?php endif; ?>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end"
                            aria-labelledby="userProfileDropdown">

                            <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                                <li>
                                    <a class="dropdown-item text-warning fw-bold" href="admin.php">
                                        <i class="bi bi-speedometer2 me-2"></i> Admin Dashboard
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            
                            <li>
                                <a class="dropdown-item" href="profile.php">
                                    <i class="bi bi-person me-2"></i> View Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="bi bi-gear me-2"></i> Settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" 
                                href="#" 
                                data-bs-toggle="modal" 
                                data-bs-target="#logoutConfirmModal">
                                    <i class="bi bi-box-arrow-right me-2"></i> Log Out
                                </a>
                            </li>
                        </ul>
                    </div>

                <?php else: ?>

                    <!-- Login Button (LOGGED OUT) -->
                    <button class="btn btn-sm btn-outline-light d-flex align-items-center"
                            type="button"
                            data-bs-toggle="modal"
                            data-bs-target="#loginModal"
                            aria-label="Login">
                        <i class="bi bi-person-circle me-1"></i> Login
                    </button>

                <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>