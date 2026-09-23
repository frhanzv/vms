<?php

namespace Tests\Unit;

use App\Services\InvitationLinkExpiryService;
use CodeIgniter\Test\CIUnitTestCase;

final class InvitationLinkExpiryServiceTest extends CIUnitTestCase
{
    public function testConfiguredExpiryUsesEndOfSelectedDay(): void
    {
        $result = (new InvitationLinkExpiryService())->resolve('2026-09-23');

        $this->assertSame('2026-09-23 23:59:59', $result);
    }

    public function testMissingConfiguredExpiryUsesLatestVisitDate(): void
    {
        $result = (new InvitationLinkExpiryService())->resolve(null, [
            ['date_to' => '2026-09-23 10:00:00'],
            ['date_to' => '2026-09-30 16:30:00'],
        ]);

        $this->assertSame('2026-09-30 16:30:00', $result);
    }

    public function testNoConfiguredExpiryOrScheduleHasNoExpiry(): void
    {
        $this->assertNull((new InvitationLinkExpiryService())->resolve(null, []));
    }

    public function testSystemDerivedDatetimeRetainsExactTime(): void
    {
        $result = (new InvitationLinkExpiryService())->resolve('2026-09-30 16:30:00');

        $this->assertSame('2026-09-30 16:30:00', $result);
    }

    public function testOnlyPendingRegistrationIsAvailable(): void
    {
        $service = new InvitationLinkExpiryService();
        $future = '2999-09-30 16:30:00';

        $this->assertNull($service->registrationUnavailableReason('Pending', $future));
        $this->assertSame(
            'Registration has already been completed.',
            $service->registrationUnavailableReason('Submitted', $future)
        );
        $this->assertSame(
            'This invitation registration link has expired.',
            $service->registrationUnavailableReason('Expired', $future)
        );
        $this->assertNotNull($service->registrationUnavailableReason('Approved', $future));
        $this->assertNotNull($service->registrationUnavailableReason('Rejected', $future));
    }
}
