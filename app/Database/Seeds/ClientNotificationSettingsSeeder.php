<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\ClientNotificationSettingModel;

/**
 * Seeds client_notification_settings for every company in the database.
 *
 * Produces one row per company × channel × notification_type combination
 * (3 channels × 8 types = 24 rows per company).
 *
 * Defaults (matching the model's getForCompany() behaviour):
 *   email     → enabled  (1)
 *   whatsapp  → disabled (0)
 *   telegram  → disabled (0)
 *
 * Safe to re-run: uses ON DUPLICATE KEY UPDATE so existing rows are
 * refreshed to their defaults without throwing a unique-key error.
 */
class ClientNotificationSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $companies = $this->db->table('companies')->select('id')->get()->getResultArray();

        if (empty($companies)) {
            echo "  No companies found — skipping ClientNotificationSettingsSeeder.\n";
            return;
        }

        $channels = ClientNotificationSettingModel::allChannels(); // ['email', 'whatsapp', 'telegram']
        $types    = array_keys(ClientNotificationSettingModel::allTypes());
        $now      = date('Y-m-d H:i:s');
        $batch    = [];

        foreach ($companies as $company) {
            foreach ($channels as $channel) {
                $defaultEnabled = $channel === 'email' ? 1 : 0;
                foreach ($types as $type) {
                    $batch[] = [
                        'company_id'        => (int) $company['id'],
                        'channel'           => $channel,
                        'notification_type' => $type,
                        'enabled'           => $defaultEnabled,
                        'created_at'        => $now,
                        'updated_at'        => $now,
                    ];
                }
            }
        }

        $this->upsertBatch('client_notification_settings', $batch, ['enabled', 'updated_at']);

        $perCompany = count($channels) * count($types);
        $total      = count($companies) * $perCompany;
        echo "  ClientNotificationSettingsSeeder: {$total} row(s) upserted for "
            . count($companies) . " company/companies "
            . "({$perCompany} rows each: " . count($channels) . " channels × " . count($types) . " types).\n";
    }

    private function upsertBatch(string $table, array $rows, array $updateCols): void
    {
        if (empty($rows)) {
            return;
        }

        $cols        = array_keys($rows[0]);
        $colList     = implode(', ', array_map(fn($c) => "`{$c}`", $cols));
        $updateParts = implode(', ', array_map(fn($c) => "`{$c}` = VALUES(`{$c}`)", $updateCols));
        $placeholder = '(' . implode(', ', array_fill(0, count($cols), '?')) . ')';
        $valueSets   = implode(', ', array_fill(0, count($rows), $placeholder));

        $bindings = [];
        foreach ($rows as $row) {
            foreach ($cols as $col) {
                $bindings[] = $row[$col];
            }
        }

        $sql = "INSERT INTO `{$table}` ({$colList}) VALUES {$valueSets} "
             . "ON DUPLICATE KEY UPDATE {$updateParts}";

        $this->db->query($sql, $bindings);
    }
}
