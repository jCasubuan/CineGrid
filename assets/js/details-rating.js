document.addEventListener('DOMContentLoaded', () => {
    const starRating = document.getElementById('starRating');
    if (!starRating) return;

    /* ===== 10-POINT STAR RATING ===== */
    const stars = starRating.querySelectorAll('i');
    const ratingValue = document.getElementById('ratingValue');
    let selectedRating = 0;

    stars.forEach(star => {
        star.style.cursor = 'pointer';

        star.addEventListener('mouseenter', () => {
            highlightStars(star.dataset.rating);
        });

        star.addEventListener('click', () => {
            selectedRating = star.dataset.rating;
            if (ratingValue) {
                ratingValue.textContent = `${selectedRating}/10`;
                ratingValue.style.display = 'block';
            }
        });
    });

    starRating.addEventListener('mouseleave', () => {
        selectedRating ? highlightStars(selectedRating) : resetStars();
    });

    function highlightStars(rating) {
        stars.forEach(star => {
            star.classList.toggle(
                'bi-star-fill',
                star.dataset.rating <= rating
            );
            star.classList.toggle(
                'bi-star',
                star.dataset.rating > rating
            );
            star.classList.toggle(
                'text-warning',
                star.dataset.rating <= rating
            );
        });
    }

    function resetStars() {
        stars.forEach(star => {
            star.classList.remove('bi-star-fill', 'text-warning');
            star.classList.add('bi-star');
        });
    }

    /* ===== SUBMIT RATING ===== */
    document.getElementById('submitRating')?.addEventListener('click', () => {
        if (!selectedRating) {
            alert('Please select a rating first!');
            return;
        }

        const type = document.body.dataset.type || 'item';
        alert(`You rated this ${type} ${selectedRating}/10`);

        bootstrap.Modal.getInstance(
            document.getElementById('ratingModal')
        )?.hide();
    });

    /* ===== REVIEW STARS (5-POINT) ===== */
    const reviewStars = document.querySelectorAll('#reviewStarRating i');
    let reviewRating = 0;

    reviewStars.forEach(star => {
        star.style.cursor = 'pointer';

        star.addEventListener('click', () => {
            reviewRating = star.dataset.rating;

            reviewStars.forEach(s => {
                s.classList.toggle(
                    'bi-star-fill',
                    s.dataset.rating <= reviewRating
                );
                s.classList.toggle(
                    'bi-star',
                    s.dataset.rating > reviewRating
                );
                s.classList.toggle(
                    'text-warning',
                    s.dataset.rating <= reviewRating
                );
            });
        });
    });

    /* ===== SUBMIT REVIEW ===== */
    document.getElementById('reviewForm')?.addEventListener('submit', e => {
        e.preventDefault();

        if (!reviewRating) {
            alert('Please rate before submitting!');
            return;
        }

        alert('Review submitted successfully!');
        bootstrap.Modal.getInstance(
            document.getElementById('reviewModal')
        )?.hide();
    });

    /* ===== EPISODE CLICK (SERIES ONLY) ===== */
    if (document.body.dataset.type === 'series') {
        document.querySelectorAll('.episode-card').forEach(card => {
            card.addEventListener('click', () => {
                alert('Episode details or playback would open here!');
            });
        });
    }
});
