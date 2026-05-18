<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds default visitor types.
 *
 * The `path` column references a pathway name from the pathways table.
 * It is left null here because pathways are site-specific.
 * Operators can assign pathways via Config → Visitor Types after setup.
 *
 * Safe to re-run: uses ON DUPLICATE KEY UPDATE on the unique `name` column.
 */
class VisitorTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now  = date('Y-m-d H:i:s');
        $rows = [
            ['name' => 'General Visitor', 'path' => null],
            ['name' => 'Contractor',      'path' => null],
            ['name' => 'Vendor',          'path' => null],
            ['name' => 'VIP',             'path' => null],
            ['name' => 'Delivery',        'path' => null],
        ];

        foreach ($rows as $row) {
            $this->db->query(
                'INSERT INTO `visitor_types` (`name`, `path`, `created_at`, `updated_at`)
                 VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE `updated_at` = VALUES(`updated_at`)',
                [$row['name'], $row['path'], $now, $now]
            );
        }

        echo '  VisitorTypeSeeder: ' . count($rows) . " default visitor type(s) upserted.\n";
    }
}
