<?php
namespace App\Models;

use CodeIgniter\Model;

class AnimeViewModel extends Model
{
    protected $table = 'anime_views';
    protected $primaryKey = 'anime_id';
    protected $allowedFields = ['anime_id', 'title', 'user_ip', 'views'];
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
        if ($existing) {
            // Increment views
            $this->update($existing['anime_id'], [
                'views' => $existing['views'] + 1
            ]);
        } else {
            // Insert new record with views = 1
            $data = [
                'anime_id' => $anime_id,
                'title' => $title,
                'user_ip' => $user_ip,
                'views' => 1
            ];
            $this->insert($data);
        }
    }
}
