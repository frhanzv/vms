<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddQrScanSupport extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Drop the FK on visitor_card_id so we can make the column nullable
        $db->query('ALTER TABLE visitor_card_logs DROP FOREIGN KEY visitor_card_logs_visitor_card_id_foreign');

        // Make visitor_card_id nullable (QR scans have no physical card)
        $db->query('ALTER TABLE visitor_card_logs MODIFY visitor_card_id INT(11) UNSIGNED NULL');

        // Re-add FK with SET NULL on delete so orphan rows stay intact
        $db->query('ALTER TABLE visitor_card_logs ADD CONSTRAINT visitor_card_logs_visitor_card_id_foreign
            FOREIGN KEY (visitor_card_id) REFERENCES visitor_cards(id) ON DELETE SET NULL ON UPDATE CASCADE');

        // Add scan_source to track whether the scan came from RFID or QR reader
        $this->forge->addColumn('visitor_card_logs', [
            'scan_source' => [
                'type'       => 'ENUM',
                'constraint' => ['rfid', 'qr_code'],
                'default'    => 'rfid',
                'null'       => false,
                'after'      => 'scanned_at',
            ],
        ]);
    }

    public function down()
    {
        $db = \Config\Database::connect();

        $this->forge->dropColumn('visitor_card_logs', 'scan_source');

        $db->query('ALTER TABLE visitor_card_logs DROP FOREIGN KEY visitor_card_logs_visitor_card_id_foreign');
        $db->query('ALTER TABLE visitor_card_logs MODIFY visitor_card_id INT(11) UNSIGNED NOT NULL');
        $db->query('ALTER TABLE visitor_card_logs ADD CONSTRAINT visitor_card_logs_visitor_card_id_foreign
            FOREIGN KEY (visitor_card_id) REFERENCES visitor_cards(id) ON DELETE CASCADE ON UPDATE CASCADE');
    }
}
