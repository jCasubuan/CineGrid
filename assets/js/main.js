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

    // Login error
    // document.addEventListener('DOMContentLoaded', function() {
    //     const urlParams = new URLSearchParams(window.location.search);
    //     if (urlParams.has('login_error')) {
    //         // Use Bootstrap's Modal API to open the modal
    //         var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    //         loginModal.show();
    //     }
    // });

});
