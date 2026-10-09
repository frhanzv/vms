<?php

namespace App\Controllers;

/**
 * The staff pass card pipeline after approval — KPK process-list /
 * issuance-list / closed-list filtered to visitorOrVip = STAFF, in the same
 * shape as the Vendor* list controllers:
 *
 *   Process List  = Approved, not printed yet (no receipt_no / card serial)
 *   Printing List = same set, bulk-print view
 *   Issuance List = printed, card not handed over yet (card_status Inactive)
 *   Closed List   = card issued (Active) or terminated
 *
 * Inactive employees (is_active = 0) are left out of Process / Printing /
 * Issuance — KPK only processes cards for active staff.
 */
class StaffPipeline extends StaffPassBase
{
    private const SORTS = [
        'date_desc'    => ['created_at', 'DESC'],
        'date_asc'     => ['created_at', 'ASC'],
        'name_asc'     => ['full_name', 'ASC'],
        'name_desc'    => ['full_name', 'DESC'],
        'staffno_asc'  => ['staff_no', 'ASC'],
        'staffno_desc' => ['staff_no', 'DESC'],
    ];

    private function notPrinted()
    {
        $b = $this->db()->table('staff')
            ->where('status', 'Approved')
            ->where('card_status', 'Inactive')
            ->groupStart()->where('receipt_no', null)->orWhere('receipt_no', '')->groupEnd();
        if ($this->hasColumn('is_active')) {
            $b->where('is_active', 1);
        }
        return $this->scope($b);
    }

    private function common(): array
    {
        $sort = (string) ($this->request->getGet('sort_by') ?? 'date_desc');
        return [
            trim((string) ($this->request->getGet('search') ?? '')),
            isset(self::SORTS[$sort]) ? $sort : 'date_desc',
            (int) ($this->request->getGet('page') ?? 1),
        ];
    }

    private function rowBasics(array $row, int $no): array
    {
        helper('privacy');
        return [
            'id'                 => (int) $row['id'],
            'no'                 => $no,
            'app_no'             => $row['app_no'] ?: '-',
            'receipt_no'         => $row['receipt_no'] ?: '-',
            'type'               => strtoupper((string) ($row['type_of_application'] ?: 'NEW')),
            'full_name'          => $row['full_name'] ?: '-',
            'staff_no'           => $row['staff_no'] ?: '',
            'department'         => $row['department'] ?: '-',
            'ic_passport_masked' => mask_ic_passport($row['ic_passport'] ?? '', 'N/A'),
            'photo_url'          => $this->photoUrl($row),
            'card_expiry'        => $this->fmtDate($row['card_expiry'] ?? null),
        ];
    }

    // ------------------------------------------------------------------

    public function processList()
    {
        helper(['access', 'feature', 'role']);
        [$search, $sortBy, $page] = $this->common();
        $missing = (string) ($this->request->getGet('missing') ?? '');

        $builder = $this->notPrinted();
        $this->applySearch($builder, $search, ['department']);
        if ($missing === 'photo') {
            $builder->groupStart()->where('photo', null)->orWhere('photo', '')->groupEnd();
        } elseif ($missing === 'staff_no') {
            $builder->groupStart()->where('staff_no', null)->orWhere('staff_no', '')->groupEnd();
        }

        [$field, $dir] = self::SORTS[$sortBy];
        [$rows, $pagination] = $this->paginate($builder, $page, 10, $field, $dir);

        $list = [];
        foreach ($rows as $i => $row) {
            $item = $this->rowBasics($row, ($pagination['current_page'] - 1) * 10 + $i + 1);
            $item['ready'] = $item['photo_url'] !== null && $item['staff_no'] !== '';
            $list[] = $item;
        }

        return view('staffs/process_list', [
            'pageTitle'  => 'Staff Process List - SafeG',
            'list'       => $list,
            'searchTerm' => $search,
            'missing'    => $missing,
            'sortBy'     => $sortBy,
            'pagination' => $pagination,
        ]);
    }

