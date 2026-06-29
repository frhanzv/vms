<?php

namespace App\Commands;

use App\Services\SyncService;
use App\Services\SyncTrigger;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SyncDoctor extends BaseCommand
{
    protected $group       = 'sync';
    protected $name        = 'sync:doctor';
    protected $description = 'Diagnose peer DB connectivity and auto-sync settings (cloud ↔ Jetson).';
    protected $usage       = 'sync:doctor';

    public function run(array $params): void
    {
        CLI::write('VMS Sync Doctor', 'yellow');
        CLI::write(str_repeat('-', 50));

        $local = db_connect('default');
        CLI::write('Local database: ' . ($local->getDatabase() ?: '(unknown)'), 'white');

        $peerConfigured = SyncService::isPeerConfigured();
        CLI::write('Peer DB configured (database.cloud.*): ' . ($peerConfigured ? 'yes' : 'no'), $peerConfigured ? 'green' : 'red');

        if ($peerConfigured) {
            $cfg = config('Database')->cloud;
            CLI::write('  Peer host: ' . ($cfg['hostname'] ?? ''), 'white');
            CLI::write('  Peer database: ' . ($cfg['database'] ?? ''), 'white');

            $test = SyncService::testPeerConnection();
            if ($test['ok']) {
                CLI::write('  Peer connection: OK', 'green');
            } else {
                CLI::write('  Peer connection: FAILED — ' . ($test['message'] ?? ''), 'red');
            }
        }

        $auto = env('SYNC.auto');
        $autoOn = SyncTrigger::isEnabled();
        CLI::write('Auto-sync enabled (SYNC.auto): ' . ($autoOn ? 'yes' : 'no') . ($auto === null ? ' (default)' : " ({$auto})"), $autoOn ? 'green' : 'yellow');

        $notifyUrl = env('SYNC.notifyUrl');
        $webhookToken = env('SYNC.webhookToken');
        if ($notifyUrl) {
            CLI::write('SYNC.notifyUrl: ' . $notifyUrl, 'cyan');
            CLI::write('SYNC.webhookToken: ' . (empty($webhookToken) ? 'MISSING' : 'set'), empty($webhookToken) ? 'red' : 'green');
        } else {
            CLI::write('SYNC.notifyUrl: not set (direct peer MySQL only)', 'white');
        }

        if ($local->tableExists('sync_log')) {
            $row = $local->table('sync_log')
                ->orderBy('synced_at', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();
            if ($row) {
                CLI::write('Last sync_log: ' . $row['synced_at'] . ' — ' . ($row['table_name'] ?? '') . ' ' . ($row['status'] ?? ''), 'white');
            }
        }

        CLI::write(str_repeat('-', 50));
        CLI::write('How cloud data reaches Jetson:', 'yellow');
        CLI::write('  1) Run sync ON JETSON (pulls from peer DB into local vms)', 'white');
        CLI::write('  2) Run sync ON CLOUD with peer = Jetson (pushes into Jetson vms)', 'white');
        CLI::write('  3) Cloud saves + SYNC.notifyUrl → Jetson POST /api/sync/trigger → Jetson pull', 'white');
        CLI::write('If peer connection fails from cloud, use option 3 or open Jetson MySQL port 3306.', 'cyan');
    }
}
