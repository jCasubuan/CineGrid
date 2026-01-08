// Section Navigation
const menuLinks = document.querySelectorAll('.sidebar-menu-link');
const sections = document.querySelectorAll('.content-section');

menuLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        const sectionName = link.getAttribute('data-section');
        if (!sectionName) return;

        e.preventDefault();

        // Update active link
        menuLinks.forEach(l => l.classList.remove('active'));
        link.classList.add('active');

        // Show selected section
        sections.forEach(section => {
            section.style.display = 'none';
        });
        document.getElementById(sectionName + 'Section').style.display = 'block';
    });
});

// mobile Toggle
const mobileToggle = document.getElementById('mobileToggle');
const sidebar = document.getElementById('sidebar');

mobileToggle?.addEventListener('click', () => {
    sidebar.classList.toggle('show');
});

// Close sidebar when clicking outside (mobile)
document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768 &&
        !sidebar.contains(e.target) &&
        !mobileToggle.contains(e.target) &&
        sidebar.classList.contains('show')) {
        sidebar.classList.remove('show');
    }
});

// Search Functionality
function setupSearch(inputId, tableBodyId) {
    const searchInput = document.getElementById(inputId);
    const tableBody = document.getElementById(tableBodyId) || document.querySelector(`#${inputId.replace('Search', 'Section')} tbody`);

    if (!searchInput || !tableBody) return;

    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const rows = tableBody.querySelectorAll('tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
}

setupSearch('movieSearch', 'moviesTableBody');
setupSearch('seriesSearch');
setupSearch('userSearch');
setupSearch('reviewSearch');

// CRUD Functions
function viewMovie(id) {
    alert(`Viewing movie with ID: ${id}\nThis would open a detailed view or redirect to movie-details.html`);
}

function deleteMovie(id) {
    if (confirm(`Are you sure you want to delete this movie?`)) {
        alert(`Movie ${id} deleted!\nIn production, this would make an API call to delete the movie.`);
        // Reload table or remove row
    }
}

function viewSeries(id) {
    alert(`Viewing series with ID: ${id}`);
}

function deleteSeries(id) {
    if (confirm(`Are you sure you want to delete this series?`)) {
        alert(`Series ${id} deleted!`);
    }
}

function viewUser(id) {
    alert(`Viewing user with ID: ${id}`);
}

function deleteUser(id) {
    if (confirm(`Are you sure you want to delete this user?`)) {
        alert(`User ${id} deleted!`);
    }
}

function viewReview(id) {
    alert(`Viewing review with ID: ${id}`);
}

function deleteReview(id) {
    if (confirm(`Are you sure you want to delete this review?`)) {
        alert(`Review ${id} deleted!`);
    }
}

// Form Submissions
// add movie (STEP 1)
document.addEventListener('DOMContentLoaded', function () {
    const addMovieForm = document.getElementById('addMovieForm');
    const addMovieBtn = document.getElementById('addMovieBtn');
    const addMovieSpinner = document.getElementById('addMovieSpinner');
    const addMovieBtnText = document.getElementById('addMovieBtnText');
    const addMovieAlert = document.getElementById('addMovieAlert');
    const addMovieAlertMessage = document.getElementById('addMovieAlertMessage');

    if (!addMovieForm) return;

    addMovieForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(addMovieForm);

        const durationValue = formData.get('duration');
        if (!durationValue || durationValue <= 0) {
            showAlert('Please enter a valid movie duration (minutes).', 'danger');
            return;
        }

        // Validation for new fields: Language and Content Rating
        if (!formData.get('language') || !formData.get('content_rating')) {
            showAlert('Please select a Language and Content Rating.', 'danger');
            return;
        }

        // Basic validation for existing fields
        if (
            !formData.get('title')?.trim() ||
            !formData.get('overview')?.trim() ||
            !formData.get('release_year') ||
            !formData.get('duration')
        ) {
            showAlert('Please fill in all required fields.', 'danger');
            return;
        }

        // Loading state
        addMovieBtn.disabled = true;
        addMovieSpinner.classList.remove('d-none');
        addMovieBtnText.textContent = 'Saving...';

        fetch('admin-actions/save-movie-basic.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                showAlert(data.error, 'danger');
                resetBtn();
                return;
            }

            // STEP 1 SUCCESS → move to STEP 2
            bootstrap.Modal.getInstance(
                document.getElementById('addMovieModal')
            ).hide();

            new bootstrap.Modal(
                document.getElementById('addMovieGenresModal')
            ).show();

            addMovieForm.reset();
            resetBtn();
        })
        .catch(() => {
            showAlert('Unexpected error occurred.', 'danger');
            resetBtn();
        });
    });

    function showAlert(message, type) {
        addMovieAlert.className = `alert alert-${type} alert-dismissible fade show`;
        addMovieAlertMessage.textContent = message;
    }

    function resetBtn() {
        addMovieBtn.disabled = false;
        addMovieSpinner.classList.add('d-none');
        addMovieBtnText.textContent = 'Add Movie';
    }
});

