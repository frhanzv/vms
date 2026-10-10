<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * Which clients are involved in a vendor pass, and what each has decided.
 *
 * A pass carries Location Access codes; each location is owned by ONE client
 * (vendor_locations.client_id). Every client that owns at least one selected
 * location gets a row in vendor_client_approvals: it sees the pass and
 * approves / rejects its own part. The pass as a whole is Approved only when
 * every involved client has approved.
 *
 * Locations that no client owns yet (client_id NULL) involve nobody — a pass
 * made only of those behaves the old way (scoped by vendors.company_id).
 *
 * Pure class: takes the DB connection, so it is unit-testable.
 */
class VendorClientApprovals
{
    /** Approval table + its pass-id column; StaffClientApprovals overrides both. */
    protected const TABLE = 'vendor_client_approvals';
    protected const KEY   = 'vendor_id';

    /** Rows of the table that map a location code to its owning client. */
    protected static function ownerRows(BaseConnection $db, array $codes): array
    {
        return $db->table('vendor_locations')->select('client_id')->whereIn('code', $codes)
            ->where('client_id IS NOT NULL', null, false)->get()->getResultArray();
    }

    /** @return list<int> client ids owning at least one of the location codes */
    public static function clientsForLocations(BaseConnection $db, string $locationCsv): array
    {
        $codes = array_values(array_filter(array_map('trim', explode(',', $locationCsv)), static fn($c) => $c !== ''));
        if ($codes === []) {
            return [];
        }

        $rows = static::ownerRows($db, $codes);

        return array_values(array_unique(array_map(static fn($r) => (int) $r['client_id'], $rows)));
    }

    /**
     * Make the approval rows match the pass's current locations: add rows for
     * newly involved clients (Pending), drop rows for clients no longer involved.
     * Returns the involved client ids.
     *
     * @return list<int>
     */
    public static function sync(BaseConnection $db, int $vendorId, string $locationCsv): array
    {
        $want = static::clientsForLocations($db, $locationCsv);
        $have = array_map(static fn($r) => (int) $r['client_id'], $db->table(static::TABLE)->select('client_id')->where(static::KEY, $vendorId)->get()->getResultArray());
        $now  = date('Y-m-d H:i:s');

        foreach (array_diff($want, $have) as $cid) {
            $db->table(static::TABLE)->insert([static::KEY => $vendorId, 'client_id' => $cid, 'status' => 'Pending', 'created_at' => $now, 'updated_at' => $now]);
        }
        $gone = array_diff($have, $want);
        if ($gone !== []) {
            $db->table(static::TABLE)->where(static::KEY, $vendorId)->whereIn('client_id', array_values($gone))->delete();
        }

        return $want;
    }

