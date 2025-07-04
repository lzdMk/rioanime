<?php

if (!function_exists('createSlug')) {
    /**
     * Create a URL-safe slug from a title
     * Examples: 
     * - "Toradora!" becomes "toradora"
     * - "Naruto S2" becomes "naruto-s2"
     * - "One Piece: Special" becomes "one-piece-special"
     */
    function createSlug($title) {
        // Convert to lowercase
        $slug = strtolower($title);
        
        // Remove all special characters except spaces and alphanumeric
        $slug = preg_replace('/[^a-z0-9\s]/', '', $slug);
        
        // Replace spaces with hyphens
        $slug = preg_replace('/\s+/', '-', $slug);
        
        // Remove leading and trailing hyphens
        $slug = trim($slug, '-');
        
        // Remove consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);
        
        return $slug;
    }
}

if (!function_exists('titleFromSlug')) {
    /**
     * Convert a slug back to a title for database lookup
     * Since we remove special characters, we need to search the database
     */
    function titleFromSlug($slug) {
        // For reverse lookup, we'll need to search the database
        // since we can't perfectly reconstruct the original title
        return $slug;
    }
}
