<?php

namespace App\Controllers;

use App\Models\AccountModel;
use CodeIgniter\RESTful\ResourceController;

class Account extends BaseController
{
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
                'isLoggedIn' => true
            ]);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Login successful',
                'redirect' => ($user['type'] === 'admin') ? base_url('admin') : base_url('/'),
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'type' => $user['type']
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid email or password.'
            ]);
        }
    }
    protected $accountModel;
    
    public function __construct()
    {
        $this->accountModel = new AccountModel();
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
