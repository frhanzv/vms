<?php

namespace App\Controllers;

/**
 * Staff Pass List — KPK "Staff List" (getVendorPassStaffList) and
 * "Inactive Staff List" (getPortPassStaffInactiveListPage), plus the
 * approve / reject step KPK runs from the port-pass Request List for
 * visitorOrVip = STAFF.
 *
 *  APPROVE  - allowed from Pending or Rejected; blocked if blacklisted.
 *           - BOTH-branch passes need a KSB and a KPK approver (two steps),
 *             same as the vendor pipeline (see VendorList for the details).
 *           - approving a RENEWAL sends the pass back through Printing /
 *             Issuance for a new card.
 *           - optional "direct close" activates the card immediately.
 *  REJECT   - allowed from Pending, Approved or Rejected, reason required.
 *  ACTIVE / INACTIVE - KPK's IS_ACTIVE flag (employee still with company).
 *  CHANGE STAFF NO   - KPK changeStaffNo(): must be unique.
 */
class StaffList extends StaffPassBase
{
    private array $tierCache = [];

    private const SORTS = [
        'date_desc'     => ['created_at', 'DESC'],
        'date_asc'      => ['created_at', 'ASC'],
        'name_asc'      => ['full_name', 'ASC'],
        'name_desc'     => ['full_name', 'DESC'],
        'staffno_asc'   => ['staff_no', 'ASC'],
        'staffno_desc'  => ['staff_no', 'DESC'],
    ];

    private const STATUSES = ['all', 'Draft', 'Pending', 'Approved', 'Rejected', 'Suspended'];

    public function index()
    {
        helper(['access', 'feature', 'privacy', 'role']);

        $tab     = $this->request->getGet('tab') === 'inactive' ? 'inactive' : 'active';
        $search  = trim((string) ($this->request->getGet('search') ?? ''));
        $status  = (string) ($this->request->getGet('status') ?? 'all');
        $type    = strtoupper((string) ($this->request->getGet('type') ?? 'ALL'));
        $sortBy  = (string) ($this->request->getGet('sort') ?? 'date_desc');
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);

        if (! in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }
        if (! isset(self::SORTS[$sortBy])) {
            $sortBy = 'date_desc';
        }
        if (! in_array($status, self::STATUSES, true)) {
            $status = 'all';
        }
        if (! in_array($type, ['ALL', 'NEW', 'RENEWAL', 'REPLACEMENT'], true)) {
            $type = 'ALL';
        }

        $builder = $this->listQuery($tab, $search, $status, $type);
        [$sortField, $sortDir] = self::SORTS[$sortBy];
        [$rows, $pagination] = $this->paginate($builder, $page, $perPage, $sortField, $sortDir);

        $showApprove = $this->cfg('approve_button');
        $showReject  = $this->cfg('reject_button');
        $awaiting    = ['ksb_approve' => 'Awaiting KSB approval', 'kpk_approve' => 'Awaiting KPK approval'];

        $list   = [];
        $offset = ($pagination['current_page'] - 1) * $perPage;

        // Shared product: which clients still have to approve each pass on this page.
        $approvalRows = $this->approvalsReady()
            ? \App\Libraries\StaffClientApprovals::rowsForVendors($this->db(), array_map('intval', array_column($rows, 'id')))
            : [];
        $myClientId = is_platform_superadmin() ? null : (int) current_client_id();

