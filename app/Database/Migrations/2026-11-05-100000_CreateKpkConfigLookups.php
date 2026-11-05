<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * KPK Config lists that VMS did not have yet (see App\Libraries\ConfigLookups):
 *   payment_types       KPK payment-info        type STAFF/LORRY, period, amount  -> TWP rates
 *   branch_offices      KPK branch-office       code KSB/KPK, name, address, state -> Approving Branch
 *   release_reasons     KPK release-reason      reason, BLACKLIST/SUSPEND          -> Blacklist release
 *   operating_hours     KPK operating-hours     day, start/end time                -> kiosk walk-in expiry
 *   bank_names          KPK bank-name-info      name
 *   sub_business_types  KPK sub-categories-info name, business type, PLI
 * client_id NULL = shared by every client (managed by the platform admin); otherwise the client's own.
 * Nothing is seeded. Also adds blacklist.release_reason and the 'released' blacklist status.
 */
class CreateKpkConfigLookups extends Migration
{
    private function base(array $fields): array
    {
        return ['id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'client_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true]]
            + $fields
            + ['status' => ['type' => 'ENUM', 'constraint' => ['active', 'inactive'], 'default' => 'active'],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true]];
    }

    private function make(string $table, array $fields): void
    {
        if ($this->db->tableExists($table)) {
            return;
        }
        $this->forge->addField($this->base($fields));
        $this->forge->addKey('id', true);
        $this->forge->addKey('client_id');
        $this->forge->createTable($table);
    }

    public function up()
    {
        $this->make('payment_types', [
            'type'   => ['type' => 'VARCHAR', 'constraint' => 20],
            'period' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'ONE MONTH'],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
        ]);
        $this->make('branch_offices', [
            'code'    => ['type' => 'VARCHAR', 'constraint' => 10],
            'name'    => ['type' => 'VARCHAR', 'constraint' => 150],
            'address' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'state'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        ]);
        $this->make('release_reasons', [
            'reason'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'blacklist_type' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'BLACKLIST'],
        ]);
        $this->make('operating_hours', [
            'day'        => ['type' => 'VARCHAR', 'constraint' => 10],
            'start_time' => ['type' => 'TIME'],
            'end_time'   => ['type' => 'TIME'],
        ]);
        $this->make('bank_names', [
            'name' => ['type' => 'VARCHAR', 'constraint' => 150],
        ]);
        $this->make('sub_business_types', [
            'name'             => ['type' => 'VARCHAR', 'constraint' => 150],
            'business_type_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'pli'              => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => ''],
        ]);

        if ($this->db->tableExists('blacklist') && ! $this->db->fieldExists('release_reason', 'blacklist')) {
            $this->forge->addColumn('blacklist', [
                'release_reason' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'released_date'],
            ]);
        }
        // Blacklist Closed List > Release saves status 'released' (BlacklistRequestModel::release and its
        // validation already allow it) but the column was created as ENUM('active','pending','closed'),
        // so Release failed on MySQL strict mode. Add the missing value; existing rows are unchanged.
        if ($this->db->tableExists('blacklist') && $this->db->fieldExists('status', 'blacklist')) {
            $this->forge->modifyColumn('blacklist', [
                'status' => ['name' => 'status', 'type' => 'ENUM', 'constraint' => ['active', 'pending', 'closed', 'released'], 'default' => 'pending', 'null' => false],
            ]);
        }
    }

    public function down()
    {
        foreach (['payment_types', 'branch_offices', 'release_reasons', 'operating_hours', 'bank_names', 'sub_business_types'] as $t) {
            $this->forge->dropTable($t, true);
        }
        if ($this->db->tableExists('blacklist') && $this->db->fieldExists('release_reason', 'blacklist')) {
            $this->forge->dropColumn('blacklist', 'release_reason');
        }
    }
}
