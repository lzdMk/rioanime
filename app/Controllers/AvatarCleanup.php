<?php

namespace App\Controllers;

use App\Models\AccountModel;

class AvatarCleanup extends BaseController
{
    /**
     * Clean up old avatar files - run this once to remove duplicates
     * Access via: /avatar-cleanup
     */
    public function index()
    {
        // Safety check - only allow in development
        if (ENVIRONMENT !== 'development') {
            return $this->response->setStatusCode(403)->setBody('Not allowed in production');
        }

        $accountModel = new AccountModel();
        $users = $accountModel->findAll();
        
        $avatarDir = FCPATH . 'uploads/avatars/';
        $allFiles = glob($avatarDir . 'avatar_*');
        $activeAvatars = [];
        
        // Get all currently active avatars
        foreach ($users as $user) {
            if (!empty($user['user_profile'])) {
                $filename = basename(parse_url($user['user_profile'], PHP_URL_PATH));
                $activeAvatars[] = $avatarDir . $filename;
            }
        }
        
        $deletedCount = 0;
        $keptCount = 0;
        
        foreach ($allFiles as $file) {
            if (!in_array($file, $activeAvatars)) {
                if (@unlink($file)) {
                    $deletedCount++;
                    echo "Deleted: " . basename($file) . "<br>";
                }
            } else {
                $keptCount++;
                echo "Kept: " . basename($file) . " (active)<br>";
            }
        }
        
        echo "<hr>";
        echo "Cleanup complete!<br>";
        echo "Deleted: {$deletedCount} old files<br>";
        echo "Kept: {$keptCount} active files<br>";
    }
}
