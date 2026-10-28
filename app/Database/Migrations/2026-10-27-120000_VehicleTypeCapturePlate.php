<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Per vehicle type: does the gate take a plate number photo, or is tapping the pass enough. */
class VehicleTypeCapturePlate extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('capture_plate', 'vehicle_types')) {
            $this->forge->addColumn('vehicle_types', [
                'capture_plate' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1, 'after' => 'duplicate_plate_ok'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('capture_plate', 'vehicle_types')) {
            $this->forge->dropColumn('vehicle_types', 'capture_plate');
        }
    }
}
