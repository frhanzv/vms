<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * KPK's real Issuance List action (issueCards() / finishPortPassProcessing()
 * in VendorPassServiceImpl.java) records who physically collected the card
 * — collector name and collector IC/passport — plus when. Our Issuance List
 * previously just flipped a card_status flag with no audit trail at all,
 * which is a real gap (not a stylistic simplification) against the real
 * system: this adds that trail.
 *
 * Note: this table already has a `card_id` column (INT unsigned, added by
 * AddCardIdToVendorsTable) that looks like a foreign key to a physical
 * card record — it is NOT reused here as a free-text field, to avoid
 * clashing with whatever already references it.
 */
class AddCollectorFieldsToVendorsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('vendors', [
            'collector_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'card_status',
            ],
            'collector_ic_passport' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'collector_name',
            ],
            'issued_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'collector_ic_passport',
            ],
            'issued_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'issued_by',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('vendors', ['collector_name', 'collector_ic_passport', 'issued_by', 'issued_at']);
    }
}
