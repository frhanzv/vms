<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * KPK "List of Vehicle Type" has one yes/no column each for Commercial, Container, Cargo, Non-Cargo, Public,
 * Car, Motorbike and Van. They are added as flags; the older category / vehicle_group / operation columns stay
 * and are kept in sync from the flags on every save, so nothing that reads them changes.
 * Existing types are back-filled from those older columns. Safe to run more than once.
 */
class VehicleTypeKpkFlags extends Migration
{
    private const FLAGS = ['is_commercial', 'is_container', 'is_cargo', 'is_non_cargo', 'is_public', 'is_car', 'is_motorbike', 'is_van'];

    public function up()
    {
        if (! $this->db->tableExists('vehicle_types')) {
            return;
        }
        $add = [];
        foreach (self::FLAGS as $f) {
            if (! $this->db->fieldExists($f, 'vehicle_types')) {
                $add[$f] = ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0];
            }
        }
        if (! $add) {
            return;
        }
        $this->forge->addColumn('vehicle_types', $add);

        $t = $this->db->prefixTable('vehicle_types');
        $hasGroup = $this->db->fieldExists('vehicle_group', 'vehicle_types');
        $hasOp    = $this->db->fieldExists('operation', 'vehicle_types');
        $this->db->query("UPDATE {$t} SET is_car = (category = 'car'), is_motorbike = (category = 'motorbike'), is_van = (category = 'van'), is_commercial = (category = 'truck')");
        if ($hasGroup) {
            $this->db->query("UPDATE {$t} SET is_public = (vehicle_group IS NULL OR vehicle_group IN ('public', 'both'))");
        } else {
            $this->db->query("UPDATE {$t} SET is_public = 1");
        }
        if ($hasOp) {
            $this->db->query("UPDATE {$t} SET is_container = (operation IN ('CONTAINER', 'CONTAINER, CARGO AND NON-CARGO')), is_cargo = (operation IN ('CARGO AND NON-CARGO', 'CONTAINER, CARGO AND NON-CARGO')), is_non_cargo = (operation IN ('NON-CARGO', 'CARGO AND NON-CARGO', 'CONTAINER, CARGO AND NON-CARGO'))");
            if ($hasGroup) {
                // Cargo | Non-Cargo types that had no operation: keep them visible on the Cargo | Non-Cargo form
                $this->db->query("UPDATE {$t} SET is_cargo = 1, is_non_cargo = 1 WHERE vehicle_group IN ('cnc', 'both') AND (operation IS NULL OR operation = '')");
            }
        } elseif ($hasGroup) {
            $this->db->query("UPDATE {$t} SET is_cargo = 1, is_non_cargo = 1 WHERE vehicle_group IN ('cnc', 'both')");
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('vehicle_types')) {
            return;
        }
        foreach (self::FLAGS as $f) {
            if ($this->db->fieldExists($f, 'vehicle_types')) {
                $this->forge->dropColumn('vehicle_types', $f);
            }
        }
    }
}
