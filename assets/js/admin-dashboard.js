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
                // If no data-section attribute, let the link work normally
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
        // Send POST request to the backend script
        const res = await fetch(`admin-actions/toggle-featured.php?id=${movieId}`, { 
            method: 'POST' 
        });
        
        if (!res.ok) throw new Error('Server error');
        
        const data = await res.json();
        
        if (data.status === 'success') {
            // 1. Target the button using the movieId
            const btn = document.querySelector(`[onclick="toggleFeatured(${movieId})"]`);
            
            if (btn) {
                const icon = btn.querySelector('i');
                
                if (data.new_state) {
                    // 2. Change to Featured State (Yellow + Filled Lightning)
                    btn.classList.remove('btn-outline-secondary');
                    btn.classList.add('btn-warning');
                    
                    icon.classList.remove('bi-lightning');
                    icon.classList.add('bi-lightning-fill');
                } else {
                    // 3. Change to Normal State (Gray + Outline Lightning)
                    btn.classList.remove('btn-warning');
                    btn.classList.add('btn-outline-secondary');
                    
                    icon.classList.remove('bi-lightning-fill');
                    icon.classList.add('bi-lightning');
                }
            }

            // 4. Show toast notification using SweetAlert
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

// Make it globally accessible
window.toggleFeatured = toggleFeatured;

// ========================================
// 6. MULTI-STEP MODAL HANDLER (Improved)
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
            
            // Disable button and show loading
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
                    // Show success state
                    if (submitBtn) {
                        submitBtn.classList.replace('btn-primary', 'btn-success');
                        submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Saved!';
                    }

                    // Wait briefly, then transition to next modal
                    await new Promise(resolve => setTimeout(resolve, 600));
                    
                    const currentModal = bootstrap.Modal.getInstance(document.getElementById(currentModalId));
                    const nextModal = new bootstrap.Modal(document.getElementById(nextModalId));
                    
                    currentModal.hide();
                    nextModal.show();
                    
                    // Reset button
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
                    text: error.message
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
                        showConfirmButton: false
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

            // Show loading
            Swal.fire({
                title: 'Saving...',
                allowOutsideClick: false,
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
                        confirmButtonColor: '#0d6efd'
                    });

                    // Clear draft and reload
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
                    icon: 'error'
                });
            }
        });
    }
}

// ========================================
// 10. DRAFT CLEANUP ON MODAL CLOSE
// ========================================
class DraftCleanup {
    constructor() {
        this.mainModal = document.getElementById('addMovieModal');
        this.isInModalFlow = false;
        
        if (this.mainModal) {
            this.init();
        }
    }

