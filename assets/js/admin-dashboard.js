// ========================================
// 1. SECTION NAVIGATION (Unified Approach)
// ========================================
class SectionManager {
    constructor() {
        this.sections = document.querySelectorAll('.content-section');
        this.links = document.querySelectorAll('.sidebar-menu-link');
        this.init();
    }

    init() {
        // Handle sidebar clicks
        this.links.forEach(link => {
            link.addEventListener('click', (e) => {
                const sectionName = link.getAttribute('data-section');
                
                // Only prevent default if this is a section link (not an external link)
                if (sectionName) {
                    e.preventDefault();
                    this.showSection(sectionName + 'Section');
                }
            });
        });

        // Handle URL parameters on page load
        const urlParams = new URLSearchParams(window.location.search);
        const section = urlParams.get('section');
        
        if (section) {
            this.showSection(section + 'Section');
        } else {
            this.showSection('dashboardSection'); // Default
        }
    }

    showSection(sectionId) {
        // Hide all sections
        this.sections.forEach(section => {
            section.style.display = 'none';
        });

        // Show target section
        const target = document.getElementById(sectionId);
        if (target) {
            target.style.display = 'block';
        }

        // Update active link state
        this.links.forEach(link => {
            link.classList.remove('active');
            const linkSection = link.getAttribute('data-section');
            if (linkSection && sectionId === linkSection + 'Section') {
                link.classList.add('active');
            }
        });
    }
}

// ========================================
// 2. MOBILE SIDEBAR
// ========================================
class MobileSidebar {
    constructor() {
        this.toggle = document.getElementById('mobileToggle');
        this.sidebar = document.getElementById('sidebar');
        
        if (this.toggle && this.sidebar) {
            this.init();
        }
    }

    init() {
        // Toggle button
        this.toggle.addEventListener('click', () => {
            this.sidebar.classList.toggle('show');
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 &&
                !this.sidebar.contains(e.target) &&
                !this.toggle.contains(e.target) &&
                this.sidebar.classList.contains('show')) {
                this.sidebar.classList.remove('show');
            }
        });
    }
}

// ========================================
// 3. SEARCH FUNCTIONALITY
// ========================================
class SearchManager {
    constructor(config) {
        this.searches = config;
        this.init();
    }

    init() {
        this.searches.forEach(({ inputId, tableBodyId }) => {
            const input = document.getElementById(inputId);
            const tableBody = document.getElementById(tableBodyId);

            if (!input || !tableBody) {
                console.warn(`Search setup failed: ${inputId} or ${tableBodyId} not found`);
                return;
            }

            input.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                const rows = tableBody.querySelectorAll('tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        });
    }
}

// ========================================
// 4. CRUD OPERATIONS
// ========================================

/* ===================================
   DELETE MOVIE FUNCTION
   =================================== */
function deleteMovie(movieId, movieTitle) {
    Swal.fire({
        title: 'Delete Movie?',
        html: `Are you sure you want to delete <strong>"${movieTitle}"</strong>?<br><small class="text-danger">This action cannot be undone.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        background: '#1a1d20',
        color: '#fff',
        customClass: {
            popup: 'border border-secondary'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleting...',
                html: 'Please wait while we delete the movie.',
                allowOutsideClick: false,
                background: '#1a1d20',
                color: '#fff',
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('admin-actions/delete-movie.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `movie_id=${movieId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: data.message,
                        icon: 'success',
                        background: '#1a1d20',
                        color: '#fff',
                        confirmButtonColor: '#0d6efd'
                    }).then(() => {
                        window.location.href = '?section=movies';
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        background: '#1a1d20',
                        color: '#fff',
                        confirmButtonColor: '#d33'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while deleting the movie.',
                    icon: 'error',
                    background: '#1a1d20',
                    color: '#fff',
                    confirmButtonColor: '#d33'
                });
            });
        }
    });
}

/* ===================================
   EDIT/UPDATE MOVIE FUNCTIONS
   =================================== */
