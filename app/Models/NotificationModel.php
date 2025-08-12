<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'user_notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['user_id', 'type', 'message', 'is_read', 'created_at'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'type' => 'required|string|max_length[32]',
        'message' => 'required|string'
    ];

    /**
     * Get notifications for a specific user
     */
    public function getUserNotifications($userId, $limit = 50)
    {
        return $this->where('user_id', $userId)
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
                   ->where('is_read', 0)
                   ->countAllResults();
    }

    /**
     * Create a new notification
     */
    public function createNotification($userId, $type, $message)
    {
        return $this->insert([
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s', time())
        ]);
    }

    /**
     * Mark specific notifications as read
     */
    public function markAsRead($notificationIds, $userId)
    {
        return $this->whereIn('id', $notificationIds)
                   ->where('user_id', $userId)
                   ->set('is_read', 1)
                   ->update();
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId)
    {
        return $this->where('user_id', $userId)
                   ->set('is_read', 1)
                   ->update();
    }

    /**
     * Delete specific notifications
     */
    public function deleteNotifications($notificationIds, $userId)
    {
        return $this->whereIn('id', $notificationIds)
                   ->where('user_id', $userId)
                   ->delete();
    }

    /**
     * Delete all notifications for a user
     */
    public function deleteAllForUser($userId)
    {
        return $this->where('user_id', $userId)->delete();
    }

    /**
     * Clean up old read notifications (optional cleanup method)
     */
    public function cleanupOldNotifications($daysOld = 30)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysOld} days"));
        return $this->where('is_read', 1)
                   ->where('created_at <', $cutoffDate)
                   ->delete();
    }
}
