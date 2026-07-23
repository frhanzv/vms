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

        if ($this->db->fieldExists('company_id', $table) && $this->db->fieldExists('client_id', $table)) {
            $this->db->query("UPDATE {$table} SET client_id = company_id WHERE client_id IS NULL AND company_id IS NOT NULL AND company_id > 0");
        }

        $this->dropForeignKeyOnColumn($table, 'company_id');
        $this->dropIndexesOnColumn($table, 'company_id');

        if ($this->db->fieldExists('company_id', $table)) {
            $this->forge->dropColumn($table, 'company_id');
        }

        if ($this->db->fieldExists('client_id', $table)) {
            $this->db->query("ALTER TABLE {$table} MODIFY client_id INT UNSIGNED NOT NULL");
            $this->dropForeignKeyOnColumn($table, 'client_id');
            if (!$this->indexExists($table, 'fk_' . $table . '_client')) {
                $this->db->query('ALTER TABLE `' . $table . '` ADD CONSTRAINT `fk_' . $table . '_client` FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
            }
            $uniqueName = $table . '_client_unique';
            if (!$this->indexExists($table, $uniqueName)) {
                $cols = implode('`,`', $uniqueCols);
                $this->db->query("ALTER TABLE `{$table}` ADD UNIQUE KEY `{$uniqueName}` (`{$cols}`)");
            }
        }
    }

    private function finishUsers(): void
    {
        if (!$this->db->tableExists('users') || !$this->db->fieldExists('client_id', 'users')) {
            return;
        }

        $this->db->query('UPDATE users SET client_id = company_id WHERE client_id IS NULL AND company_id IS NOT NULL AND company_id > 0');
        $this->db->query('UPDATE users SET company_id = NULL WHERE client_id IS NOT NULL');

        $this->dropForeignKeyOnColumn('users', 'company_id');
        $this->dropForeignKeyOnColumn('users', 'client_id');
        if (!$this->indexExists('users', 'fk_users_client')) {
            $this->db->query('ALTER TABLE users ADD CONSTRAINT fk_users_client FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL ON UPDATE CASCADE');
        }
    }

    private function finishInvitations(): void
    {
        if (!$this->db->tableExists('invitations') || !$this->db->fieldExists('client_id', 'invitations')) {
            return;
        }

        $this->db->query('UPDATE invitations SET client_id = company_id WHERE client_id IS NULL AND company_id IS NOT NULL AND company_id > 0');
        $this->db->query('UPDATE invitations SET company_id = NULL WHERE client_id IS NOT NULL');
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

    private function dropIndexesOnColumn(string $table, string $column): void
    {
        $dbName = $this->db->getDatabase();
        $rows = $this->db->query(
            "SELECT INDEX_NAME FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND INDEX_NAME != 'PRIMARY'",
            [$dbName, $table, $column]
        )->getResultArray();

        foreach ($rows as $row) {
            $this->db->query('ALTER TABLE `' . $table . '` DROP INDEX `' . $row['INDEX_NAME'] . '`');
        }
    }
}
