<?php
use CodeIgniter\Test\CIUnitTestCase;
use App\Services\RequestViewService;
use App\Controllers\RequestList;
final class RequestViewTest extends CIUnitTestCase
{
    public function testDefaultsAndExplicitHiddenSections(): void
    {
        $this->assertNotContains(false, RequestViewService::normalize([]));
        $settings = RequestViewService::normalize(['watchlist'=>false, 'assets'=>false, 'unknown'=>false]);
        $this->assertFalse($settings['watchlist']);
        $this->assertFalse($settings['assets']);
        $this->assertTrue($settings['identity']);
        $this->assertArrayNotHasKey('unknown', $settings);
    }
    public function testOnlyAdminRolesCanEdit(): void
    {
        foreach (['admin','superadmin','client_super_admin'] as $role) $this->assertTrue(RequestViewService::canEdit($role));
        foreach (['host','guard','approver','officer',''] as $role) $this->assertFalse(RequestViewService::canEdit($role));
    }
    public function testNonAdminSaveIsRejected(): void
    {
        session()->set('role','host');
        $controller = (new ReflectionClass(RequestList::class))->newInstanceWithoutConstructor();
        $controller->initController(service('request'),service('response'),service('logger'));
        $this->assertSame(403,$controller->saveViewSettings()->getStatusCode());
        session()->remove('role');
    }
    public function testAdminCannotSaveAnotherClientView(): void
    {
        session()->set(['role'=>'admin','client_id'=>10]);
        $request=service('request');
        $request->setBody(json_encode(['client_id'=>20,'sections'=>[]]));
        $controller = (new ReflectionClass(RequestList::class))->newInstanceWithoutConstructor();
        $controller->initController($request,service('response'),service('logger'));
        $this->assertSame(403,$controller->saveViewSettings()->getStatusCode());
        session()->remove(['role','client_id']);
    }
}
