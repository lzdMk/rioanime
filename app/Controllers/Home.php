<?php

namespace App\Controllers;

use App\Models\AnimeModel;

class Home extends BaseController
{
    public function index(): string
    {
        $animeModel = new AnimeModel();
        
        $data = [
            'featuredAnime' => $animeModel->getFeaturedAnime(3),
            'recentlyUpdated' => $animeModel->getRecentlyUpdated(12),
            'actionAnime' => $animeModel->getAnimeByGenre('Action', 6),
            'movieAnime' => $animeModel->getAnimeByType('Movie', 6),
            'completedAnime' => $animeModel->getAnimeByStatus('Finished Airing', 6),
            'recommendedAnime' => $animeModel->getRecommendedAnime(10),
            'trendingAnime' => $animeModel->getTrendingAnime(6)
        ];
        
        return view('homepage', $data);
    }
}
