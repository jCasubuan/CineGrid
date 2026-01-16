<?php if ($current_page !== 'admin'): ?>
<!-- Regular user footer -->
<footer class="bg-dark text-center py-4 mt-5">
    <div class="container">
        <p class="mb-2">© 2026 
            <strong>CineGrid</strong>. All rights reserved.
        </p>
        <div class="text-center mb-3">
            <a href="#" class="text-white text-decoration-none">About</a>
            <span class="text-white mx-2">&bull;</span>
            <a href="#" class="text-white text-decoration-none">Privacy</a>
            <span class="text-white mx-2">&bull;</span>
            <a href="#" class="text-white text-decoration-none">Terms</a>
            <span class="text-white mx-2">&bull;</span>
            <a href="#" class="text-white text-decoration-none">Contact</a>
        </div>
        <div class="d-flex justify-content-center gap-2">   
            <a href="#" class="text-white" aria-label="X (formerly Twitter)">
                <i class="bi bi-twitter-x"></i>
            </a>
            <a href="#" class="text-white"><i class="bi bi-facebook"></i></a>
            <a href="#" class="text-white"><i class="bi bi-instagram"></i></a>
        </div>

        <!-- Disclaimer Section - Collapsible with Version 1 Formatting -->
        <div class="border-top border-secondary pt-3 mt-3">
            <button class="btn btn-link text-white-50 text-decoration-none small p-0 mb-2" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#footerDisclaimer" 
                    aria-expanded="false" 
                    aria-controls="footerDisclaimer">
                <i class="bi bi-info-circle me-1"></i> Legal Information & Disclaimer
                <i class="bi bi-chevron-down ms-1" id="disclaimerChevron"></i>
            </button>
            
            <div class="collapse" id="footerDisclaimer">
                <div class="pt-2" style="max-width: 800px; margin: 0 auto;">
                    <p class="text-white-50 small mb-2">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Disclaimer:</strong> This website is an independent project and is not affiliated with or endorsed by IMDb or any film studio.
                    </p>
                    <p class="text-white-50 small mb-0">
                        <i class="bi bi-shield-check me-1"></i>
                        <strong>Copyright Notice:</strong> All movie titles and related factual information are used for informational purposes only. Trademarks belong to their respective owners.
                    </p>
                </div>
            </div>
        </div>

    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const disclaimerCollapse = document.getElementById('footerDisclaimer');
    const chevron = document.getElementById('disclaimerChevron');
    
    if (disclaimerCollapse && chevron) {
        disclaimerCollapse.addEventListener('show.bs.collapse', function() {
            chevron.classList.remove('bi-chevron-down');
            chevron.classList.add('bi-chevron-up');
        });
        
        disclaimerCollapse.addEventListener('hide.bs.collapse', function() {
            chevron.classList.remove('bi-chevron-up');
            chevron.classList.add('bi-chevron-down');
        });
    }
});

</script>

<?php endif; ?>

<?php include 'includes/main-modals.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>

    <?php if (!empty($_SESSION['login_error'])): ?>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const loginModal = new bootstrap.Modal(
            document.getElementById('loginModal')
        );
        loginModal.show();
    });
    </script>
    <?php endif; ?>

<!-- Global JavaScript link only -->
<script src="assets/js/main.js"></script>

<!-- Page-specific JS -->
<?php if ($current_page === 'movies'): ?>
    <script src="assets/js/movies.js"></script>
<?php endif; ?>

<?php if ($current_page === 'series'): ?>
    <script src="assets/js/series.js"></script>
<?php endif; ?>

<?php if (in_array($current_page, ['movie-details', 'series-details'])): ?>
    <script src="assets/js/details-rating.js"></script>
<?php endif; ?>

<?php if ($current_page === 'admin'): ?>
    <script src="assets/js/admin-dashboard.js"></script>
    <script src="assets/js/movie-validation.js"></script>
    <script src="assets/js/people-validation.js"></script>
<?php endif; ?>

<!-- 
    Note for Developers for the .js file declaration:
        The global .js should be declare first 
        which is named main.js)
        before page-specific js, this is
        mandatory!
-->

</body>
</html>