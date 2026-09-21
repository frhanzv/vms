<?php

use App\Controllers\VisitorList;
use CodeIgniter\Test\CIUnitTestCase;

final class VisitorListGxoStatusTest extends CIUnitTestCase
{
    public function testUnvisitedRecordBecomesExpiredAfterVisitPeriodEnds(): void
    {
        $this->assertSame('Expired', $this->resolveGxoStatus([
            'sch_date_to' => '2000-01-01 00:00:00',
        ]));
    }

    public function testFutureUnvisitedRecordRemainsExpected(): void
    {
        $this->assertSame('Expected', $this->resolveGxoStatus([
            'sch_date_to' => '2999-01-01 00:00:00',
        ]));
    }

    public function testCompletedAndRejectedStatusesTakePriorityOverExpiry(): void
    {
        $expired = ['sch_date_to' => '2000-01-01 00:00:00'];

        $this->assertSame('Rejected Entry', $this->resolveGxoStatus($expired + ['guard_entry_status' => 'Rejected Entry']));
        $this->assertSame('Checked Out', $this->resolveGxoStatus($expired + ['check_out_time' => '2026-09-21 11:00:00']));
        $this->assertSame('Checked In', $this->resolveGxoStatus($expired + ['check_in_time' => '2026-09-21 10:00:00']));
    }

    private function resolveGxoStatus(array $row): string
    {
        $controller = (new ReflectionClass(VisitorList::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(VisitorList::class, 'gxoEntryStatus');

        return $method->invoke($controller, $row);
    }
}