// edit movie
document.getElementById('editMovieForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Movie updated successfully!');
    bootstrap.Modal.getInstance(document.getElementById('editMovieModal')).hide();
});

// add series
document.getElementById('addSeriesForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Series added successfully!');
    bootstrap.Modal.getInstance(document.getElementById('addSeriesModal')).hide();
    e.target.reset();
});

// edit series
document.getElementById('editSeriesForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Series updated successfully!');
    bootstrap.Modal.getInstance(document.getElementById('editSeriesModal')).hide();
});

// add users
document.getElementById('addUserForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    alert('User added successfully!');
    bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
    e.target.reset();
});

// edit users
document.getElementById('editUserForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    alert('User updated successfully!');
    bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
});

// Filter Functions
document.getElementById('movieGenreFilter')?.addEventListener('change', (e) => {
    console.log('Filtering by genre:', e.target.value);
    // Implement filter logic here
});

document.getElementById('movieYearFilter')?.addEventListener('change', (e) => {
    console.log('Filtering by year:', e.target.value);
    // Implement filter logic here
});

// Initialize tooltips (if using Bootstrap tooltips)
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// add genres
document.addEventListener('DOMContentLoaded', () => {
    const genreForm = document.getElementById('movieGenresForm');
    const genreAlert = document.createElement('div'); // Creating alert dynamically if not in HTML
    genreAlert.id = 'genreAlert';
    
    if (!genreForm) return;

    // Inject alert at top of form if it doesn't exist
    genreForm.prepend(genreAlert);
    genreAlert.className = 'alert d-none';

    genreForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData(genreForm);
        
        // Validation: Check if at least one genre is selected
        if (!formData.has('genres[]')) {
            showGenreAlert('Please select at least one genre.', 'danger');
            return;
        }

        fetch('admin-actions/save-movie-genres.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                showGenreAlert(data.error, 'danger');
                return;
            }

            showGenreAlert('Genres saved successfully.', 'success');

            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById('addMovieGenresModal')
                );
                modal.hide();

                // Move to Trailer Modal
                new bootstrap.Modal(
                    document.getElementById('addMovieTrailerModal')
                ).show();

            }, 800);
        })
        .catch(() => {
            showGenreAlert('Unexpected error occurred.', 'danger');
        });
    });

    function showGenreAlert(message, type) {
        genreAlert.className = `alert alert-${type}`;
        genreAlert.textContent = message;
        genreAlert.classList.remove('d-none');
    }
});

// add trailer
document.addEventListener('DOMContentLoaded', () => {
    const trailerForm = document.getElementById('movieTrailerForm');
    const trailerAlert = document.getElementById('trailerAlert');

    if (!trailerForm) return;

    trailerForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData(trailerForm);

        fetch('admin-actions/save-movie-trailer.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                showTrailerAlert(data.error, 'danger');
                return;
            }

            showTrailerAlert('Trailer saved successfully.', 'success');

            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById('addMovieTrailerModal')
                );
                modal.hide();

                new bootstrap.Modal(
                    document.getElementById('addMovieDirectorsModal')
                ).show();

            }, 800);
        })
        .catch(() => {
            showTrailerAlert('Unexpected error occurred.', 'danger');
        });
    });

    function showTrailerAlert(message, type) {
        trailerAlert.className = `alert alert-${type}`;
        trailerAlert.textContent = message;
        trailerAlert.classList.remove('d-none');
    }
});

// add directors
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('movieDirectorsForm');
    const container = document.getElementById('directorsContainer');
    const alertBox = document.getElementById('directorsAlert');
    const addBtn = document.getElementById('addDirectorBtn');

    if (!form) return;

    addBtn.addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'input-group mb-2 director-row';
        row.innerHTML = `
            <input type="text" name="directors[]" class="form-control" placeholder="Director name" required>
            <button type="button" class="btn btn-outline-danger remove-director">
                <i class="bi bi-x"></i>
            </button>
        `;
        container.appendChild(row);
    });

    container.addEventListener('click', (e) => {
        if (e.target.closest('.remove-director')) {
            e.target.closest('.director-row').remove();
        }
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        fetch('admin-actions/save-movie-directors.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                showAlert(data.error, 'danger');
                return;
            }

            showAlert('Directors saved successfully.', 'success');

            setTimeout(() => {
                const directorsModal = bootstrap.Modal.getInstance(
                    document.getElementById('addMovieDirectorsModal')
                );
                directorsModal.hide();

                const castModal = new bootstrap.Modal(
                    document.getElementById('addMovieWritersModal')
                );
                castModal.show();
            }, 800);

        })
        .catch(() => {
            showAlert('Unexpected error occurred.', 'danger');
        });
    });

    function showAlert(message, type) {
        alertBox.className = `alert alert-${type}`;
        alertBox.textContent = message;
        alertBox.classList.remove('d-none');
    }
});

