<?php

use App\Controllers\VisitorList;
use CodeIgniter\Test\CIUnitTestCase;

final class VisitorListHostNameTest extends CIUnitTestCase
{
    public function testStaffNumberResolvesToStaffDirectoryName(): void
    {
        $db = $this->database();
        $db->table('staff')->insert(['id' => 1, 'staff_no' => 'AK47', 'full_name' => 'Azim Host']);
        $db->table('invitations')->insert([
            'id' => 1,
            'client_id' => 10,
            'staff_id' => null,
            'invited_by' => 'AK47',
        ]);

        $row = $db->table('invitations i')
            ->select($this->expression() . ' AS host_name', false)
            ->where('i.id', 1)
            ->get()
            ->getRowArray();

        $this->assertSame('Azim Host', $row['host_name']);
        $db->close();
    }

    public function testStoredHostNameRemainsAsFallback(): void
    {
        $db = $this->database();
        $db->table('invitations')->insert([
            'id' => 1,
            'client_id' => 10,
            'staff_id' => null,
            'invited_by' => 'GXO Admin',
        ]);

        $row = $db->table('invitations i')
            ->select($this->expression() . ' AS host_name', false)
            ->where('i.id', 1)
            ->get()
            ->getRowArray();

        $this->assertSame('GXO Admin', $row['host_name']);
        $db->close();
    }

    private function database()
    {
        $db = \Config\Database::connect([
            'DBDriver' => 'SQLite3',
            'database' => ':memory:',
            'DBPrefix' => '',
        ], false);
        $db->query('CREATE TABLE invitations (id INTEGER PRIMARY KEY, client_id INTEGER, staff_id TEXT, invited_by TEXT)');
        $db->query('CREATE TABLE users (id INTEGER PRIMARY KEY, client_id INTEGER, staff_id TEXT, username TEXT, full_name TEXT, is_active INTEGER)');
        $db->query('CREATE TABLE staff (id INTEGER PRIMARY KEY, staff_no TEXT, full_name TEXT)');

        return $db;
    }

    private function expression(): string
    {
        $controller = (new ReflectionClass(VisitorList::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(VisitorList::class, 'hostNameSelectExpression');

        return $method->invoke($controller);
    }
}
