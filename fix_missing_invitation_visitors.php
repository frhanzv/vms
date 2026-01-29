<?php
// Quick fix script to create missing invitation_visitors records
require __DIR__ . '/vendor/autoload.php';

// Load CodeIgniter
$app = require_once FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require_once FCPATH . '../vendor/codeigniter4/framework/system/bootstrap.php';

$app = Config\Services::codeigniter();
$app->initialize();

// Get database connection
$db = \Config\Database::connect();

// Find approved invitations without invitation_visitors records
$approvedInvitations = $db->query("
    SELECT i.id, i.full_name, i.status, i.created_at
    FROM invitations i
    LEFT JOIN invitation_visitors iv ON iv.invitation_id = i.id
    WHERE i.status = 'Approved'
    AND iv.id IS NULL
")->getResultArray();

echo "Found " . count($approvedInvitations) . " approved invitations without invitation_visitors records\n\n";

foreach ($approvedInvitations as $invitation) {
    echo "Creating invitation_visitor record for ID: {$invitation['id']} - {$invitation['full_name']}\n";
    
    $db->table('invitation_visitors')->insert([
        'invitation_id' => $invitation['id'],
        'visitor_card_id' => null,
        'check_in_time' => null,
        'check_out_time' => null,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ]);
    
    echo "✓ Created successfully\n";
}

echo "\nDone! All approved invitations now have invitation_visitors records.\n";