function loadEditData(movieId) {
    const loadingState = document.getElementById('editLoadingState');
    const editForm = document.getElementById('editMovieForm');
    const alertDiv = document.getElementById('editMovieAlert');
    
    if (loadingState) loadingState.style.display = 'block';
    if (editForm) editForm.style.display = 'none';
    if (alertDiv) alertDiv.classList.add('d-none');

    fetch(`admin-actions/get-movie-data.php?movie_id=${movieId}`)
        .then(response => response.json())
        .then(data => {
            if (loadingState) loadingState.style.display = 'none';
            
            if (data.success) {
                populateEditModal(data.movie);
                if (editForm) editForm.style.display = 'block';
            } else {
                showEditAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (loadingState) loadingState.style.display = 'none';
            showEditAlert('An error occurred while loading movie data.', 'danger');
        });
}

function populateEditModal(movie) {
    // Basic fields
    document.getElementById('edit_movie_id').value = movie.movie_id;
    document.getElementById('edit_title').value = movie.title;
    document.getElementById('edit_overview').value = movie.overview;
    document.getElementById('edit_release_year').value = movie.release_year;
    document.getElementById('edit_duration').value = movie.duration;
    document.getElementById('edit_rating').value = movie.rating;
    document.getElementById('edit_content_rating').value = movie.content_rating;
    document.getElementById('edit_language').value = movie.language;
    document.getElementById('edit_tmdb_id').value = movie.tmdb_id || '';
    document.getElementById('edit_poster_path').value = movie.poster_path;
    document.getElementById('edit_backdrop_path').value = movie.backdrop_path;
    document.getElementById('edit_trailer_url').value = movie.trailer_url || '';
    document.getElementById('edit_status').value = movie.status;
    
    // Genres
    document.querySelectorAll('[name="genres[]"]').forEach(cb => {
        cb.checked = movie.genres.includes(cb.value);
    });
    
    // Directors
    const directorsContainer = document.getElementById('edit_directors_container');
    directorsContainer.innerHTML = '';
    movie.directors.forEach(director => {
        addEditDirectorRow(director);
    });
    if (movie.directors.length === 0) {
        addEditDirectorRow('');
    }
    
    // Writers
    const writersContainer = document.getElementById('edit_writers_container');
    writersContainer.innerHTML = '';
    movie.writers.forEach(writer => {
        addEditWriterRow(writer);
    });
    if (movie.writers.length === 0) {
        addEditWriterRow('');
    }
    
    // Cast
    const castContainer = document.getElementById('edit_cast_container');
    castContainer.innerHTML = '';
    movie.cast.forEach(member => {
        addEditCastRow(member.actor, member.character, member.image);
    });
    if (movie.cast.length === 0) {
        addEditCastRow('', '', '');
    }
}

// Helper functions for dynamic rows
function addEditDirectorRow(name = '') {
    const container = document.getElementById('edit_directors_container');
    const div = document.createElement('div');
    div.className = 'input-group mb-2 edit-director-row';
    div.innerHTML = `
        <input type="text" name="directors[]" class="form-control bg-dark text-white border-secondary" 
               placeholder="Director name" value="${name}" required>
        <button type="button" class="btn btn-outline-danger remove-edit-director">
            <i class="bi bi-x"></i>
        </button>
    `;
    container.appendChild(div);
}

function addEditWriterRow(name = '') {
    const container = document.getElementById('edit_writers_container');
    const div = document.createElement('div');
    div.className = 'input-group mb-2 edit-writer-row';
    div.innerHTML = `
        <input type="text" name="writers[]" class="form-control bg-dark text-white border-secondary" 
               placeholder="Writer name" value="${name}" required>
        <button type="button" class="btn btn-outline-danger remove-edit-writer">
            <i class="bi bi-x"></i>
        </button>
    `;
    container.appendChild(div);
}

function addEditCastRow(actor = '', character = '', image = '') {
    const container = document.getElementById('edit_cast_container');
    const div = document.createElement('div');
    div.className = 'row g-2 mb-2 edit-cast-row';
    div.innerHTML = `
        <div class="col-md-4">
            <input type="text" name="actors[]" class="form-control bg-dark text-white border-secondary" 
                   placeholder="Actor name" value="${actor}" required>
        </div>
        <div class="col-md-4">
            <input type="text" name="characters[]" class="form-control bg-dark text-white border-secondary" 
                   placeholder="Character name" value="${character}" required>
        </div>
        <div class="col-md-3">
            <input type="text" name="actor_images[]" class="form-control bg-dark text-white border-secondary" 
                   placeholder="Image path" value="${image}">
        </div>
        <div class="col-md-1 d-grid">
            <button type="button" class="btn btn-outline-danger remove-edit-cast">
                <i class="bi bi-x"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
}

function showEditAlert(message, type) {
    const alertDiv = document.getElementById('editMovieAlert');
    if (alertDiv) {
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = message;
        alertDiv.classList.remove('d-none');
    }
}

function handleEditFormSubmit(e) {
    e.preventDefault();
    
    const updateBtn = document.getElementById('updateMovieBtn');
    const updateSpinner = document.getElementById('updateMovieSpinner');
    const updateBtnText = document.getElementById('updateMovieBtnText');
    
    if (updateBtn) updateBtn.disabled = true;
    if (updateSpinner) updateSpinner.classList.remove('d-none');
    if (updateBtnText) updateBtnText.textContent = 'Saving...';
    
    const formData = new FormData(e.target);
    
    fetch('admin-actions/update-movie.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (updateBtn) updateBtn.disabled = false;
        if (updateSpinner) updateSpinner.classList.add('d-none');
        if (updateBtnText) updateBtnText.textContent = 'Save Changes';
        
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                background: '#1a1d20',
                color: '#fff',
                confirmButtonColor: '#0d6efd'
            }).then(() => {
                bootstrap.Modal.getInstance(document.getElementById('editMovieModal')).hide();
                window.location.href = '?section=movies';
            });
        } else {
            showEditAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (updateBtn) updateBtn.disabled = false;
        if (updateSpinner) updateSpinner.classList.add('d-none');
        if (updateBtnText) updateBtnText.textContent = 'Save Changes';
        showEditAlert('An error occurred while updating the movie.', 'danger');
    });
}

// Make CRUD functions globally accessible
window.deleteMovie = deleteMovie;
window.loadEditData = loadEditData;

// ========================================
// 5. FEATURED TOGGLE (No Page Reload)
// ========================================
async function toggleFeatured(movieId) {
    try {
        const res = await fetch(`admin-actions/toggle-featured.php?id=${movieId}`, { 
            method: 'POST' 
        });
        
        if (!res.ok) throw new Error('Server error');
        
        const data = await res.json();
        
        if (data.status === 'success') {
            const btn = document.querySelector(`[onclick="toggleFeatured(${movieId})"]`);
            
            if (btn) {
                const icon = btn.querySelector('i');
                
                if (data.new_state) {
                    btn.classList.remove('btn-outline-secondary');
                    btn.classList.add('btn-warning');
                    
                    icon.classList.remove('bi-lightning');
                    icon.classList.add('bi-lightning-fill');
                } else {
                    btn.classList.remove('btn-warning');
                    btn.classList.add('btn-outline-secondary');
                    
                    icon.classList.remove('bi-lightning-fill');
                    icon.classList.add('bi-lightning');
                }
            }

            const msg = data.new_state ? 'Added to Hero Banner' : 'Removed from Hero Banner';
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: msg,
                showConfirmButton: false,
                timer: 2000
            });
        } else {
            throw new Error(data.error || 'Failed to toggle featured status');
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message
        });
    }
}

window.toggleFeatured = toggleFeatured;

// ========================================
// 6. MULTI-STEP MODAL HANDLER
// ========================================
class MultiStepModalHandler {
    constructor(steps) {
        this.steps = steps;
        this.init();
    }

    init() {
        this.steps.forEach(({ formId, currentModalId, nextModalId }) => {
            this.setupStep(formId, currentModalId, nextModalId);
        });
    }

    setupStep(formId, currentModalId, nextModalId) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitBtn = form.querySelector('[type="submit"]');
            const originalBtnText = submitBtn?.innerHTML || '';
            
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
            }

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form)
                });

                if (!res.ok) throw new Error('Server error');
                
                const data = await res.json();

                if (data.status === 'ok') {
                    if (submitBtn) {
                        submitBtn.classList.replace('btn-primary', 'btn-success');
                        submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Saved!';
                    }

                    await new Promise(resolve => setTimeout(resolve, 600));
                    
                    const currentModal = bootstrap.Modal.getInstance(document.getElementById(currentModalId));
                    const nextModal = new bootstrap.Modal(document.getElementById(nextModalId));
                    
                    currentModal.hide();
                    nextModal.show();
                    
                    if (submitBtn) {
                        submitBtn.classList.replace('btn-success', 'btn-primary');
                        submitBtn.innerHTML = originalBtnText;
                    }
                } else {
                    throw new Error(data.error || 'Validation failed. Check your inputs.');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message,
                    background: '#1a1d20',
                    color: '#fff'
                });
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            }
        });
    }
}

// ========================================
// 7. DYNAMIC LIST MANAGER
// ========================================
class DynamicListManager {
    constructor(config) {
        this.lists = config;
        this.init();
    }

    init() {
        this.lists.forEach(({ btnId, containerId, rowClass, template }) => {
            this.setupList(btnId, containerId, rowClass, template);
        });
    }

    setupList(btnId, containerId, rowClass, template) {
        const btn = document.getElementById(btnId);
        const container = document.getElementById(containerId);

        if (!btn || !container) return;

        // Add row
        btn.addEventListener('click', () => {
            const div = document.createElement('div');
            div.innerHTML = template;
            container.appendChild(div.firstElementChild);
        });

        // Remove row (event delegation)
        container.addEventListener('click', (e) => {
            const removeBtn = e.target.closest('.btn-outline-danger');
            if (removeBtn) {
                const rows = container.querySelectorAll(`.${rowClass}`);
                if (rows.length > 1) {
                    removeBtn.closest(`.${rowClass}`).remove();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cannot Remove',
                        text: 'At least one entry is required.',
                        timer: 2000,
                        showConfirmButton: false,
                        background: '#1a1d20',
                        color: '#fff'
                    });
                }
            }
        });
    }
}

// ========================================
// 8. DRAFT REVIEW MODAL
// ========================================
class DraftReviewModal {
    constructor() {
        this.modal = document.getElementById('reviewMovieDraftModal');
        this.content = document.getElementById('reviewContent');
        this.alert = document.getElementById('reviewAlert');
        
        if (this.modal) {
            this.init();
        }
    }

    init() {
        this.modal.addEventListener('show.bs.modal', async () => {
            try {
                const res = await fetch('admin-actions/get-movie-draft.php');
                
                if (!res.ok) throw new Error('Failed to fetch draft');
                
                const data = await res.json();
                
                if (data.error) {
                    this.showAlert(data.error, 'danger');
                    return;
                }
                
                this.renderDraft(data);
            } catch (error) {
                this.showAlert(error.message, 'danger');
            }
        });
    }

    renderDraft(draft) {
        let html = '';

        // Visual Preview
        html += this.section('Visuals', `
            <div class="row g-2">
                <div class="col-md-8">
                    <small class="text-white d-block mb-1">Backdrop Preview:</small>
                    <img src="${draft.basic.backdrop_path || 'assets/img/default-backdrop.jpg'}" 
                        class="img-fluid rounded border border-secondary" 
                        style="height: 150px; width: 100%; object-fit: cover;">
                </div>
                <div class="col-md-4 text-center">
                    <small class="text-white d-block mb-1">Poster:</small>
                    <img src="${draft.basic.poster_path || 'assets/img/default-poster.jpg'}" 
                        class="img-thumbnail" 
                        style="height: 150px; width: 100px; object-fit: cover;">
                </div>
            </div>
        `);

        // Basic Information
        html += this.section('Basic Information', `
            <div class="row">
                <div class="col-12">
                    <h4 class="text-primary mb-1">${this.escapeHtml(draft.basic.title)}</h4>
                    <div class="mb-2">
                        <span class="badge bg-warning text-dark">${draft.basic.content_rating}</span>
                        <span class="badge bg-secondary">${draft.basic.language}</span>
                        <span class="badge bg-dark border border-secondary">TMDB ID: ${draft.basic.tmdb_id || 'N/A'}</span>
                    </div>
                    <p class="mb-1"><strong>Year:</strong> ${draft.basic.release_year} | <strong>Duration:</strong> ${draft.basic.duration} mins</p>
                    <p class="mb-2"><strong>Rating:</strong> <i class="bi bi-star-fill text-warning"></i> ${draft.basic.rating}/10</p>
                    
                    <div class="p-2 bg-dark rounded border border-secondary mb-2">
                        <strong>Genres:</strong> 
                        <span class="text-info">${draft.genres.length > 0 ? draft.genres.join(' • ') : 'No genres selected'}</span>
                    </div>

                    <div class="p-2 bg-secondary bg-opacity-10 rounded">
                        <strong>Overview:</strong><br>
                        <small class="text-white-50">${this.escapeHtml(draft.basic.overview)}</small>
                    </div>
                </div>
            </div>
        `);

        // Crew
        html += this.section('Crew', `
            <div class="row">
                <div class="col-md-6">
                    <strong class="text-white">Directors:</strong>
                    <p class="small text-info">${draft.directors.length > 0 ? draft.directors.join(', ') : 'None'}</p>
                </div>
                <div class="col-md-6">
                    <strong class="text-white">Writers:</strong>
                    <p class="small text-info">${draft.writers.length > 0 ? draft.writers.join(', ') : 'None'}</p>
                </div>
            </div>
        `);

        // Cast
        html += this.section('Cast', `
            <div class="row row-cols-2 row-cols-md-4 g-2">
                ${draft.cast.map(c => `
                    <div class="col">
                        <div class="d-flex align-items-center p-1 bg-dark rounded border border-secondary">
                            <img src="${c.image || 'assets/img/default-actor.jpg'}" 
                                class="rounded-circle me-2" 
                                style="width: 35px; height: 35px; object-fit: cover; border: 1px solid #444;">
                            <div style="font-size: 0.7rem;" class="text-truncate">
                                <div class="fw-bold text-white">${this.escapeHtml(c.actor)}</div>
                                <div class="text-white">${this.escapeHtml(c.character)}</div>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `);

        // Trailer
        if (draft.trailer && draft.trailer.url) {
            html += this.section('Trailer', `
                <div class="p-2 bg-dark rounded border border-primary">
                    <i class="bi bi-youtube text-danger me-2"></i>
                    <small class="text-white-50">${this.escapeHtml(draft.trailer.url)}</small>
                </div>
            `);
        }

        this.content.innerHTML = html;
    }

    section(title, body) {
        return `
            <div class="mb-4">
                <h6 class="border-bottom pb-1 mb-2">${title}</h6>
                ${body}
            </div>
        `;
    }

    showAlert(msg, type) {
        this.alert.className = `alert alert-${type}`;
        this.alert.textContent = msg;
        this.alert.classList.remove('d-none');
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// ========================================
// 9. CONFIRM MOVIE SAVE
// ========================================
class ConfirmMovieSave {
    constructor() {
        this.confirmBtn = document.getElementById('confirmMovieSaveBtn');
        if (this.confirmBtn) {
            this.init();
        }
    }

    init() {
        this.confirmBtn.addEventListener('click', async (e) => {
            e.preventDefault();

            Swal.fire({
                title: 'Saving...',
                allowOutsideClick: false,
                background: '#1a1d20',
                color: '#fff',
                didOpen: () => { Swal.showLoading(); }
            });

            this.confirmBtn.disabled = true;

            try {
                const res = await fetch('admin-actions/commit-movie.php', { method: 'POST' });
                
                if (!res.ok) throw new Error('Server error');
                
                const data = await res.json();

                if (data.status === 'success') {
                    await Swal.fire({
                        title: 'Success!',
                        text: 'The movie has been added successfully.',
                        icon: 'success',
                        background: '#1a1d20',
                        color: '#fff',
                        confirmButtonColor: '#0d6efd'
                    });

                    await fetch('admin-actions/clear-movie-draft.php');
                    location.reload();
                } else {
                    throw new Error(data.error || 'Failed to save movie.');
                }
            } catch (error) {
                this.confirmBtn.disabled = false;
                Swal.fire({
                    title: 'Error!',
                    text: error.message,
                    icon: 'error',
                    background: '#1a1d20',
                    color: '#fff'
                });
            }
        });
    }
}

// ========================================
// 10. SERIES REVIEW MANAGER
// ========================================
class SeriesReviewManager {
    constructor() {
        this.modal = document.getElementById('reviewSeriesModal');
        if (this.modal) {
            this.modal.addEventListener('show.bs.modal', () => this.loadDraftData());
        }
    }

    async loadDraftData() {
        console.log('Loading series draft data...');
        
        try {
            const response = await fetch('admin-actions/get-series-draft.php');
            const data = await response.json();

            console.log('Draft response:', data);

            if (data.status === 'ok') {
                const draft = data.draft;
                
                console.log('Draft received:', draft);
                console.log('Seasons:', draft.seasons);
                console.log('Episodes:', draft.episodes);

                // 1. Fill Title
                const titleEl = document.getElementById('rev-series-title');
                if (titleEl) {
                    titleEl.textContent = draft.title || 'Untitled';
                }

                // 2. Fill Year
                const yearEl = document.getElementById('rev-series-year');
                if (yearEl) {
                    yearEl.textContent = draft.release_year || '';
                }

                // 3. Fill Content Rating
                const ratingEl = document.getElementById('rev-series-rating');
                if (ratingEl) {
                    ratingEl.textContent = draft.content_rating || 'NR';
                }

                // 4. Fill Genres with count
                const genresEl = document.getElementById('rev-series-genres');
                if (genresEl) {
                    const genres = draft.genres || [];
                    genresEl.textContent = genres.length > 0 ? genres.join(', ') : 'No genres selected';
                }

                // 5. Calculate and display series stats (FIXED LOGIC)
                const statsEl = document.getElementById('rev-series-stats');
                if (statsEl) {
                    const seasons = draft.seasons || [];
                    const genres = draft.genres || [];
                    
                    // FIXED: Count total episodes correctly
                    let totalEpisodes = 0;
                    
                    // If episodes exist, count them properly
                    if (draft.episodes && typeof draft.episodes === 'object') {
                        // Episodes are stored by season number: { 1: [...], 2: [...] }
                        Object.keys(draft.episodes).forEach(seasonNum => {
                            const episodesArray = draft.episodes[seasonNum];
                            if (Array.isArray(episodesArray)) {
                                totalEpisodes += episodesArray.length;
                            }
                        });
                    }
                    
                    console.log('Total episodes calculated:', totalEpisodes);
                    
                    statsEl.innerHTML = `
                        <i class="bi bi-collection"></i> ${seasons.length}, 
                        <i class="bi bi-play-circle"></i> ${totalEpisodes}, 
                        <i class="bi bi-tags"></i> ${genres.length}
                    `;
                }

                // 6. Fill Poster with error handling
                const posterEl = document.getElementById('rev-series-poster');
                if (posterEl) {
                    const posterPath = draft.poster_path || 'assets/img/no-poster.jpg';
                    posterEl.src = posterPath;
                    posterEl.onerror = function() {
                        this.src = 'assets/img/no-poster.jpg';
                    };
                }

                // 7. Fill Backdrop
                const backdropEl = document.getElementById('rev-series-backdrop');
                if (backdropEl) {
                    const backdropPath = draft.backdrop_path || 'assets/img/no-backdrop.jpg';
                    backdropEl.src = backdropPath;
                    backdropEl.onerror = function() {
                        this.src = 'assets/img/no-backdrop.jpg';
                    };
                }

                // 8. Fill Directors
                const directorsEl = document.getElementById('rev-series-directors');
                if (directorsEl) {
                    const directors = draft.directors || [];
                    directorsEl.textContent = directors.length > 0 ? directors.join(', ') : 'None listed';
                }

                // 9. Fill Writers
                const writersEl = document.getElementById('rev-series-writers');
                if (writersEl) {
                    const writers = draft.writers || [];
                    writersEl.textContent = writers.length > 0 ? writers.join(', ') : 'None listed';
                }

                // 10. Build Cast Grid
                const castContainer = document.getElementById('rev-series-cast');
                if (castContainer) {
                    const castArray = draft.cast || [];
                    
                    if (castArray.length > 0) {
                        castContainer.innerHTML = castArray.map(c => {
                            const actorName = c.name || c.actor || 'Unknown Actor';
                            const character = c.character || 'Unknown Role';
                            const image = c.image || 'assets/img/no-actor.jpg';
                            
                            return `
                                <div class="col-md-6 col-lg-4">
                                    <div class="d-flex align-items-center bg-dark p-2 rounded border border-secondary h-100">
                                        <img src="${image}" 
                                             class="rounded-circle me-2" 
                                             style="width: 35px; height: 35px; object-fit: cover; border: 1px solid #444;"
                                             onerror="this.src='assets/img/no-actor.jpg'">
                                        <div class="overflow-hidden">
                                            <div class="small fw-bold text-truncate" title="${actorName}">${actorName}</div>
                                            <div class="extra-small text-white-50 text-truncate" title="${character}">${character}</div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }).join('');
                    } else {
                        castContainer.innerHTML = '<div class="col-12 text-white-50 small">No cast added</div>';
                    }
                }

                // 11. Build Seasons Accordion
                const accordion = document.getElementById('rev-series-accordion');
                if (accordion) {
                    accordion.innerHTML = ''; 

                    const seasons = draft.seasons || [];
                    
                    if (seasons.length > 0) {
                        seasons.forEach((season, index) => {
                            const seasonNumber = season.number || (index + 1);
                            const seasonTitle = season.title || `Season ${seasonNumber}`;
                            
                            // Get episodes for THIS season
                            const episodes = (draft.episodes && draft.episodes[seasonNumber]) ? draft.episodes[seasonNumber] : [];
                            
                            console.log(`Season ${seasonNumber} has ${episodes.length} episodes`);
                            
                            accordion.innerHTML += `
                                <div class="accordion-item bg-dark text-white border-bottom border-secondary">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-dark text-white shadow-none" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#revCollapse${index}">
                                            Season ${seasonNumber}: ${seasonTitle} (${episodes.length} Episodes)
                                        </button>
                                    </h2>
                                    <div id="revCollapse${index}" 
                                         class="accordion-collapse collapse" 
                                         data-bs-parent="#rev-series-accordion">
                                        <div class="accordion-body p-0">
                                            <ul class="list-group list-group-flush">
                                                ${episodes.length > 0 ? episodes.map(ep => `
                                                    <li class="list-group-item bg-dark text-white-50 border-secondary d-flex justify-content-between align-items-center py-2">
                                                        <span class="small">${ep.episode_number}. ${ep.title}</span>
                                                        <span class="badge bg-secondary rounded-pill extra-small">${ep.duration || 0}m</span>
                                                    </li>
                                                `).join('') : '<li class="list-group-item bg-dark text-white-50 border-secondary small">No episodes added for this season.</li>'}
                                            </ul>
                                        </div>
                                    </div>
                                </div>`;
                        });
                    } else {
                        accordion.innerHTML = '<div class="p-3 text-white-50 small">No seasons configured.</div>';
                    }
                }

                console.log('Review modal loaded successfully!');

            } else {
                throw new Error(data.message || 'Failed to load draft');
            }
        } catch (error) {
            console.error('Error in loadDraftData:', error);
            
            Swal.fire({
                icon: 'error',
                title: 'Error Loading Draft',
                text: 'Failed to load series data: ' + error.message,
                background: '#1a1d20',
                color: '#fff',
                confirmButtonColor: '#d33'
            });
        }
    }
}

// ========================================
// 11. SERIES SEARCH MANAGER
// ========================================
class SeriesSearchManager {
    constructor() {
        this.searchForm = document.getElementById('seriesSearchForm');
        this.searchInput = document.getElementById('seriesSearch');
        this.genreFilter = document.getElementById('seriesGenreFilter');
        this.yearFilter = document.getElementById('seriesYearFilter');
        this.timeout = null;

        if (this.searchForm) {
            this.init();
        }
    }

    init() {
        // Real-time search with 500ms delay
        if (this.searchInput) {
            this.searchInput.addEventListener('input', () => {
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => this.searchForm.submit(), 500);
            });
        }

        // Instant filter on dropdown change
        if (this.genreFilter) {
            this.genreFilter.addEventListener('change', () => this.applySeriesFilters());
        }

        if (this.yearFilter) {
            this.yearFilter.addEventListener('change', () => this.applySeriesFilters());
        }
    }

    applySeriesFilters() {
        const genre = this.genreFilter ? this.genreFilter.value : '';
        const year = this.yearFilter ? this.yearFilter.value : '';
        const search = this.searchInput ? this.searchInput.value : '';
        
        let url = '?section=series&page=1';
        
        if (search) {
            url += '&series_search=' + encodeURIComponent(search);
        }
        if (genre) {
            url += '&series_genre=' + encodeURIComponent(genre);
        }
        if (year) {
            url += '&series_year=' + encodeURIComponent(year);
        }
        
        window.location.href = url;
    }

    destroy() {
        clearTimeout(this.timeout);
    }
}

// ========================================
// 12. CONFIRM SERIES SAVE
// ========================================
class ConfirmSeriesSave {
    constructor() {
        this.btn = document.getElementById('publishSeriesBtn');
        if (this.btn) {
            this.init();
        }
    }

    init() {
        this.btn.addEventListener('click', async () => {
            this.btn.disabled = true;
            const spinner = document.getElementById('publishSeriesSpinner');
            if (spinner) spinner.classList.remove('d-none');

            try {
                const response = await fetch('admin-actions/commit-series.php', { method: 'POST' });
                const data = await response.json();

                if (data.status === 'success') {
                    await Swal.fire({
                        title: 'Success!',
                        text: 'Series Published Successfully!',
                        icon: 'success',
                        background: '#1a1d20',
                        color: '#fff',
                        confirmButtonColor: '#0d6efd'
                    });
                    window.location.reload();
                } else {
                    throw new Error(data.error || 'Failed to publish series');
                }
            } catch (error) {
                Swal.fire({
                    title: 'Error!',
                    text: error.message,
                    icon: 'error',
                    background: '#1a1d20',
                    color: '#fff'
                });
            } finally {
                this.btn.disabled = false;
                const spinner = document.getElementById('publishSeriesSpinner');
                if (spinner) spinner.classList.add('d-none');
            }
        });
    }
}

// ========================================
// 13. DRAFT CLEANUP ON MODAL CLOSE
// ========================================
class DraftCleanup {
    constructor() {
        this.movieModal = document.getElementById('addMovieModal');
        this.seriesModal = document.getElementById('addSeriesModal');
        this.isInModalFlow = false;
        
        if (this.movieModal || this.seriesModal) {
            this.init();
        }
    }

    init() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('show.bs.modal', () => {
                this.isInModalFlow = true;
            });
        });

        if (this.movieModal) {
            this.movieModal.addEventListener('hidden.bs.modal', async () => {
                await this.handleModalClose();
            });
        }

        if (this.seriesModal) {
            this.seriesModal.addEventListener('hidden.bs.modal', async () => {
                await this.handleModalClose();
            });
        }
    }

    async handleModalClose() {
        await new Promise(resolve => setTimeout(resolve, 500));
        
        if (!document.querySelector('.modal.show')) {
            this.isInModalFlow = false;
            await this.clearDraft();
        }
    }

    async clearDraft() {
        try {
            await fetch('admin-actions/clear-movie-draft.php');
            await fetch('admin-actions/clear-series-draft.php');

            const forms = [
                'addMovieForm', 'movieGenresForm', 'movieTrailerForm',
                'movieDirectorsForm', 'movieWritersForm', 'movieCastForm',
                'addSeriesForm', 'seriesGenresForm', 'seriesTrailerForm', 
                'seriesSeasonsForm', 'seriesEpisodesForm', 'seriesCastForm',
                'seriesDirectorsForm', 'seriesWritersForm'
            ];

            forms.forEach(id => {
                const form = document.getElementById(id);
                if (form) {
                    form.reset();
                    
                    const overview = form.querySelector('textarea[name="overview"]');
                    if (overview) overview.value = '';
                    
                    form.querySelectorAll('input[type="hidden"]').forEach(hidden => {
                        hidden.value = '';
                    });
                }
            });

            console.log('Draft cleared successfully');
        } catch (error) {
            console.error('Failed to clear draft:', error);
        }
    }
}

// ========================================
// 14. SERIES DYNAMIC ROW MANAGERS
// ========================================
function addCastRow(actor = '', character = '', image = '') {
    const container = document.getElementById('cast-container') || document.getElementById('seriesCastContainer');
    if (!container) {
        console.error('Cast container not found');
        return;
    }
    
    const div = document.createElement('div');
    div.className = 'row g-2 mb-2 cast-row';
    div.innerHTML = `
        <div class="col-md-4">
            <input type="text" name="actors[]" class="form-control bg-dark text-white border-secondary" 
                   placeholder="Actor name" value="${actor}" required>
        </div>
        <div class="col-md-4">
            <input type="text" name="characters[]" class="form-control bg-dark text-white border-secondary" 
                   placeholder="Character name" value="${character}" required>
        </div>
        <div class="col-md-3">
            <input type="text" name="actor_images[]" class="form-control bg-dark text-white border-secondary" 
                   placeholder="Image path" value="${image}">
        </div>
        <div class="col-md-1 d-grid">
            <button type="button" class="btn btn-outline-danger" onclick="removeCastRow(this)">
                <i class="bi bi-x"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
}

function removeCastRow(btn) {
    const container = btn.closest('.cast-row').parentElement;
    const rows = container.querySelectorAll('.cast-row');
    
    if (rows.length > 1) {
        btn.closest('.cast-row').remove();
    } else {
        // Just clear the inputs if it's the last row
        btn.closest('.cast-row').querySelectorAll('input').forEach(input => input.value = '');
    }
}

function addSeasonRow() {
    const container = document.getElementById('seasons-container');
    if (!container) return;

    const rows = container.querySelectorAll('.season-row');
    const currentCount = rows.length;
    
    const div = document.createElement('div');
    div.className = 'row g-2 mb-3 season-row align-items-end';
    div.innerHTML = `
        <div class="col-md-2">
            <label class="form-label small text-white-50">Season #*</label>
            <input type="number" name="season_numbers[]" class="form-control bg-dark text-white border-secondary" 
                   value="${currentCount + 1}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-white-50">Season Title (Optional)</label>
            <input type="text" name="season_titles[]" class="form-control bg-dark text-white border-secondary" 
                   placeholder="e.g. Genesis">
        </div>
        <div class="col-md-3">
            <label class="form-label small text-white-50">Release Year</label>
            <input type="number" name="season_years[]" class="form-control bg-dark text-white border-secondary" 
                   placeholder="2024" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-white-50">Poster Path *</label>
            <input type="text" name="season_posters[]" class="form-control bg-dark text-white border-secondary" 
                   placeholder="assets/img/" required>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-outline-danger remove-season-btn" onclick="removeSeasonRow(this)">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
}

function removeSeasonRow(btn) {
    const container = document.getElementById('seasons-container');
    const rows = container.querySelectorAll('.season-row');
    
    if (rows.length > 1) {
        btn.closest('.season-row').remove();
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Action Denied',
            text: 'You must have at least one season.',
            timer: 2000,
            showConfirmButton: false,
            background: '#1a1d20',
            color: '#fff'
        });
    }
}

function addEpisodeRow() {
    const container = document.getElementById('episodes-container');
    if (!container) return;

    const rows = container.querySelectorAll('.episode-row');
    const currentCount = rows.length;
    
    const div = document.createElement('div');
    div.className = 'episode-row border border-secondary p-3 rounded mb-3 position-relative';
    div.innerHTML = `
        <button type="button" 
                class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 remove-episode-btn" 
                onclick="removeEpisodeRow(this)">
            <i class="bi bi-x"></i>
        </button>
        <div class="row g-3">
            <div class="col-md-2">
                <label class="form-label small text-white-50">Ep #</label>
                <input type="number" name="ep_numbers[]" class="form-control bg-dark text-white border-secondary" 
                       value="${currentCount + 1}" required>
            </div>
            <div class="col-md-7">
                <label class="form-label small text-white-50">Episode Title *</label>
                <input type="text" name="ep_titles[]" class="form-control bg-dark text-white border-secondary" 
                       placeholder="e.g. Pilot" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-white-50">Duration (Min)</label>
                <input type="number" name="ep_durations[]" class="form-control bg-dark text-white border-secondary" 
                       placeholder="45" required>
            </div>
            <div class="col-12">
                <label class="form-label small text-white-50">Episode Synopsis</label>
                <textarea name="ep_overviews[]" class="form-control bg-dark text-white border-secondary" rows="2"></textarea>
            </div>
            <div class="col-md-12">
                <label class="form-label small text-white-50">Still Path (Thumbnail)</label>
                <input type="text" name="ep_stills[]" class="form-control bg-dark text-white border-secondary" 
                       placeholder="assets/img/series/s1e1.jpg">
            </div>
        </div>
    `;
    container.appendChild(div);
}

function removeEpisodeRow(btn) {
    const container = document.getElementById('episodes-container');
    const rows = container.querySelectorAll('.episode-row');
    
    if (rows.length > 1) {
        btn.closest('.episode-row').remove();
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Cannot Remove',
            text: 'At least one episode is required.',
            timer: 2000,
            showConfirmButton: false,
            background: '#1a1d20',
            color: '#fff'
        });
    }
}

// Make functions globally accessible
window.addCastRow = addCastRow;
window.removeCastRow = removeCastRow;
window.addSeasonRow = addSeasonRow;
window.removeSeasonRow = removeSeasonRow;
window.addEpisodeRow = addEpisodeRow;
window.removeEpisodeRow = removeEpisodeRow;


class SeriesDynamicRows {
    constructor() {
        this.init();
    }

    init() {
        // The global functions handle the logic
        // This class now just handles the button clicks via event delegation
        this.setupEventDelegation();
    }

    setupEventDelegation() {
        // Handle add director button
        const addDirBtn = document.getElementById('addSeriesDirectorBtn');
        if (addDirBtn) {
            addDirBtn.addEventListener('click', () => this.addDirectorRow());
        }

        // Handle add writer button
        const addWriterBtn = document.getElementById('addSeriesWriterBtn');
        if (addWriterBtn) {
            addWriterBtn.addEventListener('click', () => this.addWriterRow());
        }

        // Handle add cast button (if there's a specific button with ID)
        const addCastBtn = document.getElementById('addSeriesCastBtn');
        if (addCastBtn) {
            addCastBtn.addEventListener('click', () => window.addCastRow());
        }
    }

    addDirectorRow(name = '') {
        const container = document.getElementById('seriesDirectorsContainer');
        if (!container) return;
        
        const div = document.createElement('div');
        div.className = 'input-group mb-2 series-director-row';
        div.innerHTML = `
            <input type="text" name="directors[]" class="form-control bg-dark text-white border-secondary" 
                   placeholder="Director name" value="${name}" required>
            <button type="button" class="btn btn-outline-danger remove-series-director">
                <i class="bi bi-x"></i>
            </button>
        `;
        container.appendChild(div);
    }

    addWriterRow(name = '') {
        const container = document.getElementById('seriesWritersContainer');
        if (!container) return;
        
        const div = document.createElement('div');
        div.className = 'input-group mb-2 series-writer-row';
        div.innerHTML = `
            <input type="text" name="writers[]" class="form-control bg-dark text-white border-secondary" 
                   placeholder="Writer name" value="${name}" required>
            <button type="button" class="btn btn-outline-danger remove-series-writer">
                <i class="bi bi-x"></i>
            </button>
        `;
        container.appendChild(div);
    }

    removeRow(btn, containerSelector, rowClass, minRows = 1) {
        const container = document.querySelector(containerSelector);
        if (!container) return;

        const rows = container.querySelectorAll(`.${rowClass}`);
        if (rows.length > minRows) {
            btn.closest(`.${rowClass}`).remove();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Cannot Remove',
                text: `At least ${minRows} entry is required.`,
                timer: 2000,
                showConfirmButton: false,
                background: '#1a1d20',
                color: '#fff'
            });
        }
    }
}

// ========================================
// 15. FILTER MANAGER
// ========================================
class FilterManager {
    constructor() {
        this.init();
    }

    init() {
        const genreFilter = document.getElementById('movieGenreFilter');
        const yearFilter = document.getElementById('movieYearFilter');

        if (genreFilter) {
            genreFilter.addEventListener('change', () => this.applyFilters());
        }

        if (yearFilter) {
            yearFilter.addEventListener('change', () => this.applyFilters());
        }
    }

    applyFilters() {
        const genreFilter = document.getElementById('movieGenreFilter');
        const yearFilter = document.getElementById('movieYearFilter');
        const searchInput = document.getElementById('movieSearch');

        if (!genreFilter || !yearFilter || !searchInput) return;

        const genre = genreFilter.value;
        const year = yearFilter.value;
        const search = searchInput.value;
        
        let url = '?section=movies&page=1';
        
        if (search) url += '&search=' + encodeURIComponent(search);
        if (genre) url += '&genre=' + encodeURIComponent(genre);
        if (year) url += '&year=' + encodeURIComponent(year);
        
        window.location.href = url;
    }
}

// ========================================
// 16. REAL-TIME MOVIE SEARCH
// ========================================
class RealTimeMovieSearch {
    constructor() {
        this.searchInput = document.getElementById('movieSearch');
        this.searchForm = document.getElementById('searchForm');
        this.timeout = null;

        if (this.searchInput && this.searchForm) {
            this.init();
        }
    }

    init() {
        this.searchInput.addEventListener('input', () => {
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                this.searchForm.submit();
            }, 500);
        });
    }

    destroy() {
        clearTimeout(this.timeout);
    }
}

// ========================================
// 17. SIMPLE MODAL FORMS
// ========================================
function setupSimpleModalForm(formId, modalId, successMessage) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        alert(successMessage);
        const modalInstance = bootstrap.Modal.getInstance(document.getElementById(modalId));
        if (modalInstance) modalInstance.hide();
        form.reset();
    });
}

// ========================================
// 18. GENRE VALIDATION
// ========================================
class GenreValidation {
    constructor() {
        this.init();
    }

    init() {
        this.setupMovieGenreValidation();
        this.setupSeriesGenreValidation();
    }

    setupMovieGenreValidation() {
        const form = document.getElementById('movieGenresForm');
        if (!form) return;

        form.addEventListener('submit', (e) => {
            const checkboxes = form.querySelectorAll('input[name="genres[]"]');
            const errorMsg = document.getElementById('genre-error');
            const isChecked = Array.from(checkboxes).some(cb => cb.checked);

            if (!isChecked) {
                e.preventDefault();
                e.stopImmediatePropagation();
                
                if (errorMsg) errorMsg.classList.remove('d-none');
                
                const modalContent = form.closest('.modal-content');
                if (modalContent) {
                    modalContent.classList.add('shake-animation');
                    setTimeout(() => modalContent.classList.remove('shake-animation'), 500);
                }
            } else {
                if (errorMsg) errorMsg.classList.add('d-none');
            }
        });
    }

    setupSeriesGenreValidation() {
        const form = document.getElementById('seriesGenresForm');
        if (!form) return;

        form.addEventListener('submit', (e) => {
            const checkboxes = form.querySelectorAll('input[name="genres[]"]');
            const errorMsg = document.getElementById('series-genre-error');
            const isChecked = Array.from(checkboxes).some(cb => cb.checked);

            if (!isChecked) {
                e.preventDefault();
                e.stopImmediatePropagation();
                
                if (errorMsg) errorMsg.classList.remove('d-none');
                
                const modalContent = form.closest('.modal-content');
                if (modalContent) {
                    modalContent.classList.add('shake-animation');
                    setTimeout(() => modalContent.classList.remove('shake-animation'), 500);
                }
            } else {
                if (errorMsg) errorMsg.classList.add('d-none');
            }
        });
    }
}

// ========================================
// 19. UNIVERSAL EVENT DELEGATION
// ========================================
class UniversalEventHandler {
    constructor() {
        this.seriesRows = new SeriesDynamicRows();
        this.init();
    }

    init() {
        document.addEventListener('click', (e) => {
            // Edit Movie - Directors
            if (e.target.closest('#edit_add_director_btn')) {
                addEditDirectorRow('');
            }
            
            // Edit Movie - Writers
            if (e.target.closest('#edit_add_writer_btn')) {
                addEditWriterRow('');
            }
            
            // Edit Movie - Cast
            if (e.target.closest('#edit_add_cast_btn')) {
                addEditCastRow('', '', '');
            }
            
            // Remove Edit Director
            if (e.target.closest('.remove-edit-director')) {
                const container = document.getElementById('edit_directors_container');
                if (container && container.querySelectorAll('.edit-director-row').length > 1) {
                    e.target.closest('.edit-director-row').remove();
                }
            }
            
            // Remove Edit Writer
            if (e.target.closest('.remove-edit-writer')) {
                const container = document.getElementById('edit_writers_container');
                if (container && container.querySelectorAll('.edit-writer-row').length > 1) {
                    e.target.closest('.edit-writer-row').remove();
                }
            }
            
            // Remove Edit Cast
            if (e.target.closest('.remove-edit-cast')) {
                const container = document.getElementById('edit_cast_container');
                if (container && container.querySelectorAll('.edit-cast-row').length > 1) {
                    e.target.closest('.edit-cast-row').remove();
                }
            }

            // Remove Series Director
            if (e.target.closest('.remove-series-director')) {
                this.seriesRows.removeRow(e.target, '#seriesDirectorsContainer', 'series-director-row', 1);
            }
            
            // Remove Series Writer
            if (e.target.closest('.remove-series-writer')) {
                this.seriesRows.removeRow(e.target, '#seriesWritersContainer', 'series-writer-row', 1);
            }
            
            // Remove Series/Movie Cast
            if (e.target.closest('.remove-cast')) {
                const container = e.target.closest('.remove-cast').closest('.row').parentElement;
                if (container && container.querySelectorAll('.cast-row').length > 1) {
                    e.target.closest('.cast-row').remove();
                }
            }

            // Remove Season
            if (e.target.closest('.remove-season-btn')) {
                this.seriesRows.removeRow(e.target, '#seasons-container', 'season-row', 1);
            }

            // Remove Episode
            if (e.target.closest('.remove-episode-btn')) {
                this.seriesRows.removeRow(e.target, '#episodes-container', 'episode-row', 1);
            }
        });
    }
}

// reset draft
class SeriesDraftResetter {
    constructor() {
        this.addSeriesModal = document.getElementById('addSeriesModal');
        this.isNewSession = true;
        
        if (this.addSeriesModal) {
            this.init();
        }
    }

    init() {
        // When the FIRST modal opens
        this.addSeriesModal.addEventListener('show.bs.modal', async () => {
            if (this.isNewSession) {
                try {
                    // Clear server-side draft
                    await fetch('admin-actions/clear-series-draft.php', { method: 'POST' });
                    
                    // Reset ALL series forms
                    this.resetAllForms();
                    
                    console.log('Series draft cleared - fresh start');
                } catch (error) {
                    console.error('Failed to clear draft:', error);
                }
                
                this.isNewSession = false;
            } else {
                console.log('Returning to first modal - keeping draft data');
            }
        });
        
        // Reset the flag when modal is fully closed
        this.addSeriesModal.addEventListener('hidden.bs.modal', () => {
            setTimeout(() => {
                if (!document.querySelector('.modal.show')) {
                    this.isNewSession = true;
                    console.log('Workflow ended - next open will be fresh');
                }
            }, 500);
        });
    }

    resetAllForms() {
        // List of all series form IDs
        const formIds = [
            'addSeriesForm',
            'seriesGenresForm', 
            'seriesTrailerForm',
            'seriesSeasonsForm',
            'seriesEpisodesForm',
            'seriesCastForm',
            'seriesDirectorsForm',
            'seriesWritersForm'
        ];

        formIds.forEach(formId => {
            const form = document.getElementById(formId);
            if (form) {
                form.reset();
            }
        });

        // Clear dynamic containers
        this.clearDynamicContainers();
    }

    clearDynamicContainers() {
        // Clear and reset cast container
        const castContainer = document.getElementById('cast-container') || 
                             document.getElementById('seriesCastContainer');
        if (castContainer) {
            castContainer.innerHTML = `
                <div class="row g-2 mb-2 cast-row">
                    <div class="col-md-4">
                        <input type="text" name="actors[]" class="form-control bg-dark text-white border-secondary" 
                               placeholder="Actor name" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="characters[]" class="form-control bg-dark text-white border-secondary" 
                               placeholder="Character name" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="actor_images[]" class="form-control bg-dark text-white border-secondary" 
                               placeholder="Image path">
                    </div>
                    <div class="col-md-1 d-grid">
                        <button type="button" class="btn btn-outline-danger" onclick="removeCastRow(this)">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            `;
        }

        // Clear and reset directors container
        const directorsContainer = document.getElementById('seriesDirectorsContainer');
        if (directorsContainer) {
            directorsContainer.innerHTML = `
                <div class="input-group mb-2 series-director-row">
                    <input type="text" name="directors[]" class="form-control bg-dark text-white border-secondary" 
                           placeholder="Director name" required>
                    <button type="button" class="btn btn-outline-danger remove-series-director">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            `;
        }

        // Clear and reset writers container
        const writersContainer = document.getElementById('seriesWritersContainer');
        if (writersContainer) {
            writersContainer.innerHTML = `
                <div class="input-group mb-2 series-writer-row">
                    <input type="text" name="writers[]" class="form-control bg-dark text-white border-secondary" 
                           placeholder="Writer name" required>
                    <button type="button" class="btn btn-outline-danger remove-series-writer">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            `;
        }

        // Clear seasons container
        const seasonsContainer = document.getElementById('seasons-container');
        if (seasonsContainer) {
            seasonsContainer.innerHTML = `
                <div class="row g-2 mb-3 season-row align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small text-white-50">Season #*</label>
                        <input type="number" name="season_numbers[]" class="form-control bg-dark text-white border-secondary" value="1" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-white-50">Season Title (Optional)</label>
                        <input type="text" name="season_titles[]" class="form-control bg-dark text-white border-secondary" placeholder="e.g. Genesis">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-white-50">Release Year</label>
                        <input type="number" name="season_years[]" class="form-control bg-dark text-white border-secondary" placeholder="2024" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-white-50">Poster Path *</label>
                        <input type="text" name="season_posters[]" class="form-control bg-dark text-white border-secondary" placeholder="assets/img/" required>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger remove-season-btn d-none" onclick="removeSeasonRow(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        }

        // Clear episodes container
        const episodesContainer = document.getElementById('episodes-container');
        if (episodesContainer) {
            episodesContainer.innerHTML = `
                <div class="episode-row border border-secondary p-3 rounded mb-3 position-relative">
                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 remove-episode-btn d-none" onclick="removeEpisodeRow(this)">
                        <i class="bi bi-x"></i>
                    </button>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label small text-white-50">Ep #</label>
                            <input type="number" name="ep_numbers[]" class="form-control bg-dark text-white border-secondary" value="1" required>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small text-white-50">Episode Title *</label>
                            <input type="text" name="ep_titles[]" class="form-control bg-dark text-white border-secondary" placeholder="e.g. Pilot" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-white-50">Duration (Min)</label>
                            <input type="number" name="ep_durations[]" class="form-control bg-dark text-white border-secondary" placeholder="45">
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-white-50">Episode Synopsis</label>
                            <textarea name="ep_overviews[]" class="form-control bg-dark text-white border-secondary" rows="2"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small text-white-50">Still Path (Thumbnail)</label>
                            <input type="text" name="ep_stills[]" class="form-control bg-dark text-white border-secondary" placeholder="assets/img/series/s1e1.jpg">
                        </div>
                    </div>
                </div>
            `;
        }

        console.log('All dynamic containers cleared');
    }
}


// ========================================
// 20. INITIALIZE EVERYTHING
// ========================================
document.addEventListener('DOMContentLoaded', () => {
    // Core Navigation & Search
    new SectionManager();
    new MobileSidebar();
    new SearchManager([
        { inputId: 'movieSearch', tableBodyId: 'moviesTableBody' },
        { inputId: 'seriesSearch', tableBodyId: 'seriesTableBody' },
        { inputId: 'userSearch', tableBodyId: 'usersTableBody' },
        { inputId: 'reviewSearch', tableBodyId: 'reviewsTableBody' }
    ]);
    new SeriesDraftResetter();

    // Multi-step modals
    new MultiStepModalHandler([
        // Movie Modals
        { formId: 'addMovieForm', currentModalId: 'addMovieModal', nextModalId: 'addMovieGenresModal' },
        { formId: 'movieGenresForm', currentModalId: 'addMovieGenresModal', nextModalId: 'addMovieTrailerModal' },
        { formId: 'movieTrailerForm', currentModalId: 'addMovieTrailerModal', nextModalId: 'addMovieDirectorsModal' },
        { formId: 'movieDirectorsForm', currentModalId: 'addMovieDirectorsModal', nextModalId: 'addMovieWritersModal' },
        { formId: 'movieWritersForm', currentModalId: 'addMovieWritersModal', nextModalId: 'addMovieCastModal' },
        { formId: 'movieCastForm', currentModalId: 'addMovieCastModal', nextModalId: 'reviewMovieDraftModal' },

        // Series Modals
        { formId: 'addSeriesForm', currentModalId: 'addSeriesModal', nextModalId: 'addSeriesGenresModal' },
        { formId: 'seriesGenresForm', currentModalId: 'addSeriesGenresModal', nextModalId: 'addSeriesTrailerModal' },
        { formId: 'seriesTrailerForm', currentModalId: 'addSeriesTrailerModal', nextModalId: 'addSeriesSeasonsModal' },
        { formId: 'seriesSeasonsForm', currentModalId: 'addSeriesSeasonsModal', nextModalId: 'addSeriesEpisodesModal' },
        { formId: 'seriesEpisodesForm', currentModalId: 'addSeriesEpisodesModal', nextModalId: 'addSeriesCastModal' },
        { formId: 'seriesCastForm', currentModalId: 'addSeriesCastModal', nextModalId: 'addSeriesDirectorsModal' },
        { formId: 'seriesDirectorsForm', currentModalId: 'addSeriesDirectorsModal', nextModalId: 'addSeriesWritersModal' },
        { formId: 'seriesWritersForm', currentModalId: 'addSeriesWritersModal', nextModalId: 'reviewSeriesModal' }
    ]);

    // Dynamic lists for Movies
    const directorTpl = `<div class="input-group mb-2 director-row">
        <input type="text" name="directors[]" class="form-control bg-dark text-white border-secondary" placeholder="Director name" required>
        <button type="button" class="btn btn-outline-danger"><i class="bi bi-x"></i></button>
    </div>`;

    const writerTpl = `<div class="input-group mb-2 writer-row">
        <input type="text" name="writers[]" class="form-control bg-dark text-white border-secondary" placeholder="Writer name" required>
        <button type="button" class="btn btn-outline-danger"><i class="bi bi-x"></i></button>
    </div>`;

    const castTpl = `<div class="row g-2 mb-2 cast-row">
        <div class="col-md-4"><input type="text" name="actors[]" class="form-control bg-dark text-white border-secondary" placeholder="Actor" required></div>
        <div class="col-md-4"><input type="text" name="characters[]" class="form-control bg-dark text-white border-secondary" placeholder="Character" required></div>
        <div class="col-md-3"><input type="text" name="actor_images[]" class="form-control bg-dark text-white border-secondary" placeholder="Image Path"></div>
        <div class="col-md-1 d-grid"><button type="button" class="btn btn-outline-danger"><i class="bi bi-x"></i></button></div>
    </div>`;

    new DynamicListManager([
        { btnId: 'addDirectorBtn', containerId: 'directorsContainer', rowClass: 'director-row', template: directorTpl },
        { btnId: 'addWriterBtn', containerId: 'writersContainer', rowClass: 'writer-row', template: writerTpl },
        { btnId: 'addCastBtn', containerId: 'castContainer', rowClass: 'cast-row', template: castTpl }
    ]);

    // Draft management
    new DraftReviewModal();
    new ConfirmMovieSave();
    new SeriesReviewManager();
    new ConfirmSeriesSave();
    new DraftCleanup();

    // Series functionality
    new SeriesDynamicRows();
    new SeriesSearchManager();

    // Filters and search
    new FilterManager();
    new RealTimeMovieSearch();

    // Genre validation
    new GenreValidation();

    // Universal event handler
    new UniversalEventHandler();

    // Simple modal forms
    setupSimpleModalForm('editSeriesForm', 'editSeriesModal', 'Series updated successfully!');
    setupSimpleModalForm('addUserForm', 'addUserModal', 'User added successfully!');
    setupSimpleModalForm('editUserForm', 'editUserModal', 'User updated successfully!');

    // Initialize edit form handler
    const editFormElement = document.getElementById('editMovieForm');
    if (editFormElement) {
        editFormElement.addEventListener('submit', handleEditFormSubmit);
    }

    // Bootstrap carousel
    const carouselEl = document.getElementById('featuredSlider');
    if (carouselEl && typeof bootstrap !== 'undefined') {
        const carousel = new bootstrap.Carousel(carouselEl, {
            interval: 4000,
            ride: 'carousel',
            pause: 'hover'
        });
        carousel.cycle();
    }

    // Bootstrap tooltips
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
    }

    console.log('Admin Dashboard initialized successfully');
});