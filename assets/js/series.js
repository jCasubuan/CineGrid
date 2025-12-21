document.addEventListener('DOMContentLoaded', () => {
    // 1. Redefining the sidebar elements locally so this file can see them
    const applyFilters = document.getElementById('applyFilters');
    const clearFilters = document.getElementById('clearFilters');
    const filterSidebar = document.getElementById('filterSidebar');
    const filterOverlay = document.getElementById('filterOverlay');

    // --- APPLY FILTERS LOGIC ---
    applyFilters?.addEventListener('click', () => {
        const filters = {
            genres: [],
            status: [],
            yearFrom: document.getElementById('yearFrom')?.value,
            yearTo: document.getElementById('yearTo')?.value,
            ratings: [],
            seasons: []
        };

        // We use a more robust way to find checkboxes by looking for parent labels/sections
        // This prevents the "nth-child" error if your HTML structure changes
        document.querySelectorAll('input[type="checkbox"]:checked').forEach(cb => {
            // Check the name or a data-attribute to sort them into the right array
            const filterGroup = cb.closest('.filter-section')?.querySelector('h6')?.textContent.toLowerCase();
            
            if (filterGroup?.includes('genre')) filters.genres.push(cb.id);
            if (filterGroup?.includes('status')) filters.status.push(cb.id);
            if (filterGroup?.includes('rating')) filters.ratings.push(cb.id);
            if (filterGroup?.includes('season')) filters.seasons.push(cb.id);
        });

        console.log('Applied Series Filters:', filters);
        alert('Series Filters applied! Check console.');

        // 2. Safe Close (using ?. ensures it doesn't crash if the element is missing)
        filterSidebar?.classList.remove('show');
        filterOverlay?.classList.remove('show');
    });

    // --- CLEAR FILTERS LOGIC ---
    clearFilters?.addEventListener('click', () => {
        document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
            cb.checked = false;
        });

        const yearFrom = document.getElementById('yearFrom');
        const yearTo = document.getElementById('yearTo');
        
        if (yearFrom) yearFrom.selectedIndex = 0;
        if (yearTo) yearTo.selectedIndex = 0;

        alert('Series filters cleared!');
    });
});