<?php

use App\Libraries\InvitationEmailSender;
use CodeIgniter\Test\CIUnitTestCase;

final class InvitationEmailSenderScheduleTest extends CIUnitTestCase
{
    public function testKioskApprovalEmailUsesRegistrationTimeUntilEndOfDay(): void
    {
        $schedules = $this->resolveSchedules([
            'registration_source' => 'kiosk',
            'created_at' => '2026-09-25 10:00:00',
            'schedules' => [],
        ]);

        $this->assertSame([[
            'date_from' => '2026-09-25 10:00:00',
            'date_to' => '2026-09-25 23:59:59',
        ]], $schedules);
    }

    public function testInvitationApprovalEmailDoesNotInventMissingSchedule(): void
    {
        $this->assertSame([], $this->resolveSchedules([
            'registration_source' => 'Invitation',
            'created_at' => '2026-09-25 10:00:00',
            'schedules' => [],
        ]));
    }

    public function testSavedSchedulesTakePriority(): void
    {
        $saved = [[
            'date_from' => '2026-09-26 09:00:00',
            'date_to' => '2026-09-27 17:00:00',
        ]];

        $this->assertSame($saved, $this->resolveSchedules([
            'registration_source' => 'kiosk',
            'created_at' => '2026-09-25 10:00:00',
            'schedules' => $saved,
        ]));
    }

    private function resolveSchedules(array $invitation): array
    {
        $sender = (new ReflectionClass(InvitationEmailSender::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(InvitationEmailSender::class, 'getApprovalEmailSchedules');

        return $method->invoke($sender, $invitation);
    }
}
