<?php

namespace App\Controllers;

/**
 * Shared plumbing for the KPK staff pass pipeline (Staff List, Request,
 * Process, Printing, Issuance, Closed, Card Info).
 *
 * In KPK a staff pass is a VendorPass row with visitorOrVip = 'STAFF' that
 * goes through the same port-pass pipeline as a vendor. VMS keeps staff in
 * its own `staff` table, so this mirrors what the Vendor* controllers do
 * against `vendors`, with the STAFF-specific rules from KPK on top:
 *   - Staff No. is mandatory before a card can be printed.
 *   - A photo is mandatory before a card can be printed.
 *   - No payment / receipt step (a lost-card reprint needs no receipt).
 *   - Department + Sub Type are staff-only fields.
 *   - Active / Inactive employee flag (is_active) separate from pass status.
 *   - Renewal only from a closed (issued) pass.
 *
 * Vehicle parts of KPK's staff module are not ported.
 */
abstract class StaffPassBase extends BaseController
{
    protected const FORM = 'staff_pass_request';

    /** Every staff table column, cached per request — lets code skip a column a migration hasn't added yet. */
    private ?array $staffColumns = null;

    protected function db()
    {
        return \Config\Database::connect();
    }

    /**
     * Company scope. Staff rows created before the pipeline existed have no
     * company_id; they stay visible to everyone in VMS (same as before).
     */
    protected function scope($builder, string $alias = '')
    {
        helper(['feature', 'role', 'client_visibility']);
        if (is_platform_superadmin()) {
            return $builder;
        }
        if (! $this->hasColumn('company_id')) {
            return $builder;
        }
        // Shared product: a client sees staff passes of its own company AND every pass
        // using a gate it owns (per-client approval rows). See client_visibility_helper.
        if ($this->approvalsReady()) {
            $builder->where(visibility_staff_sql((int) current_client_id(), $alias !== '' ? $alias : 'staff'), null, false);
            return $builder;
        }
        $col = ($alias !== '' ? $alias . '.' : '') . 'company_id';
        $builder->groupStart()
            ->where($col, current_company_id())
            ->orWhere($col . ' IS NULL', null, false)
            ->groupEnd();
        return $builder;
    }

    /** False until the multi-client migration has been run (keeps the old company rule meanwhile). */
    protected function approvalsReady(): bool
    {
        static $ready = null;
        return $ready ??= $this->db()->tableExists('staff_client_approvals');
    }

    /**
     * Location Access for a staff pass: the posted gates the user may use + any
     * gates of other clients already on the pass (an edit never drops those).
     * Returns the comma list, '' when nothing is left.
     */
    protected function resolveLocationCsv($posted, string $existingCsv = ''): string
    {
        $allowed = $this->locationValues();
        $chosen  = array_values(array_intersect(array_map('strval', array_map('trim', (array) $posted)), $allowed));
        $hidden  = array_filter(array_map('trim', explode(',', $existingCsv)), static fn($c) => $c !== '' && ! in_array($c, $allowed, true));

        return implode(',', array_values(array_unique(array_merge($chosen, $hidden))));
    }

    /** Keep staff_client_approvals in step with the pass's gates (no-op before the migration). */
    protected function syncApprovals(int $staffId, ?string $locationCsv, bool $draft = false): void
    {
        if ($this->approvalsReady()) {
            \App\Libraries\StaffClientApprovals::sync($this->db(), $staffId, $draft ? '' : (string) $locationCsv);
        }
    }

    protected function loadScopedStaff(int $id): ?array
    {
        $builder = $this->db()->table('staff')->where('id', $id);
        $this->scope($builder);
        return $builder->get()->getRowArray() ?: null;
    }

    protected function hasColumn(string $column): bool
    {
        if ($this->staffColumns === null) {
            $this->staffColumns = $this->db()->getFieldNames('staff');
        }
        return in_array($column, $this->staffColumns, true);
    }

