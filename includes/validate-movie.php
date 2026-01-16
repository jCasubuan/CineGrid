<?php
/**
 * Validate movie title to prevent gibberish/random text
 */
function isValidMovieTitle($title) {
    $title = trim($title);
    
    if (strlen($title) < 2 || strlen($title) > 200) {
        return [
            'valid' => false,
            'error' => 'Title must be between 2 and 200 characters.'
        ];
    }
    
    if (!preg_match('/^[A-Za-z0-9\s\-:,.\'"&!?()]+$/', $title)) {
        return [
            'valid' => false,
            'error' => 'Title contains invalid characters. Use only letters, numbers, and common punctuation.'
        ];
    }
    
    if (!preg_match('/[aeiouAEIOU]/', $title)) {
        return [
            'valid' => false,
            'error' => 'Please enter a valid movie title.'
        ];
    }
    
    preg_match_all('/[aeiouAEIOU]/', $title, $vowels);
    $vowelCount = count($vowels[0]);
    preg_match_all('/[a-zA-Z]/', $title, $letters);
    $totalLetters = count($letters[0]);
    
    if ($totalLetters > 0) {
        $vowelRatio = $vowelCount / $totalLetters;
        if ($vowelRatio < 0.15) {
            return [
                'valid' => false,
                'error' => 'Please enter a valid movie title (appears to be random text).'
            ];
        }
    }
    
    if (preg_match('/[bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]{6,}/', $title)) {
        return [
            'valid' => false,
            'error' => 'Please enter a valid movie title.'
        ];
    }
    
    return [
        'valid' => true,
        'error' => null
    ];
}

/**
 * Validate release year - must be exactly 4 digits
 */
function isValidReleaseYear($year) {
    if (empty($year)) {
        return [
            'valid' => false,
            'error' => 'Release year is required.'
        ];
    }
    
    $yearStr = (string)$year;
    
    if (strlen($yearStr) !== 4) {
        return [
            'valid' => false,
            'error' => 'Release year must be exactly 4 digits.'
        ];
    }
    
    if (!preg_match('/^\d{4}$/', $yearStr)) {
        return [
            'valid' => false,
            'error' => 'Release year must contain only numbers.'
        ];
    }
    
    $yearInt = (int)$year;
    if ($yearInt < 1888 || $yearInt > 2026) {
        return [
            'valid' => false,
            'error' => 'Release year must be between 1888 and 2026.'
        ];
    }
    
    return [
        'valid' => true,
        'error' => null
    ];
}

/**
 * Validate movie duration - must be positive and reasonable
 */
function isValidDuration($duration) {
    // Check if empty
    if (empty($duration) && $duration !== 0) {
        return [
            'valid' => false,
            'error' => 'Duration is required.'
        ];
    }
    
    // Check if numeric
    if (!is_numeric($duration)) {
        return [
            'valid' => false,
            'error' => 'Duration must be a number.'
        ];
    }
    
    $duration = (int)$duration;
    
    // Check if negative or zero
    if ($duration <= 0) {
        return [
            'valid' => false,
            'error' => 'Duration must be greater than 0 minutes.'
        ];
    }
    
    // Check minimum duration
    if ($duration < 1) {
        return [
            'valid' => false,
            'error' => 'Duration must be at least 1 minute.'
        ];
    }
    
    // Check maximum duration (1440 minutes = 24 hours)
    // This allows for very long films like experimental cinema or extended cuts
    if ($duration > 1440) {
        return [
            'valid' => false,
            'error' => 'Duration cannot exceed 1440 minutes (24 hours). Please verify the duration.'
        ];
    }
    
    return [
        'valid' => true,
        'error' => null
    ];
}

/**
 * Validate TMDB ID - flexible for future growth
 */
function isValidTmdbId($tmdbId) {
    // Check if empty
    if (empty($tmdbId) && $tmdbId !== 0) {
        return [
            'valid' => false,
            'error' => 'TMDB ID is required.'
        ];
    }
    
    // Check if numeric
    if (!is_numeric($tmdbId)) {
        return [
            'valid' => false,
            'error' => 'TMDB ID must be a number.'
        ];
    }
    
    $tmdbId = (int)$tmdbId;
    
    // Check if positive
    if ($tmdbId <= 0) {
        return [
            'valid' => false,
            'error' => 'TMDB ID must be a positive number.'
        ];
    }
    
    // Check length (1-8 digits for flexibility)
    // Current TMDB IDs are around 6-7 digits, setting max to 8 allows for growth
    $length = strlen((string)$tmdbId);
    if ($length < 1 || $length > 8) {
        return [
            'valid' => false,
            'error' => 'TMDB ID must be between 1 and 8 digits.'
        ];
    }
    
    return [
        'valid' => true,
        'error' => null
    ];
}

/**
 * Validate movie overview/synopsis
 */
