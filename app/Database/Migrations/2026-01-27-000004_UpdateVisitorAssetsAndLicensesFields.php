<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateVisitorAssetsAndLicensesFields extends Migration
{
    public function up()
    {
        // Add fields to visitor_equipment
        $this->forge->addColumn('visitor_equipment', [
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'invitation_id'
            ],
            'size' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'after' => 'equipment_type'
            ],
            'transport' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'size'
            ],
            'purpose' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'transport'
            ],
            'voltage' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => true,
                'after' => 'purpose'
            ],
        ]);

        // Add license_class to visitor_licenses
        $this->forge->addColumn('visitor_licenses', [
            'license_class' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
                'after' => 'invitation_id'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('visitor_equipment', ['category', 'size', 'transport', 'purpose', 'voltage']);
        $this->forge->dropColumn('visitor_licenses', ['license_class']);
    }
}