    public function printingList()
    {
        helper(['access', 'feature', 'role']);
        [$search, $sortBy, $page] = $this->common();

        $builder = $this->notPrinted();
        $this->applySearch($builder, $search, ['department']);
        [$field, $dir] = self::SORTS[$sortBy];
        [$rows, $pagination] = $this->paginate($builder, $page, 10, $field, $dir);

        $list = [];
        foreach ($rows as $i => $row) {
            $item = $this->rowBasics($row, ($pagination['current_page'] - 1) * 10 + $i + 1);
            $item['ready']   = $item['photo_url'] !== null && $item['staff_no'] !== '';
            $item['blocker'] = $item['photo_url'] === null ? 'Photo missing' : ($item['staff_no'] === '' ? 'Staff No missing' : null);
            $list[] = $item;
        }

        return view('staffs/printing_list', [
            'pageTitle'  => 'Staff Printing List - SafeG',
            'list'       => $list,
            'searchTerm' => $search,
            'sortBy'     => $sortBy,
            'canPrint'   => $this->canEdit() && $this->cfg('printing_generate_button'),
            'pagination' => $pagination,
        ]);
    }

    /**
     * KPK generateVendorPassCardSerialNo() + doPrint() checks for STAFF:
     * photo required, Staff No required. Re-printing reuses the serial and is
     * logged as a reprint (with a reason when given, e.g. lost card — staff
     * don't need a payment receipt for that, unlike vendors).
     */
    public function generateSerial($id)
    {
        if (! $this->canEdit() || ! $this->cfg('printing_generate_button')) {
            return $this->fail('Not allowed.');
        }
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return $this->fail('Record not found.');
        }
        if (($staff['status'] ?? '') !== 'Approved') {
            return $this->fail('Only an approved staff pass can be printed.');
        }
        if (empty($staff['photo'])) {
            return $this->fail('Please upload a photo for this Pass.');
        }
        if (trim((string) ($staff['staff_no'] ?? '')) === '') {
            return $this->fail('Staff No Missing.');
        }

        $db        = $this->db();
        $isReprint = ! empty($staff['receipt_no']);
        $serial    = $staff['receipt_no'];
        if (! $isReprint) {
            $serial = $this->nextSerialNo();
            $db->table('staff')->where('id', (int) $id)->update($this->onlyColumns(['receipt_no' => $serial, 'updated_at' => date('Y-m-d H:i:s')]));
        }

