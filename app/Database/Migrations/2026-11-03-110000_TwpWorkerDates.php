<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * KPK Work Pass Info "Update": each worker pass has its own Date and Time In / Out.
 * Empty = the request's dates (twp_requests.date_from / date_to).
 */
class TwpWorkerDates extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('date_from', 'twp_workers')) {
            $this->forge->addColumn('twp_workers', [
                'date_from' => ['type' => 'DATETIME', 'null' => true, 'after' => 'qr_string'],
                'date_to'   => ['type' => 'DATETIME', 'null' => true, 'after' => 'date_from'],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('twp_workers', ['date_from', 'date_to']);
    }
}
