<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVisaLicenseFieldsToStaff extends Migration
{
    public function up()
    {
        $fields = [
            'visa_expiry'   => ['type' => 'DATE', 'null' => true, 'after' => 'ic_passport'],
            'license_class' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'visa_expiry'],
            'license_expiry' => ['type' => 'DATE', 'null' => true, 'after' => 'license_class'],
        ];

        $this->forge->addColumn('staff', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('staff', ['visa_expiry', 'license_class', 'license_expiry']);
    }
}
