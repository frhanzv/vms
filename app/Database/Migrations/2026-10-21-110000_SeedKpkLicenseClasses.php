<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Config > License Class: the classes KPK already has, with their minimum ages, as shared rows (client_id NULL)
 * that every client sees. A class that is already there (same name) is left alone, so this is safe to run
 * more than once and never overwrites an age someone changed.
 */
class SeedKpkLicenseClasses extends Migration
{
    private const CLASSES = [
        'B' => 16, 'B1' => 16, 'B2' => 16, 'C' => 16,
        'D' => 17, 'DA' => 17,
        'E' => 21, 'E1' => 21, 'E2' => 21, 'E3' => 21,
    ];

    public function up()
    {
        if (! $this->db->tableExists('license_classes')) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        foreach (self::CLASSES as $name => $age) {
            $exists = $this->db->table('license_classes')->where('client_id IS NULL', null, false)->where('LOWER(name)', strtolower($name))->countAllResults() > 0;
            if (! $exists) {
                $this->db->table('license_classes')->insert(['client_id' => null, 'name' => $name, 'min_age' => $age, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]);
            }
        }
    }

    public function down()
    {
        // left in place: rows may already be linked to vehicle types
    }
}
