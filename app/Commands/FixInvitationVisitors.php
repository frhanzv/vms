<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixInvitationVisitors extends BaseCommand
{
    protected $group = 'App';
    protected $name = 'fix:invitation-visitors';
    protected $description = 'Create missing invitation_visitors records for approved invitations';
    protected $usage = 'fix:invitation-visitors';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('Checking for approved invitations without invitation_visitors records...', 'yellow');
        
        // Find approved invitations without invitation_visitors records
        $query = $db->query("
            SELECT i.id, i.full_name, i.status, i.created_at
            FROM invitations i
            LEFT JOIN invitation_visitors iv ON iv.invitation_id = i.id
            WHERE i.status = 'Approved'
            AND iv.id IS NULL
        ");
        
        $approvedInvitations = $query->getResultArray();
        
        CLI::write('Found ' . count($approvedInvitations) . ' approved invitations without invitation_visitors records', 'cyan');
        CLI::newLine();
        
        if (count($approvedInvitations) === 0) {
            CLI::write('No records to fix. Everything is up to date!', 'green');
            return;
        }
        
        foreach ($approvedInvitations as $invitation) {
            CLI::write("Creating invitation_visitor record for ID: {$invitation['id']} - {$invitation['full_name']}", 'yellow');
            
            $db->table('invitation_visitors')->insert([
                'invitation_id' => $invitation['id'],
                'visitor_card_id' => null,
                'check_in_time' => null,
                'check_out_time' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            CLI::write('✓ Created successfully', 'green');
        }
        
        CLI::newLine();
        CLI::write('Done! All approved invitations now have invitation_visitors records.', 'green');
    }
}
