<?php
// Test the new slug function
require_once '../app/Helpers/url_helper.php';

// Test cases
$testTitles = [
    'Toradora!',
    'Naruto S2',
    'One Piece: Special',
    'Attack on Titan (Final Season)',
    'JoJo\'s Bizarre Adventure',
    'Kimetsu no Yaiba: Demon Slayer',
    'Re:Zero - Starting Life in Another World'
];

echo "<h2>Slug Generation Test</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Original Title</th><th>Generated Slug</th></tr>";

foreach ($testTitles as $title) {
    $slug = createSlug($title);
    echo "<tr><td>{$title}</td><td>{$slug}</td></tr>";
}

echo "</table>";
?>
