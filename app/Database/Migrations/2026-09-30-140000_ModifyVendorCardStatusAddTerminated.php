<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Real KPK's Closed List "Card Terminate" button needs a third card_status
 * value — our card_status column was ENUM('Active','Inactive') only, with
 * no way to represent a terminated card without overloading 'Inactive'
 * (which already means "not yet printed").
 */
class ModifyVendorCardStatusAddTerminated extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('vendors', [
            'card_status' => [
                'name'       => 'card_status',
                'type'       => 'ENUM',
                'constraint' => ['Active', 'Inactive', 'Terminated'],
                'default'    => 'Inactive',
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        // Any 'Terminated' rows are coerced back to 'Inactive' first so the
        // narrower enum doesn't reject them.
        $db = \Config\Database::connect();
        $db->table('vendors')->where('card_status', 'Terminated')->update(['card_status' => 'Inactive']);

        $this->forge->modifyColumn('vendors', [
            'card_status' => [
                'name'       => 'card_status',
                'type'       => 'ENUM',
                'constraint' => ['Active', 'Inactive'],
                'default'    => 'Inactive',
                'null'       => false,
            ],
        ]);
    }
}
