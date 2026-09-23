<?php
use CodeIgniter\Test\CIUnitTestCase;
use App\Controllers\SecurityBriefing;
use App\Models\InvitationModel;
use App\Models\InvitationScheduleModel;

final class SecurityBriefingGateTest extends CIUnitTestCase
{
    private function complete(array $row, array $payload): array
    {
        $controller = (new ReflectionClass(SecurityBriefing::class))->newInstanceWithoutConstructor();
        $model = $this->getMockBuilder(InvitationModel::class)->disableOriginalConstructor()->onlyMethods(['find'])->getMock();
        $model->method('find')->willReturn($row);
        $property = new ReflectionProperty(SecurityBriefing::class, 'invitationModel');
        $property->setValue($controller, $model);
        $scheduleModel = $this->getMockBuilder(InvitationScheduleModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findAll'])
            ->addMethods(['where'])
            ->getMock();
        $scheduleModel->method('where')->willReturnSelf();
        $scheduleModel->method('findAll')->willReturn([]);
        $scheduleProperty = new ReflectionProperty(SecurityBriefing::class, 'scheduleModel');
        $scheduleProperty->setValue($controller, $scheduleModel);
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

    public function testCompletedBriefingCannotBeSubmittedAgain(): void
    {
        $result = $this->complete(
            ['id' => 1, 'status' => 'Approved', 'client_id' => 0, 'video_watched' => 1],
            ['token' => base64_encode('1'), 'watched_duration' => 100, 'video_duration' => 100, 'acknowledged' => true]
        );

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('already been completed', $result['message']);
    }

    public function testExpiredBriefingCannotBeCompleted(): void
    {
        $result = $this->complete(
            ['id' => 1, 'status' => 'Approved', 'client_id' => 0, 'link_expiry' => '2000-01-01 12:00:00'],
            ['token' => base64_encode('1'), 'watched_duration' => 100, 'video_duration' => 100, 'acknowledged' => true]
        );

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('expired', $result['message']);
    }
}
