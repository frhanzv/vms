<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddScanTypeToLanes extends Migration
{
    public function up()
    {
        $this->forge->addColumn('lanes', [
            'scan_type' => [
                'type'       => 'ENUM',
                'constraint' => ['rfid', 'qr_code'],
                'default'    => 'rfid',
                'null'       => false,
                'after'      => 'rfid_enabled',
            ],
        ]);

        // Seed the global scan_type setting if not already present
        $db = \Config\Database::connect();
        $exists = $db->table('settings')->where('setting_key', 'scan_type')->countAllResults();
        if (!$exists) {
            $db->table('settings')->insert([
                'setting_key'   => 'scan_type',
                'setting_value' => 'rfid',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('lanes', 'scan_type');

        $db = \Config\Database::connect();
        $db->table('settings')->where('setting_key', 'scan_type')->delete();
    }
}
