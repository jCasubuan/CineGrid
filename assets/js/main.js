document.addEventListener('DOMContentLoaded', () => {

    // Mobile Filter Toggle
    const filterToggle = document.getElementById('filterToggle');
    const filterSidebar = document.getElementById('filterSidebar');
    const filterOverlay = document.getElementById('filterOverlay');
    const closeFilter = document.getElementById('closeFilter');

    filterToggle?.addEventListener('click', () => {
        filterSidebar?.classList.add('show');
        filterOverlay?.classList.add('show');
    });

    [closeFilter, filterOverlay].forEach(el => {
        el?.addEventListener('click', () => {
            filterSidebar?.classList.remove('show');
            filterOverlay?.classList.remove('show');
        });
    });

    // View Toggle (Grid/List)
    // --- VIEW TOGGLE (GRID/LIST) ---
    const gridViewBtn = document.getElementById('gridView');
    const listViewBtn = document.getElementById('listView');
    // We check for both possible IDs
    const gridContainer = document.getElementById('movieGrid') || document.getElementById('seriesGrid');

    if (gridViewBtn && listViewBtn && gridContainer) {
        gridViewBtn.addEventListener('click', () => {
            gridContainer.classList.remove('list-view');
            gridViewBtn.classList.add('active');
            listViewBtn.classList.remove('active');
        });

        listViewBtn.addEventListener('click', () => {
            gridContainer.classList.add('list-view');
            listViewBtn.classList.add('active');
            gridViewBtn.classList.remove('active');
        });
    }

    // --- SORT & PAGINATION ---
    document.getElementById('sortBy')?.addEventListener('change', (e) => {
        console.log('Sorting by:', e.target.value);
    });

    document.querySelectorAll('.pagination .page-link').forEach(link => {
        link.addEventListener('click', (e) => {
            const item = e.target.closest('.page-item');
            if (!item.classList.contains('disabled') && e.target.textContent !== '...') {
                document.querySelectorAll('.pagination .page-item').forEach(p => p.classList.remove('active'));
                item.classList.add('active');
            }
        });
    });

    // --- SMOOTH SCROLL ---
    document.querySelectorAll('a[href^="#"]:not([href="#"])').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

});

// for login modals
/* ===================================
   LOGIN MODAL FUNCTIONALITY
   =================================== */

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const loginSpinner = document.getElementById('loginSpinner');
    const loginBtnText = document.getElementById('loginBtnText');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('passwordInput');
    const toggleIcon = document.getElementById('toggleIcon');
    const emailInput = document.getElementById('emailInput');

    // Check if login modal elements exist before running
    if (!loginForm) return;

    // Password Toggle Functionality
    if (togglePassword && passwordInput && toggleIcon) {
        togglePassword.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent any default behavior
            
            console.log('Toggle clicked!'); // Debug
            console.log('Current type:', passwordInput.getAttribute('type'));
            
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            console.log('New type:', type); // Debug
            
            // Toggle icon logic
            if (type === 'password') {
                // It's hidden, so the eye should be slashed (closed)
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                // It's visible, so the eye should be open
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
            
            // Add visual feedback
            togglePassword.style.transform = 'scale(1.1)';
            setTimeout(() => {
                togglePassword.style.transform = 'scale(1)';
            }, 150);
        });
    } else {
        console.error('Password toggle elements not found:', {
            togglePassword: !!togglePassword,
            passwordInput: !!passwordInput,
            toggleIcon: !!toggleIcon
        });
    }

    // Form Submission with Loading State
    if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function(e) {
            // Basic client-side validation
            const email = emailInput ? emailInput.value.trim() : '';
            const password = passwordInput ? passwordInput.value.trim() : '';

            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all fields.');
                return;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                if (emailInput) {
                    emailInput.classList.add('is-invalid');
                }
                return;
            }

            // Show loading state
            loginBtn.disabled = true;
            if (loginSpinner) {
                loginSpinner.classList.remove('d-none');
            }
            if (loginBtnText) {
                loginBtnText.textContent = 'Logging in...';
            }
        });
    }

    // Remove invalid class on input
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    }
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    }
});

/* ===================================
   SIGNUP MODAL FUNCTIONALITY
   =================================== */

