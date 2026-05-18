<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\ClientFeatureModel;

/**
 * Seeds client_features for every company in the database.
 *
 * Safe to re-run: uses ON DUPLICATE KEY UPDATE so existing rows are
 * refreshed to their defaults rather than throwing a unique-key error.
 *
 * All features default to enabled (1). The model's "absence = enabled"
 * contract means enabled rows are technically redundant, but seeding them
 * explicitly makes the DB easier to inspect and manage via direct SQL.
 */
class ClientFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        $companies = $this->db->table('companies')->select('id')->get()->getResultArray();

        if (empty($companies)) {
            echo "  No companies found — skipping ClientFeaturesSeeder.\n";
            return;
        }

        $features = ClientFeatureModel::allFeatures();
        $now      = date('Y-m-d H:i:s');
        $batch    = [];

        foreach ($companies as $company) {
            foreach ($features as $key => $label) {
                $batch[] = [
                    'company_id'  => (int) $company['id'],
                    'feature_key' => $key,
                    'is_enabled'  => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }

        $this->upsertBatch('client_features', $batch, ['is_enabled', 'updated_at']);

        $total = count($companies) * count($features);
        echo "  ClientFeaturesSeeder: {$total} row(s) upserted for "
            . count($companies) . " company/companies ("
            . count($features) . " feature(s) each).\n";
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
