<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCountriesTable extends Migration
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
                'constraint' => 100,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Active', 'Inactive'],
                'default'    => 'Active',
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
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('countries');

        // Insert sample data
        $data = [
            ['name' => 'Malaysia', 'code' => 'MY', 'status' => 'Active'],
            ['name' => 'Singapore', 'code' => 'SG', 'status' => 'Active'],
            ['name' => 'Indonesia', 'code' => 'ID', 'status' => 'Active'],
            ['name' => 'Thailand', 'code' => 'TH', 'status' => 'Active'],
            ['name' => 'Philippines', 'code' => 'PH', 'status' => 'Active'],
            ['name' => 'China', 'code' => 'CN', 'status' => 'Active'],
            ['name' => 'Japan', 'code' => 'JP', 'status' => 'Inactive'],
            ['name' => 'South Korea', 'code' => 'KR', 'status' => 'Active'],
            ['name' => 'Vietnam', 'code' => 'VN', 'status' => 'Active'],
            ['name' => 'Australia', 'code' => 'AU', 'status' => 'Active'],
        ];

        $this->db->table('countries')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('countries');
    }
}
