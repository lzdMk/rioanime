<?php

namespace App\Controllers;

use App\Models\AccountModel;
use CodeIgniter\Controller;

class Follow extends Controller
{
    protected $accountModel;

    public function __construct()
    {
        $this->accountModel = new AccountModel();
    }

    /**
     * Check if user is following an anime
     */
    public function checkStatus($animeId = null)
    {
        if (!session('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Not logged in',
                'isFollowing' => false
            ]);
        }

        if (!$animeId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anime ID required',
                'isFollowing' => false
            ]);
        }

        $userId = session('user_id');
        $isFollowing = $this->accountModel->isFollowingAnime($userId, $animeId);

        return $this->response->setJSON([
            'success' => true,
            'isFollowing' => $isFollowing
        ]);
    }

    /**
     * Follow an anime
     */
    public function follow()
    {
        if (!session('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Not logged in'
            ]);
        }

        $input = $this->request->getJSON(true);
        
        if (!isset($input['anime_id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anime ID required'
            ]);
        }

        $userId = session('user_id');
        $animeId = $input['anime_id'];

        $result = $this->accountModel->followAnime($userId, $animeId);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Anime followed successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to follow anime'
            ]);
        }
    }

    /**
     * Unfollow an anime
     */
    public function unfollow()
    {
        if (!session('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Not logged in'
            ]);
        }

        $input = $this->request->getJSON(true);
        
        if (!isset($input['anime_id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anime ID required'
            ]);
        }

        $userId = session('user_id');
        $animeId = $input['anime_id'];

        $result = $this->accountModel->unfollowAnime($userId, $animeId);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Anime unfollowed successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to unfollow anime'
            ]);
        }
    }
}
