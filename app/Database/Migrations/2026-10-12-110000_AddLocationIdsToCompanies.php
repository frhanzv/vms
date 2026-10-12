<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/** A vendor company can have several Location Access choices (comma separated location ids). */
class AddLocationIdsToCompanies extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('location_ids', 'companies')) {
            $this->forge->addColumn('companies', [
                'location_ids' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true, 'after' => 'location_id'],
            ]);
        }
        // Existing companies keep their single location.
        $this->db->query('UPDATE ' . $this->db->prefixTable('companies') . ' SET location_ids = CAST(location_id AS CHAR) WHERE location_ids IS NULL AND location_id IS NOT NULL');
    }

    public function down()
    {
        if ($this->db->fieldExists('location_ids', 'companies')) {
            $this->forge->dropColumn('companies', 'location_ids');
        }
    }
}
