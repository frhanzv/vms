<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * "Company Name In Port Pass" belongs to the vendor COMPANY (Config >
 * Company Management, `companies` table), not to the client/tenant.
 *
 * - Adds companies.pass_name.
 * - If an earlier build of the self-registration feature already added
 *   clients.pass_name, copies nothing across (it was never used by anything
 *   real) and drops it so the two lists don't drift apart.
 */
class MoveVendorPassNameToCompanies extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('pass_name', 'companies')) {
            $this->forge->addColumn('companies', [
                'pass_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'name',
                ],
            ]);
        }

        if ($this->db->fieldExists('pass_name', 'clients')) {
            $this->forge->dropColumn('clients', 'pass_name');
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('pass_name', 'companies')) {
            $this->forge->dropColumn('companies', 'pass_name');
        }
    }
}
