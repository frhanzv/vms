<?php
$db = new mysqli('localhost', 'root', '', 'vms');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Truncate the table
$db->query("TRUNCATE TABLE email_templates");

$now = date('Y-m-d H:i:s');
$seedCodes = [
    'VISITOR_INVITE',
    'VISITOR_REQ_REJECT',
    'VISITOR_INVITATION_APPROVAL_PENDING',
    'PORT_PASS_APPROVE_REJECT_WITH_QR',
    'VISITOR_REQ_APPROVAL',
];

$defaults = [
    'INVITATION' => [
        'subject' => 'Visitor Invitation - SafeG',
        'body' => "Dear {{visitor_name}},\n\nYou have been invited to visit {{company}}. Please complete your registration by clicking the button below:\n\n{{registration_link}}\n\nThank you.",
    ],
    'VISITOR_INVITE' => [
        'subject' => 'Visitor Invitation - SafeG',
        'body' => "Dear {{visitor_name}},\n\nYou have been invited to visit {{company}}. Please complete your registration by clicking the button below:\n\n{{registration_link}}\n\nThank you.",
    ],
    'VISITOR_REQ_APPROVAL' => [
        'subject' => 'Visitor Request Approved',
        'body' => "Dear {{visitor_name}},\n\nYour visitor request to {{company}} has been approved.\n\nThank you.",
    ],
    'VISITOR_REQ_REJECT' => [
        'subject' => 'Visitor Request Update',
        'body' => "Dear {{visitor_name}},\n\nYour visitor request to {{company}} could not be approved at this time.\n\nThank you.",
    ],
    'PORT_PASS_APPROVE_REJECT_WITH_QR' => [
        'subject' => 'Port Pass Update',
        'body' => "Dear {{visitor_name}},\n\nYour port pass request has been updated. Please refer to the QR code / pass details provided.\n\nThank you.",
    ],
    'VISITOR_INVITATION_APPROVAL_PENDING' => [
        'subject' => 'Visitor Invitation Pending Approval',
        'body' => "Dear {{visitor_name}},\n\nYour invitation is pending approval.\n\nThank you.",
    ],
];

foreach ($seedCodes as $code) {
    $mapped = $defaults[$code] ?? null;
    $subject = $mapped ? $mapped['subject'] : ucwords(strtolower(str_replace('_', ' ', $code)));
    $body = $mapped ? $mapped['body'] : "Dear {{visitor_name}},\n\n[Write your email content here]\n\nThank you.";
    
    $subject = $db->real_escape_string($subject);
    $body = $db->real_escape_string($body);
    $primary_color = '#137FEC';
    $content_bg_color = '#F8F9FA';
    $text_color = '#333333';
    
    $query = "INSERT INTO email_templates 
              (code, subject, body, primary_color, content_bg_color, text_color, created_at, updated_at) 
              VALUES 
              ('$code', '$subject', '$body', '$primary_color', '$content_bg_color', '$text_color', '$now', '$now')";
    
    if (!$db->query($query)) {
        echo "Error inserting $code: " . $db->error . "\n";
    } else {
        echo "Inserted $code successfully.\n";
    }
}
echo "Done.\n";
