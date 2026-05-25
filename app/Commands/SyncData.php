<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\SyncService;

class SyncData extends BaseCommand
{
    protected $group       = 'sync';
    protected $name        = 'sync:run';
    protected $description = 'Full bidirectional sync between Jetson (local vms) and Cloud (vms_cloud) for all tables — inserts, updates, and deletes.';

    protected $usage     = 'sync:run [options]';
    protected $arguments = [];
    protected $options   = [
        '--quiet' => 'Suppress output (useful for scheduled runs)',
    ];

    public function run(array $params): void
    {
        $quiet = CLI::getOption('quiet');

        if (!$quiet) {
            CLI::write('VMS Data Sync', 'yellow');
            CLI::write(str_repeat('-', 40));
        }

        try {
            $service = new SyncService();
            $log     = $service->sync();

            if (!$quiet) {
                foreach ($log as $line) {
                    $color = str_contains($line, 'ERROR') ? 'red' : 'white';
                    CLI::write($line, $color);
                }
                CLI::write(str_repeat('-', 40));
                CLI::write('Done.', 'green');
            }
        } catch (\Throwable $e) {
            CLI::error('Sync failed: ' . $e->getMessage());
            log_message('error', '[Sync] Fatal: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            exit(1);
        }
    }
}
