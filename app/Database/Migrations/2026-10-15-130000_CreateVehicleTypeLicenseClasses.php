<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** Which License Classes may drive each Vehicle Type (Config > Vehicle Types > License Class), as in KPK. */
class CreateVehicleTypeLicenseClasses extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('vehicle_type_license_classes')) {
            return;
        }
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'vehicle_type_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'license_class_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('vehicle_type_id');
        $this->forge->addKey('license_class_id');
        $this->forge->createTable('vehicle_type_license_classes');
    }

    public function down()
    {
        $this->forge->dropTable('vehicle_type_license_classes', true);
    }
}
