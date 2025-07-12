<?php
namespace App\Models;

use CodeIgniter\Model;

class AnimeViewModel extends Model
{
    protected $table = 'anime_views';
    protected $primaryKey = 'anime_id';
    protected $allowedFields = ['anime_id', 'title', 'user_ip', 'views', 'viewed_at'];
    protected $returnType = 'array';
    public $useTimestamps = false;

    /**
     * Increment view count for an anime and user IP, or insert if not exists
     */
    public function incrementView($anime_id, $title, $user_ip)
    {
        $existing = $this->where([
            'anime_id' => $anime_id,
            'user_ip' => $user_ip
        ])->first();
        $now = date('Y-m-d H:i:s');
        if ($existing) {
            // Increment views and update viewed_at
            $this->update($existing['anime_id'], [
                'views' => $existing['views'] + 1,
                'viewed_at' => $now
            ]);
        } else {
            $data = [
                'anime_id' => $anime_id,
                'title' => $title,
                'user_ip' => $user_ip,
                'views' => 1,
                'viewed_at' => $now
            ];
            $this->insert($data);
        }
    }
    /**
     * Get trending anime by view count for a given period
     * period 'today', 'week', 'month'
     */
    public function getTrendingByPeriod($period = 'today', $limit = 10)
    {
        $db = \Config\Database::connect();
        // Define periods in fallback order
        $periodOrder = ['today', 'week', 'month', 'all'];
        $periods = [
            'today' => date('Y-m-d 00:00:00'),
            'week' => date('Y-m-d H:i:s', strtotime('-7 days')),
            'month' => date('Y-m-d H:i:s', strtotime('-1 month')),
            'all' => null // all-time
        ];
        // Start with the selected period, then fallback to next periods
        $startIndex = array_search($period, $periodOrder);
        $orderedPeriods = array_slice($periodOrder, $startIndex);
        $result = [];
        $animeIds = [];
        // For each period in order, fill up to $limit unique anime
        foreach ($orderedPeriods as $p) {
            $builder = $db->table('anime_views');
            $builder->select('anime_views.anime_id, anime_data.title, anime_data.type, anime_data.ratings, anime_data.backgroundImage, anime_data.genres, anime_data.total_ep, anime_data.status, anime_data.studios, anime_data.synopsis, SUM(anime_views.views) as total_views')
                ->join('anime_data', 'anime_data.anime_id = anime_views.anime_id')
                ->groupBy('anime_views.anime_id')
                ->orderBy('total_views', 'DESC');
            if ($periods[$p] !== null) {
                $builder->where('anime_views.viewed_at >=', $periods[$p]);
            }
            $rows = $builder->get()->getResultArray();
            foreach ($rows as $row) {
                if (count($result) >= $limit) break;
                if (!in_array($row['anime_id'], $animeIds)) {
                    $result[] = $row;
                    $animeIds[] = $row['anime_id'];
                }
            }
            if (count($result) >= $limit) break;
        }
        // Return up to $limit unique anime, prioritizing the selected period, then fallback
        return array_slice($result, 0, $limit);
    }
}
