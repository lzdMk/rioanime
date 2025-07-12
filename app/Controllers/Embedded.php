<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Embedded extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function watch($animeId, $episodeNumber = 1)
    {
        // Security: Only allow requests from your domain or localhost
        $allowedDomains = [
            'http://localhost/rioanime',
        ];
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $allowed = false;
        foreach ($allowedDomains as $domain) {
            if (!empty($referer) && strpos($referer, $domain) === 0) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed) {
            return $this->response->setStatusCode(403)->setBody('Forbidden - Access is restricted.');
        }

        // Get anime data from database
        $query = $this->db->query("SELECT * FROM anime_data WHERE anime_id = ?", [$animeId]);
        $anime = $query->getRowArray();

        if (!$anime) {
            return $this->response->setStatusCode(404)->setBody('Anime not found');
        }

        // Parse episode URLs (stored as comma-separated string)
        $episodeUrls = [];
        if (!empty($anime['urls'])) {
            $urls = explode(',', $anime['urls']);
            foreach ($urls as $index => $url) {
                $episodeUrls[$index + 1] = trim($url);
            }
        }

        // Get the specific episode URL
        if (!isset($episodeUrls[$episodeNumber])) {
            return $this->response->setStatusCode(404)->setBody('Episode not found');
        }

        $videoUrl = $episodeUrls[$episodeNumber];

        // Determine source type
        $sourceType = $this->determineSourceType($videoUrl);

        // Prepare data for the view
        $data = [
            'anime' => $anime,
            'episodeNumber' => $episodeNumber,
            'videoUrl' => $videoUrl,
            'sourceType' => $sourceType,
            'title' => $anime['title'] . ' - Episode ' . $episodeNumber
        ];

        return view('embedded_player', $data);
    }

    private function determineSourceType($url)
    {
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            return 'YouTube';
        } elseif (strpos($url, 'drive.google.com') !== false) {
            return 'Gdrive';
        } elseif (strpos($url, 'blogger.com') !== false || strpos($url, 'blogspot.com') !== false) {
            return 'Blogger';
        } else {
            return 'Direct';
        }
    }
}
