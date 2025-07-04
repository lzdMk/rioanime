<?php

namespace App\Controllers;

class Search extends BaseController
{
    protected $animeModel;

    public function __construct()
    {
        $this->animeModel = new \App\Models\AnimeModel();
    }

    /**
     * AJAX search endpoint
     */
    public function index()
    {
        $request = $this->request;
        
        // Only allow AJAX requests
        if (!$request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request method']);
        }

        $query = $request->getGet('q');
        
        if (empty(trim($query))) {
            return $this->response->setJSON(['results' => []]);
        }

        // Minimum 1 character to search
        if (strlen(trim($query)) < 1) {
            return $this->response->setJSON(['results' => []]);
        }

        try {
            $results = $this->animeModel->searchAnime($query, 8);
            
            // Format results for frontend
            $formattedResults = [];
            foreach ($results as $anime) {
                $formattedResults[] = [
                    'id' => $anime['anime_id'],
                    'title' => $anime['title'],
                    'slug' => $anime['slug'],
                    'type' => $anime['type'],
                    'status' => $anime['status'],
                    'rating' => $anime['ratings'],
                    'image' => $anime['backgroundImage'] ?? 'https://via.placeholder.com/300x400/1a1a2e/ffffff?text=No+Image',
                    'watchUrl' => base_url('watch/' . $anime['slug'])
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'results' => $formattedResults,
                'query' => $query
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => 'Search failed: ' . $e->getMessage()
            ]);
        }
    }
}