        foreach ($rows as $i => $row) {
            $rowStatus = $row['status'] ?: 'Pending';
            $cRows     = $approvalRows[(int) $row['id']] ?? [];
            $awaitingText = $awaiting[$row['next_action'] ?? ''] ?? null;
            if ($cRows) {
                $names = array_column(array_filter($cRows, static fn($r) => $r['status'] !== 'Approved'), 'client_name');
                $awaitingText = ($names && $rowStatus === 'Pending') ? 'Awaiting ' . implode(' & ', $names) . ' approval' : null;
            }
            $list[] = [
                'id'           => (int) $row['id'],
                'no'           => $offset + $i + 1,
                'date'         => $this->fmtDate($row['created_at'] ?? null),
                'app_no'       => $row['app_no'] ?: '-',
                'type'         => strtoupper((string) ($row['type_of_application'] ?: 'NEW')),
                'full_name'    => $row['full_name'] ?: '-',
                'ic_passport'  => $row['ic_passport'] ?? '',
                'staff_no'     => $row['staff_no'] ?: '-',
                'department'   => $row['department'] ?: '-',
                'status'       => $rowStatus,
                'awaiting'     => $awaitingText,
                'client_approvals' => $cRows,
                'reject_reason'=> $row['reject_reason'] ?? null,
                'card_status'  => $row['card_status'] ?: 'Inactive',
                'card_expiry'  => $this->fmtDate($row['card_expiry'] ?? null),
                'is_active'    => (int) ($row['is_active'] ?? 1) === 1,
                'can_approve'  => $showApprove && in_array($rowStatus, ['Pending', 'Rejected'], true)
                    && ($cRows ? $this->canActPerClient($cRows, $myClientId, 'approve') : $this->canActOn($row, 'approve')),
                'can_reject'   => $showReject && in_array($rowStatus, ['Pending', 'Approved', 'Rejected'], true)
                    && ($cRows ? $this->canActPerClient($cRows, $myClientId, 'reject') : $this->canActOn($row, 'reject')),
                'can_edit_row' => in_array($rowStatus, ['Draft', 'Pending', 'Rejected'], true) || is_platform_superadmin() || is_client_superadmin(),
            ];
        }

        $count = fn(string $t, string $s) => (int) $this->listQuery($t, '', $s, 'ALL')->countAllResults();

