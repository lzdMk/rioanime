<?php

namespace App\Models;

use CodeIgniter\Model;


class AnimeModel extends Model
{
    protected $table = 'anime_data';
    protected $primaryKey = 'anime_id';
    protected $allowedFields = [
        'title',
        'language',
        'type',
        'total_ep',
        'ratings',
        'genres',
        'status',
        'studios',
        'urls',
        'backgroundImage',
        'synopsis',
        'published',
        'published_at',
        'unpublished_at'
    ];

    protected $returnType = 'array';

    /**
     * Get a random anime
     */
    public function getRandomAnime()
    {
        return $this->builder()
            ->where('published', 1)
            ->orderBy('RAND()')
            ->limit(1)
            ->get()
            ->getRowArray();
    }

    /**
     * Get recently updated anime
     */
    public function getRecentlyUpdated($limit = 12, $type = null)
    {
        $builder = $this->builder();
        
        $builder->where('published', 1);
        
        if ($type && $type !== 'all') {
            $builder->where('type', $type);
        }
        
        return $builder->orderBy('anime_id', 'DESC')
                      ->limit($limit)
                      ->get()
                      ->getResultArray();
    }

    /**
     * Get anime by genre/category
     */
    public function getAnimeByGenre($genre, $limit = 5)
    {
        return $this->builder()
                    ->where('published', 1)
                    ->like('genres', $genre)
                    ->limit($limit)
                    ->get()
                    ->getResultArray();
    }

    /**
     * Get featured anime for carousel
     */
    public function getFeaturedAnime($limit = 5)
    {
        return $this->builder()
                    ->where('published', 1)
                    ->where('ratings >=', 8.0)
                    ->orderBy('ratings', 'DESC')
                    ->limit($limit)
                    ->get()
                    ->getResultArray();
    }

    /**
     * Get anime by type
     */
    public function getAnimeByType($type, $limit = 8)
    {
        return $this->builder()
                    ->where('published', 1)
                    ->where('type', $type)
                    ->orderBy('anime_id', 'DESC')
                    ->limit($limit)
                    ->get()
                    ->getResultArray();
    }

    /**
     * Get anime by status
     */
    public function getAnimeByStatus($status, $limit = 8)
    {
        return $this->builder()
                    ->where('published', 1)
                    ->where('status', $status)
                    ->orderBy('anime_id', 'DESC')
                    ->limit($limit)
                    ->get()
                    ->getResultArray();
    }

    /**
     * Get recommended anime based on various criteria
     * This includes high-rated anime and popular genres
     */
    public function getRecommendedAnime($limit = 8)
    {
        return $this->builder()
                    ->where('published', 1)
                    ->where('ratings >=', 7.5) // Good rating threshold
                    ->whereIn('status', ['Finished Airing', 'Currently Airing']) // Available to watch
                    ->orderBy('ratings', 'DESC')
                    ->orderBy('anime_id', 'DESC') // Recent additions as tiebreaker
                    ->limit($limit)
                    ->get()
                    ->getResultArray();
    }

    /**
     * Get anime recommendations based on a specific anime's genres
     */
    public function getRelatedAnime($animeId, $limit = 6)
    {
        // First get the genres of the current anime
        $currentAnime = $this->find($animeId);
        
        if (!$currentAnime || empty($currentAnime['genres'])) {
            return $this->getRecommendedAnime($limit);
        }

        // Extract genres (assuming they're comma-separated)
        $genres = explode(',', $currentAnime['genres']);
        $firstGenre = trim($genres[0]);

        return $this->builder()
                    ->where('published', 1)
                    ->like('genres', $firstGenre)
                    ->where('anime_id !=', $animeId) // Exclude current anime
                    ->where('ratings >=', 7.0)
                    ->orderBy('ratings', 'DESC')
                    ->limit($limit)
                    ->get()
                    ->getResultArray();
    }

    /**
     * Get trending anime (high ratings + recent additions)
     */
    public function getTrendingAnime($limit = 10)
    {
        return $this->builder()
                    ->where('published', 1)
                    ->where('ratings >=', 8.0)
                    ->where('status', 'Currently Airing')
                    ->orderBy('ratings', 'DESC')
                    ->orderBy('anime_id', 'DESC')
                    ->limit($limit)
                    ->get()
                    ->getResultArray();
    }

    /**
     * Get anime by title
     */
    public function getAnimeByTitle($title)
    {
        return $this->builder()
                    ->where('published', 1)
                    ->where('title', $title)
                    ->get()
                    ->getRowArray();
    }

    /**
     * Search anime by title with relevance scoring
     */
    public function searchAnime($query, $limit = 10)
    {
        if (empty(trim($query))) {
            return [];
        }

        $builder = $this->builder();
        
        // Search for exact matches first, then partial matches
        $results = $builder->select('anime_id, title, type, status, ratings, backgroundImage')
                          ->where('published', 1)
                          ->groupStart()
                              ->like('title', $query, 'both')
                          ->groupEnd()
                          ->orderBy("CASE 
                              WHEN LOWER(title) = LOWER('$query') THEN 1
                              WHEN LOWER(title) LIKE LOWER('$query%') THEN 2
                              WHEN LOWER(title) LIKE LOWER('%$query%') THEN 3
                              ELSE 4 
                          END")
                          ->orderBy('ratings', 'DESC')
                          ->limit($limit)
                          ->get()
                          ->getResultArray();

        // Add slug to each result for proper routing
        foreach ($results as &$anime) {
            $anime['slug'] = createSlug($anime['title']);
        }

        return $results;
    }

    /**
     * Get anime by slug (searches through all anime and finds matching slug)
     */
    public function getAnimeBySlug($slug)
    {
        // Get all anime titles and find the one that matches the slug
        $allAnime = $this->builder()
                         ->where('published', 1)
                         ->select('anime_id, title, backgroundImage, type, status, synopsis, urls, ratings, studios, genres')
                         ->get()
                         ->getResultArray();

        foreach ($allAnime as $anime) {
            if (createSlug($anime['title']) === $slug) {
                return $anime;
            }
        }

        return null;
    }

    /**
     * Get multiple anime by array of IDs (preserve given order)
     */
    public function getAnimeByIds(array $ids)
    {
        if (empty($ids)) {
            return [];
        }
        $rows = $this->builder()
                     ->where('published', 1)
                     ->select('anime_id, title, backgroundImage, type, total_ep, status, ratings')
                     ->whereIn('anime_id', $ids)
                     ->get()
                     ->getResultArray();
        // Index by id for order restoration
        $indexed = [];
        foreach ($rows as $r) {
            $indexed[$r['anime_id']] = $r;
        }
        $ordered = [];
        foreach ($ids as $id) {
            if (isset($indexed[$id])) {
                $ordered[] = $indexed[$id];
            }
        }
        return $ordered;
    }
}
