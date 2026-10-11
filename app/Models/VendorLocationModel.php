<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Vendor "Location Access" choices. There is ONE list of gates for the whole
 * product - the `locations` table (Config > Location Access Management) - used
 * by visitors, staff and vendors alike. A vendor pass stores each chosen gate as
 * a code = slug of its location_access name (e.g. "CHANGING ROOM 1 IN" ->
 * changing_room_1_in), so the comma separated value never contains spaces.
 *
 * The owner of a gate is locations.client_id (set from the Client dropdown;
 * BOTH = no single owner = every client).
 */
class VendorLocationModel extends Model
{
    protected $table            = 'locations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    /** code => label of active gates, in display order. */
    public function getActiveOptions(): array
    {
        return $this->options($this->db->table('locations')->where('status', 'active')->orderBy('location_access', 'ASC')->get()->getResultArray());
    }

    /**
     * Client ids whose gates the current user may pick from: its own client (and any
     * site group it was put in before). Null = no restriction (platform superadmin).
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
        $me       = $db->fieldExists('site_group', 'clients') ? $db->table('clients')->select('site_group')->where('id', $clientId)->get()->getRowArray() : null;
        $ids      = [$clientId];
        if ($me && trim((string) ($me['site_group'] ?? '')) !== '') {
            foreach ($db->table('clients')->select('id')->where('site_group', $me['site_group'])->get()->getResultArray() as $r) {
                $ids[] = (int) $r['id'];
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * Active gates the current user may choose (code => label): its own client's,
     * plus the ones set to BOTH. $withClient appends the owner's name when the user
     * can see gates of several clients (platform admin).
     */
    public function getOptionsForUser(bool $withClient = false, ?array $clientIds = null): array
    {
        $clientIds = $clientIds ?? self::visibleClientIds();
        $b = $this->db->table('locations l')->select('l.location_access, l.client_id, c.name AS client_name')
            ->join('clients c', 'c.id = l.client_id', 'left')->where('l.status', 'active');
        if ($clientIds !== null) {
            $b->groupStart()->where('l.client_id IS NULL', null, false)->orWhereIn('l.client_id', $clientIds ?: [0])->groupEnd();
        }
        $rows = $b->orderBy('l.location_access', 'ASC')->get()->getResultArray();

        $multi   = count(array_unique(array_filter(array_column($rows, 'client_id')))) > 1;
        $options = [];
        foreach ($rows as $row) {
            $code = self::slugifyCode((string) $row['location_access']);
            if ($code === '' || isset($options[$code])) {
                continue;
            }
            $label = (string) $row['location_access'];
            if ($withClient && $multi && ! empty($row['client_name'])) {
                $label .= ' (' . $row['client_name'] . ')';
            }
            $options[$code] = $label;
        }

        return $options;
    }

    /** code => label for EVERY gate, active or not (shows an already-saved value back by name). */
    public function getAllLabelMap(): array
    {
        return $this->options($this->db->table('locations')->orderBy('location_access', 'ASC')->get()->getResultArray());
    }

    /** @param list<array> $rows rows of `locations` */
    private function options(array $rows): array
    {
        $out = [];
        foreach ($rows as $r) {
            $code = self::slugifyCode((string) $r['location_access']);
            if ($code !== '' && ! isset($out[$code])) {
                $out[$code] = (string) $r['location_access'];
            }
        }

        return $out;
    }

    /** Name -> safe code (lowercase, underscores). */
    public static function slugifyCode(string $code): string
    {
        $slug = strtolower(trim($code));
        $slug = preg_replace('/[^a-z0-9]+/', '_', $slug);

        return trim((string) $slug, '_');
    }
}
