<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVideosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => '500',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default' => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('name');
        $this->forge->addKey('status');
        $this->forge->createTable('videos');

        // Insert sample data - all inactive until real videos are uploaded
        $data = [
            [
                'name' => 'Safety Orientation Video',
                'file_path' => 'assets/videos/safety-orientation.mp4',
                'status' => 'inactive',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Visitor Guidelines',
                'file_path' => 'assets/videos/visitor-guidelines.mp4',
                'status' => 'inactive',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Emergency Procedures',
                'file_path' => 'assets/videos/emergency-procedures.mp4',
                'status' => 'inactive',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Company Introduction',
                'file_path' => 'assets/videos/company-introduction.mp4',
                'status' => 'inactive',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Security Training',
                'file_path' => 'assets/videos/security-training.mp4',
                'status' => 'inactive',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('videos')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('videos');
    }
}
