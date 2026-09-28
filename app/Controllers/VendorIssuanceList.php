<?php

namespace App\Controllers;

/**
 * Issuance List stage: card printed (receipt_no set) but not yet handed
 * over/activated (card_status still Inactive). Handing it over sets
 * card_status = Active, which moves it into the Closed List.
 */
class VendorIssuanceList extends BaseController
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
            ->where('receipt_no IS NOT NULL', null, false);

        if (! is_platform_superadmin()) {
            $builder->where('company_id', current_company_id());
        }
        if ($searchTerm !== '') {
            $builder->groupStart()
                ->like('full_name', $searchTerm)
                ->orLike('app_no', $searchTerm)
                ->orLike('receipt_no', $searchTerm)
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
                'receipt_no'          => $row['receipt_no'] ?? '-',
                'full_name'           => $row['full_name'] ?? 'N/A',
                'vendor_company_name' => $row['vendor_company_name'] ?? 'N/A',
                'card_type'           => $row['card_type'] ?? '-',
            ];
        }

        return view('vendors/issuance_list', [
            'pageTitle'  => 'Vendor Issuance List - SafeG',
            'list'       => $list,
            'searchTerm' => $searchTerm,
            'canIssue'   => has_access('vendor_pass_list', 'edit'),
            'pagination' => ['current_page' => $page, 'last_page' => $lastPage, 'total' => $totalCount],
        ]);
    }

    /** Hands the card over to the vendor — activates it, which moves it to the Closed List. */
    public function issue($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }
        $db = \Config\Database::connect();
        $db->table('vendors')->where('id', (int) $id)->update(['card_status' => 'Active']);
        return $this->response->setJSON(['success' => true, 'message' => 'Card issued — moved to Closed List.']);
    }
}
