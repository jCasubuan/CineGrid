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

                    <form id="addMovieForm" action="../admin-actions/save-movie-basic.php" method="POST" autocomplete="off">
                        
                        <div class="row">

                            <div class="col-md-8 mb-3">
                                <label class="form-label">Movie Title *</label>
                                <input type="text" 
                                    name="title" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    placeholder="e.g., The Dark Knight"
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Release Year *</label>
                                <input type="number" 
                                    name="release_year" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    min="1900" 
                                    max="2030" 
                                    placeholder="2024"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-white-50">Duration (Minutes)*</label>
                                <input type="number" 
                                    name="duration" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    placeholder="e.g., 149"
                                    required>
                                <small class="text-secondary">149 = 2h 29m</small>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-white-50">Rating (0-10)</label>
                                    <input type="number" 
                                        name="rating" 
                                        class="form-control bg-dark text-white border-secondary" 
                                        step="0.1"
                                        min="0" 
                                        max="10" 
                                        placeholder="8.5">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-white-50 small">Content Rating *</label>
                                    <select name="content_rating" class="form-select bg-dark text-white border-secondary shadow-none" required>
                                        <option value="G">G</option>
                                        <option value="PG">PG</option>
                                        <option value="PG-13" selected>PG-13</option>
                                        <option value="R">R</option>
                                        <option value="NC-17">NC-17</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-white-50">Language *</label>
                                    <input type="text" name="language" class="form-control bg-dark text-white border-secondary" placeholder="e.g., English" value="English" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Overview / Synopsis *</label>
                            <textarea name="overview" 
                                    class="form-control" 
                                    rows="4" 
                                    placeholder="Enter a brief description of the movie..."
                                    required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white-50 small">TMDB ID (Optional)</label>
                            <input type="number" name="tmdb_id" class="form-control bg-dark text-white border-secondary" placeholder="e.g. 634649">
                            <div class="form-text text-muted" style="font-size: 0.75rem;">
                                Only the number from the TMDB URL.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Poster Path/URL</label>
                                <input type="text" 
                                    name="poster_path" 
                                    class="form-control" 
                                    placeholder="/poster/movie-poster.jpg"
                                    autocomplete="off">
                                <small class="text-white">Relative path or full URL</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Backdrop Path/URL</label>
                                <input type="text" 
                                    name="backdrop_path" 
                                    class="form-control" 
                                    placeholder="/backdrop/movie-backdrop.jpg"
                                    autocomplete="off">
                                <small class="text-white">Relative path or full URL</small>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Tip:</strong> You can leave poster and backdrop paths empty for now and add them later.
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
                    <form id="movieGenresForm" action="../admin-actions/save-movie-genres.php" method="POST">
                        <div class="d-flex flex-wrap gap-2">
                            <?php 
                            $genres = ['Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Drama', 'Fantasy', 'Horror', 'Sci-Fi', 'Thriller'];
                            foreach($genres as $g): ?>
                                <div class="genre-item">
                                    <input type="checkbox" class="btn-check" name="genres[]" value="<?= $g ?>" id="btn_<?= $g ?>" autocomplete="off">
                                    <label class="btn btn-outline-primary btn-sm" for="btn_<?= $g ?>"><?= $g ?></label>
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

                    <form id="movieTrailerForm" action="../admin-actions/save-movie-trailer.php" method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">YouTube Trailer URL</label>
                            <input
                                type="url"
                                name="trailer_url"
                                class="form-control"
                                placeholder="https://www.youtube.com/watch?v=xxxx"
                            >
                            <small class="text-white">
                                Optional. Can be added later.
                            </small>
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
                        <i class="bi bi-person-video3 me-2"></i>Movie Directors
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="directorsAlert" class="alert d-none"></div>

                    <form id="movieDirectorsForm" action="../admin-actions/save-movie-directors.php" method="POST" autocomplete="off">
                        <div id="directorsContainer">
                            <div class="input-group mb-2 director-row">
                                <input
                                    type="text"
                                    name="directors[]"
                                    class="form-control"
                                    placeholder="Director name"
                                    required
                                >
                                <button type="button" class="btn btn-outline-danger remove-director">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
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
                        <i class="bi bi-pen-fill me-2"></i>Movie Writers
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="writersAlert" class="alert d-none"></div>

                    <form id="movieWritersForm" action="../admin-actions/save-movie-writers.php" method="POST" autocomplete="off">
                        <div id="writersContainer">
                            <div class="input-group mb-2 writer-row">
                                <input type="text" 
                                    name="writers[]" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    placeholder="Writer name (e.g., Jonathan Nolan)" 
                                    required>
                                <button type="button" class="btn btn-outline-danger remove-writer">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
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
                        <i class="bi bi-people-fill me-2"></i>Movie Cast
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="castAlert" class="alert d-none"></div>

                    <form id="movieCastForm" action="../admin-actions/save-movie-cast.php" method="POST" autocomplete="off">
                        <div id="castContainer">
                            <div class="row g-2 mb-2 cast-row">
                                <div class="col-md-4">
                                    <label class="form-label d-md-none">Actor Name</label>
                                    <input type="text" 
                                        name="actors[]" 
                                        class="form-control bg-dark text-white border-secondary" 
                                        placeholder="Actor name" 
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label d-md-none">Character Name</label>
                                    <input type="text" 
                                        name="characters[]" 
                                        class="form-control bg-dark text-white border-secondary" 
                                        placeholder="Character name" 
                                        required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label d-md-none">Image URL/Path</label>
                                    <input type="text" 
                                        name="actor_images[]" 
                                        class="form-control bg-dark text-white border-secondary" 
                                        placeholder="Image path (e.g. /assets/img/cast/name.jpg)">
                                </div>
                                <div class="col-md-1 d-grid">
                                    <button type="button" class="btn btn-outline-danger remove-cast">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Movie</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editMovieForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Movie Title *</label>
                                <input type="text" class="form-control" value="The Dark Knight" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Year *</label>
                                <input type="number" class="form-control" value="2008" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Genre *</label>
                                <select class="form-select" required>
                                    <option>Action</option>
                                    <option>Comedy</option>
                                    <option>Drama</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Duration (minutes) *</label>
                                <input type="number" class="form-control" value="152" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Synopsis *</label>
                            <textarea class="form-control" rows="4" required>Batman faces the Joker...</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="editMovieForm" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>