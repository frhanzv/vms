<?php

namespace App\Controllers;

/**
 * Printing List stage — rebuilt to follow KPK's real printing-list.component.ts
 * more closely, per instruction: "the printing list make sure to follow
 * accordingly."
 *
 * What KPK's real page actually does (traced from printing-list.component.ts
 * + VendorPassServiceImpl.generateVendorPassCardSerialNo/generatePortPassCardSerialNo):
 *  - Lists Approved passes waiting to be carded (their query keys off
 *    NEXTACTION = 'process'; we key off card_type being chosen but not yet
 *    printed, since our schema doesn't carry a separate next_action value
 *    for every KPK sub-stage — same effect, one field fewer).
 *  - Each row has a checkbox; "Select all" is supported.
 *  - Printing a card runs a serial-number generator server-side
 *    (format: <YYYYMM><running id for that month>) and stores it — that's
 *    what "Receipt No" is in KPK's table.
 *  - A row that already has a serial number shows a "Printed" badge but can
 *    still be re-printed (KPK allows reprints too).
 *  - The printed artifact is a physical card sized to a CR80 card
 *    (54mm x 85.6mm) with the person's photo, name, IC/passport and card
 *    validity — KPK renders this from proprietary government card artwork
 *    (vendor_permanent.jpg / vendor_temporary.jpg) which we don't have and
 *    isn't ours to copy, so the view below draws an original card design of
 *    the same physical size and fields, color-coded by card_type the same
 *    way KPK's asset split works (Permanent vs Temporary).
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
                'card_type'           => $row['card_type'] ?? '-',
                'receipt_no'          => $row['receipt_no'] ?? null,
                'printed'             => ! empty($row['receipt_no']),
                'photo_url'           => $photoUrl,
            ];
        }

        return view('vendors/printing_list', [
            'pageTitle'  => 'Vendor Printing List - SafeG',
            'list'       => $list,
            'searchTerm' => $searchTerm,
            'canPrint'   => has_access('vendor_pass_list', 'edit'),
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
     * one, so printing never duplicates numbers.
     */
    public function generateSerial($id)
    {
        helper(['access', 'privacy']);
        if (! has_access('vendor_pass_list', 'edit')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('vendors')->where('id', (int) $id);
        if (! is_platform_superadmin()) {
            $builder->where('company_id', current_company_id());
        }
        $vendor = $builder->get()->getRowArray();

        if (! $vendor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Record not found.']);
        }
        if (empty($vendor['card_type'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'This record has no card type assigned yet — send it through Process List first.']);
        }

        $isReprint = ! empty($vendor['receipt_no']);
        $serial    = $vendor['receipt_no'];
        if (empty($serial)) {
            $serial = $this->nextSerialNo($db);
            $db->table('vendors')->where('id', (int) $id)->update(['receipt_no' => $serial]);
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
                'ic_no'       => $vendor['ic_no'] ?: ($vendor['passport_no'] ?? ''),
                'company'     => $vendor['vendor_company_name'] ?? '',
                'card_type'   => $vendor['card_type'],
                'receipt_no'  => $serial,
                'valid_until' => $vendor['pass_expiry'] ? date('d M Y', strtotime($vendor['pass_expiry'])) : '-',
                'photo_url'   => $photoUrl,
            ],
        ]);
    }

    /**
     * <YYYYMM><running id>, matching Constant currentYearMonth + runningId
     * used for real KPK card serial numbers. The running id restarts every
     * month and is derived from however many serials already exist for
     * this month, rather than a separate sequence table.
     */
    private function nextSerialNo($db): string
    {
        $prefix = date('Ym');
        $count  = (int) $db->table('vendors')
            ->like('receipt_no', $prefix, 'after')
            ->countAllResults();

        return $prefix . str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
    }
}