function isValidOverview($overview) {
    // Trim whitespace
    $overview = trim($overview);
    
    // Check if empty
    if (empty($overview)) {
        return [
            'valid' => false,
            'error' => 'Overview is required.'
        ];
    }
    
    // Check minimum length
    if (strlen($overview) < 10) {
        return [
            'valid' => false,
            'error' => 'Overview must be at least 10 characters.'
        ];
    }
    
    // Check maximum length
    if (strlen($overview) > 2000) {
        return [
            'valid' => false,
            'error' => 'Overview cannot exceed 2000 characters.'
        ];
    }
    
    // Check if it's just repeated characters
    if (preg_match('/^(.)\1{9,}$/', $overview)) {
        return [
            'valid' => false,
            'error' => 'Please enter a valid overview (not just repeated characters).'
        ];
    }
    
    // Check for vowels
    if (!preg_match('/[aeiouAEIOU]/', $overview)) {
        return [
            'valid' => false,
            'error' => 'Please enter a valid overview.'
        ];
    }
    
    // Calculate vowel ratio
    preg_match_all('/[aeiouAEIOU]/', $overview, $vowels);
    $vowelCount = count($vowels[0]);
    preg_match_all('/[a-zA-Z]/', $overview, $letters);
    $totalLetters = count($letters[0]);
    
    if ($totalLetters > 0) {
        $vowelRatio = $vowelCount / $totalLetters;
        if ($vowelRatio < 0.10) {
            return [
                'valid' => false,
                'error' => 'Please enter a valid overview (appears to be random text).'
            ];
        }
    }
    
    // Check for excessive consecutive consonants
    if (preg_match('/[bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]{9,}/', $overview)) {
        return [
            'valid' => false,
            'error' => 'Please enter a valid overview.'
        ];
    }
    
    // Check word count (must have at least 3 words)
    $words = preg_split('/\s+/', $overview, -1, PREG_SPLIT_NO_EMPTY);
    if (count($words) < 3) {
        return [
            'valid' => false,
            'error' => 'Overview must contain at least 3 words.'
        ];
    }
    
    // Check for excessive numbers
    preg_match_all('/\d/', $overview, $digits);
    $digitCount = count($digits[0]);
    $digitRatio = strlen($overview) > 0 ? $digitCount / strlen($overview) : 0;
    
    if ($digitRatio > 0.2) {
        return [
            'valid' => false,
            'error' => 'Overview contains too many numbers.'
        ];
    }
    
    return [
        'valid' => true,
        'error' => null
    ];
}

/**
 * Validate image path (Poster or Backdrop)
 */
function isValidImagePath($path, $type = 'Image') {
    // Trim whitespace
    $path = trim($path);
    
    // Check if empty
    if (empty($path)) {
        return [
            'valid' => false,
            'error' => "$type path is required."
        ];
    }
    
    // Check length
    if (strlen($path) < 5) {
        return [
            'valid' => false,
            'error' => "$type path is too short."
        ];
    }
    
    if (strlen($path) > 500) {
        return [
            'valid' => false,
            'error' => "$type path is too long (max 500 characters)."
        ];
    }
    
    // Valid image extensions
    $validExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    
    // Extract extension (handle query strings)
    $pathParts = explode('?', $path);
    $pathWithoutQuery = $pathParts[0];
    $extension = strtolower(pathinfo($pathWithoutQuery, PATHINFO_EXTENSION));
    
    // Check if valid extension
    if (!in_array($extension, $validExtensions)) {
        return [
            'valid' => false,
            'error' => "$type path must end with a valid image extension (.jpg, .jpeg, .png, .webp, .gif)"
        ];
    }
    
    // Check if it's a URL or relative path
    $isUrl = preg_match('/^https?:\/\/.+/i', $path);
    $isRelativePath = preg_match('/^\//', $path) || preg_match('/^assets\//', $path);
    
    if (!$isUrl && !$isRelativePath) {
        return [
            'valid' => false,
            'error' => "$type path must be either a full URL (https://...) or a relative path (/... or assets/...)"
        ];
    }
    
    // If it's a URL, validate URL format
    if ($isUrl) {
        if (!filter_var($path, FILTER_VALIDATE_URL)) {
            return [
                'valid' => false,
                'error' => "Invalid $type URL format."
            ];
        }
        
        // Check if URL uses http or https
        if (!preg_match('/^https?:\/\//', $path)) {
            return [
                'valid' => false,
                'error' => "$type URL must use http:// or https://"
            ];
        }
    }
    
    // Check for spaces (should be encoded as %20 in URLs)
    if (preg_match('/\s/', $path)) {
        return [
            'valid' => false,
            'error' => "$type path cannot contain spaces. Use %20 for spaces in URLs."
        ];
    }
    
    // Check for invalid characters (basic check)
    if (preg_match('/[<>"|*?]/', $path)) {
        return [
            'valid' => false,
            'error' => "$type path contains invalid characters."
        ];
    }
    
    return [
        'valid' => true,
        'error' => null
    ];
}

function isValidMovieRating($rating)
{
    $rating = trim((string)$rating);

    // Required
    if ($rating === '') {
        return [
            'valid' => false,
            'error' => 'Rating is required.'
        ];
    }

    // Reject scientific notation, letters, symbols
    if (!preg_match('/^\d+(\.\d+)?$/', $rating)) {
        return [
            'valid' => false,
            'error' => 'Rating must be a valid number.'
        ];
    }

    // Reject extremely long input (anti-abuse / anti-overflow)
    if (strlen($rating) > 4) {
        return [
            'valid' => false,
            'error' => 'Rating value is too large.'
        ];
    }

    // Enforce correct range + precision
    if (!preg_match('/^(10(\.0)?|[0-9](\.[0-9])?)$/', $rating)) {
        return [
            'valid' => false,
            'error' => 'Rating must be between 0 and 10 with at most one decimal place.'
        ];
    }

    return [
        'valid' => true,
        'error' => null
    ];
}



?>