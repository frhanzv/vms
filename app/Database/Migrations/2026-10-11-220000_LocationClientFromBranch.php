<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Location Access "Branch" is now a Client dropdown (each client, or BOTH).
 * Existing rows whose branch text equals a client's name become that client's
 * gate; anything else (BOTH, old free text) is left untouched.
 */
class LocationClientFromBranch extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('locations') || ! $this->db->fieldExists('client_id', 'locations')) {
            return;
        }
        foreach ($this->db->table('clients')->select('id, name')->get()->getResultArray() as $c) {
            $this->db->table('locations')
                ->where('client_id IS NULL', null, false)
                ->where('LOWER(branch)', strtolower(trim((string) $c['name'])))
                ->update(['client_id' => (int) $c['id']]);
        }
    }

    public function down()
    {
    }
}
