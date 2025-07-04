<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AddTestAnime extends BaseCommand
{
    protected $group       = 'demo';
    protected $name        = 'demo:add-test-anime';
    protected $description = 'Add test anime data for development';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        // Create a test anime with episode URLs
        $testAnime = [
            'title' => 'Test Anime',
            'language' => 'Japanese',
            'type' => 'TV',
            'total_ep' => '3',
            'ratings' => '8.5',
            'genres' => 'Action, Adventure',
            'status' => 'Completed',
            'studios' => 'Test Studio',
            'backgroundImage' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=300&h=400&fit=crop',
            'synopsis' => 'A test anime for development purposes.',
            'urls' => "https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4\nhttps://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4\nhttps://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4"
        ];

        // Check if test anime already exists
        $existingAnime = $db->table('anime_data')->where('title', 'Test Anime')->get()->getRowArray();

        if (!$existingAnime) {
            // Insert test anime
            $db->table('anime_data')->insert($testAnime);
            CLI::write('Test anime inserted successfully!', 'green');
        } else {
            CLI::write('Test anime already exists.', 'yellow');
        }

        CLI::write('You can now test the watch page at: http://localhost:8080/watch/test-anime', 'cyan');
    }
}
