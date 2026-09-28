<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Fields found in KPK's real my-port-pass.component.ts (titled "SafeG |
 * Vendor Pass" — the actual full vendor request page): MySejahtera
 * (vaccination cert, 2 uploads) and a separate facial-identity photo,
 * distinct from the general profile photo already on this table.
 */
class AddVerificationDocsToVendorsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('vendors', [
            'mysejahtera_cert' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'other_doc',
            ],
            'mysejahtera_cert_2' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'mysejahtera_cert',
            ],
            'facial_photo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'mysejahtera_cert_2',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('vendors', ['mysejahtera_cert', 'mysejahtera_cert_2', 'facial_photo']);
    }
}
