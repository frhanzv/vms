<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Admin-editable replacement for the old hardcoded LOCATION_OPTIONS const
 * that was duplicated across VendorPassRequest and VendorCardInfo (and read
 * from the former by VendorList / VendorProcessDetail). A new location
 * added through VendorLocations::create() shows up immediately on every
 * page that reads getActiveOptions() — the request form, the detail page,
 * Card Info, and Process Detail.
 */
class VendorLocationModel extends Model
{
    protected $table            = 'vendor_locations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['code', 'label', 'sort_order', 'is_active', 'client_id'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'code'  => 'required|max_length[50]|is_unique[vendor_locations.code,id,{id}]',
        'label' => 'required|max_length[150]',
    ];

    /**
     * code => label map of active locations, in display order. This is the
     * drop-in replacement for LOCATION_OPTIONS everywhere it was used.
     */
    public function getActiveOptions(): array
    {
        $rows = $this->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('label', 'ASC')
            ->findAll();

        $options = [];
        foreach ($rows as $row) {
            $options[$row['code']] = $row['label'];
        }

        return $options;
    }

    /**
     * Client ids whose locations the current user may pick from: its own
     * client plus every client in the same site_group (the clients sharing one
     * building). Null = no restriction (platform superadmin).
     *
     * @return list<int>|null
     */
    public static function visibleClientIds(?int $clientId = null): ?array
    {
        helper(['feature', 'role']);
        if (is_platform_superadmin()) {
            return null;
        }
        $clientId = $clientId ?? (int) current_company_id();
        $db       = \Config\Database::connect();
        $me       = $db->table('clients')->select('site_group')->where('id', $clientId)->get()->getRowArray();
        $ids      = [$clientId];
        if ($me && trim((string) ($me['site_group'] ?? '')) !== '') {
            $rows = $db->table('clients')->select('id')->where('site_group', $me['site_group'])->get()->getResultArray();
            foreach ($rows as $r) {
                $ids[] = (int) $r['id'];
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * Active locations the current user may choose (code => label), in display
     * order: own client + site-group siblings + locations nobody owns yet.
     * $withClient appends the owning client's name when several clients share
     * the choices, so a vendor can tell "Gate 1 (KSB)" from "Gate 1 (KPK)".
     */
    public function getOptionsForUser(bool $withClient = false, ?array $clientIds = null): array
    {
        $clientIds = $clientIds ?? self::visibleClientIds();
        $b = $this->db->table($this->table . ' l')->select('l.code, l.label, l.client_id, c.name AS client_name')
            ->join('clients c', 'c.id = l.client_id', 'left')->where('l.is_active', 1);
        if ($clientIds !== null) {
            $b->groupStart()->where('l.client_id IS NULL', null, false)->orWhereIn('l.client_id', $clientIds ?: [0])->groupEnd();
        }
        $rows = $b->orderBy('l.sort_order', 'ASC')->orderBy('l.label', 'ASC')->get()->getResultArray();

        $multi   = count(array_unique(array_filter(array_column($rows, 'client_id')))) > 1;
        $options = [];
        foreach ($rows as $row) {
            $label = $row['label'];
            if ($withClient && $multi && ! empty($row['client_name'])) {
                $label .= ' (' . $row['client_name'] . ')';
            }
            $options[$row['code']] = $label;
        }

        return $options;
    }

    /** All rows (active + inactive) for the management page — inactive ones stay visible there so they can be re-enabled, just not offered as a choice elsewhere. */
    public function getAllOrdered(): array
    {
        return $this->orderBy('sort_order', 'ASC')->orderBy('label', 'ASC')->findAll();
    }

    /**
     * code => label for EVERY row, active or not. Used wherever an already
     * -saved location_access value needs to be shown back as a label (the
     * read-only detail page, Card Info) — a vendor record saved against a
     * location that's since been deactivated should still show its real
     * name instead of silently dropping it.
     */
    public function getAllLabelMap(): array
    {
        $rows = $this->orderBy('sort_order', 'ASC')->orderBy('label', 'ASC')->findAll();

        $options = [];
        foreach ($rows as $row) {
            $options[$row['code']] = $row['label'];
        }

        return $options;
    }

    /** Turns a free-typed code into a safe slug (lowercase, underscores) so it matches cleanly against stored comma-separated location_access values. */
    public static function slugifyCode(string $code): string
    {
        $slug = strtolower(trim($code));
        $slug = preg_replace('/[^a-z0-9]+/', '_', $slug);
        return trim($slug, '_');
    }
}