    /** Back to Pending for everybody (a rejected pass was edited and resubmitted). */
    public static function resetAll(BaseConnection $db, int $vendorId): void
    {
        $db->table(static::TABLE)->where(static::KEY, $vendorId)
            ->update(['status' => 'Pending', 'acted_by' => null, 'acted_at' => null, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /** @return list<array{client_id:int,status:string,client_name:string}> */
    public static function rows(BaseConnection $db, int $vendorId): array
    {
        return array_map(static function ($r) {
            return ['client_id' => (int) $r['client_id'], 'status' => (string) $r['status'], 'client_name' => (string) ($r['name'] ?? ('Client ' . $r['client_id']))];
        }, $db->table(static::TABLE . ' a')->select('a.client_id, a.status, c.name')
            ->join('clients c', 'c.id = a.client_id', 'left')->where('a.' . static::KEY, $vendorId)->orderBy('c.name', 'ASC')->get()->getResultArray());
    }

    /** Names of clients that still have to approve, for the "Awaiting ..." label. */
    public static function awaitingLabel(BaseConnection $db, int $vendorId): ?string
    {
        $names = [];
        foreach (static::rows($db, $vendorId) as $r) {
            if ($r['status'] !== 'Approved') {
                $names[] = $r['client_name'];
            }
        }

        return $names === [] ? null : 'Awaiting ' . implode(' & ', $names) . ' approval';
    }

    public static function hasRows(BaseConnection $db, int $vendorId): bool
    {
        return $db->table(static::TABLE)->where(static::KEY, $vendorId)->countAllResults() > 0;
    }

    /**
     * Record a decision. $clientId null = platform superadmin acting for every
     * involved client at once. Returns [ok, message, allApproved].
     *
     * @return array{0:bool,1:string,2:bool}
     */
    public static function decide(BaseConnection $db, int $vendorId, ?int $clientId, string $decision, string $actor, string $remark = ''): array
    {
        $rows = static::rows($db, $vendorId);
        if ($rows === []) {
            return [false, 'This pass has no client approvals.', false];
        }
        if ($clientId !== null && ! in_array($clientId, array_column($rows, 'client_id'), true)) {
            return [false, 'None of the locations on this pass belong to your client.', false];
        }

        $q = $db->table(static::TABLE)->where(static::KEY, $vendorId);
        if ($clientId !== null) {
            $q->where('client_id', $clientId);
        }
        $q->update([
            'status' => $decision, 'acted_by' => $actor, 'acted_at' => date('Y-m-d H:i:s'),
            'remark' => $remark !== '' ? $remark : null, 'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $pending = $db->table(static::TABLE)->where(static::KEY, $vendorId)->where('status !=', 'Approved')->countAllResults();

        return [true, '', $pending === 0];
    }

    /** May this client act on the pass right now (it is involved and has not already approved)? */
    public static function clientCanApprove(BaseConnection $db, int $vendorId, int $clientId): bool
    {
        return $db->table(static::TABLE)->where([static::KEY => $vendorId, 'client_id' => $clientId])
            ->whereIn('status', ['Pending', 'Rejected'])->countAllResults() > 0;
    }

    public static function clientCanReject(BaseConnection $db, int $vendorId, int $clientId): bool
    {
        return $db->table(static::TABLE)->where([static::KEY => $vendorId, 'client_id' => $clientId])->countAllResults() > 0;
    }

    /**
     * Result of a location edit: the chosen codes plus any codes already on the
     * pass that the editor cannot see (another client's), so an edit never
     * silently drops them. Returns '' when nothing at all would remain.
     *
     * @param list<string> $chosen
     * @param list<string> $visibleCodes
     */
    public static function mergeLocations(string $existingCsv, array $chosen, array $visibleCodes): string
    {
        $hidden = array_filter(array_map('trim', explode(',', $existingCsv)), static fn($c) => $c !== '' && ! in_array($c, $visibleCodes, true));

        return implode(',', array_values(array_unique(array_merge($chosen, $hidden))));
    }

    /** Whole-pass status from the clients' decisions: any Rejected -> Rejected, all Approved -> Approved, else Pending. */
    public static function overallStatus(BaseConnection $db, int $vendorId): ?string
    {
        $rows = static::rows($db, $vendorId);
        if ($rows === []) {
            return null;
        }
        $st = array_column($rows, 'status');
        if (in_array('Rejected', $st, true)) {
            return 'Rejected';
        }

        return count(array_unique($st)) === 1 && $st[0] === 'Approved' ? 'Approved' : 'Pending';
    }

    /**
     * Approval rows for many passes at once (list pages).
     *
     * @param  list<int> $vendorIds
     * @return array<int,list<array{client_id:int,status:string,client_name:string}>>
     */
    public static function rowsForVendors(BaseConnection $db, array $vendorIds): array
    {
        if ($vendorIds === []) {
            return [];
        }
        $out = [];
        foreach ($db->table(static::TABLE . ' a')->select('a.' . static::KEY . ' AS vendor_id, a.client_id, a.status, c.name')
            ->join('clients c', 'c.id = a.client_id', 'left')->whereIn('a.' . static::KEY, $vendorIds)->orderBy('c.name', 'ASC')->get()->getResultArray() as $r) {
            $out[(int) $r['vendor_id']][] = ['client_id' => (int) $r['client_id'], 'status' => (string) $r['status'], 'client_name' => (string) ($r['name'] ?? ('Client ' . $r['client_id']))];
        }

        return $out;
    }
}
