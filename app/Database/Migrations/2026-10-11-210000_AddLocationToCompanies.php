<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * A vendor company is created under a Location Access (not a branch). The
 * location's owner client becomes the company's client, so when the vendor
 * registers it is automatically under that client.
 */
class AddLocationToCompanies extends Migration
{
    public function up()
    {
        $add = [];
        if (! $this->db->fieldExists('location_id', 'companies')) {
            $add['location_id'] = ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true];
        }
        if (! $this->db->fieldExists('client_id', 'companies')) {
            $add['client_id'] = ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true];
        }
        if ($add) {
            $this->forge->addColumn('companies', $add);
        }
    }

    public function down()
    {
        foreach (['location_id', 'client_id'] as $f) {
            if ($this->db->fieldExists($f, 'companies')) {
                $this->forge->dropColumn('companies', $f);
            }
        }
    }
}
