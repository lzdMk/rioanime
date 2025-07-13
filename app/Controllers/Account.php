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
     * Handle user registration
     */
    public function register()
    {
        // Only accept POST requests
        if ($this->request->getMethod() !== 'post') {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Invalid request method'
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
        if ($this->accountModel->registerUser($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Registration successful! You can now login.'
            ]);
        } else {
            // Return validation errors
            $errors = $this->accountModel->errors();
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
