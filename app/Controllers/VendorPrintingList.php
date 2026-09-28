<?php

namespace App\Controllers;

/**
 * Printing List stage: Approved + card_type chosen, but not yet issued
 * (card_status still Inactive). Simplified from KPK's real pipeline, which
 * has explicit numeric stage codes we don't have — this uses the fields we
 * do have (card_type assignment) as the "sent for printing" signal.
 */
class VendorPrintingList extends BaseController
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
            ->where('card_type IS NOT NULL', null, false);

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
                'card_type'           => $row['card_type'] ?? '-',
            ];
        }

        return view('vendors/printing_list', [
            'pageTitle'  => 'Vendor Printing List - SafeG',
            'list'       => $list,
            'searchTerm' => $searchTerm,
            'canIssue'   => has_access('vendor_pass_list', 'edit'),
            'pagination' => ['current_page' => $page, 'last_page' => $lastPage, 'total' => $totalCount],
        ]);
    }

    /** Marks a card as printed and moves it to the Issuance List (still Inactive, receipt_no now set). */
    public function markPrinted($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }
        $db = \Config\Database::connect();
        $db->table('vendors')->where('id', (int) $id)->update([
            'receipt_no' => 'RCPT-' . date('Ymd') . '-' . str_pad((string) $id, 4, '0', STR_PAD_LEFT),
        ]);
        return $this->response->setJSON(['success' => true, 'message' => 'Marked as printed — moved to Issuance List.']);
    }
}
