<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Gate detection order per lane (Config > Lane Management):
 *  - camera_first:  the plate camera reads the car, then the antenna / mobile app tap follows
 *  - antenna_first: the antenna / mobile app tap comes first, then the camera reads the plate
 * detect_window = seconds the other device may take. gate_plate_captures.tap_at keeps the tap
 * time of an antenna_first capture that is still waiting for the camera (category 'pending').
 */
class LaneDetectOrder extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('detect_order', 'lanes')) {
            $this->forge->addColumn('lanes', [
                'detect_order'  => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'camera_first', 'after' => 'scan_type'],
                'detect_window' => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 120, 'after' => 'detect_order'],
            ]);
        }
        if ($this->db->tableExists('gate_plate_captures') && ! $this->db->fieldExists('tap_at', 'gate_plate_captures')) {
            $this->forge->addColumn('gate_plate_captures', [
                'tap_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'captured_at'],
            ]);
        }
    }

    public function down()
    {
        foreach (['detect_order', 'detect_window'] as $c) {
            if ($this->db->fieldExists($c, 'lanes')) {
                $this->forge->dropColumn('lanes', $c);
            }
        }
        if ($this->db->tableExists('gate_plate_captures') && $this->db->fieldExists('tap_at', 'gate_plate_captures')) {
            $this->forge->dropColumn('gate_plate_captures', 'tap_at');
        }
    }
}
