<!-- MODALS -->
    <!-- Search Field Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="searchModalLabel">
                        <i class="bi bi-search me-2"></i>Search CineGrid
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="search" 
                        id="globalSearchInput"
                        class="form-control form-control-lg bg-dark text-white border-secondary"
                        placeholder="Start typing a movie, series, or actor..." 
                        aria-label="Search"
                        autocomplete="off">
                    
                    <!-- Loading Indicator -->
                    <div id="searchLoading" class="text-center py-4 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-white-50">Searching...</p>
                    </div>

                    <!-- Search Results -->
                    <div id="searchResults" class="search-results-container mt-3"></div>

                    <!-- No Results Message -->
                    <div id="noResults" class="text-center py-5 d-none">
                        <i class="bi bi-search fs-1 text-white-50"></i>
                        <p class="mt-3 text-white-50">No results found. Try a different search term.</p>
                    </div>

                    <!-- Initial State -->
                    <div id="searchInitialState" class="mt-3">
                        <small class="text-white-50">Trending: Marvel, Christopher Nolan, Squid Game</small>
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

                    <!-- Error Alert with Better Styling -->
                    <?php if (!empty($_SESSION['login_error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Error:</strong> <?= htmlspecialchars($_SESSION['login_error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php 
                        $error_field = $_SESSION['login_error_field'] ?? '';
                        unset($_SESSION['login_error']); 
                        ?>
                    <?php endif; ?>

                    <!-- Success Alert (if redirected back after successful action) -->
                    <?php if (!empty($_SESSION['login_success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <?= htmlspecialchars($_SESSION['login_success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['login_success']); ?>
                    <?php endif; ?>

                    <form id="loginForm" action="login.php" method="POST" autocomplete="off">

                        <!-- Email Input with Error State -->
                        <div class="form-floating mb-3">
                            <input type="email" 
                                name="email" 
                                class="form-control <?= (isset($error_field) && $error_field === 'email') ? 'is-invalid' : ''; ?>" 
                                id="emailInput" 
                                placeholder="name@example.com"
                                value="<?= htmlspecialchars($_SESSION['form_email'] ?? ''); ?>"
                                autocomplete="off" 
                                required>
                            <label for="emailInput">Email address</label>
                            <div class="invalid-feedback">
                                Please enter a valid email address.
                            </div>
                        </div>

                        <!-- Password Input with Error State and Toggle -->
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" 
                                name="password" 
                                class="form-control <?= (isset($error_field) && $error_field === 'password') ? 'is-invalid' : ''; ?>" 
                                id="passwordInput" 
                                placeholder="Password" 
                                autocomplete="new-password"
                                required>
                            <label for="passwordInput">Password</label>
                            
                            <!-- Password Toggle Button -->
                            <button type="button" 
                                class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-white pe-3" 
                                id="togglePassword"
                                style="z-index: 10; border: none; background: none;">
                                <i class="bi bi-eye-slash" id="toggleIcon"></i>
                            </button>
                            
                            <div class="invalid-feedback">
                                Password must be at least 6 characters.
                            </div>
                        </div>

                        <!-- Remember Me Checkbox -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="rememberMe" name="remember_me">
                            <label class="form-check-label text-white small" for="rememberMe">
                                Remember me
                            </label>
                        </div>

                        <!-- Submit Button with Loader -->
                        <button type="submit" class="btn btn-primary w-100 mt-3" id="loginBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" id="loginSpinner"></span>
                            <span class="btn-text" id="loginBtnText">Login</span>
                        </button>
                    </form>

                    <!-- Forgot Password Link -->
                    <div class="text-center mt-3">
                        <a href="#" class="text-white text-decoration-none small">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <!-- Sign Up Footer -->
                <div class="modal-footer justify-content-center border-0">
                    <small class="text-white">New user?
                        <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#signupModal"
                            class="text-decoration-none">Create Account</a>
                    </small>
                </div>
            </div>
        </div>
    </div>

<?php 
// Clear form data after displaying
unset($_SESSION['form_email']);
unset($_SESSION['login_error_field']);
?>

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

                <!-- Error Alert with Better Styling -->
                <?php if (!empty($_SESSION['signup_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Error:</strong> <?= htmlspecialchars($_SESSION['signup_error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php 
                    $signup_error_field = $_SESSION['signup_error_field'] ?? '';
                    unset($_SESSION['signup_error']); 
                    ?>
                <?php endif; ?>

                <!-- Success Alert -->
                <?php if (!empty($_SESSION['signup_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?= htmlspecialchars($_SESSION['signup_success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['signup_success']); ?>
                <?php endif; ?>

                <form id="signupForm" action="signup.php" method="POST" autocomplete="off">
                    
                    <!-- Full Name Input with Error State -->
                    <div class="form-floating mb-3">
                        <input type="text" 
                            name="fullname" 
                            class="form-control <?= (isset($signup_error_field) && $signup_error_field === 'fullname') ? 'is-invalid' : ''; ?>" 
                            id="signupName" 
                            placeholder="Full Name"
                            value="<?= htmlspecialchars($_SESSION['form_fullname'] ?? ''); ?>"
                            autocomplete="off"
                            required>
                        <label for="signupName">Full Name</label>
                        <div class="invalid-feedback">
                            Please enter your full name.
                        </div>
                    </div>

                    <!-- Email Input with Error State -->
                    <div class="form-floating mb-3">
                        <input type="email" 
                            name="email" 
                            class="form-control <?= (isset($signup_error_field) && $signup_error_field === 'email') ? 'is-invalid' : ''; ?>" 
                            id="signupEmail" 
                            placeholder="Email Address"
                            value="<?= htmlspecialchars($_SESSION['form_signup_email'] ?? ''); ?>"
                            autocomplete="off"
                            required>
                        <label for="signupEmail">Email Address</label>
                        <div class="invalid-feedback">
                            Please enter a valid email address.
                        </div>
                    </div>

                    <!-- Password Input with Error State and Toggle -->
                    <div class="form-floating mb-3 position-relative">
                        <input type="password" 
                            name="password" 
                            class="form-control <?= (isset($signup_error_field) && $signup_error_field === 'password') ? 'is-invalid' : ''; ?>" 
                            id="signupPassword" 
                            placeholder="Password" 
                            autocomplete="new-password"
                            style="padding-right: 45px;"
                            required>
                        <label for="signupPassword">Password</label>
                        
                        <!-- Password Toggle Button -->
                        <button type="button" 
                            class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted pe-3 d-flex align-items-center" 
                            id="toggleSignupPassword"
                            style="z-index: 10; border: none; background: none; margin-top: 0;">
                            <i class="bi bi-eye" id="toggleSignupIcon"></i>
                        </button>
                        
                        <div class="invalid-feedback">
                            Password must be at least 6 characters.
                        </div>
                    </div>

                    <!-- Submit Button with Loader -->
                    <button type="submit" class="btn btn-primary w-100 mt-3" id="signupBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status" id="signupSpinner"></span>
                        <span class="btn-text" id="signupBtnText">Sign up</span>
                    </button>
                </form>
            </div>

            <!-- Login Footer -->
            <div class="modal-footer justify-content-center border-0">
                <small class="text-white">Already have an account?
                    <a href="#" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#loginModal"
                        class="text-decoration-none">Log In</a>
                </small>
            </div>
        </div>
    </div>
</div>

<?php 
// Clear form data after displaying
unset($_SESSION['form_fullname']);
unset($_SESSION['form_signup_email']);
unset($_SESSION['signup_error_field']);
?>

    <!-- Logout confirmation -->
    <div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content bg-dark text-white border-secondary">
                <div class="modal-body text-center py-4">
                    <i class="bi bi-exclamation-circle text-warning display-4 mb-3"></i>
                    <h5>Logging Out?</h5>
                    <p class="text-white small">Are you sure you want to end your session?</p>
                    <div class="d-flex gap-2 mt-4">
                        <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Cancel</button>
                        <a href="logout.php" class="btn btn-danger w-100">Log Out</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-open login modal if there's an error -->
    <?php if (!empty($_SESSION['show_login_modal'])): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
    });
    </script>
    <?php 
        unset($_SESSION['show_login_modal']); // Clean up after use
    endif; 
    ?>