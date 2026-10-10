<?php

namespace App\Controllers;

/**
 * Process List stage — per the real KPK workflow (confirmed directly by the
 * user): once Approved, a pass shows up here AND in the Printing List at
 * the same time — they're two views onto the same "approved, not yet
 * printed" set, not two sequential stages. Process List is where staff
 * review, update, or reject a record — Worker Type (card_type) is chosen
 * once at intake on the request form, not here. Printing List is
 * the bulk-print screen. Either page can actually print it (Process
 * Detail's own Print button, or Printing List's row/bulk print) — whichever
 * happens first is what moves the record out of BOTH lists and into the
 * Issuance List. So the only gate here is "has it been printed yet"
 * (receipt_no set), not whether a card type has been chosen.
 */
class VendorProcessList extends BaseController
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
        $cardType   = trim((string) ($this->request->getGet('card_type') ?? ''));
        $sortBy     = trim((string) ($this->request->getGet('sort_by') ?? 'date_desc'));
        $page       = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage    = 10;

        if (! array_key_exists($sortBy, self::SORT_OPTIONS)) {
            $sortBy = 'date_desc';
        }

        $builder = $db->table('vendors')
            ->where('status', 'Approved')
            ->groupStart()
                ->where('receipt_no', null)
                ->orWhere('receipt_no', '')
            ->groupEnd();

        helper('vendor_client_scope');
        vendor_client_scope($builder);
        if ($searchTerm !== '') {
            // Matches KPK's search bar: IC / Passport / Company / Sub Company / Full Name / Receipt No / Card ID.
            $builder->groupStart()
                ->like('full_name', $searchTerm)
                ->orLike('ic_no', $searchTerm)
                ->orLike('passport_no', $searchTerm)
                ->orLike('app_no', $searchTerm)
                ->orLike('vendor_company_name', $searchTerm)
                ->orLike('receipt_no', $searchTerm)
                ->groupEnd();
        }
        if ($cardType === 'unassigned') {
            $builder->where('card_type IS NULL', null, false);
        } elseif (in_array($cardType, ['Permanent', 'Temporary'], true)) {
            $builder->where('card_type', $cardType);
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
            $photo = $row['facial_photo'] ?? null;
            $list[] = [
                'id'                  => $row['id'],
                'no'                  => ($page - 1) * $perPage + $i + 1,
                'app_no'              => $row['app_no'] ?? 'N/A',
                'full_name'           => $row['full_name'] ?? 'N/A',
                'vendor_company_name' => $row['vendor_company_name'] ?? 'N/A',
                'ic_passport_masked'  => mask_ic_passport($row['ic_no'] ?: ($row['passport_no'] ?? ''), 'N/A'),
                'card_type'           => $row['card_type'] ?? 'Not assigned',
                'pass_expiry'         => $row['pass_expiry'] ? date('d/m/Y', strtotime($row['pass_expiry'])) : '-',
                'photo_url'           => $photo ? base_url('uploads/facial_photos/' . $photo) : null,
            ];
        }

        return view('vendors/process_list', [
            'pageTitle'  => 'Vendor Process List - SafeG',
            'list'       => $list,
            'searchTerm' => $searchTerm,
            'cardType'   => $cardType,
            'sortBy'     => $sortBy,
            'pagination' => ['current_page' => $page, 'last_page' => $lastPage, 'total' => $totalCount],
        ]);
    }
}
