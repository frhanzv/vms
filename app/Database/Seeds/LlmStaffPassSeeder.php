<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Bulk staff pass list data for LLM / UI testing (target: 2000 rows).
 *
 * Run: php spark db:seed LlmStaffPassSeeder
 */
class LlmStaffPassSeeder extends Seeder
{
    use LlmSeedSupport;

    public function run(): void
    {
        set_time_limit(0);

        $db  = $this->db;
        $now = date('Y-m-d H:i:s');

        if (! $db->tableExists('staff')) {
            echo "Missing table staff. Run migrations first.\n";

            return;
        }

        $existing = $db->table('staff')->like('remark', $this->llmMarker)->countAllResults();
        $toCreate = max(0, $this->llmStaffTarget - $existing);

        if ($toCreate === 0) {
            echo "Staff LLM demo data already at {$existing} records (target {$this->llmStaffTarget}).\n";

            return;
        }

        echo "Seeding {$toCreate} staff record(s) toward target {$this->llmStaffTarget}...\n";

        $startIndex = $existing + 1;
        $hasCards   = $db->tableExists('staff_cards');
        $inserted   = 0;
        $batch      = [];

        for ($i = 0; $i < $toCreate; $i++) {
            $num = $startIndex + $i;
            $batch[] = $this->buildStaffRow($num);

            if (count($batch) >= $this->llmBatchSize) {
                $inserted += $this->flushStaffBatch($db, $batch, $hasCards, $now);
                $batch = [];
                echo "  ... {$inserted}/{$toCreate} staff inserted\n";
            }
        }

        if ($batch !== []) {
            $inserted += $this->flushStaffBatch($db, $batch, $hasCards, $now);
        }

        echo "Inserted {$inserted} staff pass record(s). Total LLM staff: " .
            $db->table('staff')->like('remark', $this->llmMarker)->countAllResults() . "\n";
    }

    private function buildStaffRow(int $num): array
    {
        $staffNo = 'LLM-S' . str_pad((string) $num, 4, '0', STR_PAD_LEFT);
        $syncKey = 'LLM-STAFF-' . str_pad((string) $num, 5, '0', STR_PAD_LEFT);

        $statuses     = ['Active', 'Active', 'Active', 'Active', 'Suspended', 'Inactive'];
        $cardStatuses = ['Active', 'Active', 'Active', 'Inactive', 'Expired'];

        $first  = $this->llmFirstNames()[$num % count($this->llmFirstNames())];
        $last   = $this->llmLastNames()[($num * 3) % count($this->llmLastNames())];
        $full   = $first . ' ' . $last;
        $status = $statuses[$num % count($statuses)];
        $cardStatus = $status === 'Active' ? $cardStatuses[$num % count($cardStatuses)] : 'Inactive';
        $createdDaysAgo = 5 + ($num % 395);

        return [
            'app_no'              => 'APP-' . $staffNo,
            'full_name'           => $full,
            'ic_passport'         => 'LLM8' . str_pad((string) (100000 + $num), 7, '0', STR_PAD_LEFT),
            'staff_no'            => $staffNo,
            'status'              => $status,
            'suspension_period'   => $status === 'Suspended' ? date('Y-m-d', strtotime('+30 days')) : null,
            'next_action'         => $status === 'Suspended' ? 'Review suspension' : ($status === 'Inactive' ? 'Renew pass' : '-'),
            'card_status'         => $cardStatus,
            'card_expiry'         => date('Y-m-d', strtotime('+' . (30 + ($num % 700)) . ' days')),
            'remark'              => $this->llmMarker . ' ' . $syncKey,
            'location_access'     => implode(',', array_slice($this->llmVisitLocations(), 0, 2 + ($num % 4))),
            'date_of_application' => date('d/m/Y', strtotime('-' . $createdDaysAgo . ' days')),
            'type_of_application' => $num % 3 === 0 ? 'Renewal' : 'New',
            'designation'         => $this->llmDesignations()[$num % count($this->llmDesignations())],
            'resident'            => $num % 5 === 0 ? 'Non-Resident' : 'Resident',
            'sub_type'            => $num % 2 === 0 ? 'Permanent' : 'Contract',
            'date_of_birth'       => date('Y-m-d', strtotime('-' . (25 + ($num % 30)) . ' years')),
            'sex'                 => $num % 2 === 0 ? 'Male' : 'Female',
            'name_on_staff_pass'  => strtoupper($last . ' ' . substr($first, 0, 1)),
            'contact_number'      => '+6012' . str_pad((string) (1000000 + $num), 7, '0', STR_PAD_LEFT),
            'email'               => strtolower(str_replace(' ', '.', $full)) . '.' . $num . '@llm-staff.test',
            'department'          => $this->llmDepartments()[$num % count($this->llmDepartments())],
            'address_1'           => (100 + $num) . ' Jalan Demo',
            'city'                => 'Shah Alam',
            'state'               => 'Selangor',
            'postal_code'         => '40' . str_pad((string) ($num % 100), 3, '0', STR_PAD_LEFT),
            'country'             => 'Malaysia',
            'csp_number'          => 'CSP-' . str_pad((string) $num, 5, '0', STR_PAD_LEFT),
            'csp_expiry_date'     => date('Y-m-d', strtotime('+1 year')),
            'evetting_result'     => 'Cleared',
            'created_at'          => date('Y-m-d H:i:s', strtotime('-' . $createdDaysAgo . ' days')),
        ];
    }

    private function flushStaffBatch($db, array $batch, bool $hasCards, string $now): int
    {
        $staffNos = array_column($batch, 'staff_no');
        $existing = $db->table('staff')->whereIn('staff_no', $staffNos)->select('staff_no')->get()->getResultArray();
        $existingNos = array_column($existing, 'staff_no');

        $toInsert = array_values(array_filter($batch, static fn (array $row): bool => ! in_array($row['staff_no'], $existingNos, true)));

        if ($toInsert === []) {
            return 0;
        }

        $db->table('staff')->insertBatch($toInsert);

        if (! $hasCards) {
            return count($toInsert);
        }

        $insertedRows = $db->table('staff')
            ->select('id, staff_no, card_expiry, status, card_status')
            ->whereIn('staff_no', array_column($toInsert, 'staff_no'))
            ->get()
            ->getResultArray();

        $cardBatch = [];
        foreach ($insertedRows as $row) {
            $num = (int) substr((string) $row['staff_no'], -4);
            if ($row['status'] === 'Active' && $row['card_status'] === 'Active' && $num % 7 !== 0) {
                $cardBatch[] = [
                    'staff_id'    => (int) $row['id'],
                    'status'      => 'active',
                    'expiry_date' => $row['card_expiry'],
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }

        if ($cardBatch !== []) {
            $db->table('staff_cards')->insertBatch($cardBatch);
        }

        return count($toInsert);
    }
}
