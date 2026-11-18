<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Vendor pass renewal (KPK renewPortPass, see App\Libraries\VendorRenewal):
 *  - renewal_pending: a renewal is going through approval / process / printing / issuance
 *  - renewed_at:      when the last renewal was submitted
 *  - renew_card_id / renew_expiry: the new card and expiry, held aside so the OLD card keeps
 *    working at the gate until the renewed card is issued
 */
class VendorPassRenewal extends Migration
{
    private const COLS = ['renewal_pending', 'renewed_at', 'renew_card_id', 'renew_expiry'];

    public function up()
    {
        if (! $this->db->fieldExists('renewal_pending', 'vendors')) {
            $this->forge->addColumn('vendors', [
                'renewal_pending' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'card_status'],
                'renewed_at'      => ['type' => 'DATETIME', 'null' => true, 'after' => 'renewal_pending'],
                'renew_card_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'renewed_at'],
                'renew_expiry'    => ['type' => 'DATE', 'null' => true, 'after' => 'renew_card_id'],
            ]);
        }
    }

    public function down()
    {
        foreach (self::COLS as $c) {
            if ($this->db->fieldExists($c, 'vendors')) {
                $this->forge->dropColumn('vendors', $c);
            }
        }
    }
}
