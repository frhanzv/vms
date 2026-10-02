<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Real KPK's Printing List / Process List keep a reprint history per vendor
 * (viewReprintHistoryDetails / viewReprintAttachment) — every time a card
 * gets (re)printed, who did it and why is logged. Our Printing List
 * previously just overwrote receipt_no with no history at all.
 */
class CreateVendorCardPrintLogsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('vendor_card_print_logs')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'vendor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'receipt_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'is_reprint' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'reason' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'printed_by' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'printed_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('vendor_id');
        $this->forge->addForeignKey('vendor_id', 'vendors', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('vendor_card_print_logs');
    }

    public function down()
    {
        $this->forge->dropTable('vendor_card_print_logs');
    }
}
