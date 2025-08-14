<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\AnimeModel;
use Exception;

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
        return view('admin/accounts');
    }

    public function animeManage()
    {
        return view('admin/anime_manage');
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
                $watchedAnime = $this->animeModel->whereIn('anime_id', $watchedIds)->findAll();
            }
        }
        
        if (!empty($account['followed_anime'])) {
            $followedIds = json_decode($account['followed_anime'], true) ?: [];
            if (!empty($followedIds)) {
                $followedAnime = $this->animeModel->whereIn('anime_id', $followedIds)->findAll();
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
     * Get accounts with AJAX pagination
     */
    public function getAccounts()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 20);
        $search = $this->request->getGet('search') ?? '';
        $type = $this->request->getGet('type') ?? '';

        $builder = $this->accountModel->orderBy('created_at', 'DESC');
        
        if (!empty($search)) {
            $builder->groupStart()
                   ->like('username', $search)
                   ->orLike('display_name', $search)
                   ->orLike('email', $search)
                   ->groupEnd();
        }

        if (!empty($type)) {
            $builder->where('type', $type);
        }

        $totalItems = $builder->countAllResults(false);
        $accounts = $builder->paginate($perPage, 'default', $page);
        $totalPages = ceil($totalItems / $perPage);

        return $this->response->setJSON([
            'success' => true,
            'accounts' => $accounts,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalItems,
                'per_page' => $perPage,
                'has_previous' => $page > 1,
                'has_next' => $page < $totalPages
            ]
        ]);
    }

    /**
     * Get anime with AJAX pagination
     */
    public function getAnimeList()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 20);
        $search = $this->request->getGet('search') ?? '';
        $type = $this->request->getGet('type') ?? '';
        $status = $this->request->getGet('status') ?? '';

        $builder = $this->animeModel->orderBy('anime_id', 'DESC');
        
        if (!empty($search)) {
            $builder->groupStart()
                   ->like('title', $search)
                   ->orLike('genres', $search)
                   ->orLike('studios', $search)
                   ->groupEnd();
        }

        if (!empty($type)) {
            $builder->where('type', $type);
        }

        if (!empty($status)) {
            $builder->where('status', $status);
        }

        $totalItems = $builder->countAllResults(false);
        $anime_list = $builder->paginate($perPage, 'default', $page);
        $totalPages = ceil($totalItems / $perPage);

        return $this->response->setJSON([
            'success' => true,
            'anime_list' => $anime_list,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalItems,
                'per_page' => $perPage,
                'has_previous' => $page > 1,
                'has_next' => $page < $totalPages
            ]
        ]);
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

    /**
     * Import anime data from JSON
     */
    public function importAnime()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        try {
            $input = $this->request->getJSON(true);
            
            if (!isset($input['anime_data']) || !is_array($input['anime_data'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid data format'
                ]);
            }

            $animeData = $input['anime_data'];
            $importedCount = 0;
            $errors = [];

            foreach ($animeData as $anime) {
                try {
                    // Prepare data for insertion
                    $insertData = [
                        'title' => $anime['title'],
                        'language' => strtolower($anime['language']),
                        'type' => $anime['type'],
                        'total_ep' => isset($anime['total ep']) ? (int)$anime['total ep'] : null,
                        'ratings' => $anime['ratings'] ?? null,
                        'genres' => $anime['genres'] ?? null,
                        'status' => $anime['status'],
                        'studios' => $anime['studios'] ?? null,
                        'backgroundImage' => $anime['backgroundImage'] ?? null,
                        'synopsis' => $anime['synopsis'] ?? null,
                        'urls' => isset($anime['urls']) && is_array($anime['urls']) 
                                ? implode("\n", $anime['urls']) 
                                : (is_string($anime['urls']) ? $anime['urls'] : null)
                    ];

                    // Check if anime with same title already exists
                    $existingAnime = $this->animeModel->where('title', $insertData['title'])->first();
                    if ($existingAnime) {
                        $errors[] = "Anime '{$insertData['title']}' already exists - skipped";
                        continue;
                    }

                    // Insert anime
                    if ($this->animeModel->insert($insertData)) {
                        $importedCount++;
                    } else {
                        $errors[] = "Failed to insert '{$insertData['title']}'";
                    }
                } catch (Exception $e) {
                    $errors[] = "Error processing '{$anime['title']}': " . $e->getMessage();
                }
            }

            $message = "Successfully imported {$importedCount} anime";
            if (count($errors) > 0) {
                $message .= ". " . count($errors) . " items had issues: " . implode(', ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= "...";
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'imported_count' => $importedCount,
                'errors' => $errors
            ]);

        } catch (Exception $e) {
            log_message('error', 'Import anime error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred during import: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Send notification to users
     */
    public function sendNotification()
    {
        if (!$this->request->isAJAX() && !$this->request->getMethod() === 'POST') {
            return redirect()->to('admin');
        }

        try {
            // Get form data
            $targetType = $this->request->getPost('target_type');
            $userIds = $this->request->getPost('user_ids'); // Changed from user_id to user_ids
            $userGroup = $this->request->getPost('user_group');
            $title = $this->request->getPost('notification_title');
            $message = $this->request->getPost('notification_message');
            $type = $this->request->getPost('notification_type');
            $priority = $this->request->getPost('notification_priority');
            $actionUrl = $this->request->getPost('action_url');
            $sendImmediately = $this->request->getPost('send_immediately') ? true : false;
            $sendEmail = $this->request->getPost('send_email') ? true : false;

            // Validate required fields
            if (empty($targetType) || empty($title) || empty($message) || empty($type) || empty($priority)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please fill in all required fields.'
                ]);
            }

            // Validate target-specific fields
            if ($targetType === 'specific' && empty($userIds)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please select at least one user.'
                ]);
            }

            if ($targetType === 'group' && empty($userGroup)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please select a user group.'
                ]);
            }

            // Get target users based on type
            $targetUsers = $this->getTargetUsers($targetType, $userIds, $userGroup);

            if (empty($targetUsers)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No users found for the selected target.'
                ]);
            }

            // Prepare notification data
            $notificationData = [
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'priority' => $priority,
                'action_url' => $actionUrl,
                'created_at' => date('Y-m-d H:i:s'),
                'is_read' => 0
            ];

            $sentCount = 0;
            $notificationModel = new \App\Models\NotificationModel();

            // Send notifications to target users
            foreach ($targetUsers as $user) {
                $notificationData['user_id'] = $user['id'];
                
                if ($notificationModel->insert($notificationData)) {
                    $sentCount++;
                    
                    // Send email if requested and user has email
                    if ($sendEmail && !empty($user['email'])) {
                        $this->sendNotificationEmail($user, $notificationData);
                    }
                }
            }

            // Log the notification send
            log_message('info', "Admin sent notification: '$title' to $sentCount users (Target: $targetType)");

            return $this->response->setJSON([
                'success' => true,
                'message' => "Notification sent successfully to $sentCount users!",
                'sent_count' => $sentCount
            ]);

        } catch (Exception $e) {
            log_message('error', 'Send notification error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while sending the notification: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get target users based on selection type
     */
    private function getTargetUsers($targetType, $userIds = null, $userGroup = null)
    {
        $users = [];

        switch ($targetType) {
            case 'all':
                $users = $this->accountModel->findAll();
                break;

            case 'specific':
                if ($userIds) {
                    // Handle comma-separated user IDs
                    $userIdArray = explode(',', $userIds);
                    $userIdArray = array_filter(array_map('trim', $userIdArray));
                    
                    if (!empty($userIdArray)) {
                        $users = $this->accountModel->whereIn('id', $userIdArray)->findAll();
                    }
                }
                break;

            case 'group':
                $users = $this->getUsersByGroup($userGroup);
                break;
        }

        return $users;
    }

    /**
     * Get users by group type
     */
    private function getUsersByGroup($groupType)
    {
        $builder = $this->accountModel->builder();

        switch ($groupType) {
            case 'premium':
                // For now, return users with type 'premium' if that field exists
                return $builder->where('type', 'premium')->get()->getResultArray();

            case 'active':
                // For now, return all users (can be enhanced later when last_login field is added)
                $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
                return $builder->where('created_at >=', $thirtyDaysAgo)->get()->getResultArray();

            case 'new':
                $sevenDaysAgo = date('Y-m-d H:i:s', strtotime('-7 days'));
                return $builder->where('created_at >=', $sevenDaysAgo)->get()->getResultArray();

            case 'inactive':
                // For now, return users older than 30 days (can be enhanced later)
                $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
                return $builder->where('created_at <', $thirtyDaysAgo)->get()->getResultArray();

            default:
                return [];
        }
    }

    /**
     * Send notification email to user
     */
    private function sendNotificationEmail($user, $notificationData)
    {
        try {
            $email = \Config\Services::email();
            
            $email->setTo($user['email']);
            $email->setSubject('[RioAnime] ' . $notificationData['title']);
            
            $emailMessage = "
                <h2>{$notificationData['title']}</h2>
                <p>{$notificationData['message']}</p>
                <p><strong>Priority:</strong> " . ucfirst($notificationData['priority']) . "</p>
            ";
            
            if (!empty($notificationData['action_url'])) {
                $emailMessage .= "<p><a href='{$notificationData['action_url']}' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Take Action</a></p>";
            }
            
            $emailMessage .= "
                <hr>
                <p><small>This is an automated notification from RioAnime. If you no longer wish to receive these emails, you can disable email notifications in your account settings.</small></p>
            ";
            
            $email->setMessage($emailMessage);
            
            return $email->send();
            
        } catch (Exception $e) {
            log_message('error', 'Email notification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get users for AJAX dropdown
     */
    public function getUsers()
    {
        // Log the request details
        log_message('info', 'getUsers called - Method: ' . $this->request->getMethod());
        log_message('info', 'getUsers called - Is AJAX: ' . ($this->request->isAJAX() ? 'true' : 'false'));
        log_message('info', 'getUsers called - Headers: ' . json_encode($this->request->getHeaders()));
        
        if (!$this->request->isAJAX()) {
            log_message('error', 'getUsers: Not AJAX request');
            return $this->response->setStatusCode(403);
        }

        try {
            $users = $this->accountModel
                ->select('id, username, email')
                ->orderBy('username', 'ASC')
                ->findAll();

            log_message('info', 'getUsers: Found ' . count($users) . ' users');

            return $this->response->setJSON([
                'success' => true,
                'users' => $users
            ]);

        } catch (Exception $e) {
            log_message('error', 'Get users error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading users: ' . $e->getMessage()
            ]);
        }
    }
}