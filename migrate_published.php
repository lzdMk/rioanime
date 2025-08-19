#!/usr/bin/env php
<?php

/**
 * Database Migration Runner
 * Run this script to apply the published column migration
 */

// Define paths
$projectRoot = dirname(__FILE__);
require_once $projectRoot . '/app/Config/Paths.php';

$paths = new Config\Paths();
require $paths->systemDirectory . '/bootstrap.php';

// Get database config and run migration
$migrate = \Config\Services::migrations();

try {
    echo "Running database migration to add published column...\n";
    
    // Run the migration
    $migrate->latest();
    
    echo "Migration completed successfully!\n";
    echo "The 'published', 'published_at', and 'unpublished_at' columns have been added to anime_data table.\n";
    echo "All existing anime records have been set to published by default.\n";
    
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    echo "You may need to run this migration manually via the CodeIgniter CLI.\n";
}
