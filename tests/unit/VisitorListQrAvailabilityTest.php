<?php

use App\Controllers\VisitorList;
use CodeIgniter\Test\CIUnitTestCase;

final class VisitorListQrAvailabilityTest extends CIUnitTestCase
{
    public function testQrIsHiddenUntilApprovedVisitorWatchesVideo(): void
    {
        $this->assertFalse($this->available(['status' => 'Approved', 'video_watched' => 0]));
        $this->assertFalse($this->available(['status' => 'Pending', 'video_watched' => 1]));
        $this->assertTrue($this->available(['status' => 'Approved', 'video_watched' => 1]));
    }

    private function available(array $invitation): bool
    {
        $controller = (new ReflectionClass(VisitorList::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(VisitorList::class, 'visitorQrAvailable');

        return $method->invoke($controller, $invitation);
    }
}
