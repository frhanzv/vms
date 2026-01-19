<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStatesTable extends Migration
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
            'country_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
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
        $this->forge->addForeignKey('country_id', 'countries', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('states');

        // Get Malaysia country ID
        $db = \Config\Database::connect();
        $malaysia = $db->table('countries')->where('code', 'MY')->get()->getRowArray();
        
        if ($malaysia) {
            $countryId = $malaysia['id'];
            
            // Insert all Malaysian states
            $states = [
                ['country_id' => $countryId, 'name' => 'Johor', 'code' => 'JHR', 'status' => 'active'],
                ['country_id' => $countryId, 'name' => 'Kedah', 'code' => 'KDH', 'status' => 'active'],
                ['country_id' => $countryId, 'name' => 'Kelantan', 'code' => 'KTN', 'status' => 'active'],
                ['country_id' => $countryId, 'name' => 'Kuala Lumpur', 'code' => 'KUL', 'status' => 'active'],
                ['country_id' => $countryId, 'name' => 'Labuan', 'code' => 'LBN', 'status' => 'active'],
                ['country_id' => $countryId, 'name' => 'Melaka', 'code' => 'MLK', 'status' => 'active'],
                ['country_id' => $countryId, 'name' => 'Negeri Sembilan', 'code' => 'NSN', 'status' => 'active'],
                ['country_id' => $countryId, 'name' => 'Pahang', 'code' => 'PHG', 'status' => 'active'],
                ['country_id' => $countryId, 'name' => 'Penang', 'code' => 'PNG', 'status' => 'active'],
                ['country_id' => $countryId, 'name' => 'Perak', 'code' => 'PRK', 'status' => 'active'],
                ['country_id' => $countryId, 'name' => 'Perlis', 'code' => 'PLS', 'status' => 'active'],
                ['country_id' => $countryId, 'name' => 'Putrajaya', 'code' => 'PJY', 'status' => 'active'],
                ['country_id' => $countryId, 'name' => 'Sabah', 'code' => 'SBH', 'status' => 'active'],
                ['country_id' => $countryId, 'name' => 'Sarawak', 'code' => 'SWK', 'status' => 'active'],
                ['country_id' => $countryId, 'name' => 'Selangor', 'code' => 'SGR', 'status' => 'active'],
                ['country_id' => $countryId, 'name' => 'Terengganu', 'code' => 'TRG', 'status' => 'active'],
            ];

            $db->table('states')->insertBatch($states);
        }
    }

    public function down()
    {
        $this->forge->dropTable('states');
    }
}
