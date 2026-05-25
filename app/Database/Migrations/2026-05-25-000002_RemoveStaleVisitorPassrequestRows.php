<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveStaleVisitorPassrequestRows extends Migration
{
    // 'visitor_passrequest' (no underscore) was a typo of 'visitor_pass_request'.
    // Five rows were saved under this invalid form_type for company_id=4 and are
    // never read by any code path. Safe to delete.
    public function up(): void
    {
        $this->db->table('client_form_fields')
            ->where('form_type', 'visitor_passrequest')
            ->delete();
    }

    public function down(): void
    {
        // Stale data — no meaningful restore needed.
    }
}
