<?php

namespace App\Controllers;

/**
 * Printing List stage — per the real KPK workflow (confirmed directly by
 * the user): this shows the exact same "approved, not yet printed" set as
 * the Process List — it's not a separate sequential stage, just a
 * bulk-print-focused view onto the same records. Whichever page actually
 * prints a record first (Process Detail's Print button, or this page's
 * row/bulk print) is what moves it out of both lists and into the
 * Issuance List, which is why the query below no longer requires a card
 * type to already be assigned — assigning one can happen from either page
 * too.
 */
class VendorPrintingList extends BaseController
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

        // Printed cards stay on this list until they are issued (there is no Issuance List)
        $builder = $db->table('vendors')
            ->where('status', 'Approved')
            ->where("COALESCE(card_status, 'Inactive') <> 'Active'", null, false);
        // Vendor Workflow: Card Processing off -> nothing waits here.
        if (! \App\Libraries\VendorWorkflow::enabled($db, (int) current_company_id(), 'card_processing')) {
            $builder->where('1 = 0', null, false);
        }

        helper('vendor_client_scope');
        vendor_client_scope($builder);
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
            $photoFile = $row['facial_photo'] ?: $row['photo'];
            $photoUrl  = $photoFile
                ? base_url('uploads/' . ($row['facial_photo'] ? 'facial_photos' : 'vendor_photos') . '/' . $photoFile)
                : null;

            $list[] = [
                'id'                  => $row['id'],
                'no'                  => ($page - 1) * $perPage + $i + 1,
                'app_no'              => $row['app_no'] ?? 'N/A',
                'full_name'           => $row['full_name'] ?? 'N/A',
                'vendor_company_name' => $row['vendor_company_name'] ?? 'N/A',
                'ic_passport_masked'  => mask_ic_passport($row['ic_no'] ?: ($row['passport_no'] ?? ''), 'N/A'),
                'card_type'           => $row['card_type'] ?? 'Not assigned',
                'receipt_no'          => $row['receipt_no'] ?? '',
                'photo_url'           => $photoUrl,
            ];
        }

        return view('vendors/printing_list', [
            'pageTitle'  => 'Vendor Printing List - SafeG',
            'list'       => $list,
            'searchTerm' => $searchTerm,
            'cardType'   => $cardType,
            'sortBy'     => $sortBy,
            'canPrint'   => has_access('vendor_pass_list', 'edit')
                && (new \App\Models\ClientFormFieldModel())->isEnabled(current_company_id(), 'vendor_pass_request', 'printing_generate_button'),
            'canIssue'   => VendorCardIssue::allowed(),
            'pagination' => ['current_page' => $page, 'last_page' => $lastPage, 'total' => $totalCount],
        ]);
    }

    /**
     * Generates the card serial number (KPK calls this "Receipt No" for
     * vendor rows) and returns everything the browser needs to draw and
     * print the physical card — mirrors generateVendorPassCardSerialNo(),
     * which does the same two things (allocate a serial, hand back the
     * data for the printable card) in one call.
     *
     * Re-printing an already-serialed card is allowed (KPK allows this too
     * — see printCards() calling generateCard again on already-printed
     * rows) — it just returns the existing serial instead of making a new
     * one, so printing never duplicates numbers. This matters because this
     * same endpoint is reused by the Card Info page's "Reprint" button,
     * which calls it on records that already left Process/Printing List
     * for the Issuance/Closed List.
     */
    public function generateSerial($id)
    {
        helper(['access', 'privacy']);
        if (! has_access('vendor_pass_list', 'edit')
            || ! (new \App\Models\ClientFormFieldModel())->isEnabled(current_company_id(), 'vendor_pass_request', 'printing_generate_button')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('vendors')->where('id', (int) $id);
        helper('vendor_client_scope');
        vendor_client_scope($builder);
        $vendor = $builder->get()->getRowArray();

        if (! $vendor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Record not found.']);
        }
        if (empty($vendor['card_type'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'This record has no card type assigned yet — send it through Process List first.']);
        }

        if (empty($vendor['facial_photo']) && empty($vendor['photo'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Upload the card photo first.']);
        }
        if (empty($vendor['pass_expiry'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Save the expiry date in Card Details first.']);
        }

        // From the Process Detail page the receipt no is only saved when Finish
        // (Scan Card) is pressed, so printing there keeps the record in Process List.
        $defer = ! empty(($this->request->getJSON(true) ?? [])['defer']);

        $lastLog = $db->table('vendor_card_print_logs')->where('vendor_id', (int) $id)->orderBy('id', 'DESC')->get()->getRowArray();
        $serial  = $vendor['receipt_no'] ?: ($defer ? ($lastLog['receipt_no'] ?? null) : null);
        $isReprint = ! empty($serial);
        if (empty($serial)) {
            $serial = $this->nextSerialNo($db);
            if (! $defer) {
                $db->table('vendors')->where('id', (int) $id)->update(['receipt_no' => $serial]);
            }
        }

        // KPK logs every print/reprint (viewReprintHistoryDetails) — do the same here.
        $db->table('vendor_card_print_logs')->insert([
            'vendor_id'  => (int) $id,
            'receipt_no' => $serial,
            'is_reprint' => $isReprint ? 1 : 0,
            'printed_by' => (string) (session()->get('full_name') ?: session()->get('username')),
            'printed_at' => date('Y-m-d H:i:s'),
        ]);

        $photoFile = $vendor['facial_photo'] ?: $vendor['photo'];
        $photoUrl  = $photoFile
            ? base_url('uploads/' . ($vendor['facial_photo'] ? 'facial_photos' : 'vendor_photos') . '/' . $photoFile)
            : base_url('assets/images/avatar-placeholder.png');

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Card ready to print.',
            'card'    => [
                'id'          => $vendor['id'],
                'full_name'   => $vendor['full_name'] ?? '',
                'display_name' => trim((string) ($vendor['name_on_vendor_pass'] ?? '')) !== '' ? $vendor['name_on_vendor_pass'] : ($vendor['full_name'] ?? ''),
                'ic_no'       => $vendor['ic_no'] ?: ($vendor['passport_no'] ?? ''),
                'company'     => $vendor['vendor_company_name'] ?? '',
                'card_type'   => $vendor['card_type'],
                'receipt_no'  => $serial,
                'valid_until' => $vendor['pass_expiry'] ? date('d M Y', strtotime($vendor['pass_expiry'])) : '-',
                'valid_until_upper' => $vendor['pass_expiry'] ? strtoupper(date('d M Y', strtotime($vendor['pass_expiry']))) : '-',
                'type_label'  => ['permanent' => 'TETAP', 'temporary' => 'SEMENTARA'][strtolower((string) $vendor['card_type'])] ?? strtoupper((string) $vendor['card_type']),
                'ic_display'  => $this->formatIc((string) ($vendor['ic_no'] ?: ($vendor['passport_no'] ?? ''))),
                'access_code' => $this->accessLabels((string) ($vendor['location_access'] ?? '')),
                'issuer'      => (string) (function_exists('current_client_name') ? (current_client_name() ?: '') : ''),
                'photo_url'   => $photoUrl,
                'templates'   => \App\Libraries\CardTemplates::resolveAll($db, 'vendor', function_exists('current_client_id') ? (int) current_client_id() ?: null : null),
            ],
        ]);
    }

    /**
     * <YYYYMM><running id>, matching Constant currentYearMonth + runningId
     * used for real KPK card serial numbers. The running id restarts every
     * month and is derived from however many serials already exist for
     * this month, rather than a separate sequence table.
     */
    /** 12-digit IC shown as 710619-11-5677; anything else (passport) unchanged. */
    private function formatIc(string $v): string
    {
        return preg_match('/^\d{12}$/', $v) === 1 ? substr($v, 0, 6) . '-' . substr($v, 6, 2) . '-' . substr($v, 8) : $v;
    }

    /** Location access as the readable names, e.g. "KPK GATE / PHASE 1". */
    private function accessLabels(string $csv): string
    {
        $labels = (new \App\Models\VendorLocationModel())->getAllLabelMap();
        $out = [];
        foreach (array_filter(array_map('trim', explode(',', $csv))) as $code) {
            $out[] = $labels[$code] ?? $code;
        }

        return implode(' / ', $out);
    }

    private function nextSerialNo($db): string
    {
        $prefix = date('Ym');
        // Highest serial already used this month - on a pass or in the print log
        // (a serial printed from Process Detail is not on the pass until Finish).
        $max = 0;
        foreach (['vendors', 'vendor_card_print_logs'] as $table) {
            $row = $db->table($table)->select('MAX(receipt_no) AS m', false)->like('receipt_no', $prefix, 'after')->get()->getRowArray();
            if (! empty($row['m'])) {
                $max = max($max, (int) substr((string) $row['m'], strlen($prefix)));
            }
        }

        return $prefix . str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }
}