    /** Drops keys for columns that don't exist yet (migration not run). */
    protected function onlyColumns(array $data): array
    {
        if ($this->staffColumns === null) {
            $this->staffColumns = $this->db()->getFieldNames('staff');
        }
        return array_intersect_key($data, array_flip($this->staffColumns));
    }

    protected function cfg(string $key): bool
    {
        helper('feature');
        static $model = null;
        $model ??= new \App\Models\ClientFormFieldModel();
        return $model->isEnabled(current_company_id(), self::FORM, $key);
    }

    protected function canEdit(): bool
    {
        helper('access');
        return has_access('staff_pass_list', 'edit');
    }

    protected function actor(): string
    {
        return (string) (session()->get('full_name') ?: session()->get('username'));
    }

    protected function input(): array
    {
        try {
            $json = $this->request->getJSON(true);
        } catch (\Throwable $e) {
            $json = null;
        }
        return is_array($json) ? $json : (array) $this->request->getPost();
    }

    protected function ok(string $message, array $extra = [])
    {
        return $this->response->setJSON(['success' => true, 'message' => $message] + $extra);
    }

    protected function fail(string $message)
    {
        return $this->response->setJSON(['success' => false, 'message' => $message]);
    }

    /** KPK's logService.doUserActionLog equivalent for staff passes. */
    protected function logAction(array $staff, string $action, ?string $toStatus, ?string $next = null, string $remark = '', ?string $reason = null): void
    {
        try {
            $this->db()->table('staff_status_logs')->insert([
                'staff_id'      => (int) $staff['id'],
                'action'        => $action,
                'from_status'   => $staff['status'] ?? null,
                'to_status'     => $toStatus,
                'next_action'   => $next,
                'remark'        => $remark !== '' ? $remark : null,
                'reject_reason' => $reason,
                'acted_by'      => $this->actor(),
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'staff_status_logs insert failed: ' . $e->getMessage());
        }
    }

    protected function photoUrl(array $staff): ?string
    {
        $photo = $staff['photo'] ?? null;
        return $photo ? base_url('uploads/staff_photos/' . $photo) : null;
    }

    /** Stores an uploaded file or a base64 camera capture into public/uploads/staff_photos. */
    protected function storePhoto(): ?string
    {
        $dir = FCPATH . 'uploads/staff_photos';
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $file = $this->request->getFile('photo');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            if (! in_array(strtolower($file->getClientExtension()), ['jpg', 'jpeg', 'png', 'webp'], true)) {
                return null;
            }
            $name = $file->getRandomName();
            $file->move($dir, $name);
            return $name;
        }

        $dataUrl = $this->request->getPost('photo_data');
        if (! $dataUrl) {
            try {
                $dataUrl = $this->request->getJSON(true)['photo_data'] ?? null;
            } catch (\Throwable $e) {
                $dataUrl = null;
            }
        }
        if ($dataUrl && preg_match('/^data:image\/(png|jpe?g);base64,/', $dataUrl, $m)) {
            $data = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1), true);
            if ($data !== false && @getimagesizefromstring($data) !== false) {
                $name = bin2hex(random_bytes(16)) . '.' . ($m[1] === 'png' ? 'png' : 'jpg');
                file_put_contents($dir . '/' . $name, $data);
                return $name;
            }
        }

        return null;
    }

    protected function rejectReasons(): array
    {
        try {
            return (new \App\Models\RejectReasonModel())
                ->select('id, reason')
                ->where('status', 'active')
                ->orderBy('reason', 'ASC')
                ->findAll();
        } catch (\Throwable $e) {
            return [];
        }
    }

    protected function licenses(int $staffId): array
    {
        if (! $this->db()->tableExists('staff_driving_licenses')) {
            return [];
        }
        return $this->db()->table('staff_driving_licenses')->where('staff_id', $staffId)->orderBy('id', 'DESC')->get()->getResultArray();
    }

    protected function printLogs(int $staffId): array
    {
        if (! $this->db()->tableExists('staff_card_print_logs')) {
            return [];
        }
        return $this->db()->table('staff_card_print_logs')->where('staff_id', $staffId)->orderBy('printed_at', 'DESC')->get()->getResultArray();
    }

    protected function statusLogs(int $staffId): array
    {
        if (! $this->db()->tableExists('staff_status_logs')) {
            return [];
        }
        return $this->db()->table('staff_status_logs')->where('staff_id', $staffId)->orderBy('id', 'DESC')->get()->getResultArray();
    }

    /**
     * KPK checkPortPassExist(): another live staff pass with the same IC /
     * passport blocks a new one. Drafts don't count (KPK excludes SAVED).
     */
    protected function duplicateIcExists(string $icPassport, ?int $excludeId = null): bool
    {
        $icPassport = trim($icPassport);
        if ($icPassport === '') {
            return false;
        }
        $builder = $this->db()->table('staff')
            ->where('ic_passport', $icPassport)
            ->groupStart()->where('status IS NULL', null, false)->orWhere('status !=', 'Draft')->groupEnd();
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }

    /** Pagination helper shared by every list page. */
    protected function paginate($builder, int $page, int $perPage, string $sortField, string $sortDir): array
    {
        $total    = (int) $builder->countAllResults(false);
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page     = min(max(1, $page), $lastPage);
        $rows     = $builder->orderBy($sortField, $sortDir)->limit($perPage, ($page - 1) * $perPage)->get()->getResultArray();

        return [$rows, ['current_page' => $page, 'last_page' => $lastPage, 'total' => $total, 'per_page' => $perPage]];
    }

    protected function applySearch($builder, string $term, array $extra = []): void
    {
        if ($term === '') {
            return;
        }
        $builder->groupStart()
            ->like('full_name', $term)
            ->orLike('ic_passport', $term)
            ->orLike('staff_no', $term)
            ->orLike('app_no', $term);
        foreach ($extra as $col) {
            $builder->orLike($col, $term);
        }
        $builder->groupEnd();
    }

    protected function fmtDate(?string $value, string $format = 'd/m/Y'): string
    {
        if (! $value || str_starts_with($value, '0000')) {
            return '-';
        }
        $ts = strtotime($value);
        return $ts ? date($format, $ts) : $value;
    }

    /** IN/OUT selector built from active Location Access Management rows. */
    protected function getLocationGroups(): array
    {
        $groups = [];
        try {
            helper('client_visibility');
            // Shared product: own gates + site-group siblings + gates nobody owns yet.
            $locations = visibility_filter_locations((new \App\Models\LocationModel())->getAllActive());
        } catch (\Throwable $e) {
            return [];
        }

        foreach ($locations as $location) {
            $access = trim((string) ($location['location_access'] ?? ''));
            if ($access === '') {
                continue;
            }
            $branch    = trim((string) ($location['branch'] ?? '')) ?: 'Locations';
            $direction = null;
            $label     = $access;
            if (preg_match('/\s+(IN|OUT)$/i', $access, $m)) {
                $direction = strtolower($m[1]);
                $label     = trim(substr($access, 0, -strlen($m[0])));
            }
            $key = strtolower($label);
            $groups[$branch][$key] ??= ['label' => $label, 'in' => null, 'out' => null];
            if ($direction !== null) {
                $groups[$branch][$key][$direction] = $access;
            } else {
                $groups[$branch][$key]['in'] = $access;
            }
        }
        foreach ($groups as $branch => $entries) {
            $groups[$branch] = array_values($entries);
        }
        return $groups;
    }

    /** Flat list of every selectable location value. */
    protected function locationValues(): array
    {
        $out = [];
        foreach ($this->getLocationGroups() as $entries) {
            foreach ($entries as $e) {
                foreach (['in', 'out'] as $d) {
                    if (! empty($e[$d])) {
                        $out[] = $e[$d];
                    }
                }
            }
        }
        return $out;
    }
}
