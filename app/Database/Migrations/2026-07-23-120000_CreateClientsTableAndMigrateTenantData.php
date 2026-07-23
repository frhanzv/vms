<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClientsTableAndMigrateTenantData extends Migration
{
    /** @var list<string> */
    private array $tenantTables = [
        'client_features',
        'client_form_fields',
        'client_notification_settings',
        'client_messaging_credentials',
        'whatsapp_templates',
    ];

    public function up()
    {
        if (!$this->db->tableExists('clients')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'code' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
                'registration_no' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'address' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'contact_no' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => true,
                ],
                'email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['active', 'inactive'],
                    'default'    => 'active',
                ],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
                'version'    => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'default'    => 1,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('name');
            $this->forge->addKey('status');
            $this->forge->createTable('clients');
        }

        $tenantCompanyIds = $this->collectTenantCompanyIds();
        $this->seedClientsFromCompanies($tenantCompanyIds);
        $this->migrateTenantTablesToClientId();
        $this->migrateUsersToClientId($tenantCompanyIds);
        $this->migrateInvitationsToClientId($tenantCompanyIds);
    }

    public function down()
    {
        // Data migration is one-way; only drop clients table on rollback.
        if ($this->db->tableExists('clients')) {
            $this->forge->dropTable('clients', true);
        }
    }

    /** @return list<int> */
    private function collectTenantCompanyIds(): array
    {
        $ids = [];

        $sources = [
            ['users', 'company_id'],
            ['client_features', 'company_id'],
            ['client_form_fields', 'company_id'],
            ['client_notification_settings', 'company_id'],
            ['client_messaging_credentials', 'company_id'],
            ['whatsapp_templates', 'company_id'],
            ['invitations', 'company_id'],
        ];

        foreach ($sources as [$table, $column]) {
            if (!$this->db->tableExists($table) || !$this->db->fieldExists($column, $table)) {
                continue;
            }
            $rows = $this->db->table($table)
                ->distinct()
                ->select($column)
                ->where($column . ' >', 0)
                ->get()
                ->getResultArray();

            foreach ($rows as $row) {
                $ids[(int) $row[$column]] = true;
            }
        }

        $result = array_keys($ids);
        sort($result);

        return $result;
    }

    /** @param list<int> $tenantCompanyIds */
    private function seedClientsFromCompanies(array $tenantCompanyIds): void
    {
        if ($tenantCompanyIds === [] || !$this->db->tableExists('companies')) {
            return;
        }

        foreach ($tenantCompanyIds as $companyId) {
            if ($this->db->table('clients')->where('id', $companyId)->countAllResults() > 0) {
                continue;
            }

            $company = $this->db->table('companies')->where('id', $companyId)->get()->getRowArray();
            if (!$company) {
                continue;
            }

            $this->db->table('clients')->insert([
                'id'              => $companyId,
                'name'            => $company['name'],
                'code'            => null,
                'registration_no' => $company['registration_no'] ?? null,
                'address'         => $company['address'] ?? null,
                'contact_no'      => $company['contact_no'] ?? null,
                'email'           => $company['email'] ?? null,
                'status'          => $company['status'] ?? 'active',
                'created_at'      => $company['created_at'] ?? date('Y-m-d H:i:s'),
                'updated_at'      => $company['updated_at'] ?? date('Y-m-d H:i:s'),
                'version'         => $company['version'] ?? 1,
            ]);
        }
    }

    private function migrateTenantTablesToClientId(): void
    {
        foreach ($this->tenantTables as $table) {
            if (!$this->db->tableExists($table) || !$this->db->fieldExists('company_id', $table)) {
                continue;
            }

            if (!$this->db->fieldExists('client_id', $table)) {
                $this->forge->addColumn($table, [
                    'client_id' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'unsigned'   => true,
                        'null'       => true,
                        'after'      => 'id',
                    ],
                ]);
            }

            $this->db->query("UPDATE `{$table}` SET client_id = company_id WHERE company_id IS NOT NULL AND company_id > 0");

            $this->dropForeignKeyOnColumn($table, 'company_id');
            $this->dropIndexesOnColumn($table, 'company_id');

            if ($this->db->fieldExists('company_id', $table)) {
                try {
                    $this->db->query("ALTER TABLE `{$table}` DROP COLUMN `company_id`");
                } catch (\Throwable $e) {
                    if ($this->db->fieldExists('company_id', $table)) {
                        throw $e;
                    }
                }
            }

            if ($this->db->fieldExists('client_id', $table)) {
                $this->db->query("ALTER TABLE `{$table}` MODIFY client_id INT UNSIGNED NOT NULL");
                $this->dropForeignKeyOnColumn($table, 'client_id');
                if (!$this->foreignKeyExists($table, 'fk_' . $table . '_client')) {
                    $this->db->query('ALTER TABLE `' . $table . '` ADD CONSTRAINT `fk_' . $table . '_client` FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
                }
            }
        }
    }

    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $dbName = $this->db->getDatabase();
        $row = $this->db->query(
            "SELECT 1 FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = 'FOREIGN KEY' LIMIT 1",
            [$dbName, $table, $constraintName]
        )->getRowArray();

        return $row !== null;
    }

    private function dropIndexesOnColumn(string $table, string $column): void
    {
        if (!$this->db->fieldExists($column, $table)) {
            return;
        }

        $dbName = $this->db->getDatabase();
        $rows = $this->db->query(
            "SELECT DISTINCT INDEX_NAME FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND INDEX_NAME != 'PRIMARY'",
            [$dbName, $table, $column]
        )->getResultArray();

        foreach ($rows as $row) {
            try {
                $this->db->query('ALTER TABLE `' . $table . '` DROP INDEX `' . $row['INDEX_NAME'] . '`');
            } catch (\Throwable $e) {
                // Index may already be removed with its foreign key.
            }
        }
    }

    /** @param list<int> $tenantCompanyIds */
    private function migrateUsersToClientId(array $tenantCompanyIds): void
    {
        if (!$this->db->tableExists('users') || !$this->db->fieldExists('company_id', 'users')) {
            return;
        }

        if (!$this->db->fieldExists('client_id', 'users')) {
            $this->forge->addColumn('users', [
                'client_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'company_id',
                ],
            ]);
        }

        if ($tenantCompanyIds !== []) {
            $idList = implode(',', array_map('intval', $tenantCompanyIds));
            $this->db->query("UPDATE users SET client_id = company_id WHERE company_id IN ({$idList})");
            $this->db->query("UPDATE users SET company_id = NULL WHERE company_id IN ({$idList})");
        }

        $this->dropForeignKeyOnColumn('users', 'company_id');
        if ($this->db->fieldExists('client_id', 'users') && !$this->foreignKeyExists('users', 'fk_users_client')) {
            $this->db->query('ALTER TABLE users ADD CONSTRAINT fk_users_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL ON UPDATE CASCADE');
        }
    }

    /** @param list<int> $tenantCompanyIds */
    private function migrateInvitationsToClientId(array $tenantCompanyIds): void
    {
        if (!$this->db->tableExists('invitations') || !$this->db->fieldExists('company_id', 'invitations')) {
            return;
        }

        if (!$this->db->fieldExists('client_id', 'invitations')) {
            $this->forge->addColumn('invitations', [
                'client_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'company_id',
                ],
            ]);
        }

        if ($tenantCompanyIds !== []) {
            $idList = implode(',', array_map('intval', $tenantCompanyIds));
            $this->db->query("UPDATE invitations SET client_id = company_id WHERE company_id IN ({$idList})");
            $this->db->query("UPDATE invitations SET company_id = NULL WHERE company_id IN ({$idList})");
        }
    }

    private function dropForeignKeyOnColumn(string $table, string $column): void
    {
        $dbName = $this->db->getDatabase();
        $rows = $this->db->query(
            "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL",
            [$dbName, $table, $column]
        )->getResultArray();

        foreach ($rows as $row) {
            $this->db->query('ALTER TABLE `' . $table . '` DROP FOREIGN KEY `' . $row['CONSTRAINT_NAME'] . '`');
        }
    }
}
