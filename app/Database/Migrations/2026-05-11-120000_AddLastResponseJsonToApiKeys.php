<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLastResponseJsonToApiKeys extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('api_keys')) {
            return;
        }

        if ($this->db->fieldExists('last_response_json', 'api_keys')) {
            return;
        }

        $this->forge->addColumn('api_keys', [
            'last_response_json' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->tableExists('api_keys') && $this->db->fieldExists('last_response_json', 'api_keys')) {
            $this->forge->dropColumn('api_keys', 'last_response_json');
        }
    }
}