// add writers
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('movieWritersForm');
    const container = document.getElementById('writersContainer');
    const alertBox = document.getElementById('writersAlert');
    const addBtn = document.getElementById('addWriterBtn');

    if (!form) return;

    addBtn.addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'input-group mb-2 writer-row';
        row.innerHTML = `
            <input type="text" name="writers[]" class="form-control bg-dark text-white border-secondary" placeholder="Writer name" required>
            <button type="button" class="btn btn-outline-danger remove-writer">
                <i class="bi bi-x"></i>
            </button>
        `;
        container.appendChild(row);
    });

    container.addEventListener('click', (e) => {
        if (e.target.closest('.remove-writer')) {
            e.target.closest('.writer-row').remove();
        }
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(form);

        fetch('admin-actions/save-movie-writers.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                showAlert(data.error, 'danger');
                return;
            }
            showAlert('Writers saved successfully!', 'success');

            setTimeout(() => {
                const writersModal = bootstrap.Modal.getInstance(
                    document.getElementById('addMovieWritersModal')
                );
                writersModal.hide();

                const castModal = new bootstrap.Modal(
                    document.getElementById('addMovieCastModal')
                );
                castModal.show();
            }, 800);
        })
        .catch(() => showAlert('Unexpected error occurred.', 'danger'));
    });

    function showAlert(message, type) {
        alertBox.className = `alert alert-${type}`;
        alertBox.textContent = message;
        alertBox.classList.remove('d-none');
    }
});

