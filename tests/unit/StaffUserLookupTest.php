<?php
use CodeIgniter\Test\CIUnitTestCase;
use App\Services\StaffUserLookupService;

final class StaffUserLookupTest extends CIUnitTestCase
{
    public function testExactStaffNumberAndOnlyUserFieldsAreReturned(): void
    {
        $db = \Config\Database::connect(['DBDriver'=>'SQLite3','database'=>':memory:','DBPrefix'=>''], false);
        $db->query('CREATE TABLE staff (staff_no TEXT, full_name TEXT, email TEXT, contact_number TEXT, ic_passport TEXT)');
        $db->table('staff')->insert(['staff_no'=>'00123','full_name'=>'Test Staff','email'=>'staff@example.test','contact_number'=>'0123456789','ic_passport'=>'private']);
        $service = new StaffUserLookupService($db);
        $this->assertFalse($service->find('123')['success']);
        $this->assertFalse($service->find('')['success']);
        $result = $service->find(' 00123 ');
        $this->assertTrue($result['success']);
        $this->assertSame(['staff_id'=>'00123','full_name'=>'Test Staff','email'=>'staff@example.test','contact_no'=>'0123456789'], $result['data']);
        $db->table('staff')->insert(['staff_no'=>'00123','full_name'=>'Duplicate']);
        $this->assertFalse($service->find('00123')['success']);
        $db->close();
    }
}
