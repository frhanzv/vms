<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\ClientFormFieldModel;

/**
 * Seeds client_form_fields for every company in the database.
 *
 * Covers all four form types:
 *   - invitation            (static field list)
 *   - staff_pass_request    (static field list)
 *   - visitor_pass_request  (static field list)
 *   - visitor_registration  (pulled live from email_template_form_fields where is_system = 1)
 *
 * Safe to re-run: uses ON DUPLICATE KEY UPDATE.
 *
 * Fields with default_enabled = false are seeded as is_enabled = 0:
 *   staff_pass_request  → csp_number, evetting, print_button
 *   visitor_pass_request → company_details
 */
class ClientFormFieldsSeeder extends Seeder
{
    public function run(): void
    {
        $companies = $this->db->table('companies')->select('id')->get()->getResultArray();

        if (empty($companies)) {
            echo "  No companies found — skipping ClientFormFieldsSeeder.\n";
            return;
        }

        // Static form types sourced directly from the model
        $staticForms = [
            'invitation'           => ClientFormFieldModel::invitationFields(),
            'staff_pass_request'   => ClientFormFieldModel::staffPassFields(),
            'visitor_pass_request' => ClientFormFieldModel::visitorPassRequestFields(),
        ];

        // visitor_registration fields come from email_template_form_fields (is_system = 1)
        $vrFields = $this->loadVisitorRegistrationFields();
        if (!empty($vrFields)) {
            $staticForms['visitor_registration'] = $vrFields;
        }

        $now   = date('Y-m-d H:i:s');
        $batch = [];
        $total = 0;

        foreach ($companies as $company) {
            foreach ($staticForms as $formType => $fields) {
                foreach ($fields as $field) {
                    $defaultEnabled = isset($field['default_enabled']) ? (int) $field['default_enabled'] : 1;
                    $batch[] = [
                        'company_id'  => (int) $company['id'],
                        'form_type'   => $formType,
                        'field_key'   => $field['field_key'],
                        'is_enabled'  => $defaultEnabled,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ];
                    $total++;
                }
            }
        }

        $this->upsertBatch('client_form_fields', $batch, ['is_enabled', 'updated_at']);

        echo "  ClientFormFieldsSeeder: {$total} row(s) upserted for "
            . count($companies) . " company/companies across "
            . count($staticForms) . " form type(s).\n";
    }

    private function loadVisitorRegistrationFields(): array
    {
        $rows = $this->db->table('email_template_form_fields')
            ->select('field_key, label')
            ->where('is_system', 1)
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResultArray();

        return array_map(fn($r) => [
            'field_key' => $r['field_key'],
            'label'     => $r['label'],
        ], $rows);
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