        return view('staffs/list', [
            'pageTitle'     => 'Staff Pass List - SafeG',
            'tab'           => $tab,
            'stats'         => [
                'total'    => $count('active', 'all'),
                'pending'  => $count('active', 'Pending'),
                'approved' => $count('active', 'Approved'),
                'inactive' => $count('inactive', 'all'),
            ],
            'staffList'     => $list,
            'rejectReasons' => $this->rejectReasons(),
            'canRequest'    => $this->cfg('request_button'),
            'canEdit'       => $this->canEdit() && $this->cfg('edit_button'),
            'canDelete'     => has_access('staff_pass_list', 'delete') && $this->cfg('delete_button'),
            'canImport'     => $this->canEdit() && $this->cfg('import_button'),
            'canExport'     => $this->cfg('export_button'),
            'canManage'     => $this->canManageStatus(),
            'showPrintButton' => $this->cfg('print_button') && has_access('staff_pass_list', 'print'),
            'searchTerm'    => $search,
            'status'        => $status,
            'type'          => $type,
            'sortBy'        => $sortBy,
            'pagination'    => $pagination,
        ]);
    }

    private function listQuery(string $tab, string $search, string $status, string $type)
    {
        $builder = $this->db()->table('staff')->select('*');
        $this->scope($builder);

        if ($this->hasColumn('is_active')) {
            $builder->where('is_active', $tab === 'inactive' ? 0 : 1);
        }
        $this->applySearch($builder, $search, ['department']);
        if ($status !== 'all') {
            $builder->where('status', $status);
        }
        if ($type !== 'ALL') {
            $builder->where('UPPER(type_of_application)', $type);
        }
        return $builder;
    }

    // ------------------------------------------------------------------
    //  Delete / Export
    // ------------------------------------------------------------------

    public function delete($id)
    {
        helper(['access', 'role']);
        if (! has_access('staff_pass_list', 'delete')) {
            return $this->response->setStatusCode(403)
                ->setJSON(['success' => false, 'message' => 'You are not allowed to delete staff records.']);
        }
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return $this->fail('Staff record not found.');
        }

        $db = $this->db();
        $db->transStart();
        foreach (['staff_driving_licenses', 'staff_card_print_logs', 'staff_status_logs'] as $child) {
            if ($db->tableExists($child)) {
                $db->table($child)->where('staff_id', (int) $id)->delete();
            }
        }
        if (! empty($staff['card_id'])) {
            $db->table('visitor_cards')->where('id', (int) $staff['card_id'])->where('status', 'in_use')->update(['status' => 'active']);
        }
        $db->table('staff')->where('id', (int) $id)->delete();
        $db->transComplete();

        return $this->ok('Staff record deleted.');
    }

    public function export()
    {
        helper(['feature', 'privacy', 'role']);
        $tab    = $this->request->getGet('tab') === 'inactive' ? 'inactive' : 'active';
        $status = (string) ($this->request->getGet('status') ?? 'all');
        $type   = strtoupper((string) ($this->request->getGet('type') ?? 'ALL'));
        $rows   = $this->listQuery(
            $tab,
            trim((string) ($this->request->getGet('search') ?? '')),
            in_array($status, self::STATUSES, true) ? $status : 'all',
            in_array($type, ['ALL', 'NEW', 'RENEWAL', 'REPLACEMENT'], true) ? $type : 'ALL'
        )->orderBy('created_at', 'DESC')->get()->getResultArray();

        $out = fopen('php://temp', 'w+');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['No', 'Date', 'App No', 'Type', 'Staff No', 'Full Name', 'IC/Passport', 'Department',
            'Designation', 'Contact No', 'Email', 'Status', 'Card Status', 'Card Expiry', 'Employee', 'Remark']);
        foreach ($rows as $i => $r) {
            fputcsv($out, [
                $i + 1,
                $this->fmtDate($r['created_at'] ?? null),
                $r['app_no'] ?? '',
                strtoupper((string) ($r['type_of_application'] ?? '')),
                $r['staff_no'] ?? '',
                $r['full_name'] ?? '',
                mask_ic_passport($r['ic_passport'] ?? ''),
                $r['department'] ?? '',
                $r['designation'] ?? '',
                ! empty($r['contact_number']) ? '="' . $r['contact_number'] . '"' : '',
                $r['email'] ?? '',
                $r['status'] ?? '',
                $r['card_status'] ?? '',
                $this->fmtDate($r['card_expiry'] ?? null),
                (int) ($r['is_active'] ?? 1) === 1 ? 'Active' : 'Inactive',
                $r['remark'] ?? '',
            ]);
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="staff-pass-list-' . date('Y-m-d-His') . '.csv"')
            ->setBody((string) $csv);
    }

    // ------------------------------------------------------------------
    //  Approve / Reject
    // ------------------------------------------------------------------

    public function approve()
    {
        helper(['access', 'feature', 'role']);
        $in     = $this->input();
        $id     = (int) ($in['id'] ?? 0);
        $remark = trim((string) ($in['remark'] ?? ''));

        $staff = $id > 0 ? $this->loadScopedStaff($id) : null;
        if (! $staff) {
            return $this->fail('Staff pass record not found.');
        }
        $db0       = $this->db();
        $perClient = $this->approvalsReady() && \App\Libraries\StaffClientApprovals::hasRows($db0, (int) $staff['id']);
        $myClient  = is_platform_superadmin() ? null : (int) current_client_id();
        if ($perClient) {
            if (! $this->canActPerClient(\App\Libraries\StaffClientApprovals::rows($db0, (int) $staff['id']), $myClient, 'approve')) {
                return $this->fail('You cannot approve this pass: none of its locations belong to your client, or your client has already approved it.');
            }
        } elseif (! $this->canActOn($staff, 'approve')) {
            return $this->fail($this->notAllowedMessage($staff, 'approve'));
        }
        if (! in_array($staff['status'], ['Pending', 'Rejected'], true)) {
            return $this->fail('Only Pending or Rejected records can be approved (current status: ' . $staff['status'] . '). Please refresh the page.');
        }

        $db = $this->db();
        $idNo = trim((string) ($staff['ic_passport'] ?? ''));
        if ($idNo !== '' && $db->tableExists('blacklist')) {
            $hit = $db->table('blacklist')->where('ic_passport_no', $idNo)->where('status', 'active')->countAllResults();
            if ($hit > 0) {
                return $this->fail('IC / Passport is Blacklisted. This pass cannot be approved.');
            }
        }

        if ($perClient) {
            return $this->approvePerClient($staff, $myClient, $remark);
        }

        $tiers  = $this->approverTiers('approve');
        $branch = strtoupper(trim((string) ($staff['access_branch'] ?? '')));
        $next   = ($staff['next_action'] ?? null) ?: null;

        $update  = ['remark' => $remark !== '' ? $remark : ($staff['remark'] ?? null)];
        $action  = 'approve';
        $message = 'Staff pass approved successfully.';

        $needsSecondStep = ! ($tiers['super'] || $tiers['generic'])
            && $branch === 'BOTH' && $next === null && ($tiers['ksb'] xor $tiers['kpk']);

        if ($needsSecondStep) {
            $update['next_action'] = $tiers['kpk'] ? 'ksb_approve' : 'kpk_approve';
            $action  = 'first_approve';
            $message = 'Your branch has approved. Now awaiting ' . ($tiers['kpk'] ? 'KSB' : 'KPK') . ' approval.';
        } else {
            $update['status']        = 'Approved';
            $update['next_action']   = null;
            $update['reject_reason'] = null;

            // Renewal: a new card goes through Printing / Issuance again.
            $hadCard = ! empty($staff['receipt_no']) || in_array($staff['card_status'] ?? '', ['Active', 'Terminated'], true);
            if (strtoupper((string) ($staff['type_of_application'] ?? 'NEW')) !== 'NEW' && $hadCard) {
                $update['receipt_no']  = null;
                $update['card_status'] = 'Inactive';
                $message .= ' Sent to Process List for a new card.';
            }

            if ($this->cfg('direct_close')) {
                $update['card_status'] = 'Active';
                $update['issued_at']   = date('Y-m-d H:i:s');
                $update['issued_by']   = $this->actor();
                $message .= ' Card activated (direct close).';
            }
        }

        if (! $this->applyTransition($staff, $update)) {
            return $this->fail('This record has already been processed by another user. Please refresh the page.');
        }
        $this->logAction($staff, $action, $update['status'] ?? $staff['status'], $update['next_action'] ?? null, $remark);

        return $this->ok($message);
    }

    public function reject()
    {
        helper(['access', 'feature', 'role']);
        $in       = $this->input();
        $id       = (int) ($in['id'] ?? 0);
        $remark   = trim((string) ($in['remark'] ?? ''));
        $reasonId = (int) ($in['reject_reason_id'] ?? 0);

        $staff = $id > 0 ? $this->loadScopedStaff($id) : null;
        if (! $staff) {
            return $this->fail('Staff pass record not found.');
        }
        $db0       = $this->db();
        $perClient = $this->approvalsReady() && \App\Libraries\StaffClientApprovals::hasRows($db0, (int) $staff['id']);
        $myClient  = is_platform_superadmin() ? null : (int) current_client_id();
        if ($perClient) {
            if (! $this->canActPerClient(\App\Libraries\StaffClientApprovals::rows($db0, (int) $staff['id']), $myClient, 'reject')) {
                return $this->fail('You cannot reject this pass: none of its locations belong to your client.');
            }
        } elseif (! $this->canActOn($staff, 'reject')) {
            return $this->fail($this->notAllowedMessage($staff, 'reject'));
        }
        if (! in_array($staff['status'], ['Pending', 'Approved', 'Rejected'], true)) {
            return $this->fail('This record cannot be rejected from status "' . $staff['status'] . '". Please refresh the page.');
        }

        $reasonText = null;
        if ($reasonId > 0) {
            $row = $this->db()->table('reject_reasons')->select('reason')->where('id', $reasonId)->where('status', 'active')->get()->getRowArray();
            $reasonText = $row['reason'] ?? null;
        }
        if ($reasonText === null) {
            return $this->fail('Please select a reject reason.');
        }

        $update = [
            'status'        => 'Rejected',
            'next_action'   => null,
            'remark'        => $remark !== '' ? $remark : ($staff['remark'] ?? null),
            'reject_reason' => $reasonText,
        ];
        // A brand-new pass has no live card; a renewal keeps its old card until a new one replaces it.
        if (! in_array($staff['card_status'] ?? '', ['Active', 'Terminated'], true)) {
            $update['card_status'] = 'Inactive';
        }

        if (! $this->applyTransition($staff, $update)) {
            return $this->fail('This record has already been processed by another user. Please refresh the page.');
        }
        if ($perClient) {
            \App\Libraries\StaffClientApprovals::decide($db0, (int) $staff['id'], $myClient, 'Rejected', $this->actor(), $reasonText);
            $remark = trim(($myClient === null ? '[All clients] ' : '[' . $this->clientName($db0, $myClient) . '] ') . $remark);
        }
        $this->logAction($staff, 'reject', 'Rejected', null, $remark, $reasonText);

        return $this->ok('Staff pass rejected successfully.');
    }

    // ------------------------------------------------------------------
    //  Shared product: each client approves its own gates
    // ------------------------------------------------------------------

    /** Approve as one client (or, with $clientId null, as platform superadmin for every involved client). */
    private function approvePerClient(array $staff, ?int $clientId, string $remark)
    {
        $db = $this->db();
        $id = (int) $staff['id'];
        [$ok, $msg, $all] = \App\Libraries\StaffClientApprovals::decide($db, $id, $clientId, 'Approved', $this->actor(), $remark);
        if (! $ok) {
            return $this->fail($msg);
        }

        $overall = \App\Libraries\StaffClientApprovals::overallStatus($db, $id) ?? 'Pending';
        $update  = ['status' => $overall, 'next_action' => null, 'remark' => $remark !== '' ? $remark : ($staff['remark'] ?? null)];
        $message = $all
            ? 'Staff pass approved by every involved client.'
            : 'Your client has approved. ' . (\App\Libraries\StaffClientApprovals::awaitingLabel($db, $id) ?? '');

        if ($overall === 'Approved') {
            $update['reject_reason'] = null;
            // Renewal: a new card goes through Printing / Issuance again.
            $hadCard = ! empty($staff['receipt_no']) || in_array($staff['card_status'] ?? '', ['Active', 'Terminated'], true);
            if (strtoupper((string) ($staff['type_of_application'] ?? 'NEW')) !== 'NEW' && $hadCard) {
                $update['receipt_no']  = null;
                $update['card_status'] = 'Inactive';
                $message .= ' Sent to Process List for a new card.';
            }
            if ($this->cfg('direct_close')) {
                $update['card_status'] = 'Active';
                $update['issued_at']   = date('Y-m-d H:i:s');
                $update['issued_by']   = $this->actor();
                $message .= ' Card activated (direct close).';
            }
        }

        if (! $this->applyTransition($staff, $update)) {
            return $this->fail('This record has already been processed by another user. Please refresh the page.');
        }
        $label = $clientId === null ? '[All clients] ' : '[' . $this->clientName($db, $clientId) . '] ';
        $this->logAction($staff, $all ? 'approve' : 'client_approve', $overall, null, trim($label . $remark));

        return $this->ok($message);
    }

    /**
     * May this user approve/reject given the clients involved in the pass?
     * Platform superadmin (clientId null) always may. A client user needs the usual
     * approve/reject permission (any tier) AND an involved client row; to approve,
     * that row must not already be Approved.
     *
     * @param list<array{client_id:int,status:string}> $rows
     */
    private function canActPerClient(array $rows, ?int $clientId, string $action): bool
    {
        if ($clientId === null) {
            return true;
        }
        $t = $this->approverTiers($action);
        if (! ($t['super'] || $t['generic'] || $t['ksb'] || $t['kpk'])) {
            return false;
        }
        foreach ($rows as $r) {
            if ((int) $r['client_id'] === $clientId) {
                return $action === 'reject' || in_array($r['status'], ['Pending', 'Rejected'], true);
            }
        }

        return false;
    }

    private function clientName($db, int $clientId): string
    {
        $row = $db->table('clients')->select('name')->where('id', $clientId)->get()->getRowArray();

        return (string) ($row['name'] ?? ('Client ' . $clientId));
    }

    // ------------------------------------------------------------------
    //  Active / Inactive employee + Change Staff No
    // ------------------------------------------------------------------

    public function setActive($id)
    {
        if (! $this->canManageStatus()) {
            return $this->fail('You are not allowed to change the employee status.');
        }
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return $this->fail('Staff record not found.');
        }
        $in     = $this->input();
        $active = ! empty($in['active']) ? 1 : 0;
        $remark = trim((string) ($in['remark'] ?? ''));

        $this->db()->table('staff')->where('id', (int) $id)->update($this->onlyColumns([
            'is_active'  => $active,
            'updated_at' => date('Y-m-d H:i:s'),
        ]));
        $this->logAction($staff, $active ? 'set_active' : 'set_inactive', $staff['status'] ?? null, null, $remark);

        return $this->ok($active ? 'Staff moved back to the active list.' : 'Staff moved to the inactive list.');
    }

    public function changeStaffNo($id)
    {
        if (! $this->canEdit()) {
            return $this->fail('You are not allowed to change the staff number.');
        }
        $staff = $this->loadScopedStaff((int) $id);
        if (! $staff) {
            return $this->fail('Staff record not found.');
        }
        $staffNo = trim((string) ($this->input()['staff_no'] ?? ''));
        if ($staffNo === '') {
            return $this->fail('Please enter the new staff number.');
        }
        if ($staffNo === (string) $staff['staff_no']) {
            return $this->fail('That is already this staff member\'s number.');
        }
        $exists = $this->db()->table('staff')->where('staff_no', $staffNo)->where('id !=', (int) $id)->countAllResults();
        if ($exists > 0) {
            return $this->fail('Staff No Exist');
        }

        $this->db()->table('staff')->where('id', (int) $id)->update($this->onlyColumns([
            'staff_no'   => $staffNo,
            'updated_at' => date('Y-m-d H:i:s'),
        ]));
        $this->logAction($staff, 'change_staff_no', $staff['status'] ?? null, null, 'Update Staff Number: ' . $staffNo);

        return $this->ok('Staff number updated to ' . $staffNo . '.');
    }

    private function canManageStatus(): bool
    {
        helper('access');
        return has_access('staff_pass_list', 'manage_status') || has_access('staff_pass_list', 'edit');
    }

    // ------------------------------------------------------------------
    //  Approver tiers (same model as VendorList)
    // ------------------------------------------------------------------

    private function approverTiers(string $action): array
    {
        if (isset($this->tierCache[$action])) {
            return $this->tierCache[$action];
        }
        helper(['access', 'role']);
        $super = is_platform_superadmin() || is_client_superadmin();

        return $this->tierCache[$action] = [
            'super'   => $super,
            'generic' => $super || has_access('staff_pass_list', $action),
            'ksb'     => $super || has_access('staff_pass_list', $action . '_ksb'),
            'kpk'     => $super || has_access('staff_pass_list', $action . '_kpk'),
        ];
    }

    private function canActOn(array $staff, string $action): bool
    {
        $t = $this->approverTiers($action);
        if ($t['super'] || $t['generic']) {
            return true;
        }
        $next   = $staff['next_action'] ?? null;
        $branch = strtoupper(trim((string) ($staff['access_branch'] ?? '')));
        if ($next === 'ksb_approve') {
            return $t['ksb'];
        }
        if ($next === 'kpk_approve') {
            return $t['kpk'];
        }
        if ($branch === 'KSB') {
            return $t['ksb'];
        }
        if ($branch === 'KPK') {
            return $t['kpk'];
        }
        return $t['ksb'] || $t['kpk'];
    }

    private function notAllowedMessage(array $staff, string $action): string
    {
        $next = $staff['next_action'] ?? null;
        if ($next === 'ksb_approve' || $next === 'kpk_approve') {
            $who = $next === 'ksb_approve' ? 'KSB' : 'KPK';
            return "This pass is awaiting {$who} approval. Only a {$who} approver can {$action} it now.";
        }
        $branch = strtoupper(trim((string) ($staff['access_branch'] ?? '')));
        if ($branch === 'KSB' || $branch === 'KPK') {
            return "This pass is for {$branch}. Only a {$branch} approver can {$action} it.";
        }
        return "You are not allowed to {$action} this staff pass.";
    }

    /** Atomic: only applies while the record is still at the status/step we read. */
    private function applyTransition(array $staff, array $update): bool
    {
        $db     = $this->db();
        $update = $this->onlyColumns($update + ['updated_at' => date('Y-m-d H:i:s')]);

        $builder = $db->table('staff')->where('id', (int) $staff['id'])->where('status', $staff['status']);
        if (($staff['next_action'] ?? null) === null || $staff['next_action'] === '') {
            $builder->where("(next_action IS NULL OR next_action = '')", null, false);
        } else {
            $builder->where('next_action', $staff['next_action']);
        }
        $builder->update($update);

        if ($db->affectedRows() === 0) {
            $fresh = $db->table('staff')->where('id', (int) $staff['id'])->get()->getRowArray();
            $same  = $fresh && $fresh['status'] === $staff['status']
                && (($fresh['next_action'] ?? null) ?: null) === (($staff['next_action'] ?? null) ?: null);
            return $same && ($update['status'] ?? null) === $staff['status'];
        }
        return true;
    }
}
