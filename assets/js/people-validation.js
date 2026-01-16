// People (Directors, Writers, Actors) Validation
document.addEventListener('DOMContentLoaded', function() {
    
    // Validate all director name inputs
    validatePeopleInputs('directorName', 'Director');
    
    // Validate all writer name inputs
    validatePeopleInputs('writerName', 'Writer');
    
    // Validate all actor name inputs
    validatePeopleInputs('actorName', 'Actor');
    
    // Validate character names
    validateCharacterInputs();
    
    function validatePeopleInputs(className, type) {
        const inputs = document.querySelectorAll(`input[name="${className}[]"]`);
        
        inputs.forEach(input => {
            input.addEventListener('input', function(e) {
                validatePersonName(e.target, type);
            });
            
            input.addEventListener('blur', function(e) {
                validatePersonName(e.target, type);
            });
            
            // Prevent numbers from being typed
            input.addEventListener('keypress', function(e) {
                // Allow letters, spaces, hyphens, apostrophes, periods
                const char = e.key;
                const validPattern = /^[A-Za-zÀ-ÿ\s\-'\.]$/;
                
                if (!validPattern.test(char) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab') {
                    e.preventDefault();
                }
            });
        });
    }
    
    function validateCharacterInputs() {
        const inputs = document.querySelectorAll('input[name="characterName[]"]');
        
        inputs.forEach(input => {
            input.addEventListener('input', function(e) {
                validateCharacterName(e.target);
            });
            
            input.addEventListener('blur', function(e) {
                validateCharacterName(e.target);
            });
        });
    }
    
    function validatePersonName(input, type) {
        const value = input.value.trim();
        
        // If empty and not required, skip validation
        if (value === '' && !input.hasAttribute('required')) {
            input.setCustomValidity('');
            input.classList.remove('is-invalid');
            return;
        }
        
        // Check if empty and required
        if (value === '' && input.hasAttribute('required')) {
            input.setCustomValidity(`${type} name is required.`);
            input.classList.add('is-invalid');
            return;
        }
        
        // Check length
        if (value.length < 2) {
            input.setCustomValidity(`${type} name must be at least 2 characters.`);
            input.classList.add('is-invalid');
            return;
        }
        
        if (value.length > 100) {
            input.setCustomValidity(`${type} name cannot exceed 100 characters.`);
            input.classList.add('is-invalid');
            return;
        }
        
        // Check for valid characters only (letters, spaces, hyphens, apostrophes, periods)
        if (!/^[A-Za-zÀ-ÿ\s\-'\.]+$/u.test(value)) {
            input.setCustomValidity(`${type} name can only contain letters, spaces, hyphens, apostrophes, and periods.`);
            input.classList.add('is-invalid');
            return;
        }
        
        // Check for numbers
        if (/\d/.test(value)) {
            input.setCustomValidity(`${type} name cannot contain numbers.`);
            input.classList.add('is-invalid');
            return;
        }
        
        // Check for vowels (gibberish detection)
        const hasVowels = /[aeiouAEIOUàèéêëïîôùûüÿæœ]/i.test(value);
        if (!hasVowels) {
            input.setCustomValidity(`Please enter a valid ${type} name.`);
            input.classList.add('is-invalid');
            return;
        }
        
        // Calculate vowel ratio
        const vowelCount = (value.match(/[aeiouAEIOUàèéêëïîôùûüÿæœ]/gi) || []).length;
        const letterCount = (value.match(/[a-zA-ZÀ-ÿ]/gu) || []).length;
        const vowelRatio = letterCount > 0 ? vowelCount / letterCount : 0;
        
        if (vowelRatio < 0.15) {
            input.setCustomValidity(`Please enter a valid ${type} name (appears to be random text).`);
            input.classList.add('is-invalid');
            return;
        }
        
        // Check for excessive consecutive consonants
        if (/[bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]{6,}/.test(value)) {
            input.setCustomValidity(`Please enter a valid ${type} name.`);
            input.classList.add('is-invalid');
            return;
        }
        
        // Check if starts/ends with invalid characters
        if (/^[\s\-'\.]|[\s\-'\.]$/.test(value)) {
            input.setCustomValidity(`${type} name cannot start or end with spaces or special characters.`);
            input.classList.add('is-invalid');
            return;
        }
        
        // Check for consecutive spaces or special characters
        if (/[\s\-'\.]{2,}/.test(value)) {
            input.setCustomValidity(`${type} name cannot have consecutive spaces or special characters.`);
            input.classList.add('is-invalid');
            return;
        }
        
        // Valid
        input.setCustomValidity('');
        input.classList.remove('is-invalid');
    }
    
    function validateCharacterName(input) {
        const value = input.value.trim();
        
        // Character name is optional
        if (value === '') {
            input.setCustomValidity('');
            input.classList.remove('is-invalid');
            return;
        }
        
        // Check length
        if (value.length < 2 || value.length > 100) {
            input.setCustomValidity('Character name must be between 2 and 100 characters.');
            input.classList.add('is-invalid');
            return;
        }
        
        // Allow letters, numbers, spaces, hyphens, apostrophes, periods (more flexible for characters like "R2-D2")
        if (!/^[A-Za-zÀ-ÿ0-9\s\-'\.]+$/u.test(value)) {
            input.setCustomValidity('Character name contains invalid characters.');
            input.classList.add('is-invalid');
            return;
        }
        
        // Must contain at least one letter
        if (!/[a-zA-Z]/.test(value)) {
            input.setCustomValidity('Character name must contain at least one letter.');
            input.classList.add('is-invalid');
            return;
        }
        
        // Valid
        input.setCustomValidity('');
        input.classList.remove('is-invalid');
    }
});