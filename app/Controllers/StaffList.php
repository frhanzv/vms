<?php

namespace App\Controllers;

class StaffList extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $searchTerm = trim((string) ($this->request->getGet('search') ?? ''));
        $page       = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage    = (int) ($this->request->getGet('per_page') ?? 10);
        $sortBy     = (string) ($this->request->getGet('sort') ?? 'date_desc');

        if (! in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        $allowedSorts = ['name_asc', 'name_desc', 'date_asc', 'date_desc'];
        if (! in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'date_desc';
        }

        $builder   = $this->buildStaffListQuery($db, $searchTerm);
        $totalStaff = (int) $builder->countAllResults(false);
        $lastPage   = max(1, (int) ceil($totalStaff / $perPage));

        if ($page > $lastPage) {
            $page = $lastPage;
        }

        $this->applyStaffSort($builder, $sortBy);

        $results = $builder
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        $checkedIn = (int) $this->buildStaffListQuery($db, $searchTerm)
            ->where('s.status', 'Active')
            ->countAllResults();

        $withCard = (int) $this->buildStaffListQuery($db, $searchTerm)
            ->where('c.status IS NOT NULL', null, false)
            ->countAllResults();

        $rowOffset = ($page - 1) * $perPage;
        $staffList = [];

        foreach ($results as $index => $row) {
            $staffList[] = [
                'id'                => $row['id'],
                'no'                => $rowOffset + $index + 1,
                'date'              => date('d/m/Y', strtotime($row['created_at'])),
                'app_no'            => $row['app_no'] ?? 'N/A',
                'full_name'         => $row['full_name'] ?? 'N/A',
                'ic_passport'       => $row['ic_passport'] ?? 'N/A',
                'staff_no'          => $row['staff_no'] ?? 'N/A',
                'status'            => $row['status'] ?? 'N/A',
                'suspension_period' => $row['suspension_period'] ?? '-',
                'next_action'       => $row['next_action'] ?? '-',
                'card_status'       => $row['card_status'] ?? '-',
                'card_expiry'       => $row['card_expiry'] ?? '-',
                'remark'            => $row['remark'] ?? '-',
                'created_at'        => $row['created_at'] ?? '',
            ];
        }

        helper('feature');
        $companyId       = current_company_id();
        $formFieldModel  = new \App\Models\ClientFormFieldModel();
        $showPrintButton = $formFieldModel->isEnabled($companyId, 'staff_pass_request', 'print_button');

        return view('staffs/list', [
            'pageTitle'       => 'Staff List - SafeG',
            'stats'           => [
                'total'     => $totalStaff,
                'checkedIn' => $checkedIn,
                'withCard'  => $withCard,
            ],
            'staffList'       => $staffList,
            'showPrintButton' => $showPrintButton,
            'canEdit'         => $this->userCan('edit'),
            'canDelete'       => $this->userCan('delete'),
            'searchTerm'      => $searchTerm,
            'sortBy'          => $sortBy,
            'pagination'      => [
                'current_page' => $page,
                'last_page'    => $lastPage,
                'total'        => $totalStaff,
                'per_page'     => $perPage,
            ],
        ]);
    }

    /**
     * @return \CodeIgniter\Database\BaseBuilder
     */
    private function buildStaffListQuery($db, string $searchTerm)
    {
        $builder = $db->table('staff s')
            ->select('s.*, c.status as card_status, c.expiry_date as card_expiry')
            ->join('staff_cards c', 'c.staff_id = s.id', 'left');

        if ($searchTerm !== '') {
            $builder->groupStart()
                ->like('s.full_name', $searchTerm)
                ->orLike('s.ic_passport', $searchTerm)
                ->orLike('s.staff_no', $searchTerm)
                ->orLike('s.app_no', $searchTerm)
                ->groupEnd();
        }

        return $builder;
    }

    private function applyStaffSort($builder, string $sortBy): void
    {
        switch ($sortBy) {
            case 'name_asc':
                $builder->orderBy('s.full_name', 'ASC');
                break;
            case 'name_desc':
                $builder->orderBy('s.full_name', 'DESC');
                break;
            case 'date_asc':
                $builder->orderBy('s.created_at', 'ASC');
                break;
            default:
                $builder->orderBy('s.created_at', 'DESC');
                break;
        }
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        $db->table('staff')->where('id', (int) $id)->delete();

        return $this->response->setJSON(['success' => true, 'message' => 'Staff record deleted.']);
    }
}
