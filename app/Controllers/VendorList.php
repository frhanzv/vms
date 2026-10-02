<?php

namespace App\Controllers;

/**
 * Vendor pass list + approve / reject.
 *
 * Approve / reject follow KPK's real rules
 * (VendorPassController.doApproveVendorPass / doRejectVendorPass and
 * VendorPassServiceImpl.doApprovePortPass / doRejectPortPass, plus the
 * enableBtns() rules in port-pass-request-list.component.ts):
 *
 *  APPROVE  - allowed from Pending or Rejected.
 *           - blocked if the IC / passport is on the active blacklist.
 *           - if the pass needs BOTH branches (access_branch = BOTH), the first
 *             approver only moves it to the other branch ("ksb_approve" /
 *             "kpk_approve" in next_action); status stays Pending. The second
 *             approver, from the other branch, finalises it.
 *           - otherwise it goes straight to Approved.
 *           - optional "direct close" (Config toggle) also activates the card,
 *             skipping Printing / Issuance — KPK's ModuleConfig.vpDirectClose.
 *  REJECT   - allowed from Pending, Approved or Rejected.
 *           - a reject reason is required (from reject_reasons); remark optional.
 *           - clears next_action and deactivates the card, as KPK does.
 *
 * KPK ties "branch" to the user account (user.branch). VMS has no such column,
 * so it is expressed through role permissions instead — see approverTiers().
 */
class VendorList extends BaseController
{
    private array $tierCache = [];

    public function index()
    {
        helper(['access', 'feature', 'privacy', 'role']);
        $db = \Config\Database::connect();

        $searchTerm = trim((string) ($this->request->getGet('search') ?? ''));
        $status     = trim((string) ($this->request->getGet('status') ?? 'all'));
        $page       = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage    = (int) ($this->request->getGet('per_page') ?? 10);
        $sortBy     = (string) ($this->request->getGet('sort') ?? 'date_desc');

        if (! in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        $allowedSorts = ['name_asc', 'name_desc', 'date_asc', 'date_desc'];
        if (! in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'date_desc';
        }

        $allowedStatus = ['all', 'Pending', 'Approved', 'Rejected', 'Suspended'];
        if (! in_array($status, $allowedStatus, true)) {
            $status = 'all';
        }

        $builder    = $this->buildVendorListQuery($db, $searchTerm, $status);
        $totalCount = (int) $builder->countAllResults(false);
        $lastPage   = max(1, (int) ceil($totalCount / $perPage));

        if ($page > $lastPage) {
            $page = $lastPage;
        }

        $this->applyVendorSort($builder, $sortBy);

        $results = $builder
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        $pendingCount  = (int) $this->buildVendorListQuery($db, '', 'Pending')->countAllResults();
        $approvedCount = (int) $this->buildVendorListQuery($db, '', 'Approved')->countAllResults();

        $formFieldModel = new \App\Models\ClientFormFieldModel();
        $companyId      = current_company_id();
        $cfg            = fn(string $key) => $formFieldModel->isEnabled($companyId, 'vendor_pass_request', $key);
        $showApproveBtn = $cfg('approve_button');
        $showRejectBtn  = $cfg('reject_button');

        $awaitingLabels = [
            'ksb_approve' => 'Awaiting KSB approval',
            'kpk_approve' => 'Awaiting KPK approval',
        ];

        $rowOffset  = ($page - 1) * $perPage;
        $vendorList = [];

        foreach ($results as $index => $row) {
            $rowStatus = $row['status'] ?? 'Pending';

            $vendorList[] = [
                'id'                   => $row['id'],
                'no'                   => $rowOffset + $index + 1,
                'date'                 => date('d/m/Y', strtotime($row['created_at'])),
                'app_no'               => $row['app_no'] ?? 'N/A',
                'full_name'            => $row['full_name'] ?? 'N/A',
                'ic_passport'          => $row['ic_no'] ?: ($row['passport_no'] ?? ''),
                'vendor_company_name'  => $row['vendor_company_name'] ?? 'N/A',
                'status'               => $rowStatus,
                'awaiting'             => $awaitingLabels[$row['next_action'] ?? ''] ?? null,
                'access_branch'        => $row['access_branch'] ?? null,
                'reject_reason'        => $row['reject_reason'] ?? null,
                'pass_expiry'          => $row['pass_expiry'] ? date('d/m/Y', strtotime($row['pass_expiry'])) : '-',
                'remark'               => $row['remark'] ?? '-',
                // Per-row: the right branch approver sees the buttons, others don't (KPK enableBtns()).
                'can_approve'          => $showApproveBtn
                    && in_array($rowStatus, ['Pending', 'Rejected'], true)
                    && $this->canActOn($row, 'approve'),
                'can_reject'           => $showRejectBtn
                    && in_array($rowStatus, ['Pending', 'Approved', 'Rejected'], true)
                    && $this->canActOn($row, 'reject'),
            ];
        }

        $rejectReasons = [];
        try {
            $rejectReasons = (new \App\Models\RejectReasonModel())
                ->select('id, reason')
                ->where('status', 'active')
                ->orderBy('reason', 'ASC')
                ->findAll();
        } catch (\Throwable $e) {
            log_message('error', 'VendorList: could not load reject reasons: ' . $e->getMessage());
        }

        return view('vendors/list', [
            'pageTitle'     => 'Vendor Pass List - SafeG',
            'stats'         => [
                'total'    => $totalCount,
                'pending'  => $pendingCount,
                'approved' => $approvedCount,
            ],
            'vendorList'    => $vendorList,
            'rejectReasons' => $rejectReasons,
            'canEdit'       => has_access('vendor_pass_list', 'edit') && $cfg('edit_button'),
            'canDelete'     => has_access('vendor_pass_list', 'delete') && $cfg('delete_button'),
            // No 'canQr' here on purpose — the QR action moved to Closed List
            // (it's a vendor-detail lookup, not a pass-verification code, so
            // it only makes sense once a card has actually been issued).
            'searchTerm'    => $searchTerm,
            'sortBy'        => $sortBy,
            'status'        => $status,
            'pagination'    => [
                'current_page' => $page,
                'last_page'    => $lastPage,
                'total'        => $totalCount,
                'per_page'     => $perPage,
            ],
        ]);
    }

    /**
     * @return \CodeIgniter\Database\BaseBuilder
     */
    private function buildVendorListQuery($db, string $searchTerm, string $status)
    {
        $builder = $db->table('vendors')->select('*');

        // Company-scoped, like every other module here — superadmin sees all.
        if (! is_platform_superadmin()) {
            $builder->where('company_id', current_company_id());
        }

        if ($searchTerm !== '') {
            $builder->groupStart()
                ->like('full_name', $searchTerm)
                ->orLike('ic_no', $searchTerm)
                ->orLike('passport_no', $searchTerm)
                ->orLike('app_no', $searchTerm)
                ->orLike('vendor_company_name', $searchTerm)
                ->groupEnd();
        }

        if ($status !== 'all') {
            $builder->where('status', $status);
        }

        return $builder;
    }

    private function applyVendorSort($builder, string $sortBy): void
    {
        switch ($sortBy) {
            case 'name_asc':
                $builder->orderBy('full_name', 'ASC');
                break;
            case 'name_desc':
                $builder->orderBy('full_name', 'DESC');
                break;
            case 'date_asc':
                $builder->orderBy('created_at', 'ASC');
                break;
            default:
                $builder->orderBy('created_at', 'DESC');
                break;
        }
    }

    public function delete($id)
    {
        helper(['access', 'role']);
        if (! has_access('vendor_pass_list', 'delete')) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['success' => false, 'message' => 'You are not allowed to delete vendor pass records.']);
        }

