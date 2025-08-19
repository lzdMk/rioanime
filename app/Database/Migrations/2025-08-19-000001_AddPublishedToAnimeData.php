<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPublishedToAnimeData extends Migration
{
    public function up()
    {
        $fields = [
            'published' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'comment' => '1 = published, 0 = unpublished/draft'
            ],
            'published_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'comment' => 'When the anime was published'
            ],
            'unpublished_at' => [
                'type' => 'TIMESTAMP', 
                'null' => true,
                'comment' => 'When the anime was unpublished'
            ]
        ];
        
        $this->forge->addColumn('anime_data', $fields);
        
        // Update existing records to be published by default
        $this->db->query("UPDATE anime_data SET published = 1, published_at = NOW() WHERE published IS NULL");
    }

    public function down()
    {
        $this->forge->dropColumn('anime_data', ['published', 'published_at', 'unpublished_at']);
    }
}
