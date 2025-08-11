<?php
namespace App\Controllers;

class Watch extends BaseController
{
    protected $animeModel;
    protected $animeViewModel;
    protected $accountModel; // For tracking watched anime

    public function __construct()
    {
        $this->animeModel = new \App\Models\AnimeModel();
        $this->animeViewModel = new \App\Models\AnimeViewModel();
    $this->accountModel = new \App\Models\AccountModel();
    }

    /**
     * Detect the source of a video URL
     */
    private function urlSource($url)
    {
        $lowerUrl = strtolower($url);
        if (strpos($lowerUrl, 'youtube.com') !== false) {
            return 'YouTube';
        } elseif (strpos($lowerUrl, 'blogger') !== false) {
            return 'Blogger';
        } elseif (strpos($lowerUrl, 'drive.google.com') !== false) {
            return 'Gdrive';
        } elseif (strpos($lowerUrl, 'terabox') !== false) {
            return 'Terabox';
        } elseif (strpos($lowerUrl, 'archive.org') !== false || strpos($lowerUrl, 'pub') !== false) {
            return 'Malupet';
        } else {
            return 'Unknown SourceType';
        }
    }

     /**
     * Display the watch page for an anime
     */
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

        // Increment view count for this anime and user IP
        $user_ip = $this->request->getIPAddress();
        $this->animeViewModel->incrementView($anime['anime_id'], $anime['title'], $user_ip);

        // If user logged in, record this anime as watched
        if (session()->get('isLoggedIn')) {
            $userId = session()->get('user_id');
            if ($userId) {
                $this->accountModel->addWatchAnime($userId, $anime['anime_id']);
            }
        }

        // Parse URLs to get episodes
        $episodes = $this->parseEpisodes($anime['urls']);

        // Prepare JavaScript data
        $jsData = [
            'animeId' => $anime['anime_id'],
            'title' => $anime['title'],
            'slug' => $slug,
            'currentEpisode' => 1,
            'totalEpisodes' => count($episodes),
            'baseUrl' => base_url(),
            'currentEpisodeUrl' => !empty($episodes) ? $episodes[0]['url'] : '',
            'sourceType' => !empty($episodes) ? $episodes[0]['sourceType'] : ''
        ];

        $data = [
            'anime' => $anime,
            'episodes' => $episodes,
            'currentEpisode' => 1,
            'currentEpisodeUrl' => !empty($episodes) ? $episodes[0]['url'] : '',
            'slug' => $slug,
            'sourceType' => !empty($episodes) ? $episodes[0]['sourceType'] : '',
            'jsData' => $jsData
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
                    'title' => 'Episode ' . $episodeCounter,
                    'sourceType' => $this->urlSource($url)
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
                    'episode' => $episode,
                    'sourceType' => $ep['sourceType']
                ]);
            }
        }

        return $this->response->setJSON(['error' => 'Episode not found']);
    }

    /**
     * Display a specific episode of an anime
     */
    public function episode($slug = null, $episodeNumber = 1)
    {
        if (!$slug) {
            return redirect()->to('/');
        }

        // Get anime by slug
        $anime = $this->animeModel->getAnimeBySlug($slug);
        if (!$anime) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Increment view count for this anime and user IP
        $user_ip = $this->request->getIPAddress();
        $this->animeViewModel->incrementView($anime['anime_id'], $anime['title'], $user_ip);

        // If user logged in, record this anime as watched
        if (session()->get('isLoggedIn')) {
            $userId = session()->get('user_id');
            if ($userId) {
                $this->accountModel->addWatchAnime($userId, $anime['anime_id']);
            }
        }

        // Parse URLs to get episodes
        $episodes = $this->parseEpisodes($anime['urls']);

        // Validate episode number
        $episodeNumber = (int)$episodeNumber;
        if ($episodeNumber < 1 || $episodeNumber > count($episodes)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Prepare JavaScript data
        $jsData = [
            'animeId' => $anime['anime_id'],
            'title' => $anime['title'],
            'slug' => $slug,
            'currentEpisode' => $episodeNumber,
            'totalEpisodes' => count($episodes),
            'baseUrl' => base_url(),
            'currentEpisodeUrl' => !empty($episodes) ? $episodes[$episodeNumber - 1]['url'] : '',
            'sourceType' => !empty($episodes) ? $episodes[$episodeNumber - 1]['sourceType'] : ''
        ];

        $data = [
            'anime' => $anime,
            'episodes' => $episodes,
            'currentEpisode' => $episodeNumber,
            'currentEpisodeUrl' => !empty($episodes) ? $episodes[$episodeNumber - 1]['url'] : '',
            'slug' => $slug,
            'sourceType' => !empty($episodes) ? $episodes[$episodeNumber - 1]['sourceType'] : '',
            'jsData' => $jsData
        ];

        return view('watch', $data);
    }
}
