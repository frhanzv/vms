<?php
use CodeIgniter\Test\CIUnitTestCase;
use App\Controllers\SecurityBriefing;
use App\Models\InvitationModel;

final class SecurityBriefingGateTest extends CIUnitTestCase
{
    private function complete(array $row, array $payload): array
    {
        $controller = (new ReflectionClass(SecurityBriefing::class))->newInstanceWithoutConstructor();
        $model = $this->getMockBuilder(InvitationModel::class)->disableOriginalConstructor()->onlyMethods(['find'])->getMock();
        $model->method('find')->willReturn($row);
        $property = new ReflectionProperty(SecurityBriefing::class, 'invitationModel');
        $property->setValue($controller, $model);
        $request = service('request');
        $request->setBody(json_encode($payload));
        $controller->initController($request, service('response'), service('logger'));
        return json_decode($controller->validateCompletion()->getJSON(), true);
    }

    public function testManualApprovalCannotBeBypassedBySubmittingVideoCompletion(): void
    {
        $result = $this->complete(['id' => 1, 'status' => 'Submitted', 'client_id' => 0],
            ['token' => base64_encode('1'), 'watched_duration' => 100, 'video_duration' => 100, 'acknowledged' => true]);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('approved', $result['message']);
    }

    public function testRejectedVisitorCannotCompleteBriefing(): void
    {
        $result = $this->complete(['id' => 1, 'status' => 'Rejected', 'client_id' => 0],
            ['token' => base64_encode('1'), 'watched_duration' => 100, 'video_duration' => 100, 'acknowledged' => true]);
        $this->assertFalse($result['success']);
    }

    public function testIncompleteVideoDoesNotIssueQr(): void
    {
        $result = $this->complete(['id' => 1, 'status' => 'Approved', 'client_id' => 0],
            ['token' => base64_encode('1'), 'watched_duration' => 20, 'video_duration' => 100, 'acknowledged' => true]);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('watch', $result['message']);
    }

    public function testZeroDurationDoesNotCompleteBriefing(): void
    {
        $result = $this->complete(['id' => 1, 'status' => 'Approved', 'client_id' => 0],
            ['token' => base64_encode('1'), 'watched_duration' => 100, 'video_duration' => 0, 'acknowledged' => true]);
        $this->assertFalse($result['success']);
    }

    public function testAcknowledgementIsRequired(): void
    {
        $result = $this->complete(['id' => 1, 'status' => 'Approved', 'client_id' => 0],
            ['token' => base64_encode('1'), 'watched_duration' => 100, 'video_duration' => 100, 'acknowledged' => false]);
        $this->assertFalse($result['success']);
    }
}
