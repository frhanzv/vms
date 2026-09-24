<?php

namespace App\Controllers;

class VendorList extends BaseController
{
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

        $rowOffset  = ($page - 1) * $perPage;
        $vendorList = [];

        foreach ($results as $index => $row) {
            $vendorList[] = [
                'id'                   => $row['id'],
                'no'                   => $rowOffset + $index + 1,
                'date'                 => date('d/m/Y', strtotime($row['created_at'])),
                'app_no'               => $row['app_no'] ?? 'N/A',
                'full_name'            => $row['full_name'] ?? 'N/A',
                'ic_passport'          => $row['ic_no'] ?: ($row['passport_no'] ?? ''),
                'vendor_company_name'  => $row['vendor_company_name'] ?? 'N/A',
                'status'               => $row['status'] ?? 'Pending',
                'pass_expiry'          => $row['pass_expiry'] ? date('d/m/Y', strtotime($row['pass_expiry'])) : '-',
                'remark'               => $row['remark'] ?? '-',
            ];
        }

        return view('vendors/list', [
            'pageTitle'  => 'Vendor Pass List - SafeG',
            'stats'      => [
                'total'    => $totalCount,
                'pending'  => $pendingCount,
                'approved' => $approvedCount,
            ],
            'vendorList' => $vendorList,
            'canEdit'    => has_access('vendor_pass_list', 'edit'),
            'canDelete'  => has_access('vendor_pass_list', 'delete'),
            'canApprove' => has_access('vendor_pass_list', 'approve'),
            'canReject'  => has_access('vendor_pass_list', 'reject'),
            'searchTerm' => $searchTerm,
            'sortBy'     => $sortBy,
            'status'     => $status,
            'pagination' => [
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
        helper('access');
        if (! has_access('vendor_pass_list', 'delete')) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['success' => false, 'message' => 'You are not allowed to delete vendor pass records.']);
        }

        $db = \Config\Database::connect();
        $db->table('vendors')->where('id', (int) $id)->delete();

        return $this->response->setJSON(['success' => true, 'message' => 'Vendor pass record deleted.']);
    }

    /**
     * Matches KPK's doApproveVendorPass: allowed from Pending or Rejected.
     * Follows RequestList::approve()/reject()'s JSON + atomic-update pattern.
     */
    public function approve()
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'approve')) {
            return $this->response->setJSON(['success' => false, 'message' => 'You are not allowed to approve vendor pass records.']);
        }

        $json = $this->request->getJSON();
        $id   = $json->id ?? null;

        if (!$id) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid vendor pass ID']);
        }

        $db     = \Config\Database::connect();
        $vendor = $db->table('vendors')->where('id', (int) $id)->get()->getRowArray();

        if (!$vendor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Vendor pass record not found']);
        }

        if (! in_array($vendor['status'], ['Pending', 'Rejected'], true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only Pending or Rejected records can be approved (current status: ' . $vendor['status'] . ')',
            ]);
        }

        // Atomic update: only succeeds if status hasn't changed since we read it.
        $db->table('vendors')
            ->where('id', (int) $id)
            ->where('status', $vendor['status'])
            ->update(['status' => 'Approved']);

        if ($db->affectedRows() === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This record has already been processed by another user. Please refresh the page.',
            ]);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Vendor pass approved successfully.']);
    }

    /**
     * Matches KPK's doRejectVendorPass: allowed from Pending, Approved, or Rejected
     * (KPK also allows re-reject after "attended training", which VMS doesn't
     * track yet — Pending/Approved/Rejected covers the cases this list shows).
     */
    public function reject()
    {
        helper('access');
        if (! has_access('vendor_pass_list', 'reject')) {
            return $this->response->setJSON(['success' => false, 'message' => 'You are not allowed to reject vendor pass records.']);
        }

        $json   = $this->request->getJSON();
        $id     = $json->id ?? null;
        $reason = trim((string) ($json->reason ?? ''));

        if (!$id) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid vendor pass ID']);
        }

        $db     = \Config\Database::connect();
        $vendor = $db->table('vendors')->where('id', (int) $id)->get()->getRowArray();

        if (!$vendor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Vendor pass record not found']);
        }

        if (! in_array($vendor['status'], ['Pending', 'Approved'], true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only Pending or Approved records can be rejected (current status: ' . $vendor['status'] . ')',
            ]);
        }

        $updateData = ['status' => 'Rejected'];
        if ($reason !== '') {
            $updateData['remark'] = $reason;
        }

        $db->table('vendors')
            ->where('id', (int) $id)
            ->where('status', $vendor['status'])
            ->update($updateData);

        if ($db->affectedRows() === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This record has already been processed by another user. Please refresh the page.',
            ]);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Vendor pass rejected successfully.']);
    }
}
