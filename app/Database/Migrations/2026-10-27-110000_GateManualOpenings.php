<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Monitoring > Open Gate (KPK BarrierGateController /api/adam/trigger + AdamService.createTransaction):
 *
 *  - gate_manual_openings      one row per barrier opened by hand from the Monitoring page: lane, gate in / out,
 *                              the reason typed in ("Manual Reason") and who opened it. Shown in the transaction
 *                              list with Mode MANUAL.
 *  - locations.adam_password   widened: it is now stored encrypted (so it can be sent to the ADAM module)
 *                              instead of hashed. Passwords saved before this must be typed in again.
 */
class GateManualOpenings extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('gate_manual_openings')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'lane_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'location_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'direction'   => ['type' => 'VARCHAR', 'constraint' => 3], // in | out
                'reason'      => ['type' => 'VARCHAR', 'constraint' => 255],
                'opened_by'   => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'opened_at'   => ['type' => 'DATETIME'],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('opened_at');
            $this->forge->addKey('location_id');
            $this->forge->createTable('gate_manual_openings');
        }

        if ($this->db->tableExists('locations') && $this->db->fieldExists('adam_password', 'locations')) {
            $this->forge->modifyColumn('locations', [
                'adam_password' => ['name' => 'adam_password', 'type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('gate_manual_openings', true);
    }
}