// add cast
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('movieCastForm');
    const container = document.getElementById('castContainer');
    const alertBox = document.getElementById('castAlert');
    const addBtn = document.getElementById('addCastBtn');

    if (!form) return;

    addBtn.addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 cast-row border-bottom border-secondary pb-2';
        row.innerHTML = `
            <div class="col-md-4">
                <input type="text" name="actors[]" class="form-control" placeholder="Actor Name" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="characters[]" class="form-control" placeholder="Character Name" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="actor_images[]" class="form-control" placeholder="Actor Image URL">
            </div>
            <div class="col-md-1 d-grid">
                <button type="button" class="btn btn-outline-danger remove-cast">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
    });

    container.addEventListener('click', (e) => {
        if (e.target.closest('.remove-cast')) {
            e.target.closest('.cast-row').remove();
        }
    });

    form.addEventListener('submit', (e) => {
    e.preventDefault();

    const formData = new FormData(form);

    fetch('admin-actions/save-movie-cast.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            showAlert(data.error, 'danger');
            return;
        }

        if (data.status === 'ok') {
            // Close Cast modal
            const castModal = bootstrap.Modal.getInstance(
                document.getElementById('addMovieCastModal')
            );
            castModal.hide();

            // Open Review modal
            const reviewModal = new bootstrap.Modal(
                document.getElementById('reviewMovieDraftModal')
            );
            reviewModal.show();
        }
    })
    .catch(() => {
        showAlert('Unexpected error occurred.', 'danger');
    });
});

    function showAlert(message, type) {
        alertBox.className = `alert alert-${type}`;
        alertBox.textContent = message;
        alertBox.classList.remove('d-none');
    }
});

// movie draft
document.addEventListener('DOMContentLoaded', () => {
    const reviewModal = document.getElementById('reviewMovieDraftModal');
    const reviewContent = document.getElementById('reviewContent');
    const reviewAlert = document.getElementById('reviewAlert');

    if (!reviewModal) return;

    reviewModal.addEventListener('show.bs.modal', () => {
        fetch('admin-actions/get-movie-draft.php')
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    showAlert(data.error, 'danger');
                    return;
                }
                renderDraft(data);
            })
            .catch(() => {
                showAlert('Failed to load draft.', 'danger');
            });
    });

    function renderDraft(draft) {
        let html = '';

        /* 1. VISUAL PREVIEW (Poster & Backdrop) */
        html += section('Visuals', `
            <div class="row g-2">
                <div class="col-md-8">
                    <small class="text-muted d-block mb-1">Backdrop Preview:</small>
                    <img src="${draft.basic.backdrop_path || 'assets/img/default-backdrop.jpg'}" 
                        class="img-fluid rounded border border-secondary" 
                        style="height: 150px; width: 100%; object-fit: cover;">
                </div>
                <div class="col-md-4 text-center">
                    <small class="text-muted d-block mb-1">Poster:</small>
                    <img src="${draft.basic.poster_path || 'assets/img/default-poster.jpg'}" 
                        class="img-thumbnail" 
                        style="height: 150px; width: 100px; object-fit: cover;">
                </div>
            </div>
        `);

        /* 2. BASIC INFORMATION */
        html += section('Basic Information', `
            <div class="row">
                <div class="col-12">
                    <h4 class="text-primary mb-1">${escapeHtml(draft.basic.title)}</h4>
                    <div class="mb-2">
                        <span class="badge bg-info text-dark">${draft.basic.type.toUpperCase()}</span>
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
                        <small class="text-white-50">${escapeHtml(draft.basic.overview)}</small>
                    </div>
                </div>
            </div>
        `);

        /* 3. CREW (Directors & Writers) */
        html += section('Crew', `
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

        /* 4. CAST */
        html += section('Cast', `
            <div class="row row-cols-2 row-cols-md-4 g-2">
                ${draft.cast.map(c => `
                    <div class="col">
                        <div class="d-flex align-items-center p-1 bg-dark rounded border border-secondary">
                            <img src="${c.image || 'assets/img/default-actor.jpg'}" 
                                class="rounded-circle me-2" 
                                style="width: 35px; height: 35px; object-fit: cover; border: 1px solid #444;">
                            <div style="font-size: 0.7rem;" class="text-truncate">
                                <div class="fw-bold text-white">${escapeHtml(c.actor)}</div>
                                <div class="text-muted">${escapeHtml(c.character)}</div>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `);

        /* 5. TRAILER */
        if (draft.trailer && draft.trailer.url) {
            html += section('Trailer', `
                <div class="p-2 bg-dark rounded border border-primary">
                    <i class="bi bi-youtube text-danger me-2"></i>
                    <small class="text-white-50">${escapeHtml(draft.trailer.url)}</small>
                </div>
            `);
        }

        reviewContent.innerHTML = html;
    }

    function section(title, body) {
        return `
            <div class="mb-4">
                <h6 class="border-bottom pb-1 mb-2">${title}</h6>
                ${body}
            </div>
        `;
    }

    function showAlert(msg, type) {
        reviewAlert.className = `alert alert-${type}`;
        reviewAlert.textContent = msg;
        reviewAlert.classList.remove('d-none');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});

// confirm button
// document.addEventListener('DOMContentLoaded', () => {
//     const confirmBtn = document.getElementById('confirmMovieSaveBtn');

//     if (!confirmBtn) return;

//     confirmBtn.addEventListener('click', () => {
//         confirmBtn.disabled = true;

//         fetch('admin-actions/commit-movie.php', { method: 'POST' })
//             .then(res => res.json())
//             .then(data => {
//                 if (data.status === 'success') {
//                     location.reload();
//                 } else {
//                     confirmBtn.disabled = false;
//                     alert('Failed to save movie.');
//                 }
//             })
//             .catch(() => {
//                 confirmBtn.disabled = false;
//                 alert('Unexpected error occurred.');
//             });
//     });
// });
// confirm button
document.addEventListener('DOMContentLoaded', () => {
    const confirmBtn = document.getElementById('confirmMovieSaveBtn');

    if (!confirmBtn) return;

    confirmBtn.addEventListener('click', () => {
        // 1. Show a "Processing" alert immediately
        Swal.fire({
            title: 'Saving Movie...',
            text: 'Please wait while we update the database.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        confirmBtn.disabled = true;

        fetch('admin-actions/commit-movie.php', { method: 'POST' })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // 2. Show Success Alert
                    Swal.fire({
                        title: 'Success!',
                        text: 'The movie has been added successfully.',
                        icon: 'success',
                        confirmButtonColor: '#0d6efd'
                    }).then(() => {
                        // 3. Reload only AFTER they click the "OK" button
                        location.reload();
                    });
                } else {
                    confirmBtn.disabled = false;
                    Swal.fire({
                        title: 'Error!',
                        text: data.error || 'Failed to save movie.',
                        icon: 'error'
                    });
                }
            })
            .catch(() => {
                confirmBtn.disabled = false;
                Swal.fire({
                    title: 'Oops!',
                    text: 'An unexpected error occurred.',
                    icon: 'error'
                });
            });
    });
});

// star for rating
function toggleFeatured(movieId) {
    fetch(`admin-actions/toggle-featured.php?id=${movieId}`, { method: 'POST' })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            // SweetAlert notification
            const icon = data.new_state ? '⭐' : '⚪';
            const msg = data.new_state ? 'Added to Highlights' : 'Removed from Highlights';
            
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: msg,
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                location.reload(); // Reload to update the star icon color
            });
        }
    });
}