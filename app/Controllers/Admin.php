<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\AnimeModel;

class Admin extends BaseController
{
    protected $accountModel;
    protected $animeModel;

    public function __construct()
    {
        $this->accountModel = new AccountModel();
        $this->animeModel = new AnimeModel();
    }

    public function index()
    {
        return view('admin/dashboard');
    }

    public function metrics()
    {
        return view('admin/metrics');
    }

    public function accounts()
    {
        // Get all accounts with pagination
        $perPage = 20;
        $page = $this->request->getGet('page') ?? 1;
        
        $accounts = $this->accountModel->orderBy('created_at', 'DESC')->paginate($perPage);
        $pager = $this->accountModel->pager;
        
        $data = [
            'accounts' => $accounts,
            'pager' => $pager
        ];
        
        return view('admin/accounts', $data);
    }

    public function animeManage()
    {
        // Get all anime with pagination
        $perPage = 20;
        $page = $this->request->getGet('page') ?? 1;
        
        $anime_list = $this->animeModel->orderBy('anime_id', 'DESC')->paginate($perPage);
        $pager = $this->animeModel->pager;
        
        $data = [
            'anime_list' => $anime_list,
            'pager' => $pager
        ];
        
        return view('admin/anime_manage', $data);
    }

    /**
     * Get anime details via AJAX
     */
    public function getAnime($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $anime = $this->animeModel->find($id);
        if (!$anime) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anime not found']);
        }

        return $this->response->setJSON([
            'success' => true,
            'anime' => $anime
        ]);
    }

    /**
     * Create new anime
     */
    public function createAnime()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'language' => $this->request->getPost('language'),
            'type' => $this->request->getPost('type'),
            'total_ep' => $this->request->getPost('total_ep') ?: null,
            'ratings' => $this->request->getPost('ratings'),
            'genres' => $this->request->getPost('genres'),
            'status' => $this->request->getPost('status'),
            'studios' => $this->request->getPost('studios'),
            'urls' => $this->request->getPost('urls'),
            'backgroundImage' => $this->request->getPost('backgroundImage'),
            'synopsis' => $this->request->getPost('synopsis')
        ];

        // Validate required fields
        if (empty($data['title']) || empty($data['type']) || empty($data['status'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Title, type, and status are required']);
        }

        $result = $this->animeModel->insert($data);
        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Anime created successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to create anime']);
        }
    }

    /**
     * Update anime
     */
    public function updateAnime($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $anime = $this->animeModel->find($id);
        if (!$anime) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anime not found']);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'language' => $this->request->getPost('language'),
            'type' => $this->request->getPost('type'),
            'total_ep' => $this->request->getPost('total_ep') ?: null,
            'ratings' => $this->request->getPost('ratings'),
            'genres' => $this->request->getPost('genres'),
            'status' => $this->request->getPost('status'),
            'studios' => $this->request->getPost('studios'),
            'urls' => $this->request->getPost('urls'),
            'backgroundImage' => $this->request->getPost('backgroundImage'),
            'synopsis' => $this->request->getPost('synopsis')
        ];

        // Validate required fields
        if (empty($data['title']) || empty($data['type']) || empty($data['status'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Title, type, and status are required']);
        }

        $result = $this->animeModel->update($id, $data);
        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Anime updated successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update anime']);
        }
    }

    /**
     * Delete anime
     */
    public function deleteAnime($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $anime = $this->animeModel->find($id);
        if (!$anime) {
            return $this->response->setJSON(['success' => false, 'message' => 'Anime not found']);
        }

        $result = $this->animeModel->delete($id);
        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Anime deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete anime']);
        }
    }

    /**
     * Get account details via AJAX
     */
    public function getAccount($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $account = $this->accountModel->find($id);
        if (!$account) {
            return $this->response->setJSON(['success' => false, 'message' => 'Account not found']);
        }

        // Get watched and followed anime
        $watchedAnime = [];
        $followedAnime = [];
        
        if (!empty($account['watched'])) {
            $watchedIds = json_decode($account['watched'], true) ?: [];
            if (!empty($watchedIds)) {
                $watchedAnime = $this->animeModel->whereIn('id', $watchedIds)->findAll();
            }
        }
        
        if (!empty($account['followed_anime'])) {
            $followedIds = json_decode($account['followed_anime'], true) ?: [];
            if (!empty($followedIds)) {
                $followedAnime = $this->animeModel->whereIn('id', $followedIds)->findAll();
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'account' => $account,
            'watched_anime' => $watchedAnime,
            'followed_anime' => $followedAnime
        ]);
    }

    /**
     * Create new account
     */
    public function createAccount()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'display_name' => $this->request->getPost('display_name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'type' => $this->request->getPost('type'),
            'watched' => '[]',
            'followed_anime' => '[]'
        ];

        // Validate required fields
        if (empty($data['username']) || empty($data['email']) || empty($data['type'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Username, email and type are required']);
        }

        // Check if username or email already exists
        if ($this->accountModel->where('username', $data['username'])->first()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Username already exists']);
        }

        if ($this->accountModel->where('email', $data['email'])->first()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Email already exists']);
        }

        $result = $this->accountModel->insert($data);
        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Account created successfully']);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create account',
                'errors' => $this->accountModel->errors()
            ]);
        }
    }

    /**
     * Update account details
     */
    public function updateAccount($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $account = $this->accountModel->find($id);
        if (!$account) {
            return $this->response->setJSON(['success' => false, 'message' => 'Account not found']);
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'display_name' => $this->request->getPost('display_name'),
            'email' => $this->request->getPost('email'),
            'type' => $this->request->getPost('type')
        ];

        // Update password only if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Check for username/email conflicts with other accounts
        if ($data['username'] !== $account['username']) {
            if ($this->accountModel->where('username', $data['username'])->where('id !=', $id)->first()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Username already exists']);
            }
        }

        if ($data['email'] !== $account['email']) {
            if ($this->accountModel->where('email', $data['email'])->where('id !=', $id)->first()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Email already exists']);
            }
        }

        $result = $this->accountModel->update($id, $data);
        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Account updated successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update account']);
        }
    }

    /**
     * Delete account
     */
    public function deleteAccount($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $account = $this->accountModel->find($id);
        if (!$account) {
            return $this->response->setJSON(['success' => false, 'message' => 'Account not found']);
        }

        // Delete avatar file if exists
        if (!empty($account['user_profile']) && strpos($account['user_profile'], 'uploads/avatars/') !== false) {
            $this->deleteUserAvatar($account['user_profile']);
        }

        $result = $this->accountModel->delete($id);
        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Account deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete account']);
        }
    }

    /**
     * Helper method to delete user avatar
     */
    private function deleteUserAvatar($avatarUrl)
    {
        try {
            $parsedUrl = parse_url($avatarUrl);
            $path = $parsedUrl['path'] ?? '';
            
            if (strpos($path, 'uploads/avatars/') !== false) {
                $startPos = strpos($path, 'uploads/avatars/');
                $relativePath = substr($path, $startPos);
                $filePath = FCPATH . $relativePath;
                
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
        } catch (Exception $e) {
            log_message('error', "Failed to delete avatar {$avatarUrl}: " . $e->getMessage());
        }
    }
}