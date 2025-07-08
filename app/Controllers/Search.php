<?php

namespace App\Controllers;

use App\Models\AnimeModel;

class Search extends BaseController
{
    protected $animeModel;

    public function __construct()
    {
        $this->animeModel = new AnimeModel();
    }

    /**
     * Handle AJAX search requests
     */
    public function ajax()
    {
        // Set response header for JSON
        $this->response->setContentType('application/json');

        // Check if request is AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request method'
            ]);
        }

        // Get search query from request
        $query = $this->request->getPost('query') ?? $this->request->getGet('query');
        
        if (empty(trim($query))) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Search query is required',
                'results' => []
            ]);
        }

        // Perform search
        try {
            $results = $this->animeModel->searchAnime($query, 5); // Limit to 5 most relevant results
            
            // Format results for frontend
            $formattedResults = [];
            foreach ($results as $anime) {
                $formattedResults[] = [
                    'id' => $anime['anime_id'],
                    'title' => $anime['title'],
                    'type' => $anime['type'] ?? 'Unknown',
                    'status' => $anime['status'] ?? 'Unknown',
                    'rating' => $anime['ratings'] ?? 'N/A',
                    'image' => $anime['backgroundImage'] ?? 'https://via.placeholder.com/56x80/8B5CF6/ffffff?text=No+Image',
                    'slug' => createSlug($anime['title']),
                    'url' => base_url('watch/' . createSlug($anime['title']))
                ];
            }

            return $this->response->setJSON([
                'status' => 'success',
                'results' => $formattedResults,
                'total' => count($formattedResults)
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while searching',
                'results' => []
            ]);
        }
    }

    /**
     * Full search page (optional - for future implementation)
     */
    public function index()
    {
        $query = $this->request->getGet('q');
        
        if (empty($query)) {
            return redirect()->to(base_url());
        }

        $results = $this->animeModel->searchAnime($query, 50); // More results for search page
        
        $data = [
            'query' => $query,
            'results' => $results,
            'total' => count($results)
        ];

        return view('search_results', $data);
    }
}
