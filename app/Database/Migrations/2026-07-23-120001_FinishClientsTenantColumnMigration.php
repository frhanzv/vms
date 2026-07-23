<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FinishClientsTenantColumnMigration extends Migration
{
    /** @var list<string> */
    private array $tenantTables = [
        'client_features'              => ['client_id', 'feature_key'],
        'client_form_fields'           => ['client_id', 'form_type', 'field_key'],
        'client_notification_settings' => ['client_id', 'channel', 'notification_type'],
        'client_messaging_credentials' => ['client_id', 'channel'],
        'whatsapp_templates'           => ['client_id', 'notification_type'],
    ];

    public function up()
    {
        foreach ($this->tenantTables as $table => $uniqueCols) {
            $this->finishTable($table, $uniqueCols);
        }

        $this->finishUsers();
        $this->finishInvitations();
    }

    public function down()
    {
        // One-way data migration.
    }

    private function finishTable(string $table, array $uniqueCols): void
    {
        if (!$this->db->tableExists($table)) {
            return;
        }

        if ($this->columnExists($table, 'company_id') && $this->columnExists($table, 'client_id')) {
            $this->db->query("UPDATE `{$table}` SET client_id = company_id WHERE client_id IS NULL AND company_id IS NOT NULL AND company_id > 0");
        }

        $this->dropColumnIfExists($table, 'company_id');

        if (!$this->columnExists($table, 'client_id')) {
            return;
        }

        $this->db->query("ALTER TABLE `{$table}` MODIFY client_id INT UNSIGNED NOT NULL");
        $this->dropForeignKeyOnColumn($table, 'client_id');

        $fkName = 'fk_' . $table . '_client';
        if (!$this->foreignKeyExists($table, $fkName)) {
            $this->db->query('ALTER TABLE `' . $table . '` ADD CONSTRAINT `' . $fkName . '` FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        }

        $uniqueName = $table . '_client_unique';
        if (!$this->indexExists($table, $uniqueName)) {
            $cols = implode('`,`', $uniqueCols);
            $this->db->query("ALTER TABLE `{$table}` ADD UNIQUE KEY `{$uniqueName}` (`{$cols}`)");
        }
    }

    private function finishUsers(): void
    {
        if (!$this->db->tableExists('users') || !$this->columnExists('users', 'client_id')) {
            return;
        }

        if ($this->columnExists('users', 'company_id')) {
            $this->db->query('UPDATE users SET client_id = company_id WHERE client_id IS NULL AND company_id IS NOT NULL AND company_id > 0');
            $this->db->query('UPDATE users SET company_id = NULL WHERE client_id IS NOT NULL');
            $this->dropForeignKeyOnColumn('users', 'company_id');
        }

        $this->dropForeignKeyOnColumn('users', 'client_id');
        if (!$this->foreignKeyExists('users', 'fk_users_client')) {
            $this->db->query('ALTER TABLE users ADD CONSTRAINT fk_users_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL ON UPDATE CASCADE');
        }
    }

    private function finishInvitations(): void
    {
        if (!$this->db->tableExists('invitations') || !$this->columnExists('invitations', 'client_id')) {
            return;
        }

        if ($this->columnExists('invitations', 'company_id')) {
            $this->db->query('UPDATE invitations SET client_id = company_id WHERE client_id IS NULL AND company_id IS NOT NULL AND company_id > 0');
        }
    }

    private function columnExists(string $table, string $column): bool
    {
        if (!$this->db->tableExists($table)) {
            return false;
        }

        return $this->db->fieldExists($column, $table);
    }

    private function dropColumnIfExists(string $table, string $column): void
    {
        if (!$this->columnExists($table, $column)) {
            return;
        }

        $this->dropForeignKeyOnColumn($table, $column);
        $this->dropIndexesOnColumn($table, $column);

        if (!$this->columnExists($table, $column)) {
            return;
        }

        try {
            $this->db->query("ALTER TABLE `{$table}` DROP COLUMN `{$column}`");
        } catch (\Throwable $e) {
            if (!$this->columnExists($table, $column)) {
                return;
            }

            throw $e;
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $dbName = $this->db->getDatabase();
        $row = $this->db->query(
            "SELECT 1 FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ? LIMIT 1",
            [$dbName, $table, $indexName]
        )->getRowArray();

        return $row !== null;
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

    private function dropForeignKeyOnColumn(string $table, string $column): void
    {
        if (!$this->columnExists($table, $column)) {
            return;
        }

        $dbName = $this->db->getDatabase();
        $rows = $this->db->query(
            "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL",
            [$dbName, $table, $column]
        )->getResultArray();

        foreach ($rows as $row) {
            $name = $row['CONSTRAINT_NAME'];
            if (!$this->foreignKeyExists($table, $name)) {
                continue;
            }

            try {
                $this->db->query('ALTER TABLE `' . $table . '` DROP FOREIGN KEY `' . $name . '`');
            } catch (\Throwable $e) {
                if (!$this->foreignKeyExists($table, $name)) {
                    continue;
                }

                throw $e;
            }
        }
    }

    private function dropIndexesOnColumn(string $table, string $column): void
    {
        if (!$this->columnExists($table, $column)) {
            return;
        }

        $dbName = $this->db->getDatabase();
        $rows = $this->db->query(
            "SELECT DISTINCT INDEX_NAME FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND INDEX_NAME != 'PRIMARY'",
            [$dbName, $table, $column]
        )->getResultArray();

        foreach ($rows as $row) {
            $indexName = $row['INDEX_NAME'];
            if (!$this->indexExists($table, $indexName)) {
                continue;
            }

            try {
                $this->db->query('ALTER TABLE `' . $table . '` DROP INDEX `' . $indexName . '`');
            } catch (\Throwable $e) {
                if (!$this->indexExists($table, $indexName)) {
                    continue;
                }

                throw $e;
            }
        }
    }
}
