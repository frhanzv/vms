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

        '--force' => 'Run even if another sync is already in progress (not recommended)',

    ];



    public function run(array $params): void

    {

        $quiet = CLI::getOption('quiet');

        $force = CLI::getOption('force');



        $lockFile = WRITEPATH . 'sync/sync.lock';

        $lockDir  = dirname($lockFile);



        if (! is_dir($lockDir)) {

            mkdir($lockDir, 0775, true);

        }



        $lock = fopen($lockFile, 'c+');



        if ($lock === false) {

            CLI::error('Sync failed: cannot open lock file.');

            exit(1);

        }



        if (! $force && ! flock($lock, LOCK_EX | LOCK_NB)) {

            if (! $quiet) {

                CLI::write('Sync skipped — another sync is already running.', 'yellow');

            }

            fclose($lock);

            return;

        }



        if (! $quiet) {

            CLI::write('VMS Data Sync', 'yellow');

            CLI::write(str_repeat('-', 40));

        }



        try {

            $service = new SyncService();

            $log     = $service->sync();



            if (! $quiet) {

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

            flock($lock, LOCK_UN);

            fclose($lock);

            exit(1);

        }



        flock($lock, LOCK_UN);

        fclose($lock);

    }

}

