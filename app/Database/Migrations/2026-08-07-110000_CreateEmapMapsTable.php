<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmapMapsTable extends Migration
{
    public function up(): void
    {
        if ($this->db->tableExists('emap_maps')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'premise_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'default'    => 'Main Premise',
            ],
            'floor_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'default'    => 'Ground Floor',
            ],
            'canvas_width' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 1000,
            ],
            'canvas_height' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 590,
            ],
            'layout_json' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['draft', 'published'],
                'default'    => 'draft',
            ],
            'version' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 1,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'published_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addKey('client_id');
        $this->forge->addKey(['client_id', 'status']);
        $this->forge->createTable('emap_maps');
    }

    public function down(): void
    {
        $this->forge->dropTable('emap_maps', true);
    }
}
