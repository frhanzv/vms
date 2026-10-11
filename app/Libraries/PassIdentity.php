<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;

/**
 * One IC / Passport = one live pass, across EVERY client and both pass types
 * (staff and vendor). Someone who left KSB but whose KSB card was never
 * deactivated cannot be registered at KPK until that card is deactivated.
 *
 * A pass stops holding its IC when it is a Draft, was Rejected, or its card
 * was Terminated (deactivated). Anything else still holds it.
 *
 * Pure class: takes the DB connection, so it is unit-testable.
 */
class PassIdentity
{
    /**
     * The live pass holding this IC / passport, or null when it is free.
     *
     * @param string|null $ignoreType 'staff' or 'vendor' - the pass being edited
     * @param int|null    $ignoreId   id of the pass being edited
     * @return array{type:string,id:int,status:string,clients:list<string>}|null
     */
    public static function holder(BaseConnection $db, string $ic, ?string $ignoreType = null, ?int $ignoreId = null): ?array
    {
        $ic = trim($ic);
        if ($ic === '') {
            return null;
        }

        $checks = [
            'staff'  => ['table' => 'staff',   'cols' => ['ic_passport']],
            'vendor' => ['table' => 'vendors', 'cols' => ['ic_no', 'passport_no']],
        ];

        foreach ($checks as $type => $c) {
            if (! $db->tableExists($c['table'])) {
                continue;
            }

            $b = $db->table($c['table'])->select('id, status, card_status, location_access');
            $b->groupStart();
            foreach ($c['cols'] as $i => $col) {
                $i === 0 ? $b->where($col, $ic) : $b->orWhere($col, $ic);
            }
            $b->groupEnd();
            // Free: Draft / Rejected passes and deactivated (Terminated) cards.
            $b->groupStart()->where('status IS NULL', null, false)->orWhereNotIn('status', ['Draft', 'Rejected'])->groupEnd();
            $b->groupStart()->where('card_status IS NULL', null, false)->orWhere('card_status !=', 'Terminated')->groupEnd();
            if ($ignoreType === $type && $ignoreId) {
                $b->where('id !=', $ignoreId);
            }

            $row = $b->get(1)->getRowArray();
            if ($row) {
                return [
                    'type'    => $type,
                    'id'      => (int) $row['id'],
                    'status'  => (string) ($row['status'] ?: 'Pending'),
                    'clients' => static::clientNames($db, (string) ($row['location_access'] ?? '')),
                ];
            }
        }

        return null;
    }

    /** Plain remark shown to whoever tries to register the IC again. */
    public static function message(string $ic, array $holder): string
    {
        $what   = $holder['type'] === 'staff' ? 'staff pass' : 'vendor pass';
        $client = $holder['clients'] !== [] ? ' under ' . implode(' / ', $holder['clients']) : '';

        return "IC / Passport '{$ic}' is still being used by an existing {$what}{$client} (status: {$holder['status']}). "
            . 'Every person must have a different IC number. Ask the previous pass holder\'s admin to terminate (deactivate) that card first, then try again.';
    }

    /** Convenience: message when the IC is taken, otherwise null. */
    public static function check(BaseConnection $db, string $ic, ?string $ignoreType = null, ?int $ignoreId = null): ?string
    {
        $holder = static::holder($db, $ic, $ignoreType, $ignoreId);

        return $holder ? static::message(trim($ic), $holder) : null;
    }

    /** @return list<string> names of the clients owning the pass's gates */
    protected static function clientNames(BaseConnection $db, string $locationCsv): array
    {
        $codes = array_values(array_filter(array_map('trim', explode(',', $locationCsv)), static fn($c) => $c !== ''));
        if ($codes === [] || ! $db->tableExists('locations')) {
            return [];
        }

        $ids = [];
        foreach ($db->table('locations')->select('location_access, client_id')->where('client_id IS NOT NULL', null, false)->get()->getResultArray() as $r) {
            if (in_array(\App\Models\VendorLocationModel::slugifyCode((string) $r['location_access']), $codes, true)) {
                $ids[(int) $r['client_id']] = true;
            }
        }
        if ($ids === []) {
            return [];
        }

        return array_column($db->table('clients')->select('name')->whereIn('id', array_keys($ids))->get()->getResultArray(), 'name');
    }
}
