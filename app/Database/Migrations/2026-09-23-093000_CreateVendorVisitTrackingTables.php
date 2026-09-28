<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

/**
 * Gate-tracking for vendors — mirrors the invitation_visitors / visitor_card_logs
 * pair used for visitors, so VendorReport can show real in-premise/out-of-window
 * data instead of just pass status.
 */
class CreateVendorVisitTrackingTables extends Migration
{
    public function up()
    {
        // One row per physical entry/exit cycle for a vendor (a vendor can
        // visit many times over the life of one approved pass).
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'vendor_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'vendor_card_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'check_in_time'   => ['type' => 'DATETIME', 'null' => true],
            'check_out_time'  => ['type' => 'DATETIME', 'null' => true],
            'version'         => ['type' => 'INT', 'constraint' => 11, 'default' => 1],
            'created_at'      => ['type' => 'DATETIME', 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('vendor_id');
        $this->forge->addKey('vendor_card_id');
        $this->forge->createTable('vendor_visits', true);

        // Detailed scan/lane history (every tap, not just the open cycle) —
        // same role as visitor_card_logs.
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'vendor_card_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'vendor_id'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'action'         => ['type' => 'VARCHAR', 'constraint' => 20], // checkin | checkout | assigned
            'lane_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'scanned_at'     => ['type' => 'DATETIME'],
            'created_at'     => ['type' => 'DATETIME', 'default' => new RawSql('CURRENT_TIMESTAMP')],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('vendor_id');
        $this->forge->addKey('vendor_card_id');
        $this->forge->addKey('scanned_at');
        $this->forge->createTable('vendor_card_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('vendor_card_logs');
        $this->forge->dropTable('vendor_visits');
    }
}
