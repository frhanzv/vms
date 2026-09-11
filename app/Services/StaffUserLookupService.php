<?php
namespace App\Services;

use CodeIgniter\Database\BaseConnection;

class StaffUserLookupService
{
    public function __construct(private ?BaseConnection $db = null)
    {
        $this->db ??= \Config\Database::connect();
    }

    public function find(string $staffId): array
    {
        $staffId = trim($staffId);
        if ($staffId === '' || mb_strlen($staffId) > 50) {
            return ['success' => false, 'message' => 'Enter a valid Staff ID (Staff No).'];
        }
        $rows = $this->db->table('staff')->select('staff_no, full_name, email, contact_number')
            ->where('staff_no', $staffId)->limit(2)->get()->getResultArray();
        if (count($rows) !== 1) {
            return ['success' => false, 'message' => $rows === []
                ? 'Staff ID was not found in the Staff List.'
                : 'More than one staff record uses this ID. Please correct the Staff List first.'];
        }
        $row = $rows[0];
        return ['success' => true, 'data' => [
            'staff_id' => trim((string) $row['staff_no']),
            'full_name' => trim((string) ($row['full_name'] ?? '')),
            'email' => trim((string) ($row['email'] ?? '')),
            'contact_no' => trim((string) ($row['contact_number'] ?? '')),
        ]];
    }
}
