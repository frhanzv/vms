<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Safety net for installs where 2026-10-13-130000 ran BEFORE 2026-10-13-100000 (the vehicles table did not
 * exist yet, so its new columns were skipped). Adds anything still missing; does nothing if all is in place.
 */
class VehicleGroupColumnsRepair extends Migration
{
    private function addIf(string $table, array $cols): void
    {
        if (! $this->db->tableExists($table)) {
            return;
        }
        $add = [];
        foreach ($cols as $name => $def) {
            if (! $this->db->fieldExists($name, $table)) {
                $add[$name] = $def;
            }
        }
        if ($add) {
            $this->forge->addColumn($table, $add);
        }
    }

    public function up()
    {
        $this->addIf('vehicles', [
            'vehicle_group'     => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'public'],
            'pass_for'          => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'vendor'],
            'vendor_company_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_by_user'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'renewed_from'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
        ]);
        $this->addIf('vehicle_types', [
            'vehicle_group' => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'both'],
        ]);
    }

    public function down()
    {
        // nothing to undo: the columns belong to 2026-10-13-130000
    }
}
