<?php
use CodeIgniter\Test\CIUnitTestCase;
use App\Services\InvitationQrDeliveryService;

final class InvitationQrDeliveryTest extends CIUnitTestCase
{
    public function testApprovalAndBriefingAreBothRequiredAndDeliveryIsIdempotent(): void
    {
        $db = \Config\Database::connect(['DBDriver' => 'SQLite3', 'database' => ':memory:', 'DBPrefix' => ''], false);
        $db->query('CREATE TABLE invitations (id INTEGER PRIMARY KEY, status TEXT, video_watched INTEGER)');
        $db->query('CREATE TABLE invitation_qr_deliveries (invitation_id INTEGER PRIMARY KEY, status TEXT, updated_at TEXT)');
        $db->table('invitations')->insert(['id' => 1, 'status' => 'Submitted', 'video_watched' => 0]);
        $calls = 0;
        $service = new InvitationQrDeliveryService($db, function () use (&$calls): bool { $calls++; return true; });
        $this->assertFalse($service->deliver(1)['success']);
        $db->table('invitations')->where('id', 1)->update(['video_watched' => 1]);
        $this->assertFalse($service->deliver(1)['success']);
        $db->table('invitations')->where('id', 1)->update(['status' => 'Approved', 'video_watched' => 0]);
        $this->assertFalse($service->deliver(1)['success']);
        $this->assertSame(0, $calls);
        $db->table('invitations')->where('id', 1)->update(['video_watched' => 1]);
        $this->assertTrue($service->deliver(1)['notification_sent']);
        $this->assertTrue($service->deliver(1)['notification_sent']);
        $this->assertSame(1, $calls);
        $db->close();
    }

    public function testFailedEmailCanBeRetriedWithoutChangingApproval(): void
    {
        $db = \Config\Database::connect(['DBDriver' => 'SQLite3', 'database' => ':memory:', 'DBPrefix' => ''], false);
        $db->query('CREATE TABLE invitations (id INTEGER PRIMARY KEY, status TEXT, video_watched INTEGER)');
        $db->query('CREATE TABLE invitation_qr_deliveries (invitation_id INTEGER PRIMARY KEY, status TEXT, updated_at TEXT)');
        $db->table('invitations')->insert(['id' => 1, 'status' => 'Approved', 'video_watched' => 1]);
        $calls = 0;
        $service = new InvitationQrDeliveryService($db, function () use (&$calls): bool { return ++$calls > 1; });
        $this->assertFalse($service->deliver(1)['notification_sent']);
        $this->assertTrue($service->deliver(1)['notification_sent']);
        $this->assertSame(2, $calls);
        $this->assertSame('Approved', $db->table('invitations')->get()->getRowArray()['status']);
        $db->close();
    }
}
