<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReasonToBlacklistTable extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('blacklist')) {
            return;
        }

        if ($this->db->fieldExists('reason', 'blacklist')) {
            return;
        }

        $this->forge->addColumn('blacklist', [
            'reason' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'after'      => 'created_date',
            ],
        ]);
    }

    public function down()
    {
        if (! $this->db->tableExists('blacklist')) {
            return;
        }

        if (! $this->db->fieldExists('reason', 'blacklist')) {
            return;
        }

        $this->forge->dropColumn('blacklist', 'reason');
    }
}
