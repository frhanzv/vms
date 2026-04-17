<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReleasedDateToBlacklistTable extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('blacklist')) {
            return;
        }
        if ($this->db->fieldExists('released_date', 'blacklist')) {
            return;
        }

        $this->forge->addColumn('blacklist', [
            'released_date' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'updated_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('blacklist', 'released_date');
    }
}