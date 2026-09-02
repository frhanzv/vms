<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddClientIdToVideos extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('client_id', 'videos')) {
            $this->forge->addColumn('videos', [
                'client_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
            $this->db->query('CREATE INDEX idx_videos_client_status ON videos (client_id, status)');
        }
    }

    public function down(): void
    {
        if ($this->db->fieldExists('client_id', 'videos')) {
            $this->forge->dropColumn('videos', 'client_id');
        }
    }
}
