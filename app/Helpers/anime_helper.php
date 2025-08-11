<?php
/**
 * Anime-related helper functions
 */

if (!function_exists('truncateTitle')) {
    function truncateTitle($title, $maxLength = 35) {
        return strlen($title) > $maxLength ? substr($title, 0, $maxLength) . '...' : $title;
    }
}
