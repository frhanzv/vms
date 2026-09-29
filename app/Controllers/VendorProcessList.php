<?php

namespace App\Controllers;

/**
 * Process List stage: Approved, but card_type not chosen yet (that's what
 * moves it to Printing List). This is the "approved and now being worked
 * on" holding stage, matching KPK's separate Process List page — the
 * pipeline is: Request -> Approve/Reject -> Process -> Printing -> Issuance -> Closed.
 */
class VendorProcessList extends BaseController
{
    public function index()
    {
        helper(['access', 'feature', 'privacy', 'role']);
        $db = \Config\Database::connect();

        $searchTerm = trim((string) ($this->request->getGet('search') ?? ''));
        $page       = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage    = 10;

        $builder = $db->table('vendors')
            ->where('status', 'Approved')
            ->where('card_status', 'Inactive')
            ->where('card_type IS NULL', null, false);

        if (! is_platform_superadmin()) {
            $builder->where('company_id', current_company_id());
        }
        if ($searchTerm !== '') {
            $builder->groupStart()
                ->like('full_name', $searchTerm)
                ->orLike('app_no', $searchTerm)
                ->orLike('vendor_company_name', $searchTerm)
                ->groupEnd();
        }

        $totalCount = (int) $builder->countAllResults(false);
        $lastPage   = max(1, (int) ceil($totalCount / $perPage));
        if ($page > $lastPage) {
            $page = $lastPage;
        }

        $rows = $builder->orderBy('created_at', 'DESC')->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        $list = [];
        foreach ($rows as $i => $row) {
            $list[] = [
                'id'                  => $row['id'],
                'no'                  => ($page - 1) * $perPage + $i + 1,
                'app_no'              => $row['app_no'] ?? 'N/A',
                'full_name'           => $row['full_name'] ?? 'N/A',
                'vendor_company_name' => $row['vendor_company_name'] ?? 'N/A',
                'ic_passport_masked'  => mask_ic_passport($row['ic_no'] ?: ($row['passport_no'] ?? ''), 'N/A'),
                'pass_expiry'         => $row['pass_expiry'] ? date('d/m/Y', strtotime($row['pass_expiry'])) : '-',
            ];
        }

        return view('vendors/process_list', [
            'pageTitle'  => 'Vendor Process List - SafeG',
            'list'       => $list,
            'searchTerm' => $searchTerm,
            'canAssign'  => has_access('vendor_pass_list', 'edit'),
            'pagination' => ['current_page' => $page, 'last_page' => $lastPage, 'total' => $totalCount],
        ]);
    }

    /** Assigns a card type, which moves the record into the Printing List. */
    public function assignCardType($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }

        $cardType = trim((string) ($this->request->getJSON(true)['card_type'] ?? ''));
        if (! in_array($cardType, ['Permanent', 'Temporary'], true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please choose Permanent or Temporary.']);
        }

        $db = \Config\Database::connect();
        $db->table('vendors')->where('id', (int) $id)->update(['card_type' => $cardType]);

        return $this->response->setJSON(['success' => true, 'message' => 'Card type assigned — moved to Printing List.']);
    }
}
