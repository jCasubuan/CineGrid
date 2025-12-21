<footer class="bg-dark text-center py-4 mt-5">
    <div class="container">
        <p class="mb-2">© 2025 CineGrid. All rights reserved.</p>
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
    </div>
</footer>

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

<!-- 
    Note for Developers for the .js file declaration:
        The global .js should be declare first 
        which is named main.js)
        before page-specific js, this is
        mandatory!
-->

</body>
</html>