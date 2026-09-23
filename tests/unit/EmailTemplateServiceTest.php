<?php

use App\Libraries\EmailTemplateService;
use CodeIgniter\Test\CIUnitTestCase;

final class EmailTemplateServiceTest extends CIUnitTestCase
{
    public function testPendingApprovalIsAConfigurableProcess(): void
    {
        $service = new EmailTemplateService();

        $this->assertTrue($service->isSupportedProcess(EmailTemplateService::PROCESS_PENDING_APPROVAL));
        $this->assertSame(
            'email_template_pending_approval_client_10',
            $service->getClientStorageKey(EmailTemplateService::PROCESS_PENDING_APPROVAL, 10)
        );
    }

    public function testPendingApprovalDefaultsContainHostAndVisitorPlaceholders(): void
    {
        $template = (new EmailTemplateService())->getDefaultTemplate(
            EmailTemplateService::PROCESS_PENDING_APPROVAL
        );

        $this->assertStringContainsString('{{host_name}}', $template['intro_line']);
        $this->assertStringContainsString('{{visitor_name}}', $template['intro_line']);
        $this->assertSame('Review Request', $template['button_text']);
        $this->assertStringContainsString('{{visit_date}}', implode(' ', $template['notes_items']));
    }
}
