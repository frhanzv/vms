<?php

use App\Controllers\VisitorList;
use CodeIgniter\Test\CIUnitTestCase;

final class VisitorListQrVisibilityTest extends CIUnitTestCase
{
    public function testInvitationVisitorAppearsOnlyAfterQrWasSent(): void
    {
        $db = \Config\Database::connect([
            'DBDriver' => 'SQLite3',
            'database' => ':memory:',
            'DBPrefix' => '',
        ], false);

        $db->query('CREATE TABLE invitations (id INTEGER PRIMARY KEY, status TEXT, registration_source TEXT)');
        $db->query('CREATE TABLE invitation_visitors (id INTEGER PRIMARY KEY, invitation_id INTEGER, check_in_time TEXT)');
        $db->query('CREATE TABLE invitation_qr_deliveries (invitation_id INTEGER PRIMARY KEY, status TEXT)');

        $db->table('invitations')->insertBatch([
            ['id' => 1, 'status' => 'Approved', 'registration_source' => 'Invitation'],
            ['id' => 2, 'status' => 'Approved', 'registration_source' => 'Invitation'],
            ['id' => 3, 'status' => 'Approved', 'registration_source' => 'kiosk'],
            ['id' => 4, 'status' => 'Approved', 'registration_source' => 'Invitation'],
            ['id' => 5, 'status' => 'Pending', 'registration_source' => 'Invitation'],
        ]);
        $db->table('invitation_visitors')->insertBatch([
            ['id' => 1, 'invitation_id' => 1, 'check_in_time' => null],
            ['id' => 2, 'invitation_id' => 2, 'check_in_time' => null],
            ['id' => 3, 'invitation_id' => 3, 'check_in_time' => null],
            ['id' => 4, 'invitation_id' => 4, 'check_in_time' => '2026-09-21 10:00:00'],
            ['id' => 5, 'invitation_id' => 5, 'check_in_time' => null],
        ]);
        $db->table('invitation_qr_deliveries')->insertBatch([
            ['invitation_id' => 1, 'status' => 'failed'],
            ['invitation_id' => 2, 'status' => 'sent'],
            ['invitation_id' => 5, 'status' => 'sent'],
        ]);

        $builder = $db->table('invitation_visitors iv')
            ->select('iv.id')
            ->join('invitations i', 'i.id = iv.invitation_id');

        $controller = (new ReflectionClass(VisitorList::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(VisitorList::class, 'applyVisitorListEligibility');
        $method->invoke($controller, $builder, $db);

        $ids = array_column($builder->orderBy('iv.id')->get()->getResultArray(), 'id');
        $this->assertSame(['2', '3', '4'], array_map('strval', $ids));

        $db->close();
    }
}
