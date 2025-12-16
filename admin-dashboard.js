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
document.getElementById('addMovieForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Movie added successfully!\nIn production, this would send data to your backend.');
    bootstrap.Modal.getInstance(document.getElementById('addMovieModal')).hide();
    e.target.reset();
});

document.getElementById('editMovieForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Movie updated successfully!');
    bootstrap.Modal.getInstance(document.getElementById('editMovieModal')).hide();
});

document.getElementById('addSeriesForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Series added successfully!');
    bootstrap.Modal.getInstance(document.getElementById('addSeriesModal')).hide();
    e.target.reset();
});

document.getElementById('editSeriesForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Series updated successfully!');
    bootstrap.Modal.getInstance(document.getElementById('editSeriesModal')).hide();
});

document.getElementById('addUserForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    alert('User added successfully!');
    bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
    e.target.reset();
});

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