<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGuardEntryDecisionToInvitations extends Migration
{
    public function up(): void
    {
        $columns = [];

        if (! $this->db->fieldExists('guard_entry_status', 'invitations')) {
            $columns['guard_entry_status'] = [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'Expected',
                'after'      => 'checked_in_at',
            ];
        }
        if (! $this->db->fieldExists('guard_decided_at', 'invitations')) {
            $columns['guard_decided_at'] = [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'guard_entry_status',
            ];
        }
        if (! $this->db->fieldExists('guard_decided_by', 'invitations')) {
            $columns['guard_decided_by'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'guard_decided_at',
            ];
        }
        if (! $this->db->fieldExists('guard_rejection_reason', 'invitations')) {
            $columns['guard_rejection_reason'] = [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'after'      => 'guard_decided_by',
            ];
        }

        if ($columns !== []) {
            $this->forge->addColumn('invitations', $columns);
        }

        $this->db->table('invitations')
            ->where('checked_in_at IS NOT NULL', null, false)
            ->update(['guard_entry_status' => 'Approved']);
    }

    public function down(): void
    {
        foreach (['guard_rejection_reason', 'guard_decided_by', 'guard_decided_at', 'guard_entry_status'] as $column) {
            if ($this->db->fieldExists($column, 'invitations')) {
                $this->forge->dropColumn('invitations', $column);
            }
        }
    }
}