        $reason = trim((string) ($this->input()['reason'] ?? ''));
        if ($db->tableExists('staff_card_print_logs')) {
            $db->table('staff_card_print_logs')->insert([
                'staff_id'   => (int) $id,
                'receipt_no' => $serial,
                'is_reprint' => $isReprint ? 1 : 0,
                'reason'     => $reason !== '' ? $reason : null,
                'printed_by' => $this->actor(),
                'printed_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->ok('Card ready to print.', ['card' => [
            'id'          => (int) $staff['id'],
            'full_name'   => $staff['name_on_staff_pass'] ?: ($staff['full_name'] ?? ''),
            'staff_no'    => $staff['staff_no'],
            'ic_no'       => $staff['ic_passport'] ?? '',
            'department'  => $staff['department'] ?? '',
            'designation' => $staff['designation'] ?? '',
            'receipt_no'  => $serial,
            'valid_until' => ! empty($staff['card_expiry']) ? date('d M Y', strtotime($staff['card_expiry'])) : '-',
            'photo_url'   => $this->photoUrl($staff) ?? base_url('assets/images/avatar-placeholder.png'),
            'reprint'     => $isReprint,
        ]]);
    }

    /** <YYYYMM><running no> — same serial scheme as the vendor cards, own sequence. */
    private function nextSerialNo(): string
    {
        $prefix = 'S' . date('Ym');
        $count  = (int) $this->db()->table('staff')->like('receipt_no', $prefix, 'after')->countAllResults();
        $serial = $prefix . str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
        while ($this->db()->table('staff')->where('receipt_no', $serial)->countAllResults() > 0) {
            $count++;
            $serial = $prefix . str_pad((string) ($count + 1), 4, '0', STR_PAD_LEFT);
        }
        return $serial;
    }

    // ------------------------------------------------------------------

    public function issuanceList()
    {
        helper(['access', 'feature', 'role']);
        [$search, $sortBy, $page] = $this->common();

        $builder = $this->db()->table('staff')
            ->where('status', 'Approved')
            ->where('card_status', 'Inactive')
            ->where('receipt_no IS NOT NULL', null, false)
            ->where('receipt_no !=', '');
        if ($this->hasColumn('is_active')) {
            $builder->where('is_active', 1);
        }
        $this->scope($builder);
        $this->applySearch($builder, $search, ['receipt_no', 'department']);
        [$field, $dir] = self::SORTS[$sortBy];
        [$rows, $pagination] = $this->paginate($builder, $page, 10, $field, $dir);

        $list = [];
        foreach ($rows as $i => $row) {
            $list[] = $this->rowBasics($row, ($pagination['current_page'] - 1) * 10 + $i + 1);
        }

        return view('staffs/issuance_list', [
            'pageTitle'  => 'Staff Issuance List - SafeG',
            'list'       => $list,
            'searchTerm' => $search,
            'sortBy'     => $sortBy,
            'canIssue'   => $this->canEdit() && $this->cfg('issuance_issue_button'),
            'pagination' => $pagination,
        ]);
    }

    /** KPK issueCards()/finishPortPassProcessing(): record who collected it, activate the card. */
    public function issue($id)
    {
        if (! $this->canEdit() || ! $this->cfg('issuance_issue_button')) {
            return $this->fail('Not allowed.');
        }
        $in   = $this->input();
        $name = trim((string) ($in['collector_name'] ?? ''));
        $ic   = trim((string) ($in['collector_ic_passport'] ?? ''));
        if ($name === '' || $ic === '') {
            return $this->fail('Please enter the collector\'s name and IC/passport before issuing the card.');
        }
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return $this->fail('Record not found.');
        }
        if (empty($staff['receipt_no'])) {
            return $this->fail('This card has not been printed yet.');
        }

        $this->db()->table('staff')->where('id', (int) $id)->update($this->onlyColumns([
            'card_status'           => 'Active',
            'collector_name'        => $name,
            'collector_ic_passport' => $ic,
            'issued_by'             => $this->actor(),
            'issued_at'             => date('Y-m-d H:i:s'),
            'terminated_at'         => null,
            'terminated_by'         => null,
            'updated_at'            => date('Y-m-d H:i:s'),
        ]));
        $this->logAction($staff, 'issue', $staff['status'], null, 'Collected by ' . $name);

        return $this->ok('Card issued to ' . $name . ' — moved to Closed List.');
    }

    // ------------------------------------------------------------------

    public function closedList()
    {
        helper(['access', 'feature', 'privacy', 'role']);
        [$search, $sortBy, $page] = $this->common();
        $f = $this->closedFilters();
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
        if (! in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        $builder = $this->closedQuery($search, $f);
        [$field, $dir] = self::SORTS[$sortBy];
        [$rows, $pagination] = $this->paginate($builder, $page, $perPage, $field, $dir);

        $list = [];
        foreach ($rows as $i => $row) {
            $item = $this->rowBasics($row, ($pagination['current_page'] - 1) * $perPage + $i + 1);
            $item += [
                'app_date'       => $this->fmtDate($row['created_at'] ?? null),
                'card_status'    => $row['card_status'],
                'status'         => $row['status'],
                'is_active'      => (int) ($row['is_active'] ?? 1) === 1,
                'collector_name' => $row['collector_name'] ?: '-',
                'issued_at'      => $this->fmtDate($row['issued_at'] ?? null, 'd/m/Y H:i'),
                'can_renew'      => ($row['status'] ?? '') === 'Approved',
            ];
            $list[] = $item;
        }

        return view('staffs/closed_list', [
            'pageTitle'      => 'Staff Pass Closed List - SafeG',
            'closedList'     => $list,
            'canExport'      => $this->cfg('closed_export_button'),
            'canCardDetails' => $this->cfg('closed_card_details_button'),
            'canRenew'       => $this->canEdit() && $this->cfg('renew_button'),
            'searchTerm'     => $search,
            'sortBy'         => $sortBy,
            'filters'        => $f,
            'pagination'     => $pagination,
        ]);
    }

    public function closedExport()
    {
        helper(['privacy']);
        $search = trim((string) ($this->request->getGet('search') ?? ''));
        $rows   = $this->closedQuery($search, $this->closedFilters())->orderBy('created_at', 'DESC')->get()->getResultArray();

        $out = fopen('php://temp', 'w+');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['No', 'App No', 'Card Serial', 'Staff No', 'Full Name', 'IC/Passport', 'Department', 'Designation',
            'Card Status', 'Card Expiry', 'Pass Status', 'Employee', 'Collected By', 'Issued At']);
        foreach ($rows as $i => $r) {
            fputcsv($out, [
                $i + 1, $r['app_no'] ?? '', $r['receipt_no'] ?? '', $r['staff_no'] ?? '', $r['full_name'] ?? '',
                mask_ic_passport($r['ic_passport'] ?? ''), $r['department'] ?? '', $r['designation'] ?? '',
                $r['card_status'] ?? '', $this->fmtDate($r['card_expiry'] ?? null), $r['status'] ?? '',
                (int) ($r['is_active'] ?? 1) === 1 ? 'Active' : 'Inactive',
                $r['collector_name'] ?? '', $this->fmtDate($r['issued_at'] ?? null, 'd/m/Y H:i'),
            ]);
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="staff-closed-list-' . date('Y-m-d-His') . '.csv"')
            ->setBody((string) $csv);
    }

    private function closedFilters(): array
    {
        $g = fn(string $k, string $d = '') => trim((string) ($this->request->getGet($k) ?? $d));
        $f = [
            'issue_date_from' => $g('issue_date_from'),
            'issue_date_to'   => $g('issue_date_to'),
            'card_status'     => $g('card_status', 'all'),
            'card_expiry'     => $g('card_expiry'),
            'department'      => $g('department'),
            'employee'        => $g('employee', 'all'),
        ];
        foreach (['issue_date_from', 'issue_date_to', 'card_expiry'] as $k) {
            if ($f[$k] !== '' && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $f[$k])) {
                $f[$k] = '';
            }
        }
        if (! in_array($f['card_status'], ['all', 'Active', 'Terminated', 'Suspended'], true)) {
            $f['card_status'] = 'all';
        }
        if (! in_array($f['employee'], ['all', 'active', 'inactive'], true)) {
            $f['employee'] = 'all';
        }
        return $f;
    }

    private function closedQuery(string $search, array $f)
    {
        // A suspended pass has its card switched off but is still a closed
        // (issued) pass, so it stays on this list. Staff cards issued before
        // the pipeline existed have no serial, so the serial isn't required.
        $b = $this->db()->table('staff')
            ->groupStart()
                ->whereIn('card_status', ['Active', 'Terminated'])
                ->orWhere('status', 'Suspended')
            ->groupEnd();
        $this->scope($b);
        $this->applySearch($b, $search, ['receipt_no', 'department']);
        if ($f['issue_date_from'] !== '') {
            $b->where('DATE(issued_at) >=', $f['issue_date_from']);
        }
        if ($f['issue_date_to'] !== '') {
            $b->where('DATE(issued_at) <=', $f['issue_date_to']);
        }
        if ($f['card_status'] === 'Suspended') {
            $b->where('status', 'Suspended');
        } elseif ($f['card_status'] !== 'all') {
            $b->where('card_status', $f['card_status'])->where('status !=', 'Suspended');
        }
        if ($f['card_expiry'] !== '') {
            $b->where('DATE(card_expiry)', $f['card_expiry']);
        }
        if ($f['department'] !== '') {
            $b->where('department', $f['department']);
        }
        if ($f['employee'] !== 'all' && $this->hasColumn('is_active')) {
            $b->where('is_active', $f['employee'] === 'active' ? 1 : 0);
        }
        return $b;
    }
}
