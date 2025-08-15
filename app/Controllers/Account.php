<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Controllers\Notification;
use CodeIgniter\RESTful\ResourceController;

class Account extends BaseController
{
    protected $accountModel;

    public function __construct()
    {
        $this->accountModel = new AccountModel();
    }

    /**
     * Show the user profile page
     */
    public function profile()
    {
        // Only allow access if logged in
        if (!session('isLoggedIn')) {
            return redirect()->to(base_url('account/login'));
        }
        // Pass user data to the view if needed
        $user_id = session('user_id');
        $user = null;
        if ($user_id) {
            $user = $this->accountModel->find($user_id);
        }
        $data = [
            'user_id' => $user_id,
            'username' => session('username'),
            'type' => session('type'),
            'email' => session('email'),
            'user_profile' => $user['user_profile'] ?? null,
            'watchedAnime' => [] // not needed on profile page
        ];
    return view('pages/User Profiles/profile', $data);
    }

    /**
     * Update profile (email, username)
     */
    public function updateProfile()
    {
        if (!session('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method not allowed']);
        }

        $userId = session('user_id');
        $email = trim((string)$this->request->getPost('email'));
        $username = trim((string)$this->request->getPost('username'));

        // Basic validation
        $errors = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email address';
        }
        if (strlen($username) < 3 || strlen($username) > 32) {
            $errors['username'] = 'Username must be 3-32 characters';
        }
        if ($errors) {
            return $this->response->setJSON(['success' => false, 'errors' => $errors]);
        }

        // Enforce unique email/username except current user
        $existingEmail = $this->accountModel->where('email', $email)->where('id !=', $userId)->first();
        if ($existingEmail) {
            return $this->response->setJSON(['success' => false, 'errors' => ['email' => 'Email already in use']]);
        }
        $existingUsername = $this->accountModel->where('username', $username)->where('id !=', $userId)->first();
        if ($existingUsername) {
            return $this->response->setJSON(['success' => false, 'errors' => ['username' => 'Username already in use']]);
        }

        $this->accountModel->update($userId, [
            'email' => $email,
            'username' => $username,
        ]);

        // Create notification for profile update
        Notification::createNotification($userId, 'profile_update', 'Your profile has been successfully updated.');

        // Refresh session
        session()->set(['email' => $email, 'username' => $username]);

        return $this->response->setJSON(['success' => true, 'message' => 'Profile updated']);
    }

    /**
     * Change password with current password verification
     */
    public function changePassword()
    {
        if (!session('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method not allowed']);
        }

        $userId = session('user_id');
        $current = (string)$this->request->getPost('current_password');
        $new = (string)$this->request->getPost('new_password');
        $confirm = (string)$this->request->getPost('confirm_password');

        $user = $this->accountModel->find($userId);
        if (!$user || !password_verify($current, $user['password'])) {
            return $this->response->setJSON(['success' => false, 'errors' => ['current_password' => 'Current password is incorrect']]);
        }
        if (strlen($new) < 8) {
            return $this->response->setJSON(['success' => false, 'errors' => ['new_password' => 'Password must be at least 8 characters']]);
        }
        if ($new !== $confirm) {
            return $this->response->setJSON(['success' => false, 'errors' => ['confirm_password' => 'Passwords do not match']]);
        }

        $hash = password_hash($new, PASSWORD_DEFAULT);
        $this->accountModel->update($userId, ['password' => $hash]);

        // Create notification for password change
        Notification::createNotification($userId, 'security', 'Your password has been successfully changed.');

        return $this->response->setJSON(['success' => true, 'message' => 'Password changed']);
    }

    /**
     * Upload avatar with 3MB limit and square crop input (client handles crop)
     */
    public function uploadAvatar()
    {
        if (!session('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'message' => 'Method not allowed']);
        }

        $userId = session('user_id');
        
        // Get current user data to check for existing avatar
        $currentUser = $this->accountModel->find($userId);
        $currentAvatar = $currentUser['user_profile'] ?? null;

        $file = $this->request->getFile('avatar');
        if (!$file || !$file->isValid()) {
            // Create notification for upload error
            Notification::createNotification($userId, 'error', 'Avatar upload failed: No valid file uploaded.');
            return $this->response->setJSON(['success' => false, 'errors' => ['avatar' => 'No file uploaded']]);
        }

        // Validate size (<= 3MB) and mime
        if ($file->getSize() > 3 * 1024 * 1024) {
            // Create notification for size error
            Notification::createNotification($userId, 'warning', 'Avatar upload failed: File too large. Maximum size is 3MB.');
            return $this->response->setJSON(['success' => false, 'errors' => ['avatar' => 'File too large. Max 3MB']]);
        }
        $mime = $file->getMimeType();
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mime, $allowed)) {
            // Create notification for format error
            Notification::createNotification($userId, 'warning', 'Avatar upload failed: Invalid file format. Only JPG, PNG, or WEBP allowed.');
            return $this->response->setJSON(['success' => false, 'errors' => ['avatar' => 'Only JPG, PNG, or WEBP allowed']]);
        }

        // Move to public/uploads/avatars for direct serving
        $dir = FCPATH . 'uploads/avatars/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $ext = $file->getExtension();

        // Build filename: avatar_(username)_(generated time data)
        // Use session username when available, fallback to DB username, then userid.
        $rawUsername = session('username') ?? ($currentUser['username'] ?? $userId);
        // Sanitize username for filenames: lowercase, replace non-alphanumeric with underscore, trim
        $safeUsername = preg_replace('/[^a-z0-9_]/', '_', strtolower((string)$rawUsername));
        $safeUsername = trim($safeUsername, '_');
        if ($safeUsername === '') {
            $safeUsername = 'user' . $userId;
        }

        // Use formatted timestamp for readability and uniqueness
        $timestamp = date('YmdHis');
        $newName = "avatar_{$safeUsername}_{$timestamp}.{$ext}";
        $file->move($dir, $newName, true);
        $fullPath = $dir . $newName;

        // Basic dimension check (require at least 128x128)
        $info = @getimagesize($fullPath);
        if (!$info || $info[0] < 128 || $info[1] < 128) {
            @unlink($fullPath);
            // Create notification for upload error
            Notification::createNotification($userId, 'error', 'Avatar upload failed: Image too small. Minimum 128x128 required.');
            return $this->response->setJSON(['success' => false, 'errors' => ['avatar' => 'Image too small. Minimum 128x128']]);
        }

        $publicPath = base_url('uploads/avatars/' . $newName);

        // Delete previous avatar if it exists and is stored in our uploads folder
        if ($currentAvatar && strpos($currentAvatar, 'uploads/avatars/') !== false) {
            log_message('info', "Found existing avatar to delete: {$currentAvatar}");
            $this->deleteOldAvatar($currentAvatar);
        } else {
            log_message('info', "No existing avatar to delete. Current avatar: " . ($currentAvatar ?: 'null'));
        }

        // Save to DB and session
        $this->accountModel->update($userId, ['user_profile' => $publicPath]);
        session()->set(['user_profile' => $publicPath]);

        // Create notification for successful avatar upload
        Notification::createNotification($userId, 'profile_update', 'Your avatar has been successfully updated.');

        return $this->response->setJSON(['success' => true, 'message' => 'Avatar updated', 'url' => $publicPath]);
    }

    /**
     * Delete old avatar file from storage
     */
    private function deleteOldAvatar($avatarUrl)
    {
        try {
            log_message('info', "Attempting to delete old avatar: {$avatarUrl}");
            
            // Extract filename from URL
            $parsedUrl = parse_url($avatarUrl);
            $path = $parsedUrl['path'] ?? '';
            
            log_message('info', "Parsed path from URL: {$path}");
            
            // Extract just the filename from the path
            // The URL will be like: http://localhost/rioanime/uploads/avatars/avatar_123_123456.png
            // We need to get: uploads/avatars/avatar_123_123456.png
            if (strpos($path, 'uploads/avatars/') !== false) {
                // Find the position of 'uploads/avatars/' and extract from there
                $startPos = strpos($path, 'uploads/avatars/');
                $relativePath = substr($path, $startPos);
                
                // Build full file path
                $filePath = FCPATH . $relativePath;
                
                log_message('info', "Attempting to delete file at: {$filePath}");
                
                // Only delete if file exists
                if (file_exists($filePath)) {
                    if (@unlink($filePath)) {
                        log_message('info', "Successfully deleted old avatar: {$filePath}");
                    } else {
                        log_message('error', "Failed to unlink file: {$filePath}");
                    }
                } else {
                    log_message('info', "File does not exist: {$filePath}");
                }
            } else {
                log_message('info', "Avatar URL does not contain uploads/avatars/ path: {$avatarUrl}");
            }
        } catch (Exception $e) {
            // Log error but don't fail the upload process
            log_message('error', "Exception when deleting old avatar {$avatarUrl}: " . $e->getMessage());
        }
    }

    /**
     * Continue Watching page
     */
    public function continueWatching()
    {
        if (!session('isLoggedIn')) {
            return redirect()->to(base_url('account/login'));
        }
        $user_id = session('user_id');
        $user = $this->accountModel->find($user_id);
        $watchedAnime = [];
        if (!empty($user['watched'])) {
            $ids = json_decode($user['watched'], true);
            if (is_array($ids) && !empty($ids)) {
                $animeModel = new \App\Models\AnimeModel();
                $watchedAnime = $animeModel->getAnimeByIds($ids);
            }
        }
        $data = [
            'user_id' => $user_id,
            'username' => session('username'),
            'type' => session('type'),
            'email' => session('email'),
            'user_profile' => $user['user_profile'] ?? null,
            'watchedAnime' => $watchedAnime,
            'activeTab' => 'continue'
        ];
    return view('pages/User Profiles/watched', $data);
    }

    /**
     * Watch list page - shows followed anime
     */
    public function watchList()
    {
        if (!session('isLoggedIn')) {
            return redirect()->to(base_url('account/login'));
        }
        
        $user_id = session('user_id');
        $user = $this->accountModel->find($user_id);
        $followedAnime = [];
        
        if (!empty($user['followed_anime'])) {
            $ids = json_decode($user['followed_anime'], true);
            if (is_array($ids) && !empty($ids)) {
                $animeModel = new \App\Models\AnimeModel();
                $followedAnime = $animeModel->getAnimeByIds($ids);
            }
        }
        
        $data = [
            'user_id' => $user_id,
            'username' => session('username'),
            'type' => session('type'),
            'email' => session('email'),
            'user_profile' => $user['user_profile'] ?? null,
            'followedAnime' => $followedAnime,
            'activeTab' => 'watchlist'
        ];
        
        return view('pages/User Profiles/watchlist', $data);
    }

    /**
     * Notifications page (UI only)
     */
    public function notifications()
    {
        if (!session('isLoggedIn')) {
            return redirect()->to(base_url('account/login'));
        }
        $user_id = session('user_id');
        $user = $this->accountModel->find($user_id);
        $data = [
            'user_id' => $user_id,
            'username' => session('username'),
            'type' => session('type'),
            'email' => session('email'),
            'user_profile' => $user['user_profile'] ?? null,
        ];
        return view('pages/User Profiles/notifications', $data);
    }

    /**
     * Get current user profile data for header refresh
     */
    public function getProfileData()
    {
        if (!session('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        
        $userId = session('user_id');
        $user = $this->accountModel->find($userId);
        
        return $this->response->setJSON([
            'success' => true,
            'user_profile' => $user['user_profile'] ?? null,
            'username' => $user['username'] ?? null
        ]);
    }

    /**
     * Handle user logout
     */
    public function logout()
    {
        // Destroy session
        session()->destroy();
        // If AJAX, return JSON
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Logout successful',
                'redirect' => base_url('/')
            ]);
        }
        // Otherwise, redirect to homepage
        return redirect()->to(base_url('/'));
    }

    /**
     * Handle user login
     */
    public function login()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method: ' . $this->request->getMethod() . '. Expected POST.'
            ]);
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->accountModel->verifyCredentials($email, $password);
        if ($user) {
            // Set session data
            session()->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'type' => $user['type'],
                'email' => $user['email'],
                'isLoggedIn' => true
            ]);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Login successful',
                'redirect' => ($user['type'] === 'admin') ? base_url('admin') : base_url('/'),
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'type' => $user['type'],
                    'email' => $user['email']
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid email or password.'
            ]);
        }
    }

    /**
     * Handle user registration
     */
    public function register()
    {
        // Only accept POST requests
        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method: ' . $this->request->getMethod() . '. Expected POST.'
            ]);
        }

        // Get form data
        $data = [
            'username' => $this->request->getPost('username'),
            'display_name' => $this->request->getPost('display_name'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'user_profile' => null // Default profile picture will be set later
        ];

        // Attempt to register user
        $result = $this->accountModel->registerUser($data);
        if ($result) {
            // Get the newly created user data
            $newUser = $this->accountModel->find($result);
            
            // Auto-login the user
            session()->set([
                'isLoggedIn' => true,
                'user_id' => $newUser['id'],
                'username' => $newUser['username'],
                'email' => $newUser['email'],
                'type' => $newUser['type'],
                'user_profile' => $newUser['user_profile'],
                'created_at' => $newUser['created_at']
            ]);
            
            // Create welcome notification for new user
            $username = $data['username'];
            $welcomeMessage = "🎉 Welcome to RioWave, {$username}! We're excited to have you join our anime community. Start exploring and enjoy watching! ✨";
            Notification::createNotification($result, 'welcome', $welcomeMessage);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Registration successful! Welcome to RioWave!',
                'auto_login' => true,
                'redirect_url' => base_url()
            ]);
        } else {
            // Return validation errors
            $errors = $this->accountModel->errors();

            // Log the actual error for debugging
            log_message('error', 'Registration failed. Errors: ' . json_encode($errors));
            log_message('error', 'User data: ' . json_encode($data));

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Registration failed',
                'errors' => $errors
            ]);
        }
    }

    /**
     * Check if username is available via AJAX
     */
    public function checkUsername()
    {
        $username = $this->request->getPost('username');

        $user = $this->accountModel->where('username', $username)->first();

        return $this->response->setJSON([
            'available' => $user === null
        ]);
    }

    /**
     * Check if email is available via AJAX
     */
    public function checkEmail()
    {
        $email = $this->request->getPost('email');

        $user = $this->accountModel->where('email', $email)->first();

        return $this->response->setJSON([
            'available' => $user === null
        ]);
    }
}
