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
        'watched',
        'user_profile',
        'created_at'
    ];
    
    // Validation rules
    protected $validationRules = [
        'username' => 'required|regex_match[/^[a-zA-Z0-9_ ]+$/]|min_length[3]|max_length[32]|is_unique[user_accounts.username]',
        'display_name' => 'required|regex_match[/^[a-zA-Z0-9_ ]+$/]|min_length[3]|max_length[64]',
        'email' => 'required|valid_email|max_length[128]|is_unique[user_accounts.email]',
        'password' => 'required|min_length[8]'
    ];

    protected $validationMessages = [
        'username' => [
            'required' => 'Username is required',
            'is_unique' => 'Username is already taken',
            'regex_match' => 'Username can only contain letters, numbers, spaces, and underscores'
        ],
        'display_name' => [
            'required' => 'Display name is required',
            'regex_match' => 'Display name can only contain letters, numbers, spaces, and underscores'
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
     * Verify user credentials for login
     */
    public function verifyCredentials($email, $password)
    {
        $user = $this->where('email', $email)->first();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
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
        $userData['created_at'] = $userData['created_at'] ?? date('Y-m-d H:i:s');
        
        // Try to insert and log any errors
        $result = $this->insert($userData);
        
        if (!$result) {
            log_message('error', 'Insert failed. Validation errors: ' . json_encode($this->errors()));
            log_message('error', 'User data being inserted: ' . json_encode($userData));
        }
        
        return $result;
    }

    /**
     * Add an anime to the user's watched list
     */
    public function addWatchAnime($userId, $animeId)
    {
        $user = $this->find($userId);
        $watched = !empty($user['watched']) ? json_decode($user['watched'], true) : [];

        if (!is_array($watched)) {
            $watched = [];
        }

        if (!in_array($animeId, $watched)) {
            $watched[] = $animeId;
            $this->update($userId, ['watched' => json_encode($watched)]);
        }

        return true;
    }

}
