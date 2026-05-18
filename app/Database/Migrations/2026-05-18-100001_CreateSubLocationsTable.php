<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubLocationsTable extends Migration
{
    public function up()
    {
        // Sub locations — subdivisions beneath a Location Access entry
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
            'location_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
        $this->forge->addKey('location_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('location_id', 'locations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sub_locations');

        // Pivot: pathways ↔ sub_locations (with unified sort_order across lanes + sub-locations)
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
            'sub_location_id' => [
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
        $this->forge->addKey('sub_location_id');
        $this->forge->addForeignKey('pathway_id', 'pathways', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('sub_location_id', 'sub_locations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pathway_sub_locations');
    }

    public function down()
    {
        $this->forge->dropTable('pathway_sub_locations');
        $this->forge->dropTable('sub_locations');
    }
}
