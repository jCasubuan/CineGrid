<!-- Add Series Modal -->
    <div class="modal fade" id="addSeriesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title"><i class="bi bi-tv me-2"></i>Add New Series</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addSeriesForm" action="admin-actions/save-series-basic.php" method="POST" autocomplete="off">
                        
                        <div class="row">

                            <div class="col-md-8 mb-3">
                                <label class="form-label">Series Title *</label>
                                <input type="text" 
                                    name="title" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    placeholder="e.g., Stranger Things" 
                                    minlength="2" 
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Year Aired *</label>
                                <input type="number" 
                                    name="release_year" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    min="1900" 
                                    max="2026" 
                                    placeholder="2026" 
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">MTRCB Rating *</label>
                                <select name="content_rating" class="form-select bg-dark text-white border-secondary shadow-none" required>
                                    <option value="G">G</option>
                                    <option value="PG" selected>PG</option>
                                    <option value="SPG">SPG</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Rating (0-10)</label>
                                <input type="number" 
                                    name="rating" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    step="0.1" 
                                    min="0" 
                                    max="10" 
                                    placeholder="8.5"
                                    required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select bg-dark text-white border-secondary shadow-none" required>
                                    <option value="Ongoing">Ongoing</option>
                                    <option value="Ended">Ended</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
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
                                <label class="form-label">TMDB ID</label>
                                <input type="number" 
                                    name="tmdb_id" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    placeholder="e.g. 12345"
                                    required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Overview / Synopsis *</label>
                            <textarea name="overview" 
                                class="form-control bg-dark text-white border-secondary" 
                                rows="4" 
                                placeholder="Enter series description..." 
                                minlength="10"
                                required></textarea>
                            <div class="invalid-feedback">
                                Please provide a series description (minimum 10 characters).
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
                    <button type="submit" form="addSeriesForm" class="btn btn-primary" id="addSeriesBtn">
                        <span class="spinner-border spinner-border-sm d-none" id="addSeriesSpinner"></span>
                        <span id="addSeriesBtnText">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Genres -->
    <div class="modal fade" id="addSeriesGenresModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title"><i class="bi bi-tags me-2"></i>Select Series Genres</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="series-genre-error" class="alert alert-danger d-none py-2 mb-3" style="font-size: 0.85rem;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Please select at least one genre.
                    </div>

                    <form id="seriesGenresForm" action="admin-actions/save-series-genres.php" method="POST">
                        <div class="d-flex flex-wrap gap-2">
                            <?php 
                            $genres = ['Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Drama', 'Fantasy', 'Horror', 'Sci-Fi', 'Thriller', 'Romance', 'Documentary'];
                            
                            // Using series_draft session to avoid mixing with movie data
                            $saved_genres = $_SESSION['series_draft']['genres'] ?? [];
                            
                            foreach($genres as $g): ?>
                                <div class="genre-item">
                                    <input type="checkbox" 
                                        class="btn-check" 
                                        name="genres[]" 
                                        value="<?= $g ?>" 
                                        id="series_btn_<?= $g ?>" 
                                        autocomplete="off"
                                        <?php echo in_array($g, $saved_genres) ? 'checked' : ''; ?>> 
                                    <label class="btn btn-outline-primary btn-sm" for="series_btn_<?= $g ?>"><?= $g ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" 
                        class="btn btn-outline-secondary" 
                        data-bs-toggle="modal" 
                        data-bs-target="#addSeriesModal" 
                        data-bs-dismiss="modal">
                        Back
                    </button>
                    <button type="submit" 
                        form="seriesGenresForm" 
                        class="btn btn-primary">
                        Save & Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Series trailer -->
    <div class="modal fade" id="addSeriesTrailerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title"><i class="bi bi-play-btn me-2"></i>Series Trailer</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="seriesTrailerForm" action="admin-actions/save-series-trailer.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Trailer URL (YouTube Link)</label>
                            <input type="url" 
                                name="trailer_url" 
                                class="form-control bg-dark text-white border-secondary" 
                                placeholder="https://www.youtube.com/watch?v=..."
                                autocomplete="off"
                                required>
                            <small class="text-white-50">Paste the full YouTube URL or embed link.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addSeriesGenresModal" data-bs-dismiss="modal">
                        Back
                    </button>
                    <button type="submit" form="seriesTrailerForm" class="btn btn-primary">
                        Next Step
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Seasons Modal -->
    <div class="modal fade" id="addSeriesSeasonsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title"><i class="bi bi-stack me-2"></i>Configure Seasons</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="seriesSeasonsForm" action="admin-actions/save-series-seasons.php" method="POST">
                        <div class="alert alert-info py-2" style="font-size: 0.85rem;">
                            <i class="bi bi-info-circle-fill me-2"></i> Define the seasons for <strong><?php echo $_SESSION['series_draft']['title'] ?? 'this series'; ?></strong>.
                        </div>

                        <div id="seasons-container">
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
                        </div>

                        <button type="button" class="btn btn-info btn-sm" onclick="addSeasonRow()">
                            <i class="bi bi-plus-lg me-1"></i>Add Another Season
                        </button>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addSeriesGenresModal" data-bs-dismiss="modal">
                        Back
                    </button>
                    <button type="submit" form="seriesSeasonsForm" class="btn btn-primary">
                        Save & Continue to Episodes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Episodes -->
    <div class="modal fade" id="addSeriesEpisodesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title"><i class="bi bi-play-circle me-2"></i>Add Episodes</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="seriesEpisodesForm" action="admin-actions/save-series-episodes.php" method="POST">
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small text-white-50">Target Season *</label>
                                <select name="target_season_number" class="form-select bg-dark text-white border-secondary" required>
                                    <?php 
                                    $seasons_data = $_SESSION['series_draft']['seasons'] ?? [];
                                    if (!empty($seasons_data)):
                                        foreach($seasons_data as $index => $s): 
                                            // If $s is an array (from the form), get the season_number
                                            $val = is_array($s) ? ($s['season_number'] ?? ($index + 1)) : $s;
                                    ?>
                                        <option value="<?= $val ?>">Season <?= $val ?></option>
                                    <?php endforeach; else: ?>
                                        <option value="1">Season 1</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div id="episodes-container">
                            <div class="episode-row border border-secondary p-3 rounded mb-3 position-relative">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 remove-episode-btn d-none" 
                                        onclick="removeEpisodeRow(this)">
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
                        </div>

                        <button type="button" class="btn btn-outline-info btn-sm" onclick="addEpisodeRow()">
                            <i class="bi bi-plus-lg me-1"></i> Add Another Episode
                        </button>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addSeriesSeasonsModal" data-bs-dismiss="modal">
                        Back
                    </button>
                    <button type="submit" form="seriesEpisodesForm" class="btn btn-success">
                        Save All & Proceed to Cast
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Series Cast -->
    <div class="modal fade" id="addSeriesCastModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl"> <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title"><i class="bi bi-people-fill me-2"></i>Series Cast *</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="seriesCastForm" action="admin-actions/save-series-cast.php" method="POST" autocomplete="off">
                        <div id="cast-container">
                            <?php 
                            // If session is empty, we MUST provide an array with one empty element so the loop runs once
                            $saved_cast = $_SESSION['series_draft']['cast'] ?? [['actor' => '', 'character' => '', 'image' => '']]; 
                            
                            foreach($saved_cast as $member): 
                            ?>
                            <div class="row g-2 mb-2 cast-row">
                                <div class="col-md-4">
                                    <input type="text" name="actors[]" class="form-control bg-dark text-white border-secondary" 
                                        placeholder="Actor name" value="<?= htmlspecialchars($member['actor'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="characters[]" class="form-control bg-dark text-white border-secondary" 
                                        placeholder="Character name" value="<?= htmlspecialchars($member['character'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="actor_images[]" class="form-control bg-dark text-white border-secondary" 
                                        placeholder="Image path" value="<?= htmlspecialchars($member['image'] ?? '') ?>">
                                </div>
                                <div class="col-md-1 d-grid">
                                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.cast-row').remove()">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="button" class="btn btn-outline-light btn-sm mt-2" onclick="addCastRow()">
                            <i class="bi bi-plus-circle me-1"></i>Add cast member
                        </button>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addSeriesEpisodesModal" data-bs-dismiss="modal">Back</button>
                    <button type="submit" form="seriesCastForm" class="btn btn-primary">Save & Continue</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Directors modal -->
    <div class="modal fade" id="addSeriesDirectorsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">
                        <i class="bi bi-person-video3 me-2"></i>Series Directors *
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="seriesDirectorsAlert" class="alert d-none"></div>

                    <form id="seriesDirectorsForm" action="admin-actions/save-series-directors.php" method="POST" autocomplete="off">
                        <div id="seriesDirectorsContainer">
                            <?php 
                            $saved_directors = $_SESSION['series_draft']['directors'] ?? ['']; 
                            foreach($saved_directors as $name): 
                            ?>
                            <div class="input-group mb-2 series-director-row">
                                <input type="text" 
                                    name="directors[]" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    placeholder="Director name" 
                                    value="<?= htmlspecialchars($name) ?>" 
                                    required>
                                <button type="button" class="btn btn-outline-danger remove-series-director">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="button" class="btn btn-outline-light btn-sm" id="addSeriesDirectorBtn">
                            <i class="bi bi-plus-circle me-1"></i>Add another director
                        </button>
                    </form>
                </div>

                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addSeriesCastModal" data-bs-dismiss="modal">
                        Back
                    </button>
                    <button type="submit" form="seriesDirectorsForm" class="btn btn-primary">
                        Save & Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Writers modal -->
    <div class="modal fade" id="addSeriesWritersModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">
                        <i class="bi bi-pen-fill me-2"></i>Series Writers *
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="seriesWritersAlert" class="alert d-none"></div>

                    <form id="seriesWritersForm" action="admin-actions/save-series-writers.php" method="POST" autocomplete="off">
                        <div id="seriesWritersContainer">
                            <?php 
                            $saved_writers = $_SESSION['series_draft']['writers'] ?? ['']; 
                            foreach($saved_writers as $name): 
                            ?>
                            <div class="input-group mb-2 series-writer-row">
                                <input type="text" 
                                    name="writers[]" 
                                    class="form-control bg-dark text-white border-secondary" 
                                    placeholder="Writer name" 
                                    value="<?= htmlspecialchars($name) ?>" 
                                    required>
                                <button type="button" class="btn btn-outline-danger remove-series-writer">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="button" class="btn btn-outline-light btn-sm" id="addSeriesWriterBtn">
                            <i class="bi bi-plus-circle me-1"></i>Add another writer
                        </button>
                    </form>
                </div>

                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addSeriesDirectorsModal" data-bs-dismiss="modal">
                        Back
                    </button>
                    <button type="submit" form="seriesWritersForm" class="btn btn-primary">
                        Next: Review Draft
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Review Series Draft Modal (UPDATED with Backdrop) -->
    <div class="modal fade" id="reviewSeriesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-info">
                        <i class="bi bi-clipboard-check me-2"></i>Review Series Draft
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="seriesReviewAlert" class="alert d-none"></div>

                    <div class="mb-3">
                        <img id="rev-series-backdrop" 
                            src="" 
                            class="img-fluid rounded border border-secondary shadow" 
                            style="width: 100%; height: 200px; object-fit: cover;" 
                            alt="Backdrop">
                    </div>

                    <div class="row">
                        <!-- Left Column: Poster & Info -->
                        <div class="col-md-4 border-end border-secondary text-center">
                            <img id="rev-series-poster" 
                                src="" 
                                class="img-fluid rounded mb-3 border border-secondary shadow" 
                                style="max-height: 400px; width: 100%; object-fit: cover;" 
                                alt="Poster">
                            
                            <div class="text-start ps-2">
                                <h4 id="rev-series-title" class="text-primary mb-1"></h4>
                                
                                <p class="mb-1">
                                    <span class="badge bg-danger" id="rev-series-rating"></span> 
                                    <span class="text-white-50" id="rev-series-year"></span>
                                </p>
                                
                                <p class="small mb-3 text-white-50" id="rev-series-stats">
                                </p>
                                
                                <p class="small mb-3">
                                    <i class="bi bi-tags me-1"></i> 
                                    <span id="rev-series-genres"></span>
                                </p>
                                
                                <h6 class="text-info border-bottom border-secondary pb-1">Directors</h6>
                                <p id="rev-series-directors" class="small text-white-50"></p>

                                <h6 class="text-info border-bottom border-secondary pb-1">Writers</h6>
                                <p id="rev-series-writers" class="small text-white-50"></p>
                            </div>
                        </div>

                        <!-- Right Column: Seasons & Cast -->
                        <div class="col-md-8">
                            <h6 class="text-primary border-bottom border-secondary pb-2 mb-3">
                                Structure & Episodes
                            </h6>
                            <div class="accordion accordion-flush border border-secondary rounded mb-4" 
                                id="rev-series-accordion">
                                <!-- Filled by JavaScript -->
                            </div>

                            <h6 class="text-primary border-bottom border-secondary pb-2 mb-3">
                                Series Cast
                            </h6>
                            <div id="rev-series-cast" class="row g-2">
                                <!-- Filled by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-secondary">
                    <button type="button" 
                            class="btn btn-outline-secondary" 
                            data-bs-toggle="modal" 
                            data-bs-target="#addSeriesWritersModal" 
                            data-bs-dismiss="modal">
                        Back to Edit
                    </button>
                    <button type="button" 
                            id="publishSeriesBtn" 
                            class="btn btn-success btn-lg px-4">
                        <span class="spinner-border spinner-border-sm d-none" id="publishSeriesSpinner"></span>
                        <i class="bi bi-check-circle me-2"></i>Confirm & Publish Series
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Series Modal -->
     <div class="modal fade" id="editSeriesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Series</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editSeriesForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Series Title *</label>
                                <input type="text" class="form-control" value="Breaking Bad" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select">
                                    <option value="ongoing">Ongoing</option>
                                    <option value="completed" selected>Completed</option>
                                    <option value="upcoming">Upcoming</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Synopsis *</label>
                            <textarea class="form-control" rows="4" required>A chemistry teacher turns to crime...</textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="editSeriesForm" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>