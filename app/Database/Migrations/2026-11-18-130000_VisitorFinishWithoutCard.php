<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * KPK visitor pass list "Finish Without Card": the visitor is cleared to go in without a physical card
 * (they use their QR code at the turnstile). invitation_visitors.no_card_at records when, and by whom.
 */
class VisitorFinishWithoutCard extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('no_card_at', 'invitation_visitors')) {
            $this->forge->addColumn('invitation_visitors', [
                'no_card_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'visitor_card_id'],
                'no_card_by' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'no_card_at'],
            ]);
        }
    }

    public function down()
    {
        foreach (['no_card_at', 'no_card_by'] as $c) {
            if ($this->db->fieldExists($c, 'invitation_visitors')) {
                $this->forge->dropColumn('invitation_visitors', $c);
            }
        }
    }
}
