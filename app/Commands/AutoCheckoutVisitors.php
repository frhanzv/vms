<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AutoCheckoutVisitors extends BaseCommand
{
    protected $group = 'Visitors';
    protected $name = 'visitors:auto-checkout';
    protected $description = 'Close open visitor visits from previous calendar days at 23:59:59.';
    protected $usage = 'visitors:auto-checkout [--dry-run]';
    protected $options = [
        '--dry-run' => 'Show how many visits would be closed without updating them.',
    ];

    public function run(array $params): void
    {
        $db = db_connect();
        $now = date('Y-m-d H:i:s');
        $todayStart = date('Y-m-d 00:00:00');
        $dryRun = CLI::getOption('dry-run') !== null;

        $openVisits = $db->table('invitation_visitors iv')
            ->select('iv.id, iv.invitation_id, iv.check_in_time')
            ->join('invitations i', 'i.id = iv.invitation_id', 'inner')
            ->join(
                'client_features cf',
                "cf.client_id = i.client_id AND cf.feature_key = 'nightly_auto_checkout' AND cf.is_enabled = 1",
                'inner'
            )
            ->where('iv.check_in_time IS NOT NULL', null, false)
            ->where('iv.check_out_time IS NULL', null, false)
            ->where('iv.check_in_time <', $todayStart)
            ->orderBy('iv.id', 'ASC')
            ->get()
            ->getResultArray();

        if ($openVisits === []) {
            CLI::write('No previous-day open visits found for clients with Nightly Auto Checkout enabled.', 'green');
            return;
        }

        if ($dryRun) {
            CLI::write(sprintf('%d open visit(s) would be checked out.', count($openVisits)), 'yellow');
            return;
        }

        $closed = 0;
        $invitationIds = [];
        $db->transStart();

        foreach ($openVisits as $visit) {
            $checkInTimestamp = strtotime((string) $visit['check_in_time']);
            if ($checkInTimestamp === false) {
                continue;
            }

            $checkoutAt = date('Y-m-d 23:59:59', $checkInTimestamp);
            $db->table('invitation_visitors')
                ->where('id', (int) $visit['id'])
                ->where('check_out_time IS NULL', null, false)
                ->update([
                    'check_out_time' => $checkoutAt,
                    'updated_at' => $now,
                ]);

            if ($db->affectedRows() > 0) {
                $closed++;
                $invitationIds[(int) $visit['invitation_id']] = $checkoutAt;
            }
        }

        foreach ($invitationIds as $invitationId => $checkoutAt) {
            $hasOpenVisit = $db->table('invitation_visitors')
                ->where('invitation_id', $invitationId)
                ->where('check_in_time IS NOT NULL', null, false)
                ->where('check_out_time IS NULL', null, false)
                ->countAllResults() > 0;

            if (! $hasOpenVisit) {
                $db->table('invitations')
                    ->where('id', $invitationId)
                    ->update([
                        'guard_entry_status' => 'Checked Out',
                        'guard_decided_at' => $checkoutAt,
                        'updated_at' => $now,
                    ]);
            }
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            CLI::error('Automatic checkout failed; the transaction was rolled back.');
            return;
        }

        log_message('info', 'Nightly visitor auto-checkout closed {count} visit(s).', ['count' => $closed]);
        CLI::write(sprintf('Checked out %d visit(s).', $closed), 'green');
    }
}
