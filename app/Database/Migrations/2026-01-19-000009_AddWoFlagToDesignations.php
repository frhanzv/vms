<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWoFlagToDesignations extends Migration
{
    public function up()
    {
        $this->forge->addColumn('designations', [
            'wo_flag' => [
                'type' => 'ENUM',
                'constraint' => ['yes', 'no'],
                'default' => 'no',
                'after' => 'description',
            ],
        ]);

        // Update existing records with sample wo_flag values
        $this->db->query("UPDATE designations SET wo_flag = 'yes' WHERE code IN ('CEO', 'CTO', 'CFO', 'MGR')");
    }

    public function down()
    {
        $this->forge->dropColumn('designations', 'wo_flag');
    }
}
