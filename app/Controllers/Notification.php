<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\RESTful\ResourceController;

class Notification extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Get user notifications (AJAX)
     */
    public function getNotifications()
    {
        if (!session('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = session('user_id');
        $notifications = $this->notificationModel->getUserNotifications($userId);

        return $this->response->setJSON([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $this->notificationModel->getUnreadCount($userId)
        ]);
    }

    /**
     * Mark notification(s) as read
     */
    public function markAsRead()
    {
        if (!session('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = session('user_id');
        $notificationIds = $this->request->getPost('notification_ids'); // Array, JSON string, CSV, single ID, or 'all'

        // Normalize input
        if ($notificationIds === 'all') {
            $this->notificationModel->markAllAsRead($userId);
            return $this->response->setJSON(['success' => true, 'message' => 'All notifications marked as read']);
        }

        // If JSON string
        if (is_string($notificationIds)) {
            $trimmed = trim($notificationIds);
            // Try JSON decode first
            $decoded = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $notificationIds = $decoded;
            } elseif (preg_match('/^\d+(,\d+)*$/', $trimmed)) {
                // CSV of numbers
                $notificationIds = array_map('intval', explode(',', $trimmed));
            } elseif (ctype_digit($trimmed)) {
                $notificationIds = [intval($trimmed)];
            } else {
                $notificationIds = [];
            }
        }

        if (is_array($notificationIds)) {
            // Sanitize to integers and ensure ownership will be enforced in model query
            $ids = array_values(array_filter(array_map('intval', $notificationIds), fn($v) => $v > 0));
            if (empty($ids)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
            }
            // Execute update; even if already read, treat as success
            $this->notificationModel->markAsRead($ids, $userId);
            return $this->response->setJSON(['success' => true, 'message' => 'Selected notifications marked as read']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
    }

    /**
     * Delete notification(s)
     */
    public function deleteNotifications()
    {
        if (!session('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = session('user_id');
        $notificationIds = $this->request->getPost('notification_ids'); // Array, JSON string, CSV, single ID, or 'all'

        if ($notificationIds === 'all') {
            $this->notificationModel->deleteAllForUser($userId);
            return $this->response->setJSON(['success' => true, 'message' => 'All notifications deleted']);
        }

        // If JSON string
        if (is_string($notificationIds)) {
            $trimmed = trim($notificationIds);
            // Try JSON decode first
            $decoded = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $notificationIds = $decoded;
            } elseif (preg_match('/^\d+(,\d+)*$/', $trimmed)) {
                // CSV of numbers
                $notificationIds = array_map('intval', explode(',', $trimmed));
            } elseif (ctype_digit($trimmed)) {
                $notificationIds = [intval($trimmed)];
            } else {
                $notificationIds = [];
            }
        }

        if (is_array($notificationIds)) {
            $ids = array_values(array_filter(array_map('intval', $notificationIds), fn($v) => $v > 0));
            if (empty($ids)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
            }
            $this->notificationModel->deleteNotifications($ids, $userId);
            return $this->response->setJSON(['success' => true, 'message' => 'Selected notifications deleted']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
    }

    /**
     * Create a notification (helper method)
     */
    public static function createNotification($userId, $type, $message)
    {
        $notificationModel = new NotificationModel();
        return $notificationModel->createNotification($userId, $type, $message);
    }

    /**
     * Get unread count for navbar badge
     */
    public function getUnreadCount()
    {
        if (!session('isLoggedIn')) {
            return $this->response->setJSON(['count' => 0]);
        }

        $userId = session('user_id');
        $count = $this->notificationModel->getUnreadCount($userId);

        return $this->response->setJSON(['count' => $count]);
    }
}
