// Movies Page Specific Functionality
document.addEventListener('DOMContentLoaded', () => {
    // Only run if we're on the movies page
    if (!document.getElementById('movieGrid')) return;

    // Apply Filters
    const applyFiltersBtn = document.getElementById('applyFilters');
    applyFiltersBtn?.addEventListener('click', () => {
        // Collect selected filters
        const filters = {
            genres: [],
            yearFrom: document.getElementById('yearFrom')?.value || '',
            yearTo: document.getElementById('yearTo')?.value || '',
            ratings: []
        };

        // Get genre checkboxes
        document.querySelectorAll('.filter-section:first-child input[type="checkbox"]:checked').forEach(cb => {
            filters.genres.push(cb.id);
        });

        // Get rating checkboxes
        document.querySelectorAll('.filter-section:nth-child(3) input[type="checkbox"]:checked').forEach(cb => {
            filters.ratings.push(cb.id);
        });

        console.log('Applied filters:', filters);
        alert('Filters applied! Check console for details.');

        // Close mobile filter
        const filterSidebar = document.getElementById('filterSidebar');
        const filterOverlay = document.getElementById('filterOverlay');
        filterSidebar?.classList.remove('show');
        filterOverlay?.classList.remove('show');

        // Here you would implement actual filtering logic
    });

    // Clear Filters
    const clearFiltersBtn = document.getElementById('clearFilters');
    clearFiltersBtn?.addEventListener('click', () => {
        // Uncheck all checkboxes
        document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
            cb.checked = false;
        });

        // Reset dropdowns
        const yearFrom = document.getElementById('yearFrom');
        const yearTo = document.getElementById('yearTo');
        if (yearFrom) yearFrom.selectedIndex = 0;
        if (yearTo) yearTo.selectedIndex = 0;

        alert('All filters cleared!');
    });
});