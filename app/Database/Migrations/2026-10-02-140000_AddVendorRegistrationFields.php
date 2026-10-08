<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Supports the vendor company self-registration flow (ACMS User Manual
 * 2.0.0, section 1.0 "Company Registration"): a new vendor company's
 * administrator registers their own login account against a company the
 * KPK admin has already pre-registered under Config > Company Management,
 * matched by SSM No (companies.registration_no). ("Company Name In Port
 * Pass" lives on companies too — see 2026-10-02-140400.)
 *
 * - users.ic_number: the administrator's IC Number, collected at
 *   registration and also used (along with username) by "forgot password".
 * - users.activation_token / activation_token_expires_at: a self-registered
 *   account is created with is_active = 0 and only flips to 1 once the
 *   emailed activation link is clicked (token cleared after use).
 */
class AddVendorRegistrationFields extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('ic_number', 'users')) {
            $this->forge->addColumn('users', [
                'ic_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'after'      => 'contact_no',
                ],
            ]);
        }

        if (! $this->db->fieldExists('activation_token', 'users')) {
            $this->forge->addColumn('users', [
                'activation_token' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'ic_number',
                ],
            ]);
        }

        if (! $this->db->fieldExists('activation_token_expires_at', 'users')) {
            $this->forge->addColumn('users', [
                'activation_token_expires_at' => [
                    'type'    => 'DATETIME',
                    'null'    => true,
                    'after'   => 'activation_token',
                ],
            ]);
        }

        // Unique index so a token can be looked up safely; a NULL-heavy unique
        // index is fine in MySQL (NULLs don't collide with each other).
        if (! $this->indexExists('users', 'uq_users_activation_token')) {
            try {
                $this->db->query('ALTER TABLE `users` ADD UNIQUE KEY `uq_users_activation_token` (`activation_token`)');
            } catch (\Throwable $e) {
                // Already there, or column missing for some reason — non-fatal.
            }
        }
    }

    public function down()
    {
        foreach (['activation_token_expires_at', 'activation_token', 'ic_number'] as $col) {
            if ($this->db->fieldExists($col, 'users')) {
                $this->forge->dropColumn('users', $col);
            }
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $dbName = $this->db->getDatabase();
        $row = $this->db->query(
            'SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ? LIMIT 1',
            [$dbName, $table, $indexName]
        )->getRowArray();

        return $row !== null;
    }
}
