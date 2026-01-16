<?php
/**
 * Validate person names (Directors, Writers, Actors)
 * Prevents gibberish, random characters, and numbers
 */
function isValidPersonName($name, $type = 'Person') {
    // Trim whitespace
    $name = trim($name);
    
    // Check if empty
    if (empty($name)) {
        return [
            'valid' => false,
            'error' => "$type name is required."
        ];
    }
    
    // Check length (minimum 2 characters, maximum 100)
    if (strlen($name) < 2 || strlen($name) > 100) {
        return [
            'valid' => false,
            'error' => "$type name must be between 2 and 100 characters."
        ];
    }
    
    // Check for valid characters only (letters, spaces, hyphens, apostrophes, periods)
    // This allows names like: "John O'Brien", "Mary-Jane", "Robert Downey Jr.", "Lupita Nyong'o"
    if (!preg_match("/^[A-Za-zÀ-ÿ\s\-'\.]+$/u", $name)) {
        return [
            'valid' => false,
            'error' => "$type name can only contain letters, spaces, hyphens, apostrophes, and periods."
        ];
    }
    
    // Check if name contains at least one vowel (prevents gibberish like "Xyz", "Bcd")
    if (!preg_match('/[aeiouAEIOUàèéêëïîôùûüÿæœ]/iu', $name)) {
        return [
            'valid' => false,
            'error' => "Please enter a valid $type name."
        ];
    }
    
    // Check vowel ratio (real names have ~20-50% vowels)
    preg_match_all('/[aeiouAEIOUàèéêëïîôùûüÿæœ]/iu', $name, $vowels);
    $vowelCount = count($vowels[0]);
    preg_match_all('/[a-zA-ZÀ-ÿ]/u', $name, $letters);
    $totalLetters = count($letters[0]);
    
    if ($totalLetters > 0) {
        $vowelRatio = $vowelCount / $totalLetters;
        if ($vowelRatio < 0.15) {
            return [
                'valid' => false,
                'error' => "Please enter a valid $type name (appears to be random text)."
            ];
        }
    }
    
    // Check for excessive consecutive consonants (more than 5 in a row is suspicious)
    if (preg_match('/[bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]{6,}/', $name)) {
        return [
            'valid' => false,
            'error' => "Please enter a valid $type name."
        ];
    }
    
    // Check if name contains only numbers
    if (preg_match('/^\d+$/', $name)) {
        return [
            'valid' => false,
            'error' => "$type name cannot contain only numbers."
        ];
    }
    
    // Check for excessive numbers in name (more than 2 digits is suspicious)
    $digitCount = preg_match_all('/\d/', $name);
    if ($digitCount > 2) {
        return [
            'valid' => false,
            'error' => "$type name contains too many numbers."
        ];
    }
    
    // Check for excessive special characters
    $specialCharCount = preg_match_all('/[\-\'\.]/', $name);
    if ($specialCharCount > 3) {
        return [
            'valid' => false,
            'error' => "$type name contains too many special characters."
        ];
    }
    
    // Check if it starts/ends with invalid characters
    if (preg_match('/^[\s\-\'\.]|[\s\-\'\.]$/', $name)) {
        return [
            'valid' => false,
            'error' => "$type name cannot start or end with spaces or special characters."
        ];
    }
    
    // Check for multiple consecutive spaces or special characters
    if (preg_match('/[\s\-\'\.]{2,}/', $name)) {
        return [
            'valid' => false,
            'error' => "$type name cannot have consecutive spaces or special characters."
        ];
    }
    
    return [
        'valid' => true,
        'error' => null
    ];
}

/**
 * Validate character name (for actors)
 */
function isValidCharacterName($name) {
    // Character names can be more flexible (e.g., "Iron Man", "T-800", "R2-D2")
    $name = trim($name);
    
    // Check if empty (character name is optional in some cases)
    if (empty($name)) {
        return [
            'valid' => true, // Allow empty for "Role TBA"
            'error' => null
        ];
    }
    
    // Check length
    if (strlen($name) < 2 || strlen($name) > 100) {
        return [
            'valid' => false,
            'error' => 'Character name must be between 2 and 100 characters.'
        ];
    }
    
    // Allow letters, numbers, spaces, hyphens, apostrophes, periods
    if (!preg_match("/^[A-Za-zÀ-ÿ0-9\s\-'\.]+$/u", $name)) {
        return [
            'valid' => false,
            'error' => 'Character name contains invalid characters.'
        ];
    }
    
    // Check if it contains at least one letter
    if (!preg_match('/[a-zA-Z]/', $name)) {
        return [
            'valid' => false,
            'error' => 'Character name must contain at least one letter.'
        ];
    }
    
    return [
        'valid' => true,
        'error' => null
    ];
}
?>