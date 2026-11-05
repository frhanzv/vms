<?php

namespace App\Commands;

use App\Libraries\PlateCapture;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Lanes with Detection Order "Antenna / Mobile App first, then Camera": give the waiting taps the
 * plate the camera read after them. Run it every minute (cron / Task Scheduler), or with --watch.
 */
class PlateResolve extends BaseCommand
{
    protected $group       = 'Gate';
    protected $name        = 'plate:resolve';
    protected $description = 'Match antenna / mobile app first taps with the plate the lane camera read after them.';
    protected $usage       = 'plate:resolve [--watch]';
    protected $options     = ['--watch' => 'Keep running, checking every 3 seconds'];

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        do {
            $n = PlateCapture::resolvePending($db);
            if ($n > 0 || ! CLI::getOption('watch')) {
                CLI::write(date('H:i:s') . " matched {$n} waiting tap(s)");
            }
            CLI::getOption('watch') && sleep(3);
        } while (CLI::getOption('watch'));
    }
}
