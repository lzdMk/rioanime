<?php

namespace App\Models;

use CodeIgniter\Model;

class UserSessionModel extends Model
{
    protected $table = 'user_sessions';
    protected $primaryKey = 'session_id';
    protected $allowedFields = [
        'session_id',
        'user_id',
        'user_ip',
        'user_agent',
        'device_type',
        'last_activity',
        'created_at'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = false;

    /**
     * Track user session
     */
    public function trackSession($sessionId, $userId = null, $userIp, $userAgent = '')
    {
        $deviceType = $this->detectDeviceType($userAgent);
        $now = date('Y-m-d H:i:s');
        
        $data = [
            'session_id' => $sessionId,
            'user_id' => $userId,
            'user_ip' => $userIp,
            'user_agent' => $userAgent,
            'device_type' => $deviceType,
            'last_activity' => $now,
            'created_at' => $now
        ];
        
        // Check if session exists
        $existing = $this->find($sessionId);
        
        if ($existing) {
            $this->update($sessionId, [
                'last_activity' => $now,
                'user_id' => $userId,
                'device_type' => $deviceType
            ]);
        } else {
            $this->insert($data);
        }
    }
    
    /**
     * Get currently online users (active in last 5 minutes)
     */
    public function getCurrentlyOnline()
    {
        return $this->where('last_activity >=', date('Y-m-d H:i:s', strtotime('-5 minutes')))
                   ->countAllResults();
    }
    
    /**
     * Get online users for a period
     */
    public function getOnlineByPeriod($period = 'today')
    {
        $builder = $this->builder();
        
        switch ($period) {
            case 'today':
                $builder->where('DATE(last_activity)', date('Y-m-d'));
                break;
            case 'week':
                $builder->where('last_activity >=', date('Y-m-d H:i:s', strtotime('-7 days')));
                break;
            case 'month':
                $builder->where('last_activity >=', date('Y-m-d H:i:s', strtotime('-30 days')));
                break;
        }
        
        return $builder->countAllResults();
    }
    
    /**
     * Get device statistics
     */
    public function getDeviceStats($period = 'week')
    {
        $builder = $this->builder();
        
        switch ($period) {
            case 'today':
                $builder->where('DATE(last_activity)', date('Y-m-d'));
                break;
            case 'week':
                $builder->where('last_activity >=', date('Y-m-d H:i:s', strtotime('-7 days')));
                break;
            case 'month':
                $builder->where('last_activity >=', date('Y-m-d H:i:s', strtotime('-30 days')));
                break;
        }
        
        $results = $builder->select('device_type, COUNT(*) as count')
                          ->groupBy('device_type')
                          ->get()
                          ->getResultArray();
        
        $stats = [
            'mobile' => 0,
            'tablet' => 0,
            'desktop' => 0
        ];
        
        foreach ($results as $result) {
            if (isset($stats[$result['device_type']])) {
                $stats[$result['device_type']] = (int)$result['count'];
            }
        }
        
        return $stats;
    }
    
    /**
     * Clean old sessions (older than 30 days)
     */
    public function cleanOldSessions()
    {
        return $this->where('last_activity <', date('Y-m-d H:i:s', strtotime('-30 days')))
                   ->delete();
    }
    
    /**
     * Detect device type from user agent
     */
    private function detectDeviceType($userAgent)
    {
        $userAgent = strtolower($userAgent);
        
        // Mobile devices
        if (preg_match('/mobile|android|iphone|ipod|blackberry|windows phone/', $userAgent)) {
            return 'mobile';
        }
        
        // Tablets
        if (preg_match('/tablet|ipad/', $userAgent)) {
            return 'tablet';
        }
        
        // Default to desktop
        return 'desktop';
    }
}
