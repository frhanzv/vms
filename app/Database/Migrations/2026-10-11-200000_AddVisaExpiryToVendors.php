<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** The vendor import template has a "Visa Expiry" column; keep it. */
class AddVisaExpiryToVendors extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('visa_expiry', 'vendors')) {
            $this->forge->addColumn('vendors', ['visa_expiry' => ['type' => 'DATE', 'null' => true]]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('visa_expiry', 'vendors')) {
            $this->forge->dropColumn('vendors', 'visa_expiry');
        }
    }
}
