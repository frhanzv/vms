<?php

namespace App\Controllers;

class VendorClosedList extends BaseController
{
    private const SORT_OPTIONS = [
        'date_desc'    => ['created_at', 'DESC'],
        'date_asc'     => ['created_at', 'ASC'],
        'name_asc'     => ['full_name', 'ASC'],
        'name_desc'    => ['full_name', 'DESC'],
        'company_asc'  => ['vendor_company_name', 'ASC'],
        'company_desc' => ['vendor_company_name', 'DESC'],
    ];

    /**
     * "Closed" here = card_status = 'Active' (a card has actually been
     * issued) — matching what KPK's Closed List represents: fully processed
     * passes with a physical card, as opposed to Approved (workflow-approved
     * but card not necessarily issued yet). A terminated card (card_status
     * = 'Terminated') also lands here rather than disappearing, since the
     * pass itself was still fully processed — the Card Status filter below
     * can narrow to just one state.
     */
    public function index()
    {
        helper(['access', 'feature', 'privacy', 'role']);
        $db = \Config\Database::connect();

        $issueDateFrom = trim((string) ($this->request->getGet('issue_date_from') ?? ''));
        $issueDateTo   = trim((string) ($this->request->getGet('issue_date_to') ?? ''));
        $appDate       = trim((string) ($this->request->getGet('app_date') ?? ''));
        $resident      = trim((string) ($this->request->getGet('resident') ?? 'all'));
        $cardStatus    = trim((string) ($this->request->getGet('card_status') ?? 'all'));
        $cardExpiry    = trim((string) ($this->request->getGet('card_expiry') ?? ''));
        $searchTerm    = trim((string) ($this->request->getGet('search') ?? ''));
        $sortBy        = trim((string) ($this->request->getGet('sort_by') ?? 'date_desc'));
        $page          = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage       = (int) ($this->request->getGet('per_page') ?? 10);

        if (! in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }
        if (! array_key_exists($sortBy, self::SORT_OPTIONS)) {
            $sortBy = 'date_desc';
        }

        $builder = $db->table('vendors')->whereIn('card_status', ['Active', 'Terminated']);

        helper('vendor_client_scope');
        vendor_client_scope($builder);
        if ($issueDateFrom !== '') {
            $builder->where('DATE(created_at) >=', $issueDateFrom);
        }
        if ($issueDateTo !== '') {
            $builder->where('DATE(created_at) <=', $issueDateTo);
        }
        if ($appDate !== '') {
            $builder->where('DATE(created_at)', $appDate);
        }
        if ($resident !== 'all') {
            $builder->where('resident', $resident);
        }
        if ($cardStatus !== 'all') {
            $builder->where('card_status', $cardStatus);
        }
        if ($cardExpiry !== '') {
            $builder->where('DATE(pass_expiry)', $cardExpiry);
        }
        if ($searchTerm !== '') {
            $builder->groupStart()
                ->like('full_name', $searchTerm)
                ->orLike('ic_no', $searchTerm)
                ->orLike('passport_no', $searchTerm)
                ->orLike('app_no', $searchTerm)
                ->orLike('receipt_no', $searchTerm)
                ->orLike('vendor_company_name', $searchTerm)
                ->groupEnd();
        }

        $totalCount = (int) $builder->countAllResults(false);
        $lastPage   = max(1, (int) ceil($totalCount / $perPage));
        if ($page > $lastPage) {
            $page = $lastPage;
        }

        [$sortField, $sortDir] = self::SORT_OPTIONS[$sortBy];
        $rows = $builder
            ->orderBy($sortField, $sortDir)
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        $rowOffset = ($page - 1) * $perPage;
        $closedList = [];
        foreach ($rows as $index => $row) {
            $closedList[] = [
                'id'                   => $row['id'],
                'no'                   => $rowOffset + $index + 1,
                'app_no'               => $row['app_no'] ?? 'N/A',
                'receipt_no'           => $row['receipt_no'] ?? '-',
                'app_date'             => date('d/m/Y', strtotime($row['created_at'])),
                'vendor_company_name'  => $row['vendor_company_name'] ?? 'N/A',
                'full_name'            => $row['full_name'] ?? 'N/A',
                'ic_passport_masked'   => mask_ic_passport($row['ic_no'] ?: ($row['passport_no'] ?? ''), 'N/A'),
                'vehicle_registration' => $row['vehicle_registration'] ?? '-',
                'card_type'            => $row['card_type'] ?? '-',
                'card_id'              => $row['card_id'] ?? '-',
                'card_status'          => $row['card_status'],
                'card_expiry'          => $row['pass_expiry'] ? date('d/m/Y', strtotime($row['pass_expiry'])) : '-',
                'collector_name'       => $row['collector_name'] ?? '-',
                'issued_at'            => ! empty($row['issued_at']) ? date('d/m/Y H:i', strtotime($row['issued_at'])) : '-',
            ];
        }

        $formFieldModel = new \App\Models\ClientFormFieldModel();
        $companyId      = current_company_id();
        $cfg            = fn(string $key) => $formFieldModel->isEnabled($companyId, 'vendor_pass_request', $key);

        return view('vendors/closed_list', [
            'pageTitle'      => 'Vendor Pass Closed List - SafeG',
            'closedList'     => $closedList,
            'canExport'      => $cfg('closed_export_button'),
            'canQr'          => $cfg('qr_button'),
            'canCardDetails' => $cfg('closed_card_details_button'),
            'searchTerm'     => $searchTerm,
            'issueDateFrom'  => $issueDateFrom,
            'issueDateTo'    => $issueDateTo,
            'appDate'        => $appDate,
            'resident'       => $resident,
            'cardStatus'     => $cardStatus,
            'cardExpiry'     => $cardExpiry,
            'sortBy'         => $sortBy,
            'pagination'     => [
                'current_page' => $page,
                'last_page'    => $lastPage,
                'total'        => $totalCount,
                'per_page'     => $perPage,
            ],
        ]);
    }
}
