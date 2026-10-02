<?php

namespace App\Controllers;

/**
 * Issuance List stage: card printed (receipt_no set) but not yet handed
 * over/activated (card_status still Inactive). Handing it over sets
 * card_status = Active, which moves it into the Closed List.
 *
 * Updated to capture who physically collected the card — matching KPK's
 * real issueCards()/finishPortPassProcessing() action, which records the
 * collector's name, IC/passport, and the physical card ID before closing
 * the pass out. Previously this just flipped a flag with no audit trail.
 */
class VendorIssuanceList extends BaseController
{
    private const SORT_OPTIONS = [
        'date_desc'    => ['created_at', 'DESC'],
        'date_asc'     => ['created_at', 'ASC'],
        'name_asc'     => ['full_name', 'ASC'],
        'name_desc'    => ['full_name', 'DESC'],
        'company_asc'  => ['vendor_company_name', 'ASC'],
        'company_desc' => ['vendor_company_name', 'DESC'],
    ];

    public function index()
    {
        helper(['access', 'feature', 'privacy', 'role']);
        $db = \Config\Database::connect();

        $searchTerm = trim((string) ($this->request->getGet('search') ?? ''));
        $sortBy     = trim((string) ($this->request->getGet('sort_by') ?? 'date_desc'));
        $page       = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage    = 10;

        if (! array_key_exists($sortBy, self::SORT_OPTIONS)) {
            $sortBy = 'date_desc';
        }

        $builder = $db->table('vendors')
            ->where('status', 'Approved')
            ->where('card_status', 'Inactive')
            ->where('receipt_no IS NOT NULL', null, false);

        if (! is_platform_superadmin()) {
            $builder->where('company_id', current_company_id());
        }
        if ($searchTerm !== '') {
            // Matches KPK's search bar: IC / Passport / Full Name / App No / Company / Receipt No.
            $builder->groupStart()
                ->like('full_name', $searchTerm)
                ->orLike('ic_no', $searchTerm)
                ->orLike('passport_no', $searchTerm)
                ->orLike('app_no', $searchTerm)
                ->orLike('vendor_company_name', $searchTerm)
                ->orLike('receipt_no', $searchTerm)
                ->groupEnd();
        }

        $totalCount = (int) $builder->countAllResults(false);
        $lastPage   = max(1, (int) ceil($totalCount / $perPage));
        if ($page > $lastPage) {
            $page = $lastPage;
        }

        [$sortField, $sortDir] = self::SORT_OPTIONS[$sortBy];
        $rows = $builder->orderBy($sortField, $sortDir)->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

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
            'sortBy'     => $sortBy,
            'canIssue'   => has_access('vendor_pass_list', 'edit')
                && (new \App\Models\ClientFormFieldModel())->isEnabled(current_company_id(), 'vendor_pass_request', 'issuance_issue_button'),
            'pagination' => ['current_page' => $page, 'last_page' => $lastPage, 'total' => $totalCount],
        ]);
    }

    /**
     * Hands the card over to the vendor — activates it, which moves it to
     * the Closed List. Now requires the collector's name and IC/passport
     * (the person standing in front of the counter collecting the card) —
     * the same audit trail KPK's real Issue Cards action records before it
     * closes the pass out. (This table's existing `card_id` column is a
     * separate INT foreign-key-shaped field, not touched here.)
     */
    public function issue($id)
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'edit')
            || ! (new \App\Models\ClientFormFieldModel())->isEnabled(current_company_id(), 'vendor_pass_request', 'issuance_issue_button')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }

        $body                = $this->request->getJSON(true) ?? [];
        $collectorName       = trim((string) ($body['collector_name'] ?? ''));
        $collectorIcPassport = trim((string) ($body['collector_ic_passport'] ?? ''));

        if ($collectorName === '' || $collectorIcPassport === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Please enter the collector\'s name and IC/passport before issuing the card.']);
        }

        $db      = \Config\Database::connect();
        $builder = $db->table('vendors')->where('id', (int) $id);
        if (! is_platform_superadmin()) {
            $builder->where('company_id', current_company_id());
        }
        $vendor = $builder->get()->getRowArray();
        if (! $vendor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Record not found.']);
        }

        $db->table('vendors')->where('id', (int) $id)->update([
            'card_status'           => 'Active',
            'collector_name'        => $collectorName,
            'collector_ic_passport' => $collectorIcPassport,
            'issued_by'             => (string) (session()->get('full_name') ?: session()->get('username')),
            'issued_at'             => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Card issued to ' . $collectorName . ' — moved to Closed List.']);
    }
}
