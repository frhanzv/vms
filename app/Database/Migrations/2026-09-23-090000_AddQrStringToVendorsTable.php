<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Matches KPK's VendorPass.qrString field — an ID-prefixed random string
 * embedded in a QR code, looked up publicly (no login) to verify a pass.
 */
class AddQrStringToVendorsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('vendors', [
            'qr_string' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'photo',
            ],
        ]);
        $this->forge->addKey('qr_string');
        $this->forge->processIndexes('vendors');
    }

    public function down()
    {
        $this->forge->dropColumn('vendors', 'qr_string');
    }
}
