<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSyncTriggers extends Migration
{
    protected array $excluded = [
        'migrations',
        'sync_log',
        'sync_deletions',
    ];

    public function up(): void
    {
        foreach ($this->syncableTables() as $table) {
            if (!$this->db->tableExists($table) || !$this->db->fieldExists('sync_uid', $table)) {
                continue;
            }

            $this->createBeforeInsertTrigger($table);
            $this->createAfterDeleteTrigger($table);
        }
    }

    public function down(): void
    {
        foreach ($this->syncableTables() as $table) {
            $this->db->query('DROP TRIGGER IF EXISTS `' . $this->insertTriggerName($table) . '`');
            $this->db->query('DROP TRIGGER IF EXISTS `' . $this->deleteTriggerName($table) . '`');
        }
    }

    protected function syncableTables(): array
    {
        $tables = array_diff($this->db->listTables(), $this->excluded);
        sort($tables);

        return array_values($tables);
    }

    protected function createBeforeInsertTrigger(string $table): void
    {
        $name = $this->insertTriggerName($table);
        $this->db->query('DROP TRIGGER IF EXISTS `' . $name . '`');

        $sql = <<<SQL
CREATE TRIGGER `{$name}` BEFORE INSERT ON `{$table}`
FOR EACH ROW
BEGIN
    IF NEW.sync_uid IS NULL OR NEW.sync_uid = '' THEN
        SET NEW.sync_uid = UUID();
    END IF;
END
SQL;

        $this->db->query($sql);
    }

    protected function createAfterDeleteTrigger(string $table): void
    {
        $name = $this->deleteTriggerName($table);
        $this->db->query('DROP TRIGGER IF EXISTS `' . $name . '`');

        $sql = <<<SQL
CREATE TRIGGER `{$name}` AFTER DELETE ON `{$table}`
FOR EACH ROW
BEGIN
    IF COALESCE(@vms_sync_skip_delete_log, 0) = 0
       AND OLD.sync_uid IS NOT NULL
       AND OLD.sync_uid != '' THEN
        INSERT INTO sync_deletions (sync_uid, table_name, deleted_at)
        VALUES (OLD.sync_uid, '{$table}', NOW())
        ON DUPLICATE KEY UPDATE deleted_at = VALUES(deleted_at);
    END IF;
END
SQL;

        $this->db->query($sql);
    }

    protected function insertTriggerName(string $table): string
    {
        return 'vms_sync_bi_' . substr(preg_replace('/[^a-z0-9_]/', '_', $table), 0, 40);
    }

    protected function deleteTriggerName(string $table): string
    {
        return 'vms_sync_ad_' . substr(preg_replace('/[^a-z0-9_]/', '_', $table), 0, 40);
    }
}
