<?php

namespace App\Controllers;

use App\Models\AnimeModel;

class Home extends BaseController
{
    public function index(): string
    {
        $animeModel = new AnimeModel();
        $animeViewModel = new \App\Models\AnimeViewModel();

        $data = [
            'featuredAnime' => $animeModel->getFeaturedAnime(3),
            'recentlyUpdated' => $animeModel->getRecentlyUpdated(12),
            'actionAnime' => $animeModel->getAnimeByGenre('Action', 6),
            'movieAnime' => $animeModel->getAnimeByType('Movie', 6),
            'completedAnime' => $animeModel->getAnimeByStatus('Finished Airing', 6),
            'recommendedAnime' => $animeModel->getRecommendedAnime(10),
            'trendingAnimeToday' => $animeViewModel->getTrendingByPeriod('today', 10),
            'trendingAnimeWeek' => $animeViewModel->getTrendingByPeriod('week', 10),
            'trendingAnimeMonth' => $animeViewModel->getTrendingByPeriod('month', 10)
        ];

        return view('homepage', $data);
    }

    /**
     * Redirect to a random anime page
     */
    public function randomAnime()
    {
        $animeModel = new AnimeModel();
        $randomAnime = $animeModel->getRandomAnime();
        if ($randomAnime && isset($randomAnime['title'])) {
            $slug = createSlug($randomAnime['title']);
            return redirect()->to(base_url('watch/' . $slug));
        }
        // Fallback: redirect to homepage if no anime found
        return redirect()->to(base_url());
    }
}