document.addEventListener('DOMContentLoaded', function() {
    const signupForm = document.getElementById('signupForm');
    const signupBtn = document.getElementById('signupBtn');
    const signupSpinner = document.getElementById('signupSpinner');
    const signupBtnText = document.getElementById('signupBtnText');
    const toggleSignupPassword = document.getElementById('toggleSignupPassword');
    const signupPasswordInput = document.getElementById('signupPassword');
    const toggleSignupIcon = document.getElementById('toggleSignupIcon');
    const signupNameInput = document.getElementById('signupName');
    const signupEmailInput = document.getElementById('signupEmail');
    const passwordStrength = document.getElementById('passwordStrength');

    // Check if signup modal elements exist before running
    if (!signupForm) return;

    // Password Toggle Functionality
    if (toggleSignupPassword && signupPasswordInput && toggleSignupIcon) {
        toggleSignupPassword.addEventListener('click', function(e) {
            e.preventDefault();
            
            const type = signupPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            signupPasswordInput.setAttribute('type', type);
            
            // Toggle icon
            // Toggle icon logic
            if (type === 'password') {
                // It's hidden, so the eye should be slashed (closed)
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                // It's visible, so the eye should be open
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
            
            // Add visual feedback
            toggleSignupPassword.style.transform = 'scale(1.1)';
            setTimeout(() => {
                toggleSignupPassword.style.transform = 'scale(1)';
            }, 150);
        });
    }

    // Password Strength Indicator
    if (signupPasswordInput && passwordStrength) {
        signupPasswordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 6) strength += 25;
            if (password.length >= 10) strength += 25;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 25;
            if (/[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) strength += 25;
            
            passwordStrength.style.width = strength + '%';
            
            // Color based on strength
            if (strength <= 25) {
                passwordStrength.className = 'progress-bar bg-danger';
            } else if (strength <= 50) {
                passwordStrength.className = 'progress-bar bg-warning';
            } else if (strength <= 75) {
                passwordStrength.className = 'progress-bar bg-info';
            } else {
                passwordStrength.className = 'progress-bar bg-success';
            }
        });
    }

    // Form Submission with Loading State
    if (signupForm && signupBtn) {
        signupForm.addEventListener('submit', function(e) {
            // Basic client-side validation
            const fullname = signupNameInput ? signupNameInput.value.trim() : '';
            const email = signupEmailInput ? signupEmailInput.value.trim() : '';
            const password = signupPasswordInput ? signupPasswordInput.value.trim() : '';

            if (!fullname || !email || !password) {
                e.preventDefault();
                alert('Please fill in all fields.');
                return;
            }

            // Name validation (at least 2 characters)
            if (fullname.length < 2) {
                e.preventDefault();
                if (signupNameInput) {
                    signupNameInput.classList.add('is-invalid');
                }
                alert('Please enter your full name (at least 2 characters).');
                return;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                if (signupEmailInput) {
                    signupEmailInput.classList.add('is-invalid');
                }
                alert('Please enter a valid email address.');
                return;
            }

            // Password validation (at least 6 characters)
            if (password.length < 6) {
                e.preventDefault();
                if (signupPasswordInput) {
                    signupPasswordInput.classList.add('is-invalid');
                }
                alert('Password must be at least 6 characters long.');
                return;
            }

            // Show loading state
            signupBtn.disabled = true;
            if (signupSpinner) {
                signupSpinner.classList.remove('d-none');
            }
            if (signupBtnText) {
                signupBtnText.textContent = 'Creating account...';
            }
        });
    }

    // Remove invalid class on input
    [signupNameInput, signupEmailInput, signupPasswordInput].forEach(input => {
        if (input) {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        }
    });
});

/* ===================================
   GLOBAL SEARCH FUNCTIONALITY
   =================================== */

document.addEventListener('DOMContentLoaded', function() {
    let searchTimeout;
    const searchInput = document.getElementById('globalSearchInput');
    const searchResults = document.getElementById('searchResults');
    const searchLoading = document.getElementById('searchLoading');
    const noResults = document.getElementById('noResults');
    const searchInitialState = document.getElementById('searchInitialState');
    const searchModal = document.getElementById('searchModal');

    // Check if search elements exist
    if (!searchInput) return;

    // Focus input when modal opens
    if (searchModal) {
        searchModal.addEventListener('shown.bs.modal', function () {
            searchInput.focus();
            searchInput.value = ''; // Clear previous search
            searchResults.innerHTML = '';
            searchLoading.classList.add('d-none');
            noResults.classList.add('d-none');
            searchInitialState.classList.remove('d-none');
        });
    }

    // Search as user types
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        // Hide all states
        searchResults.innerHTML = '';
        searchLoading.classList.add('d-none');
        noResults.classList.add('d-none');
        searchInitialState.classList.add('d-none');
        
        if (query.length === 0) {
            searchInitialState.classList.remove('d-none');
            return;
        }
        
        if (query.length < 2) {
            return;
        }
        
        // Show loading
        searchLoading.classList.remove('d-none');
        
        // Debounce search request
        searchTimeout = setTimeout(function() {
            performSearch(query);
        }, 500);
    });

    function performSearch(query) {
        fetch(`search-handler.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchLoading.classList.add('d-none');
                
                if (data.results && data.results.length > 0) {
                    displaySearchResults(data.results);
                } else {
                    noResults.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                searchLoading.classList.add('d-none');
                noResults.classList.remove('d-none');
            });
    }

    function displaySearchResults(results) {
        searchResults.innerHTML = results.map(item => {
            const posterUrl = item.poster_path || 'assets/img/no-poster.jpg';
            const detailsUrl = item.type === 'movie' ? `movie-details.php?id=${item.movie_id}` : `series-details.php?id=${item.series_id}`;
            
            return `
                <a href="${detailsUrl}" class="search-result-item text-decoration-none">
                    <img src="${posterUrl}" alt="${escapeHtml(item.title)}" class="search-result-poster" onerror="this.src='assets/img/no-poster.jpg'">
                    <div class="search-result-info">
                        <div class="search-result-title">${escapeHtml(item.title)}</div>
                        <div class="search-result-meta">
                            <span>${item.release_year || 'N/A'}</span>
                            ${item.genres ? ` • ${escapeHtml(item.genres)}` : ''}
                        </div>
                    </div>
                    <div class="search-result-rating">
                        <i class="bi bi-star-fill text-warning"></i>
                        <span>${item.rating ? parseFloat(item.rating).toFixed(1) : 'N/A'}</span>
                    </div>
                </a>
            `;
        }).join('');
    }

    // Helper function to escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});

// Start carousel immediately on page load
document.addEventListener('DOMContentLoaded', function() {
    const carouselEl = document.getElementById('featuredSlider');
    if (carouselEl) {
        var carousel = new bootstrap.Carousel(carouselEl, {
            interval: 4000,
            ride: 'carousel',
            pause: 'hover'
        });
        carousel.cycle();
    }
});