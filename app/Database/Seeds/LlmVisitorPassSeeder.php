<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Bulk visitor pass list data for LLM / UI testing (target: ~2000 approved passes).
 *
 * Run: php spark db:seed LlmVisitorPassSeeder
 */
class LlmVisitorPassSeeder extends Seeder
{
    use LlmSeedSupport;

    public function run(): void
    {
        set_time_limit(0);

        $db = $this->db;

        foreach (['invitations', 'invitation_schedules', 'invitation_visitors'] as $table) {
            if (! $db->tableExists($table)) {
                echo "Missing table {$table}. Run migrations first.\n";

                return;
            }
        }

        $source = 'LLM Demo Bulk';
        $plans  = [
            ['status' => 'Approved',  'target' => $this->llmVisitorApprovedTarget,  'with_visitor' => true],
            ['status' => 'Pending',   'target' => $this->llmVisitorPendingTarget,   'with_visitor' => false],
            ['status' => 'Submitted', 'target' => $this->llmVisitorSubmittedTarget, 'with_visitor' => true],
            ['status' => 'Rejected',  'target' => $this->llmVisitorRejectedTarget,  'with_visitor' => false],
        ];

        $hosts          = $this->loadHosts($db);
        $locations      = $this->llmVisitLocations();
        $companies      = $this->llmVisitorCompanies();
        $reasons        = $this->llmVisitReasons();
        $visitorTypes   = $this->loadVisitorTypes($db);
        $hasVersion     = $this->llmTableHasColumn($db, 'invitations', 'version');
        $hasCompanyId   = $this->llmTableHasColumn($db, 'invitations', 'company_id');
        $companyId      = $hasCompanyId ? $this->resolveCompanyId($db) : null;
        $hasIvVersion   = $this->llmTableHasColumn($db, 'invitation_visitors', 'version');
        $approvedStates = ['expected', 'expected', 'onsite', 'onsite', 'overstay', 'checkedout', 'checkedout', 'checkedout'];

        $totalInserted = 0;
        $numOffset     = $db->table('invitations')->where('registration_source', $source)->countAllResults();

        echo "Visitor pass list target: {$this->llmVisitorApprovedTarget} approved bulk rows\n";

        foreach ($plans as $plan) {
            $existing = $db->table('invitations')
                ->where('registration_source', $source)
                ->where('status', $plan['status'])
                ->countAllResults();

            $toCreate = max(0, $plan['target'] - $existing);

            if ($toCreate === 0) {
                echo "  {$plan['status']}: {$existing}/{$plan['target']} — OK\n";

                continue;
            }

            echo "  {$plan['status']}: seeding {$toCreate} (have {$existing}, want {$plan['target']})...\n";
            $inserted = 0;

            for ($i = 0; $i < $toCreate; $i++) {
                $num     = $numOffset + $totalInserted + $i + 1;
                $syncUid = 'LLM-BULK-VIS-' . str_pad((string) $num, 5, '0', STR_PAD_LEFT);
                $status  = $plan['status'];

                $first    = $this->llmFirstNames()[($num * 2) % count($this->llmFirstNames())];
                $last     = $this->llmLastNames()[($num * 5) % count($this->llmLastNames())];
                $fullName = $first . ' ' . $last;
                $host     = $hosts[$num % count($hosts)];
                $ic       = 'LLM9' . str_pad((string) (100000 + $num), 7, '0', STR_PAD_LEFT);
                $contact  = '+6017' . str_pad((string) (1000000 + $num), 7, '0', STR_PAD_LEFT);
                $now      = date('Y-m-d H:i:s');

                [$dateFrom, $dateTo, $visitState] = $this->buildScheduleWindow($status, $num, $approvedStates);

                $invitation = [
                    'sync_uid'             => $syncUid,
                    'full_name'            => $fullName,
                    'ic_passport'          => $ic,
                    'contact'              => $contact,
                    'visitor_email'        => strtolower(str_replace(' ', '.', $fullName)) . '.' . $num . '@llm-visitor.test',
                    'company'              => $companies[$num % count($companies)],
                    'location'             => $locations[$num % count($locations)],
                    'invited_by'           => $host['name'],
                    'staff_id'             => $host['staff_no'],
                    'company_visited'      => 'SafeG Demo Site',
                    'host_contact'         => $host['contact'],
                    'reason'               => $reasons[$num % count($reasons)],
                    'status'               => $status,
                    'registration_source'  => $source,
                    'vehicle_registration' => 'LLM' . str_pad((string) $num, 5, '0', STR_PAD_LEFT),
                    'created_at'           => date('Y-m-d H:i:s', strtotime('-' . (1 + ($num % 365)) . ' days')),
                    'updated_at'           => $now,
                ];

                if ($hasVersion) {
                    $invitation['version'] = 1;
                }
                if ($hasCompanyId && $companyId) {
                    $invitation['company_id'] = $companyId;
                }
                if ($visitorTypes !== []) {
                    $invitation['visitor_type_id'] = $visitorTypes[$num % count($visitorTypes)];
                }

                $db->table('invitations')->insert($invitation);
                $invitationId = (int) $db->insertID();

                $db->table('invitation_schedules')->insert([
                    'sync_uid'      => $syncUid . '-SCH',
                    'invitation_id' => $invitationId,
                    'date_from'     => $dateFrom,
                    'date_to'       => $dateTo,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);

                if ($plan['with_visitor']) {
                    [$checkIn, $checkOut] = $this->buildVisitorCheckTimes($status, $visitState, $dateFrom, $dateTo);

                    $visitorRow = [
                        'sync_uid'             => $syncUid . '-VIS',
                        'invitation_id'        => $invitationId,
                        'check_in_time'        => $checkIn,
                        'check_out_time'       => $checkOut,
                        'full_name'            => $fullName,
                        'ic_passport'          => $ic,
                        'contact'              => $contact,
                        'company'              => $invitation['company'],
                        'vehicle_registration' => $invitation['vehicle_registration'],
                        'created_at'           => $now,
                        'updated_at'           => $now,
                    ];

                    if ($hasIvVersion) {
                        $visitorRow['version'] = 1;
                    }

                    $db->table('invitation_visitors')->insert($visitorRow);
                }

                $inserted++;

                if ($inserted % 200 === 0) {
                    echo "    ... {$inserted}/{$toCreate} {$status}\n";
                }
            }

            $totalInserted += $inserted;
            echo "    inserted {$inserted} {$status}\n";
        }

        $approvedBulk = $db->table('invitations')
            ->where('registration_source', $source)
            ->where('status', 'Approved')
            ->countAllResults();

        echo "Bulk approved passes: {$approvedBulk}. ";
        echo 'Visitor pass list rows (all approved): ' . $this->countVisitorListRows($db) . "\n";
    }

    private function loadHosts($db): array
    {
        $rows = $db->table('staff')
            ->select('full_name, staff_no, contact_number')
            ->groupStart()
                ->like('remark', $this->llmMarker)
                ->orLike('staff_no', 'LLM-S', 'after')
            ->groupEnd()
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $hosts = [];
        foreach ($rows as $row) {
            $hosts[] = [
                'name'     => $row['full_name'],
                'staff_no' => $row['staff_no'],
                'contact'  => $row['contact_number'] ?? '+60129990000',
            ];
        }

        if ($hosts === []) {
            $hosts = [
                ['name' => 'Ahmad Demo Host', 'staff_no' => 'LLM-S001', 'contact' => '+60120001001'],
                ['name' => 'Nurul Demo Host', 'staff_no' => 'LLM-S002', 'contact' => '+60120001002'],
                ['name' => 'Ravi Demo Host', 'staff_no' => 'LLM-S003', 'contact' => '+60120001003'],
            ];
        }

        return $hosts;
    }

    private function loadVisitorTypes($db): array
    {
        if (! $db->tableExists('visitor_types')) {
            return [];
        }

        return array_column($db->table('visitor_types')->select('id')->get()->getResultArray(), 'id');
    }

    private function resolveCompanyId($db): ?int
    {
        $row = $db->table('companies')->select('id')->orderBy('id', 'ASC')->get()->getRowArray();

        return $row ? (int) $row['id'] : null;
    }

    /**
     * @return array{0: string, 1: string, 2: ?string}
     */
    private function buildScheduleWindow(string $status, int $num, array $approvedStates): array
    {
        $visitState = $status === 'Approved' ? $approvedStates[$num % count($approvedStates)] : null;
        $slot       = $num % 6;

        switch ($visitState) {
            case 'expected':
                $dateFrom = date('Y-m-d H:i:s', strtotime('+' . (1 + $slot) . ' hours'));
                $dateTo   = date('Y-m-d H:i:s', strtotime($dateFrom . ' +' . (2 + ($num % 4)) . ' hours'));
                break;
            case 'onsite':
                $dateFrom = date('Y-m-d H:i:s', strtotime('-' . (1 + ($num % 3)) . ' hours'));
                $dateTo   = date('Y-m-d H:i:s', strtotime('+' . (1 + ($num % 4)) . ' hours'));
                break;
            case 'overstay':
                $dateFrom = date('Y-m-d H:i:s', strtotime('-' . (4 + ($num % 4)) . ' hours'));
                $dateTo   = date('Y-m-d H:i:s', strtotime('-' . (1 + ($num % 2)) . ' hours'));
                break;
            case 'checkedout':
                $dateFrom = date('Y-m-d H:i:s', strtotime('-' . (5 + ($num % 4)) . ' hours'));
                $dateTo   = date('Y-m-d H:i:s', strtotime('-' . (2 + ($num % 2)) . ' hours'));
                break;
            default:
                if ($status === 'Pending' || $status === 'Submitted') {
                    $dateFrom = date('Y-m-d H:i:s', strtotime('+' . (1 + ($num % 5)) . ' days'));
                    $dateTo   = date('Y-m-d H:i:s', strtotime($dateFrom . ' +' . (2 + ($num % 5)) . ' hours'));
                } elseif ($status === 'Rejected') {
                    $dateFrom = date('Y-m-d H:i:s', strtotime('-' . (1 + ($num % 10)) . ' days'));
                    $dateTo   = date('Y-m-d H:i:s', strtotime($dateFrom . ' +3 hours'));
                } else {
                    $dateFrom = date('Y-m-d H:i:s', strtotime('+' . ($num % 2) . ' days'));
                    $dateTo   = date('Y-m-d H:i:s', strtotime($dateFrom . ' +4 hours'));
                }
                break;
        }

        return [$dateFrom, $dateTo, $visitState];
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    private function buildVisitorCheckTimes(
        string $status,
        ?string $visitState,
        string $dateFrom,
        string $dateTo
    ): array {
        if ($status !== 'Approved') {
            return [null, null];
        }

        return match ($visitState) {
            'onsite'     => [date('Y-m-d H:i:s', strtotime($dateFrom . ' +15 minutes')), null],
            'overstay'   => [date('Y-m-d H:i:s', strtotime($dateFrom . ' +20 minutes')), null],
            'checkedout' => [
                date('Y-m-d H:i:s', strtotime($dateFrom . ' +10 minutes')),
                date('Y-m-d H:i:s', strtotime($dateTo . ' -10 minutes')),
            ],
            default      => [null, null],
        };
    }

    private function countVisitorListRows($db): int
    {
        return $db->table('invitation_visitors iv')
            ->join('invitations i', 'i.id = iv.invitation_id')
            ->where('i.status', 'Approved')
            ->countAllResults();
    }
}
