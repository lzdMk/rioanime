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
        // Determine the start date based on the period
        $db = \Config\Database::connect();
        $builder = $db->table('anime_views');
        if ($period === 'today') {
            $start = date('Y-m-d 00:00:00');
        } elseif ($period === 'week') {
            $start = date('Y-m-d H:i:s', strtotime('-7 days'));
        } elseif ($period === 'month') {
            $start = date('Y-m-d H:i:s', strtotime('-1 month'));
        } else {
            $start = date('Y-m-d 00:00:00');
        }
        // Join with anime_data to get full details
        $builder->select('anime_views.anime_id, anime_data.title, anime_data.type, anime_data.ratings, anime_data.backgroundImage, anime_data.genres, anime_data.total_ep, anime_data.status, anime_data.studios, anime_data.synopsis, SUM(anime_views.views) as total_views')
            ->join('anime_data', 'anime_data.anime_id = anime_views.anime_id')
            ->where('anime_views.viewed_at >=', $start)
            ->groupBy('anime_views.anime_id')
            ->orderBy('total_views', 'DESC')
            ->limit($limit);
        return $builder->get()->getResultArray();
    }
}
