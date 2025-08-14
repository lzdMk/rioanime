<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNotificationFields extends Migration
{
    public function up()
    {
        // Check if columns exist before adding them
        $db = \Config\Database::connect();
        
        // Add title column if it doesn't exist
        if (!$db->fieldExists('title', 'user_notifications')) {
            $this->forge->addColumn('user_notifications', [
                'title' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true,
                    'after' => 'user_id'
                ]
            ]);
        }
        
        // Add priority column if it doesn't exist
        if (!$db->fieldExists('priority', 'user_notifications')) {
            $this->forge->addColumn('user_notifications', [
                'priority' => [
                    'type' => 'VARCHAR',
                    'constraint' => 32,
                    'default' => 'normal',
                    'after' => 'type'
                ]
            ]);
        }
        
        // Add action_url column if it doesn't exist
        if (!$db->fieldExists('action_url', 'user_notifications')) {
            $this->forge->addColumn('user_notifications', [
                'action_url' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'message'
                ]
            ]);
        }
    }

    public function down()
    {
        // Remove the added columns
        $db = \Config\Database::connect();
        
        if ($db->fieldExists('title', 'user_notifications')) {
            $this->forge->dropColumn('user_notifications', 'title');
        }
        
        if ($db->fieldExists('priority', 'user_notifications')) {
            $this->forge->dropColumn('user_notifications', 'priority');
        }
        
        if ($db->fieldExists('action_url', 'user_notifications')) {
            $this->forge->dropColumn('user_notifications', 'action_url');
        }
    }
}
