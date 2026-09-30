<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Real KPK's Card Info panel has a "Driving License" section ("No 1",
 * License Class, License Expiry — repeatable) with an "Add License"
 * button. A vendor can hold more than one license class, so this is its
 * own table rather than columns on vendors.
 */
class CreateVendorDrivingLicensesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'vendor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'license_class' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'license_expiry' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('vendor_id');
        $this->forge->addForeignKey('vendor_id', 'vendors', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('vendor_driving_licenses');
    }

    public function down()
    {
        $this->forge->dropTable('vendor_driving_licenses');
    }
}
