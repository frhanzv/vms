<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Vendor self-registration is now verified by an administrator under
 * Config > User (instead of an emailed activation link).
 *
 * users.verified_at / verified_by record when (and by whom) an account was
 * switched on. A vendor_admin account that is inactive AND has no verified_at
 * is "pending verification". Existing active accounts are back-filled so none
 * of them is mistaken for a pending one.
 */
class AddUserVerificationColumns extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('verified_at', 'users')) {
            $this->forge->addColumn('users', [
                'verified_at' => ['type' => 'DATETIME', 'null' => true],
                'verified_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            ]);
        }

        $this->db->query("UPDATE users SET verified_at = COALESCE(updated_at, created_at, NOW()) WHERE is_active = 1 AND verified_at IS NULL");
    }

    public function down()
    {
        foreach (['verified_by', 'verified_at'] as $col) {
            if ($this->db->fieldExists($col, 'users')) {
                $this->forge->dropColumn('users', $col);
            }
        }
    }
}
