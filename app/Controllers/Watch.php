<?php

namespace App\Controllers;

class Watch extends BaseController
{
    protected $animeModel;

    public function __construct()
    {
        $this->animeModel = new \App\Models\AnimeModel();
    }

    public function index($slug = null)
    {
        if (!$slug) {
            return redirect()->to('/');
        }

        // Get anime by slug
        $anime = $this->animeModel->getAnimeBySlug($slug);
        
        if (!$anime) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Parse URLs to get episodes
        $episodes = $this->parseEpisodes($anime['urls']);

        $data = [
            'anime' => $anime,
            'episodes' => $episodes,
            'currentEpisode' => 1, // Default to first episode
            'slug' => $slug
        ];

        return view('watch', $data);
    }

    public function episode($slug = null, $episode = 1)
    {
        if (!$slug) {
            return redirect()->to('/');
        }

        // Get anime by slug
        $anime = $this->animeModel->getAnimeBySlug($slug);
        
        if (!$anime) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Parse URLs to get episodes
        $episodes = $this->parseEpisodes($anime['urls']);

        $data = [
            'anime' => $anime,
            'episodes' => $episodes,
            'currentEpisode' => (int)$episode,
            'slug' => $slug
        ];

        return view('watch', $data);
    }

    /**
     * Parse episode URLs from the database
     */
    private function parseEpisodes($urlsString)
    {
        if (empty($urlsString)) {
            return [];
        }

        $episodes = [];
        
        // Split URLs by newline or comma
        $urlLines = preg_split('/[\r\n,]+/', $urlsString);
        
        $episodeCounter = 1;
        foreach ($urlLines as $url) {
            $url = trim($url);
            if (!empty($url)) {
                $episodes[] = [
                    'episode_number' => $episodeCounter,
                    'url' => $url,
                    'title' => 'Episode ' . $episodeCounter
                ];
                $episodeCounter++;
            }
        }

        return $episodes;
    }

    /**
     * AJAX endpoint to get episode URL
     */
    public function getEpisodeUrl()
    {
        $request = $this->request;
        
        if (!$request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $title = $request->getPost('title');
        $episode = $request->getPost('episode');

        if (!$title || !$episode) {
            return $this->response->setJSON(['error' => 'Missing parameters']);
        }

        $anime = $this->animeModel->getAnimeByTitle($title);

        if (!$anime) {
            return $this->response->setJSON(['error' => 'Anime not found']);
        }

        $episodes = $this->parseEpisodes($anime['urls']);
        
        foreach ($episodes as $ep) {
            if ($ep['episode_number'] == $episode) {
                return $this->response->setJSON([
                    'success' => true,
                    'url' => $ep['url'],
                    'episode' => $episode
                ]);
            }
        }

        return $this->response->setJSON(['error' => 'Episode not found']);
    }
}
