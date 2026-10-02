<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * The real KPK Reminder popup documents a "Multiple License Class Format"
 * (e.g. B,C,D) — one license record can hold several comma-separated
 * classes. The original VARCHAR(10) was sized for a single class only;
 * widen it so bulk import (VendorList::import()) can store these safely.
 */
class WidenVendorLicenseClassForImport extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('license_class', 'vendor_driving_licenses')) {
            $this->forge->modifyColumn('vendor_driving_licenses', [
                'license_class' => [
                    'name'       => 'license_class',
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('license_class', 'vendor_driving_licenses')) {
            $this->forge->modifyColumn('vendor_driving_licenses', [
                'license_class' => [
                    'name'       => 'license_class',
                    'type'       => 'VARCHAR',
                    'constraint' => 10,
                    'null'       => true,
                ],
            ]);
        }
    }
}
