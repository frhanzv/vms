<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

/**
 * These columns are read/written by VendorList::approve()/reject() but were
 * never added to the vendors table — that's the actual reason Approve/Reject
 * don't work: the controller's WHERE clause references `next_action`
 * directly, and on a table without that column MySQL/Postgres throws
 * "Unknown column", which breaks the JSON the frontend expects and makes
 * the button look like it does nothing.
 */
class AddApprovalWorkflowFieldsToVendorsTable extends Migration
{
    public function up()
    {
        // Guarded per-column: this environment's `vendors` table already had
        // some of these columns added outside of a tracked migration run, so
        // a plain addColumn() would fail with "Duplicate column name" and
        // block every migration after it. Only add what's actually missing.
        $this->db->resetDataCache();
        $columns = [
            'access_branch' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'status',
            ],
            'next_action' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'access_branch',
            ],
            'reject_reason' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'remark',
            ],
        ];
        foreach (array_keys($columns) as $field) {
            if ($this->db->fieldExists($field, 'vendors')) {
                unset($columns[$field]);
            }
        }
        if (! empty($columns)) {
            $this->forge->addColumn('vendors', $columns);
        }

        if (! $this->db->tableExists('vendor_status_logs')) {
            $this->forge->addField([
                'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'vendor_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'action'        => ['type' => 'VARCHAR', 'constraint' => 30],
                'from_status'   => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'to_status'     => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'next_action'   => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                'remark'        => ['type' => 'TEXT', 'null' => true],
                'reject_reason' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'acted_by'      => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
                'created_at'    => ['type' => 'DATETIME', 'default' => new RawSql('CURRENT_TIMESTAMP')],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('vendor_id');
            $this->forge->addForeignKey('vendor_id', 'vendors', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('vendor_status_logs');
        }
    }

    public function down()
    {
        $this->forge->dropColumn('vendors', ['access_branch', 'next_action', 'reject_reason']);
        $this->forge->dropTable('vendor_status_logs', true);
    }
}
