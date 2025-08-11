<?php

namespace App\Controllers;

use App\Models\AccountModel;
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
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Registration successful! You can now login.'
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
