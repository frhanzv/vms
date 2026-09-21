<?php

use App\Controllers\VisitorReport;
use CodeIgniter\Test\CIUnitTestCase;

final class VisitorReportCyclePairingTest extends CIUnitTestCase
{
    public function testCurrentCycleDoesNotBorrowCheckoutFromOlderLog(): void
    {
        $this->assertSame(
            ['2026-09-21 18:27:00', null],
            $this->resolve([
                'visitor_row_id' => 53,
                'reg_checkin_time' => '2026-09-21 18:27:00',
                'reg_checkout_time' => null,
                'checkin_time' => '2026-09-21 18:02:00',
                'checkout_time' => '2026-09-21 18:00:00',
            ])
        );
    }

    public function testCompletedCycleKeepsItsOwnPairedTimes(): void
    {
        $this->assertSame(
            ['2026-09-21 18:16:00', '2026-09-21 18:27:00'],
            $this->resolve([
                'visitor_row_id' => 52,
                'reg_checkin_time' => '2026-09-21 18:16:00',
                'reg_checkout_time' => '2026-09-21 18:27:00',
                'checkin_time' => '2026-09-21 18:02:00',
                'checkout_time' => '2026-09-21 18:27:00',
            ])
        );
    }

    public function testLegacyRecordCanUseEventLogPair(): void
    {
        $this->assertSame(
            ['2026-09-21 09:00:00', '2026-09-21 10:00:00'],
            $this->resolve([
                'visitor_row_id' => null,
                'reg_checkin_time' => null,
                'reg_checkout_time' => null,
                'checkin_time' => '2026-09-21 09:00:00',
                'checkout_time' => '2026-09-21 10:00:00',
            ])
        );
    }

    private function resolve(array $row): array
    {
        $controller = (new ReflectionClass(VisitorReport::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(VisitorReport::class, 'resolveVisitCycleTimes');

        return $method->invoke($controller, $row);
    }
}
