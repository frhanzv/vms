<?php

namespace App\Commands;

use App\Libraries\ReportBuilder;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Emails every scheduled report template that is due. Run it every few minutes
 * (Windows Task Scheduler / cron): php spark reports:send
 */
class SendScheduledReports extends BaseCommand
{
    protected $group       = 'Reports';
    protected $name        = 'reports:send';
    protected $description = 'Email the scheduled report templates that are due now.';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('report_templates')) {
            CLI::write('report_templates table missing - run php spark migrate.', 'red');

            return;
        }
        $rb   = new ReportBuilder();
        $now  = time();
        $sent = 0;
        foreach ($db->table('report_templates')->where('is_active', 1)->where('frequency !=', 'manual')->get()->getResultArray() as $tpl) {
            $period = $rb->due($tpl, $now);
            if ($period === null) {
                continue;
            }
            $err = $rb->send($tpl, $period[0], $period[1]);
            if ($err) {
                CLI::write("{$tpl['name']}: {$err}", 'red');
                continue;
            }
            $db->table('report_templates')->where('id', $tpl['id'])->update(['last_sent_at' => date('Y-m-d H:i:s', $now), 'last_generated_at' => date('Y-m-d H:i:s', $now)]);
            CLI::write("{$tpl['name']}: sent for {$period[0]} to {$period[1]}", 'green');
            $sent++;
        }
        CLI::write("{$sent} report(s) sent.");
    }
}