        $vendor = $this->loadScopedVendor((int) $id);
        if (! $vendor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Vendor pass record not found.']);
        }

        $db = \Config\Database::connect();
        $db->table('vendors')->where('id', (int) $id)->delete();

        return $this->response->setJSON(['success' => true, 'message' => 'Vendor pass record deleted.']);
    }

    // =====================================================================
    //  APPROVE
    // =====================================================================

    public function approve()
    {
        helper(['access', 'feature', 'role']);

        $in     = $this->input();
        $id     = (int) ($in['id'] ?? 0);
        $remark = trim((string) ($in['remark'] ?? ''));

        if ($id <= 0) {
            return $this->fail('Invalid vendor pass ID.');
        }

        $vendor = $this->loadScopedVendor($id);
        if (! $vendor) {
            return $this->fail('Vendor pass record not found.');
        }

        if (! $this->canActOn($vendor, 'approve')) {
            return $this->fail($this->notAllowedMessage($vendor, 'approve'));
        }

        // KPK: only Pending or Rejected passes can be approved.
        if (! in_array($vendor['status'], ['Pending', 'Rejected'], true)) {
            return $this->fail('Only Pending or Rejected records can be approved (current status: ' . $vendor['status'] . '). Please refresh the page.');
        }

        $db = \Config\Database::connect();

        // KPK checks the blacklist before approving (blacklistService.checkExist, "BLACKLISTED").
        $idNo = trim((string) ($vendor['ic_no'] ?: ($vendor['passport_no'] ?? '')));
        if ($idNo !== '' && $db->tableExists('blacklist')) {
            $hit = $db->table('blacklist')
                ->where('ic_passport_no', $idNo)
                ->where('status', 'active')
                ->countAllResults();
            if ($hit > 0) {
                return $this->fail('IC / Passport is Blacklisted. This pass cannot be approved.');
            }
        }

        $tiers  = $this->approverTiers('approve');
        $branch = strtoupper(trim((string) ($vendor['access_branch'] ?? '')));
        $next   = $vendor['next_action'] ?: null;

        $update = [
            'remark' => $remark !== '' ? $remark : ($vendor['remark'] ?? null),
        ];
        $action  = 'approve';
        $message = 'Vendor pass approved successfully.';

        // Two-branch approval: first approver hands over to the other branch.
        $fullAuthority   = $tiers['super'] || $tiers['generic'];
        $needsSecondStep = ! $fullAuthority
            && $branch === 'BOTH'
            && $next === null
            && ($tiers['ksb'] xor $tiers['kpk']);

        if ($needsSecondStep) {
            $update['next_action'] = $tiers['kpk'] ? 'ksb_approve' : 'kpk_approve';
            $action  = 'first_approve';
            $message = 'Your branch has approved. Now awaiting '
                . ($tiers['kpk'] ? 'KSB' : 'KPK') . ' approval.';
        } else {
            $update['status']      = 'Approved';
            $update['next_action'] = null;
            $update['reject_reason'] = null;

            // KPK ModuleConfig.vpDirectClose — skip Printing / Issuance.
            $companyId = (int) ($vendor['company_id'] ?? 0) ?: current_company_id();
            $cfgModel  = new \App\Models\ClientFormFieldModel();
            if ($cfgModel->isEnabled($companyId, 'vendor_pass_request', 'direct_close')) {
                $update['card_status'] = 'Active';
                $message .= ' Card activated (direct close).';
            }
        }

        if (! $this->applyTransition($vendor, $update)) {
            return $this->fail('This record has already been processed by another user. Please refresh the page.');
        }

        $this->logAction($vendor, $action, $update['status'] ?? $vendor['status'], $update['next_action'] ?? null, $remark, null);

        return $this->ok($message);
    }

    // =====================================================================
    //  REJECT
    // =====================================================================

    public function reject()
    {
        helper(['access', 'feature', 'role']);

        $in       = $this->input();
        $id       = (int) ($in['id'] ?? 0);
        $remark   = trim((string) ($in['remark'] ?? ''));
        $reasonId = (int) ($in['reject_reason_id'] ?? 0);

        if ($id <= 0) {
            return $this->fail('Invalid vendor pass ID.');
        }

        $vendor = $this->loadScopedVendor($id);
        if (! $vendor) {
            return $this->fail('Vendor pass record not found.');
        }

        if (! $this->canActOn($vendor, 'reject')) {
            return $this->fail($this->notAllowedMessage($vendor, 'reject'));
        }

        // KPK: Pending, Approved or Rejected can be rejected.
        if (! in_array($vendor['status'], ['Pending', 'Approved', 'Rejected'], true)) {
            return $this->fail('This record cannot be rejected from status "' . $vendor['status'] . '". Please refresh the page.');
        }

        // KPK requires a reject reason (form.rejectReason -> VendorPassRejectReason).
        $reasonText = null;
        if ($reasonId > 0) {
            $reason = \Config\Database::connect()->table('reject_reasons')
                ->select('reason')
                ->where('id', $reasonId)
                ->where('status', 'active')
                ->get()->getRowArray();
            $reasonText = $reason['reason'] ?? null;
        }
        if ($reasonText === null) {
            return $this->fail('Please select a reject reason.');
        }

        $update = [
            'status'        => 'Rejected',
            'next_action'   => null,
            'remark'        => $remark !== '' ? $remark : ($vendor['remark'] ?? null),
            'reject_reason' => $reasonText,
            'card_status'   => 'Inactive', // KPK closes the vendor's card on reject
        ];

        if (! $this->applyTransition($vendor, $update)) {
            return $this->fail('This record has already been processed by another user. Please refresh the page.');
        }

        $this->logAction($vendor, 'reject', 'Rejected', null, $remark, $reasonText);

        return $this->ok('Vendor pass rejected successfully.');
    }

    // =====================================================================
    //  Permission model
    // =====================================================================

    /**
     * KPK decides who may act from user.branch (KSB / KPK). VMS users have no
     * branch, so the same idea is expressed with role permissions:
     *
     *   approve / reject          -> full authority (any branch, single step)
     *   approve_ksb / reject_ksb  -> KSB approver
     *   approve_kpk / reject_kpk  -> KPK approver
     *
     * Superadmins always have full authority (KPK: `if (this.superadmin) enableBtns = true`).
     */
    private function approverTiers(string $action): array
    {
        if (isset($this->tierCache[$action])) {
            return $this->tierCache[$action];
        }

        helper(['access', 'role']);
        $super = is_platform_superadmin() || is_client_superadmin();

        return $this->tierCache[$action] = [
            'super'   => $super,
            'generic' => $super || has_access('vendor_pass_list', $action),
            'ksb'     => $super || has_access('vendor_pass_list', $action . '_ksb'),
            'kpk'     => $super || has_access('vendor_pass_list', $action . '_kpk'),
        ];
    }

    /**
     * Port of enableBtns() in port-pass-request-list.component.ts.
     */
    private function canActOn(array $vendor, string $action): bool
    {
        $t = $this->approverTiers($action);

        if ($t['super'] || $t['generic']) {
            return true;
        }

        $next   = $vendor['next_action'] ?? null;
        $branch = strtoupper(trim((string) ($vendor['access_branch'] ?? '')));

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

        // BOTH, or no branch set: either branch approver may act.
        return $t['ksb'] || $t['kpk'];
    }

    private function notAllowedMessage(array $vendor, string $action): string
    {
        $next = $vendor['next_action'] ?? null;
        if ($next === 'ksb_approve') {
            return 'This pass is awaiting KSB approval. Only a KSB approver can ' . $action . ' it now.';
        }
        if ($next === 'kpk_approve') {
            return 'This pass is awaiting KPK approval. Only a KPK approver can ' . $action . ' it now.';
        }
        $branch = strtoupper(trim((string) ($vendor['access_branch'] ?? '')));
        if ($branch === 'KSB' || $branch === 'KPK') {
            return 'This pass is for ' . $branch . '. Only a ' . $branch . ' approver can ' . $action . ' it.';
        }
        return 'You are not allowed to ' . $action . ' this vendor pass.';
    }

    // =====================================================================
    //  Helpers
    // =====================================================================

    /** Accepts a JSON body (like RequestList) or a normal form post. */
    private function input(): array
    {
        try {
            $json = $this->request->getJSON(true);
        } catch (\Throwable $e) {
            $json = null;
        }

        return is_array($json) ? $json : (array) $this->request->getPost();
    }

    /** Loads a vendor, enforcing the same company scoping as the list. */
    private function loadScopedVendor(int $id): ?array
    {
        helper(['feature', 'role']);
        $builder = \Config\Database::connect()->table('vendors')->where('id', $id);

        if (! is_platform_superadmin()) {
            $builder->where('company_id', current_company_id());
        }

        return $builder->get()->getRowArray() ?: null;
    }

    /**
     * Atomic update, like RequestList: only applies if the record is still in
     * the state we read (same status AND same next_action), so two approvers
     * can't both act on the same step.
     */
    private function applyTransition(array $vendor, array $update): bool
    {
        $db = \Config\Database::connect();

        // If a migration hasn't been run yet, don't 500 on the missing column —
        // just leave that column out (status / next_action always exist).
        $update = array_intersect_key($update, array_flip($db->getFieldNames('vendors')));

        $builder = $db->table('vendors')
            ->where('id', (int) $vendor['id'])
            ->where('status', $vendor['status']);

        // Only filter by next_action if that column actually exists — protects
        // against exactly the bug that broke this before the migration was run.
        if (in_array('next_action', $db->getFieldNames('vendors'), true)) {
            if (($vendor['next_action'] ?? null) === null || $vendor['next_action'] === '') {
                $builder->where('(next_action IS NULL OR next_action = \'\')', null, false);
            } else {
                $builder->where('next_action', $vendor['next_action']);
            }
        }

        $builder->update($update);

        // Re-rejecting an already-rejected pass may change nothing but the reason; treat as OK.
        if ($db->affectedRows() === 0) {
            $fresh = $db->table('vendors')->where('id', (int) $vendor['id'])->get()->getRowArray();
            $sameStep = $fresh
                && $fresh['status'] === $vendor['status']
                && (($fresh['next_action'] ?? null) ?: null) === (($vendor['next_action'] ?? null) ?: null);
            return $sameStep && ($update['status'] ?? null) === $vendor['status'];
        }

        return true;
    }

    /** KPK's logService.doUserActionLog equivalent. */
    private function logAction(array $vendor, string $action, string $toStatus, ?string $next, string $remark, ?string $reason): void
    {
        try {
            \Config\Database::connect()->table('vendor_status_logs')->insert([
                'vendor_id'     => (int) $vendor['id'],
                'action'        => $action,
                'from_status'   => $vendor['status'],
                'to_status'     => $toStatus,
                'next_action'   => $next,
                'remark'        => $remark !== '' ? $remark : null,
                'reject_reason' => $reason,
                'acted_by'      => (string) (session()->get('full_name') ?: session()->get('username')),
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // Never block an approval because the audit insert failed.
            log_message('error', 'vendor_status_logs insert failed: ' . $e->getMessage());
        }
    }

    private function ok(string $message)
    {
        return $this->response->setJSON(['success' => true, 'message' => $message]);
    }

    private function fail(string $message)
    {
        return $this->response->setJSON(['success' => false, 'message' => $message]);
    }
}
