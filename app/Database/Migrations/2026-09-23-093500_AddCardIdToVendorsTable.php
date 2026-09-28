<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCardIdToVendorsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('vendors', [
            'card_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'qr_string',
            ],
        ]);
        $this->forge->addKey('card_id');
        $this->forge->processIndexes('vendors');
    }

    public function down()
    {
        $this->forge->dropColumn('vendors', 'card_id');
    }
}
