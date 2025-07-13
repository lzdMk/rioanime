<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountModel extends Model
{
    protected $table = 'user_accounts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'username',
        'display_name',
        'email',
        'password',
        'type',
        'followed_anime',
        'user_profile',
        'created_at'
    ];
    
    // Validation rules
    protected $validationRules = [
        'username' => 'required|alpha_numeric_space|min_length[3]|max_length[32]|is_unique[user_accounts.username]',
        'display_name' => 'required|alpha_numeric_space|min_length[3]|max_length[64]',
        'email' => 'required|valid_email|max_length[128]|is_unique[user_accounts.email]',
        'password' => 'required|min_length[8]'
    ];

    protected $validationMessages = [
        'username' => [
            'required' => 'Username is required',
            'is_unique' => 'Username is already taken',
            'alpha_numeric_space' => 'Username can only contain letters, numbers, and spaces'
        ],
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please enter a valid email address',
            'is_unique' => 'This email is already registered'
        ],
        'password' => [
            'required' => 'Password is required',
            'min_length' => 'Password must be at least 8 characters long'
        ]
    ];
    
    protected $skipValidation = false;
    protected $beforeInsert = ['hashPassword'];
    
    /**
     * Hash password before inserting into database
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        
        return $data;
    }
    
    /**
     * Register a new user
     */
    public function registerUser($userData)
    {
        // Set default values
        $userData['type'] = $userData['type'] ?? 'viewer';
        $userData['followed_anime'] = $userData['followed_anime'] ?? '';
        $userData['user_profile'] = $userData['user_profile'] ?? null;
        
        return $this->insert($userData);
    }
}
