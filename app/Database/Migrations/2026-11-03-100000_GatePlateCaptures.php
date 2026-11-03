<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * KPK SAFEG_GT_A_M_NP_PHOTO + SAFEG_GT_NO_VIN_S: every plate the lane's ALPR camera (lanes.cam_id_1)
 * reads at a tap is stored against the gate transaction, whatever the vehicle:
 *   registered   - plate belongs to a registered vehicle whose type takes the plate photo
 *   tap_only     - registered vehicle whose type only needs the pass tapped (vehicle_types.capture_plate = 0)
 *   unregistered - no registered vehicle has this plate (KPK "No Vehicle Sticker")
 */
class GatePlateCaptures extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('gate_plate_captures')) {
            return;
        }
        $s  = static fn(int $n) => ['type' => 'VARCHAR', 'constraint' => $n, 'null' => true];
        $fk = ['type' => 'INT', 'unsigned' => true, 'null' => true];
        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true], 'company_id' => $fk, 'lane_id' => $fk,
            'tx_key' => $s(40), 'holder_type' => $s(20), 'card_epc' => $s(100), 'direction' => $s(5), 'plate_no' => $s(40),
            'category' => $s(20), 'vehicle_id' => $fk, 'vehicle_type' => $s(100), 'camera' => $s(50), 'alpr_ref' => $s(80),
            'image_path' => $s(255), 'captured_at' => ['type' => 'DATETIME', 'null' => true], 'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['tx_key', 'direction']);
        $this->forge->addKey('category');
        $this->forge->createTable('gate_plate_captures');
    }

    public function down()
    {
        $this->forge->dropTable('gate_plate_captures', true);
    }
}
