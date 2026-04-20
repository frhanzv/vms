<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePathwaysTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
            ],
            'version' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 1,
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
        $this->forge->addKey('status');
        $this->forge->createTable('pathways');

        // Pivot table: pathway <-> lane (doors) with sort order
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pathway_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'lane_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('pathway_id');
        $this->forge->addKey('lane_id');
        $this->forge->addForeignKey('pathway_id', 'pathways', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('lane_id', 'lanes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pathway_lanes');
    }

    public function down()
    {
        $this->forge->dropTable('pathway_lanes');
        $this->forge->dropTable('pathways');
    }
}
