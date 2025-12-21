<!-- MODALS -->
    <!-- Searh Field Modal-->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="searchModalLabel">
                        <i class="bi bi-search me-2"></i>Search CineGrid
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="search" class="form-control form-control-lg"
                        placeholder="Start typing a movie, series, or actor..." aria-label="Search">
                    <div class="mt-3">
                        <small class="text-white">Trending: Marvel, Christpher Nolan, Squid Game</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="loginModalLabel">Log In to CineGrid</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <?php if (!empty($_SESSION['login_error'])): ?>
                        <div class="alert alert-danger text-center">
                            <?= $_SESSION['login_error']; ?>
                        </div>
                        <?php unset($_SESSION['login_error']); ?>
                    <?php endif; ?>
                    
                    <form id="loginForm" action="login.php" method="POST">

                        <!-- for email input -->
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control" id="emailInput" placeholder="name@example.com" required>
                            <label for="emailInput">Email address</label>
                        </div>

                        <!-- for password input -->
                        <div class="form-floating mb-3">
                            <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Password" required>
                            <label for="passwordInput">Password</label>
                        </div>

                        <!-- submit button with a loader-->
                        <button type="submit" class="btn btn-primary w-100 mt-3" id="loginBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            <span class="btn-text">Login</span>
                        </button>
                    </form>
                </div>

                <!-- sign in setter -->
                <div class="modal-footer justify-content-center border-0">
                    <small class="text-muted">New user?
                        <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#signupModal"
                            class="text-decoration-none">Create Account</a>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Sign up Modal -->
    <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="signupModalLabel">Sign up to CineGrid</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="signupForm" action="signup.php" method="POST">
                        <!-- for fullname input -->
                        <div class="form-floating mb-3">
                            <input type="text" name="fullname" class="form-control" id="signupName" placeholder="Full Name" required>
                            <label for="signupName">Full Name</label>
                        </div>

                        <!-- for email inpput -->
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control" id="signupEmail" placeholder="Email Address" required>
                            <label for="signupEmail">Email Address</label>
                        </div>

                        <!-- for password input -->
                        <div class="form-floating mb-3">
                            <input type="password" name="password" class="form-control" id="signupPassword" placeholder="Password" required>
                            <label for="signupPassword">Password</label>
                        </div>

                        <!-- submit button with a loader -->
                        <button type="submit" class="btn btn-primary w-100 mt-3" id="signupBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            <span class="btn-text">Sign up</span>
                        </button>
                    </form>
                </div>

                <!-- login setter -->
                <div class="modal-footer justify-content-center border-0">
                    <small class="text-muted">Already have an account?
                        <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal"
                            class="text-decoration-none">Log In Account</a>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout confirmation -->
    <div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-body text-center py-4">
                    <i class="bi bi-exclamation-circle text-warning display-4 mb-3"></i>
                    <h5>Logging Out?</h5>
                    <p class="text-muted small">Are you sure you want to end your session?</p>
                    <div class="d-flex gap-2 mt-4">
                        <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Cancel</button>
                        <a href="logout.php" class="btn btn-danger w-100">Log Out</a>
                    </div>
                </div>
            </div>
        </div>
    </div>