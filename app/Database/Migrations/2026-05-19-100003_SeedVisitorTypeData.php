<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Replaces the generic visitor types with the real ISK facility entries.
 */
class SeedVisitorTypeData extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');

        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->db->table('visitor_types')->truncate();
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

        $rows = [
            ['name' => 'BUSINESS MEETING',  'path' => 'ROUTE FOR CUSTOMER BUSINESS MEET'],
            ['name' => 'CUSTOMER BUYOFF',   'path' => 'ROUTE FOR CUSTOMER BUYOFF'],
            ['name' => 'EXT STAFF',         'path' => 'ROUTE FOR EXTERNAL STAFF'],
            ['name' => 'INTERVIEW',         'path' => 'INTERVIEW'],
            ['name' => 'SUPPLIER GOODS',    'path' => 'ROUTE FOR SUPPLIER GOODS'],
            ['name' => 'SUPPLIER SERVICE',  'path' => 'ROUTE FOR SUPPLIER SERVICE'],
            ['name' => 'TRAINER',           'path' => 'TRAINING'],
        ];

        $batch = [];
        foreach ($rows as $row) {
            $batch[] = [
                'name'       => $row['name'],
                'path'       => $row['path'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $this->db->table('visitor_types')->insertBatch($batch);
    }

    public function down()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->db->table('visitor_types')->truncate();
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}
