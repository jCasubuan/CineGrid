// Movie Title Validation
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('movieTitle');
    const yearInput = document.getElementById('releaseYear');
    const durationInput = document.getElementById('movieDuration');
    const durationHelper = document.getElementById('durationHelper');
    const tmdbIdInput = document.getElementById('tmdbId');
    const overviewInput = document.getElementById('movieOverview'); 
    const overviewCounter = document.getElementById('overviewCounter'); 
    const posterPathInput = document.getElementById('posterPath');
    const backdropPathInput = document.getElementById('backdropPath');

    
    // Title validation (existing code)
    if (titleInput) {
        titleInput.addEventListener('blur', function(e) {
            validateMovieTitle(e.target);
        });
        
        titleInput.addEventListener('input', function(e) {
            const value = e.target.value.trim();
            if (value.length >= 3) {
                validateMovieTitle(e.target);
            } else {
                e.target.setCustomValidity('');
                e.target.classList.remove('is-invalid');
            }
        });
    }
    
    // Year validation (existing code)
    if (yearInput) {
        yearInput.addEventListener('input', function(e) {
            validateYear(e.target);
        });
        
        yearInput.addEventListener('blur', function(e) {
            validateYear(e.target);
        });
    }
    
    // Duration validation (existing code)
    if (durationInput) {
        durationInput.addEventListener('input', function(e) {
            validateDuration(e.target);
            updateDurationHelper(e.target.value);
        });
        
        durationInput.addEventListener('blur', function(e) {
            validateDuration(e.target);
        });
        
        durationInput.addEventListener('keydown', function(e) {
            if (e.key === '-' || e.key === '+' || e.key === 'e' || e.key === 'E') {
                e.preventDefault();
            }
        });
    }
    
    // TMDB ID validation (existing code)
    if (tmdbIdInput) {
        tmdbIdInput.addEventListener('input', function(e) {
            validateTmdbId(e.target);
        });
        
        tmdbIdInput.addEventListener('blur', function(e) {
            validateTmdbId(e.target);
        });
        
        tmdbIdInput.addEventListener('keydown', function(e) {
            if (e.key === '-' || e.key === '+' || e.key === 'e' || e.key === 'E' || e.key === '.') {
                e.preventDefault();
            }
        });
    }
    
    // Overview validation (NEW)
    if (overviewInput) {
        // Update character counter on page load
        updateOverviewCounter(overviewInput.value.length);
        
        overviewInput.addEventListener('input', function(e) {
            validateOverview(e.target);
            updateOverviewCounter(e.target.value.length);
        });
        
        overviewInput.addEventListener('blur', function(e) {
            validateOverview(e.target);
        });
    }

    // Postaer path validation
    if (posterPathInput) {
        posterPathInput.addEventListener('input', function(e) {
            validateImagePath(e.target, 'Poster');
        });

        posterPathInput.addEventListener('blur', function(e) {
            validateImagePath(e.target, 'Poster');
        });
    }

    if (backdropPathInput) {
        backdropPathInput.addEventListener('input', function(e) {
            validateImagePath(e.target, 'Backdrop');
        });

        backdropPathInput.addEventListener('blur', function(e) {
            validateImagePath(e.target, 'Backdrop');
        });
    }
    
    function validateMovieTitle(input) {
        const value = input.value.trim();
        
        if (value.length < 3) return;
        
        const hasVowels = /[aeiouAEIOU]/.test(value);
        const vowelCount = (value.match(/[aeiouAEIOU]/g) || []).length;
        const letterCount = (value.match(/[a-zA-Z]/g) || []).length;
        const vowelRatio = letterCount > 0 ? vowelCount / letterCount : 0;
        const hasExcessiveConsonants = /[bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]{6,}/.test(value);
        
        if (!hasVowels || vowelRatio < 0.15 || hasExcessiveConsonants) {
            input.setCustomValidity('Please enter a valid movie title (appears to be random text).');
            input.classList.add('is-invalid');
        } else {
            input.setCustomValidity('');
            input.classList.remove('is-invalid');
        }
    }
    
    function validateYear(input) {
        const value = input.value.trim();
        
        if (value === '') {
            input.setCustomValidity('');
            input.classList.remove('is-invalid');
            return;
        }
        
        if (value.length !== 4) {
            input.setCustomValidity('Year must be exactly 4 digits.');
            input.classList.add('is-invalid');
            return;
        }
        
        if (!/^\d{4}$/.test(value)) {
            input.setCustomValidity('Year must contain only numbers.');
            input.classList.add('is-invalid');
            return;
        }
        
        const year = parseInt(value);
        
        if (year < 1888 || year > 2026) {
            input.setCustomValidity('Year must be between 1888 and 2026.');
            input.classList.add('is-invalid');
            return;
        }
        
        input.setCustomValidity('');
        input.classList.remove('is-invalid');
    }
    
    function validateDuration(input) {
        const value = input.value.trim();
        
        if (value === '') {
            input.setCustomValidity('');
            input.classList.remove('is-invalid');
            return;
        }
        
        if (!/^\d+$/.test(value)) {
            input.setCustomValidity('Duration must be a positive number.');
            input.classList.add('is-invalid');
            return;
        }
        
        const duration = parseInt(value);
        
        if (duration <= 0) {
            input.setCustomValidity('Duration must be greater than 0 minutes.');
            input.classList.add('is-invalid');
            return;
        }
        
        if (duration < 1) {
            input.setCustomValidity('Duration must be at least 1 minute.');
            input.classList.add('is-invalid');
            return;
        }
        
        if (duration > 1440) {
            input.setCustomValidity('Duration cannot exceed 1440 minutes (24 hours).');
            input.classList.add('is-invalid');
            return;
        }
        
        input.setCustomValidity('');
        input.classList.remove('is-invalid');
    }
    
    function validateTmdbId(input) {
        const value = input.value.trim();
        
        if (value === '') {
            input.setCustomValidity('');
            input.classList.remove('is-invalid');
            return;
        }
        
        if (!/^\d+$/.test(value)) {
            input.setCustomValidity('TMDB ID must contain only numbers.');
            input.classList.add('is-invalid');
            return;
        }
        
        const tmdbId = parseInt(value);
        
        if (tmdbId <= 0) {
            input.setCustomValidity('TMDB ID must be a positive number.');
            input.classList.add('is-invalid');
            return;
        }
        
        const length = value.length;
        if (length < 1 || length > 8) {
            input.setCustomValidity('TMDB ID must be between 1 and 8 digits.');
            input.classList.add('is-invalid');
            return;
        }
        
        input.setCustomValidity('');
        input.classList.remove('is-invalid');
    }
    
    // NEW: Overview validation function
    function validateOverview(input) {
        const value = input.value.trim();
        
        // Check if empty
        if (value === '') {
            input.setCustomValidity('');
            input.classList.remove('is-invalid');
            return;
        }
        
        // Check minimum length
        if (value.length < 10) {
            input.setCustomValidity('Overview must be at least 10 characters.');
            input.classList.add('is-invalid');
            return;
        }
        
        // Check maximum length
        if (value.length > 2000) {
            input.setCustomValidity('Overview cannot exceed 2000 characters.');
            input.classList.add('is-invalid');
            return;
        }
        
        // Check if it's just repeated characters (e.g., "aaaaaaaaaa")
        if (/^(.)\1{9,}$/.test(value)) {
            input.setCustomValidity('Please enter a valid overview (not just repeated characters).');
            input.classList.add('is-invalid');
            return;
        }
        
        // Check for vowels (must have at least some vowels for real text)
        const hasVowels = /[aeiouAEIOU]/.test(value);
        if (!hasVowels) {
            input.setCustomValidity('Please enter a valid overview.');
            input.classList.add('is-invalid');
            return;
        }
        
        // Calculate vowel ratio (real text has ~20-50% vowels)
        const vowelCount = (value.match(/[aeiouAEIOU]/g) || []).length;
        const letterCount = (value.match(/[a-zA-Z]/g) || []).length;
        
        if (letterCount > 0) {
            const vowelRatio = vowelCount / letterCount;
            
            // Must have at least 10% vowels for realistic text
            if (vowelRatio < 0.10) {
                input.setCustomValidity('Please enter a valid overview (appears to be random text).');
                input.classList.add('is-invalid');
                return;
            }
        }
        
        // Check for excessive consecutive consonants (more than 8 is suspicious)
        if (/[bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]{9,}/.test(value)) {
            input.setCustomValidity('Please enter a valid overview.');
            input.classList.add('is-invalid');
            return;
        }
        
        // Check if it contains at least some spaces (real descriptions have words)
        const wordCount = value.split(/\s+/).filter(word => word.length > 0).length;
        if (wordCount < 3) {
            input.setCustomValidity('Overview must contain at least 3 words.');
            input.classList.add('is-invalid');
            return;
        }
        
        // Check for excessive numbers (more than 20% is suspicious for an overview)
        const digitCount = (value.match(/\d/g) || []).length;
        const digitRatio = value.length > 0 ? digitCount / value.length : 0;
        if (digitRatio > 0.2) {
            input.setCustomValidity('Overview contains too many numbers.');
            input.classList.add('is-invalid');
            return;
        }
        
        // Valid
        input.setCustomValidity('');
        input.classList.remove('is-invalid');
    }
    
    // NEW: Update character counter
    function updateOverviewCounter(length) {
        if (!overviewCounter) return;
        
        const maxLength = 2000;
        const remaining = maxLength - length;
        
        if (length > maxLength) {
            overviewCounter.textContent = `${length} / ${maxLength} characters (${Math.abs(remaining)} over limit)`;
            overviewCounter.classList.remove('text-secondary');
            overviewCounter.classList.add('text-danger');
        } else if (length >= maxLength * 0.9) {
            overviewCounter.textContent = `${length} / ${maxLength} characters (${remaining} remaining)`;
            overviewCounter.classList.remove('text-secondary', 'text-danger');
            overviewCounter.classList.add('text-warning');
        } else {
            overviewCounter.textContent = `${length} / ${maxLength} characters`;
            overviewCounter.classList.remove('text-warning', 'text-danger');
            overviewCounter.classList.add('text-secondary');
        }
    }
    
    function updateDurationHelper(minutes) {
        if (!durationHelper) return;
        
        if (!minutes || minutes <= 0) {
            durationHelper.textContent = '149 = 2h 29m';
            return;
        }
        
        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;
        
        if (hours > 0) {
            durationHelper.textContent = `${minutes} = ${hours}h ${mins}m`;
        } else {
            durationHelper.textContent = `${minutes} = ${mins}m`;
        }
    }

    function validateImagePath(input, type) {
        const value = input.value.trim();
        
        // Check if empty
        if (value === '') {
            input.setCustomValidity('');
            input.classList.remove('is-invalid');
            return;
        }
        
        // Valid image extensions
        const validExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        
        // Get file extension
        const extension = value.split('.').pop().toLowerCase().split('?')[0]; // Handle query strings
        
        // Check if valid extension
        if (!validExtensions.includes(extension)) {
            input.setCustomValidity(`${type} path must end with a valid image extension (.jpg, .jpeg, .png, .webp, .gif)`);
            input.classList.add('is-invalid');
            return;
        }
        
        // Check if it's a URL or relative path
        const isUrl = /^https?:\/\/.+/i.test(value);
        const isRelativePath = /^\//.test(value) || /^assets\//.test(value);
        
        if (!isUrl && !isRelativePath) {
            input.setCustomValidity(`${type} path must be either a full URL (https://...) or a relative path (/... or assets/...)`);
            input.classList.add('is-invalid');
            return;
        }
        
        // If it's a URL, validate URL format
        if (isUrl) {
            try {
                const url = new URL(value);
                if (!['http:', 'https:'].includes(url.protocol)) {
                    input.setCustomValidity(`${type} URL must use http:// or https://`);
                    input.classList.add('is-invalid');
                    return;
                }
            } catch (e) {
                input.setCustomValidity(`Invalid ${type} URL format`);
                input.classList.add('is-invalid');
                return;
            }
        }
        
        // Check for spaces in path (spaces should be encoded as %20)
        if (/\s/.test(value)) {
            input.setCustomValidity(`${type} path cannot contain spaces. Use %20 for spaces in URLs.`);
            input.classList.add('is-invalid');
            return;
        }
        
        // Check path length (not too short, not too long)
        if (value.length < 5) {
            input.setCustomValidity(`${type} path is too short`);
            input.classList.add('is-invalid');
            return;
        }
        
        if (value.length > 500) {
            input.setCustomValidity(`${type} path is too long (max 500 characters)`);
            input.classList.add('is-invalid');
            return;
        }
        
        // Valid
        input.setCustomValidity('');
        input.classList.remove('is-invalid');
    }

    


});