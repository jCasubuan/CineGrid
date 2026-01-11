<!-- Add Movie Modal -->
    <div class="modal fade" id="addMovieModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add New Movie</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    
                    <!-- Success/Error Messages -->
                    <div id="addMovieAlert" class="alert alert-dismissible fade d-none" role="alert">
                        <span id="addMovieAlertMessage"></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>

                    <form id="addMovieForm" action="admin-actions/save-movie-basic.php" method="POST" autocomplete="off">
                        
                        <div class="row">

                            <div class="col-md-8 mb-3">
                                <label class="form-label">Movie Title *</label>
                                <input type="text" 
                                    name="title" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    placeholder="e.g., The Dark Knight"
                                    minlength="2"
                                    value="<?php echo $_SESSION['movie_draft']['basic']['title'] ?? ''; ?>"
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Release Year *</label>
                                <input type="number" 
                                    name="release_year" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    min="1888" 
                                    max="2026" 
                                    placeholder="2026"
                                    value="<?php echo $_SESSION['movie_draft']['basic']['release_year'] ?? ''; ?>"
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-white-50">Duration (Minutes)*</label>
                                <input type="number" 
                                    name="duration" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    placeholder="e.g., 149"
                                    min="1"
                                    value="<?php echo $_SESSION['movie_draft']['basic']['duration'] ?? ''; ?>"
                                    required>
                                <small class="text-secondary">149 = 2h 29m</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-white-50">Rating (0-10) *</label>
                                <input type="number" 
                                    name="rating" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    step="0.1"
                                    min="0" 
                                    max="10"
                                    value="<?php echo $_SESSION['movie_draft']['basic']['rating'] ?? ''; ?>" 
                                    placeholder="8.5"
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-white-50 small">MTRCB Rating *</label>
                                <select name="content_rating" class="form-select bg-dark text-white border-secondary shadow-none" required>
                                    <?php 
                                    $savedRating = $_SESSION['movie_draft']['basic']['content_rating'] ?? 'PG'; 
                                    $options = ['G', 'PG', 'R-13', 'R-16', 'R-18'];
                                    foreach ($options as $opt): ?>
                                        <option value="<?= $opt ?>" <?= ($savedRating == $opt) ? 'selected' : '' ?>>
                                            <?= $opt ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Original Language *</label>
                                <select name="language" id="language" class="form-select bg-dark text-white border-secondary shadow-none" value="<?php echo $_SESSION['movie_draft']['basic']['language'] ?? ''; ?>" required>
                                    <option value="English" selected>English</option>
                                    <option value="Tagalog">Tagalog (Filipino)</option>
                                    <option value="Japanese">Japanese</option>
                                    <option value="Korean">Korean</option>
                                    <option value="Mandarin">Mandarin</option>
                                    <option value="Spanish">Spanish</option>
                                    <option value="French">French</option>
                                </select>
                            </div>  
                            <div class="col-md-6 mb-3">
                                <label class="form-label">TMDB ID *</label>
                                <input 
                                    type="number" 
                                    name="tmdb_id" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    placeholder="e.g. 634649" 
                                    value="<?php echo $_SESSION['movie_draft']['basic']['tmdb_id'] ?? ''; ?>"
                                    required>                          
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Overview / Synopsis *</label>
                            <textarea name="overview" 
                                    class="form-control" 
                                    rows="4" 
                                    placeholder="Enter a brief description of the movie..."
                                    minlength="10" 
                                    required><?php echo $_SESSION['movie_draft']['basic']['overview'] ?? ''; ?></textarea>
                            <div class="invalid-feedback">
                                Please provide a movie description (minimum 10 characters).
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Poster Path/URL *</label>
                                <input type="text" 
                                    name="poster_path" 
                                    class="form-control" 
                                    placeholder="/poster/movie-poster.jpg"
                                    autocomplete="off"
                                    value="<?php echo $_SESSION['movie_draft']['basic']['poster_path'] ?? ''; ?>"
                                    required
                                >
                                <small class="text-white">Relative path or full URL</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Backdrop Path/URL *</label>
                                <input type="text" 
                                    name="backdrop_path" 
                                    class="form-control" 
                                    placeholder="/backdrop/movie-backdrop.jpg"
                                    autocomplete="off"
                                    value="<?php echo $_SESSION['movie_draft']['basic']['backdrop_path'] ?? ''; ?>"
                                    required
                                >
                                <small class="text-white">Relative path or full URL</small>
                            </div>
                        </div>

                        <div class="alert alert-warning py-2 small">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Note: All fields marked with * are mandatory to satisfy database constraints.
                        </div>

                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="addMovieForm" class="btn btn-primary" id="addMovieBtn">
                        <span class="spinner-border spinner-border-sm d-none" id="addMovieSpinner"></span>
                        <span id="addMovieBtnText">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Genre -->
    <div class="modal fade" id="addMovieGenresModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title"><i class="bi bi-tags me-2"></i>Select Genres</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="genre-error" class="alert alert-danger d-none py-2 mb-3" style="font-size: 0.85rem;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Please select at least one genre.
                    </div>

                    <form id="movieGenresForm" action="admin-actions/save-movie-genres.php" method="POST">
                        <div class="d-flex flex-wrap gap-2">
                            <?php 
                            $genres = ['Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Drama', 'Fantasy', 'Horror', 'Sci-Fi', 'Thriller'];
                            
                            // Get currently saved genres from session (default to empty array)
                            $saved_genres = $_SESSION['movie_draft']['genres'] ?? [];
                            
                            foreach($genres as $g): ?>
                                <div class="genre-item">
                                    <input type="checkbox" 
                                        class="btn-check" 
                                        name="genres[]" 
                                        value="<?= $g ?>" 
                                        id="btn_<?= $g ?>" 
                                        autocomplete="off"
                                        <?php echo in_array($g, $saved_genres) ? 'checked' : ''; ?>> <label class="btn btn-outline-primary btn-sm" for="btn_<?= $g ?>"><?= $g ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" 
                        class="btn btn-outline-secondary" 
                        data-bs-toggle="modal" 
                        data-bs-target="#addMovieModal" 
                        data-bs-dismiss="modal">
                        Back
                    </button>
                    <button type="submit" 
                        form="movieGenresForm" 
                        class="btn btn-primary">
                        Save & Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Movie Trailer Modal -->
    <div class="modal fade" id="addMovieTrailerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">
                        <i class="bi bi-youtube me-2"></i>Movie Trailer
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="trailerAlert" class="alert d-none"></div>

                    <form id="movieTrailerForm" action="admin-actions/save-movie-trailer.php" method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">YouTube Trailer URL *</label>
                            <input
                                type="url"
                                name="trailer_url"
                                class="form-control"
                                placeholder="https://www.youtube.com/watch?v=xxxx"
                                value="<?php echo $_SESSION['movie_draft']['trailer']['url'] ?? ''; ?>"
                                required
                            >
                        </div>
                    </form>

                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        This trailer will be linked to the movie after final confirmation.
                    </div>
                </div>

                <div class="modal-footer border-secondary">
                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-toggle="modal"
                        data-bs-target="#addMovieGenresModal"
                        data-bs-dismiss="modal">
                        Back
                    </button>

                    <button
                        type="submit"
                        form="movieTrailerForm"
                        class="btn btn-primary"
                        id="saveTrailerBtn">
                        Save & Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Movie Directors Modal -->
    <div class="modal fade" id="addMovieDirectorsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">
                        <i class="bi bi-person-video3 me-2"></i>Movie Directors *
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="directorsAlert" class="alert d-none"></div>

                    <form id="movieDirectorsForm" action="admin-actions/save-movie-directors.php" method="POST" autocomplete="off">
                        <div id="directorsContainer">
                            <?php 
                            $saved_directors = $_SESSION['movie_draft']['directors'] ?? ['']; // Default to one empty input
                            foreach($saved_directors as $index => $name): 
                            ?>
                            <div class="input-group mb-2 director-row">
                                <input type="text" 
                                    name="directors[]" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    placeholder="Director name" 
                                    value="<?= htmlspecialchars($name) ?>" 
                                    required>
                                <button type="button" class="btn btn-outline-danger remove-director">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <button
                            type="button"
                            class="btn btn-outline-light btn-sm"
                            id="addDirectorBtn">
                            <i class="bi bi-plus-circle me-1"></i>Add another director
                        </button>
                    </form>
                </div>

                <div class="modal-footer border-secondary">
                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-toggle="modal"
                        data-bs-target="#addMovieTrailerModal"
                        data-bs-dismiss="modal">
                        Back
                    </button>

                    <button
                        type="submit"
                        form="movieDirectorsForm"
                        class="btn btn-primary">
                        Save & Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- add writers -->
    <div class="modal fade" id="addMovieWritersModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">
                        <i class="bi bi-pen-fill me-2"></i>Movie Writers *
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="writersAlert" class="alert d-none"></div>

                    <form id="movieWritersForm" action="admin-actions/save-movie-writers.php" method="POST" autocomplete="off">
                        <div id="writersContainer">
                            <?php 
                            $saved_writers = $_SESSION['movie_draft']['writers'] ?? ['']; 
                            foreach($saved_writers as $name): 
                            ?>
                            <div class="input-group mb-2 writer-row">
                                <input type="text" 
                                    name="writers[]" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    placeholder="Writer name (e.g., Jonathan Nolan)" 
                                    value="<?= htmlspecialchars($name) ?>" 
                                    required>
                                <button type="button" class="btn btn-outline-danger remove-writer">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="button" 
                                class="btn btn-outline-light btn-sm" 
                                id="addWriterBtn">
                            <i class="bi bi-plus-circle me-1"></i>Add another writer
                        </button>
                    </form>
                </div>

                <div class="modal-footer border-secondary">
                    <button type="button" 
                            class="btn btn-outline-secondary" 
                            data-bs-toggle="modal" 
                            data-bs-target="#addMovieDirectorsModal" 
                            data-bs-dismiss="modal">
                        Back
                    </button>

                    <button type="submit" 
                            form="movieWritersForm" 
                            class="btn btn-primary">
                        Save & Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Movie Cast Modal -->
    <div class="modal fade" id="addMovieCastModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">
                        <i class="bi bi-people-fill me-2"></i>Movie Cast *
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="castAlert" class="alert d-none"></div>

                    <form id="movieCastForm" action="admin-actions/save-movie-cast.php" method="POST" autocomplete="off">
                        <div id="castContainer">
                            <?php 
                            $saved_cast = $_SESSION['movie_draft']['cast'] ?? [['actor' => '', 'character' => '', 'image' => '']]; 
                            foreach($saved_cast as $member): 
                            ?>
                            <div class="row g-2 mb-2 cast-row">
                                <div class="col-md-4">
                                    <input type="text" name="actors[]" class="form-control bg-dark text-white border-secondary" 
                                        placeholder="Actor name" value="<?= htmlspecialchars($member['actor']) ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="characters[]" class="form-control bg-dark text-white border-secondary" 
                                        placeholder="Character name" value="<?= htmlspecialchars($member['character']) ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="actor_images[]" class="form-control bg-dark text-white border-secondary" 
                                        placeholder="Image path" value="<?= htmlspecialchars($member['image']) ?>">
                                </div>
                                <div class="col-md-1 d-grid">
                                    <button type="button" class="btn btn-outline-danger remove-cast">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <button
                            type="button"
                            class="btn btn-outline-light btn-sm"
                            id="addCastBtn">
                            <i class="bi bi-plus-circle me-1"></i>Add cast member
                        </button>
                    </form>
                </div>

                <div class="modal-footer border-secondary">
                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-toggle="modal"
                        data-bs-target="#addMovieWritersModal"
                        data-bs-dismiss="modal">
                        Back
                    </button>

                    <button
                        type="submit"
                        form="movieCastForm"
                        class="btn btn-primary">
                        Save & Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Movie Draft Modal -->
    <div class="modal fade" id="reviewMovieDraftModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">
                        <i class="bi bi-clipboard-check me-2"></i>Review Movie Draft
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="reviewAlert" class="alert d-none"></div>

                    <div id="reviewContent">
                        <!-- Filled dynamically -->
                    </div>
                </div>

                <div class="modal-footer border-secondary">
                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-toggle="modal"
                        data-bs-target="#addMovieCastModal"
                        data-bs-dismiss="modal">
                        Back
                    </button>

                    <button
                        type="button"
                        id="confirmMovieSaveBtn"
                        class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Confirm & Save Movie
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Movie Modal -->
    <div class="modal fade" id="editMovieModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil me-2"></i>Edit Movie
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="editMovieAlert" class="alert d-none"></div>
                    
                    <!-- Loading State -->
                    <div id="editLoadingState" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-white-50">Loading movie data...</p>
                    </div>

                    <!-- Edit Form (hidden initially) -->
                    <form id="editMovieForm" style="display: none;">
                        <input type="hidden" id="edit_movie_id" name="movie_id">
                        
                        <!-- Basic Information -->
                        <h6 class="border-bottom pb-2 mb-3">Basic Information</h6>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Movie Title *</label>
                                <input type="text" name="title" id="edit_title" 
                                    class="form-control bg-dark text-white border-secondary" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Release Year *</label>
                                <input type="number" name="release_year" id="edit_release_year" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    min="1888" max="2026" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Duration (Minutes) *</label>
                                <input type="number" name="duration" id="edit_duration" 
                                    class="form-control bg-dark text-white border-secondary" min="1" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Rating (0-10) *</label>
                                <input type="number" name="rating" id="edit_rating" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    step="0.1" min="0" max="10" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Content Rating *</label>
                                <select name="content_rating" id="edit_content_rating" 
                                        class="form-select bg-dark text-white border-secondary" required>
                                    <option value="G">G</option>
                                    <option value="PG">PG</option>
                                    <option value="R-13">R-13</option>
                                    <option value="R-16">R-16</option>
                                    <option value="R-18">R-18</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Language *</label>
                                <select name="language" id="edit_language" 
                                        class="form-select bg-dark text-white border-secondary" required>
                                    <option value="English">English</option>
                                    <option value="Tagalog">Tagalog (Filipino)</option>
                                    <option value="Japanese">Japanese</option>
                                    <option value="Korean">Korean</option>
                                    <option value="Mandarin">Mandarin</option>
                                    <option value="Spanish">Spanish</option>
                                    <option value="French">French</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">TMDB ID</label>
                                <input type="number" name="tmdb_id" id="edit_tmdb_id" 
                                    class="form-control bg-dark text-white border-secondary">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Overview *</label>
                            <textarea name="overview" id="edit_overview" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    rows="4" required></textarea>
                        </div>

                        <!-- Genres -->
                        <h6 class="border-bottom pb-2 mb-3">Genres</h6>
                        <div class="d-flex flex-wrap gap-2 mb-3" id="edit_genres_container">
                            <?php 
                            $genres = ['Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Drama', 'Fantasy', 'Horror', 'Sci-Fi', 'Thriller'];
                            foreach($genres as $g): ?>
                                <div>
                                    <input type="checkbox" class="btn-check" name="genres[]" 
                                        value="<?= $g ?>" id="edit_btn_<?= $g ?>" autocomplete="off">
                                    <label class="btn btn-outline-primary btn-sm" for="edit_btn_<?= $g ?>">
                                        <?= $g ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Images -->
                        <h6 class="border-bottom pb-2 mb-3">Images</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Poster Path *</label>
                                <input type="text" name="poster_path" id="edit_poster_path" 
                                    class="form-control bg-dark text-white border-secondary" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Backdrop Path *</label>
                                <input type="text" name="backdrop_path" id="edit_backdrop_path" 
                                    class="form-control bg-dark text-white border-secondary" required>
                            </div>
                        </div>

                        <!-- Trailer -->
                        <h6 class="border-bottom pb-2 mb-3">Trailer</h6>
                        <div class="mb-3">
                            <label class="form-label">YouTube Trailer URL</label>
                            <input type="url" name="trailer_url" id="edit_trailer_url" 
                                class="form-control bg-dark text-white border-secondary" 
                                placeholder="https://www.youtube.com/watch?v=xxxx">
                        </div>

                        <!-- Status -->
                        <h6 class="border-bottom pb-2 mb-3">Status</h6>
                        <div class="mb-3">
                            <select name="status" id="edit_status" 
                                    class="form-select bg-dark text-white border-secondary">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="editMovieForm" class="btn btn-primary" id="updateMovieBtn">
                        <span class="spinner-border spinner-border-sm d-none" id="updateMovieSpinner"></span>
                        <span id="updateMovieBtnText">Save Changes</span>
                    </button>
                </div>
            </div>
        </div>
    </div>