    init() {
        // Track when we're in the modal flow
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('show.bs.modal', () => {
                this.isInModalFlow = true;
            });
        });

        // Only clear when truly exiting
        this.mainModal.addEventListener('hidden.bs.modal', async () => {
            // Wait for modal transitions to complete
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // If no other modal is showing, we've exited the flow
            if (!document.querySelector('.modal.show')) {
                this.isInModalFlow = false;
                await this.clearDraft();
            }
        });
    }

    async clearDraft() {
        try {
            // Clear server-side draft
            await fetch('admin-actions/clear-movie-draft.php');

            // Reset all forms
            const forms = [
                'addMovieForm', 'movieGenresForm', 'movieTrailerForm',
                'movieDirectorsForm', 'movieWritersForm', 'movieCastForm'
            ];

            forms.forEach(id => {
                const form = document.getElementById(id);
                if (form) {
                    form.reset();
                    
                    // Clear textareas
                    const overview = form.querySelector('textarea[name="overview"]');
                    if (overview) overview.value = '';
                    
                    // Clear hidden fields
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
// 11. SIMPLE MODAL FORMS
// ========================================
function setupSimpleModalForm(formId, modalId, successMessage) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        alert(successMessage);
        bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
        form.reset();
    });
}

// ========================================
// 12. INITIALIZE EVERYTHING
// ========================================
document.addEventListener('DOMContentLoaded', () => {
    // Core Navigation
    new SectionManager();
    new MobileSidebar();

    // Search with explicit table body IDs
    new SearchManager([
        { inputId: 'movieSearch', tableBodyId: 'moviesTableBody' },
        { inputId: 'seriesSearch', tableBodyId: 'seriesTableBody' },
        { inputId: 'userSearch', tableBodyId: 'usersTableBody' },
        { inputId: 'reviewSearch', tableBodyId: 'reviewsTableBody' }
    ]);

    // Multi-step modal flow
    new MultiStepModalHandler([
        { formId: 'addMovieForm', currentModalId: 'addMovieModal', nextModalId: 'addMovieGenresModal' },
        { formId: 'movieGenresForm', currentModalId: 'addMovieGenresModal', nextModalId: 'addMovieTrailerModal' },
        { formId: 'movieTrailerForm', currentModalId: 'addMovieTrailerModal', nextModalId: 'addMovieDirectorsModal' },
        { formId: 'movieDirectorsForm', currentModalId: 'addMovieDirectorsModal', nextModalId: 'addMovieWritersModal' },
        { formId: 'movieWritersForm', currentModalId: 'addMovieWritersModal', nextModalId: 'addMovieCastModal' },
        { formId: 'movieCastForm', currentModalId: 'addMovieCastModal', nextModalId: 'reviewMovieDraftModal' }
    ]);

    // Dynamic lists
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

    // Draft review and save
    new DraftReviewModal();
    new ConfirmMovieSave();
    new DraftCleanup();

    // Simple modal forms
    // setupSimpleModalForm('editMovieForm', 'editMovieModal', 'Movie updated successfully!');
    setupSimpleModalForm('addSeriesForm', 'addSeriesModal', 'Series added successfully!');
    setupSimpleModalForm('editSeriesForm', 'editSeriesModal', 'Series updated successfully!');
    setupSimpleModalForm('addUserForm', 'addUserModal', 'User added successfully!');
    setupSimpleModalForm('editUserForm', 'editUserModal', 'User updated successfully!');

    // Filter handlers
    document.getElementById('movieGenreFilter')?.addEventListener('change', (e) => {
        console.log('Filtering by genre:', e.target.value);
    });

    document.getElementById('movieYearFilter')?.addEventListener('change', (e) => {
        console.log('Filtering by year:', e.target.value);
    });

    // Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

    // Initialize edit form handler
    const editFormElement = document.getElementById('editMovieForm');
    if (editFormElement) {
        editFormElement.addEventListener('submit', handleEditFormSubmit);
    }

    document.addEventListener('click', function(e) {
        // Add director
        if (e.target.id === 'edit_add_director_btn' || e.target.closest('#edit_add_director_btn')) {
            addEditDirectorRow('');
        }
        
        // Add writer
        if (e.target.id === 'edit_add_writer_btn' || e.target.closest('#edit_add_writer_btn')) {
            addEditWriterRow('');
        }
        
        // Add cast
        if (e.target.id === 'edit_add_cast_btn' || e.target.closest('#edit_add_cast_btn')) {
            addEditCastRow('', '', '');
        }
        
        // Remove director
        if (e.target.closest('.remove-edit-director')) {
            const container = document.getElementById('edit_directors_container');
            if (container.querySelectorAll('.edit-director-row').length > 1) {
                e.target.closest('.edit-director-row').remove();
            }
        }
        
        // Remove writer
        if (e.target.closest('.remove-edit-writer')) {
            const container = document.getElementById('edit_writers_container');
            if (container.querySelectorAll('.edit-writer-row').length > 1) {
                e.target.closest('.edit-writer-row').remove();
            }
        }
        
        // Remove cast
        if (e.target.closest('.remove-edit-cast')) {
            const container = document.getElementById('edit_cast_container');
            if (container.querySelectorAll('.edit-cast-row').length > 1) {
                e.target.closest('.edit-cast-row').remove();
            }
        }
    });

});

// for genre
document.getElementById('movieGenresForm').addEventListener('submit', function(e) {
    // Get all checkboxes inside the form
    const checkboxes = this.querySelectorAll('input[name="genres[]"]');
    const errorMsg = document.getElementById('genre-error');
    
    // Check if at least one is checked
    const isChecked = Array.from(checkboxes).some(cb => cb.checked);

    if (!isChecked) {
        // Stop the form from submitting
        e.preventDefault();
        
        // Show the error message
        errorMsg.classList.remove('d-none');
        
        // Optional: shake the modal to grab attention
        this.closest('.modal-content').classList.add('shake-animation');
        setTimeout(() => {
            this.closest('.modal-content').classList.remove('shake-animation');
        }, 500);
    } else {
        // Hide error message if they finally picked one
        errorMsg.classList.add('d-none');
    }
});

// Real-time search functionality
let searchTimeout;
const movieSearchInput = document.getElementById('movieSearch');

if (movieSearchInput) {
    movieSearchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            document.getElementById('searchForm').submit();
        }, 500); // Wait 500ms after user stops typing
    });
}

// Apply filters function
function applyFilters() {
    const genre = document.getElementById('movieGenreFilter').value;
    const year = document.getElementById('movieYearFilter').value;
    const search = document.getElementById('movieSearch').value;
    
    let url = '?section=movies&page=1';
    
    if (search) {
        url += '&search=' + encodeURIComponent(search);
    }
    if (genre) {
        url += '&genre=' + encodeURIComponent(genre);
    }
    if (year) {
        url += '&year=' + encodeURIComponent(year);
    }
    
    window.location.href = url;
}

document.addEventListener('DOMContentLoaded', function() {
    // Genre filter
    const genreFilter = document.getElementById('movieGenreFilter');
    if (genreFilter) {
        genreFilter.addEventListener('change', applyFilters);
    }
    
    // Year filter
    const yearFilter = document.getElementById('movieYearFilter');
    if (yearFilter) {
        yearFilter.addEventListener('change', applyFilters);
    }
    
    // Start carousel immediately on page load
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