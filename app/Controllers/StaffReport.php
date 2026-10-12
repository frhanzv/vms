<?php

namespace App\Controllers;

use App\Libraries\StaffGate;

/**
 * Staff Report — KPK's three staff reports on one page:
 *
 *   KPK Staff Report          -> view "All movements" (every gate visit in the
 *                                date range; status Gate In / Gate Out / Rejected)
 *   KPK Staff In-Premise      -> view "In premise" (gated in, not yet out — any date)
 *   KPK Staff Out-of-Window   -> view "Out of window" (on site 24h or more:
 *                                still inside for 24h+, or a visit in the date
 *                                range that lasted 24h+)
 *
 * Reads staff_visits / staff_card_logs, written by App\Libraries\StaffGate.
 * Same company / shared-gate scope as the rest of the staff pass module.
 */
class StaffReport extends StaffPassBase
{
    private const MAX_ROWS = 2000;

    private const VIEWS    = ['all', 'in_premise', 'out_of_window'];
    private const STATUSES = ['all', 'gate_in', 'gate_out', 'rejected'];
    private const SORTS    = [
        'time_desc'  => ['time_in', 'DESC'],
        'time_asc'   => ['time_in', 'ASC'],
        'name_asc'   => ['s.full_name', 'ASC'],
        'name_desc'  => ['s.full_name', 'DESC'],
        'staff_asc'  => ['s.staff_no', 'ASC'],
        'staff_desc' => ['s.staff_no', 'DESC'],
    ];

    public function index()
    {
        helper(['access']);
        if (! has_access('report', 'staff_report')) {
            return redirect()->to(base_url('dashboard'))->with('error', 'You are not allowed to view the Staff Report.');
        }

        return view('reports/staff_report', [
            'pageTitle'   => 'Staff Report - SafeG',
            'departments' => $this->departments(),
            'trackingOn'  => StaffGate::ready($this->db()),
            'oowHours'    => StaffGate::OUT_OF_WINDOW_HOURS,
        ]);
    }

    public function generate()
    {
        helper(['access', 'privacy']);
        if (! has_access('report', 'staff_report')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Not allowed.']);
        }
        $db = $this->db();
        if (! StaffGate::ready($db)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Staff gate tracking is not set up yet — run "php spark migrate".']);
        }

        $in   = $this->input();
        $from = $this->validDate($in['from'] ?? '') ?? date('Y-m-d', strtotime('-30 days'));
        $to   = $this->validDate($in['to'] ?? '') ?? date('Y-m-d');
        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }
        $view   = in_array($in['view'] ?? '', self::VIEWS, true) ? $in['view'] : 'all';
        $status = in_array($in['status'] ?? '', self::STATUSES, true) ? $in['status'] : 'all';
        $sort   = isset(self::SORTS[$in['sort'] ?? '']) ? $in['sort'] : 'time_desc';
        $search = trim((string) ($in['search'] ?? ''));
        $dept   = trim((string) ($in['department'] ?? ''));

        $f = [
            'start'  => $from . ' 00:00:00',
            'end'    => $to . ' 23:59:59',
            'now'    => date('Y-m-d H:i:s'),
            'cutoff' => date('Y-m-d H:i:s', time() - StaffGate::OUT_OF_WINDOW_HOURS * 3600),
            'search' => $search,
            'dept'   => $dept,
        ];

        if ($view === 'all' && $status === 'rejected') {
            $rows = $this->rejectedRows($f, $sort);
        } else {
            $rows = $this->visitRows($f, $view, $status, $sort);
        }
        $truncated = count($rows) > self::MAX_ROWS;
        $rows      = array_slice($rows, 0, self::MAX_ROWS);

        return $this->response->setJSON([
            'success'   => true,
            'rows'      => $rows,
            'counts'    => $this->counts($f),
            'date_from' => $from,
            'date_to'   => $to,
            'truncated' => $truncated,
            'message'   => $truncated ? 'Showing the first ' . self::MAX_ROWS . ' rows. Narrow the date range or search to see the rest.' : null,
        ]);
    }

    // ------------------------------------------------------------------

    /** staff_visits joined to the staff pass, scoped and filtered by search / department. */
    private function visitsBase()
    {
        $b = $this->db()->table('staff_visits sv')->join('staff s', 's.id = sv.staff_id');
        $this->scope($b, 's');
        return $b;
    }

    private function applyFilters($b, array $f): void
    {
        if ($f['search'] !== '') {
            $b->groupStart()
                ->like('s.full_name', $f['search'])
                ->orLike('s.staff_no', $f['search'])
                ->orLike('s.ic_passport', $f['search'])
                ->orLike('s.contact_number', $f['search'])
                ->groupEnd();
        }
        if ($f['dept'] !== '') {
            $b->where('s.department', $f['dept']);
        }
    }

    private function inRange($b, array $f): void
    {
        $b->where('sv.check_in_time >=', $f['start'])->where('sv.check_in_time <=', $f['end']);
    }

    /** KPK out-of-window: inside for 24h+ now, or a visit in range that lasted 24h+. */
    private function outOfWindow($b, array $f): void
    {
        $db    = $this->db();
        $hours = (int) StaffGate::OUT_OF_WINDOW_HOURS;
        $b->groupStart()
            ->groupStart()
                ->where('sv.check_out_time IS NULL', null, false)
                ->where('sv.check_in_time <=', $f['cutoff'])
            ->groupEnd()
            ->orGroupStart()
                ->where('sv.check_out_time IS NOT NULL', null, false)
                ->where('sv.check_in_time >=', $f['start'])
                ->where('sv.check_in_time <=', $f['end'])
                ->where('TIMESTAMPDIFF(MINUTE, sv.check_in_time, sv.check_out_time) >= ' . ($hours * 60), null, false)
            ->groupEnd()
        ->groupEnd();
    }

    private function visitRows(array $f, string $view, string $status, string $sort): array
    {
        $db  = $this->db();
        $now = $db->escape($f['now']);

        $b = $this->visitsBase()
            ->select('sv.id, sv.check_in_time AS time_in, sv.check_out_time AS time_out,
                      s.id AS staff_id, s.full_name, s.staff_no, s.ic_passport, s.contact_number, s.department,
                      li.lane AS in_lane, loci.location_access AS in_location,
                      lo.lane AS out_lane, loco.location_access AS out_location', false)
            ->select("TIMESTAMPDIFF(MINUTE, sv.check_in_time, COALESCE(sv.check_out_time, {$now})) AS minutes", false)
            ->join('lanes li', 'li.id = sv.check_in_lane_id', 'left')
            ->join('locations loci', 'loci.id = li.location_id', 'left')
            ->join('lanes lo', 'lo.id = sv.check_out_lane_id', 'left')
            ->join('locations loco', 'loco.id = lo.location_id', 'left');
        $this->applyFilters($b, $f);

        if ($view === 'in_premise') {
            $b->where('sv.check_out_time IS NULL', null, false);
        } elseif ($view === 'out_of_window') {
            $this->outOfWindow($b, $f);
        } else {
            $this->inRange($b, $f);
            if ($status === 'gate_in') {
                $b->where('sv.check_out_time IS NULL', null, false);
            } elseif ($status === 'gate_out') {
                $b->where('sv.check_out_time IS NOT NULL', null, false);
            }
        }

        [$col, $dir] = self::SORTS[$sort];
        $b->orderBy($col === 'time_in' ? 'sv.check_in_time' : $col, $dir)->orderBy('sv.id', 'DESC')->limit(self::MAX_ROWS + 1);

        $oowMinutes = StaffGate::OUT_OF_WINDOW_HOURS * 60;
        $out = [];
        foreach ($b->get()->getResultArray() as $r) {
            $inside  = empty($r['time_out']);
            $minutes = max(0, (int) $r['minutes']);
            $out[] = $this->rowBase($r) + [
                'location'      => $this->locationLabel($r['in_lane'], $r['in_location']),
                'exit_location' => $inside ? '-' : $this->locationLabel($r['out_lane'], $r['out_location']),
                'time_in'       => $this->fmtDate($r['time_in'], 'd/m/Y h:i A'),
                'time_out'      => $inside ? '-' : $this->fmtDate($r['time_out'], 'd/m/Y h:i A'),
                'minutes'       => $minutes,
                'duration'      => StaffGate::formatDuration($minutes * 60) . ($inside ? ' (so far)' : ''),
                'status'        => $inside ? 'Gate In' : 'Gate Out',
                'out_of_window' => $minutes >= $oowMinutes,
                'remark'        => '',
            ];
        }
        return $out;
    }

    /** KPK "Rejected": refused gate-in attempts. */
    private function rejectedRows(array $f, string $sort): array
    {
        $b = $this->db()->table('staff_card_logs sl')
            ->select('sl.id, sl.scanned_at AS time_in, sl.message, s.id AS staff_id, s.full_name, s.staff_no, s.ic_passport,
                      s.contact_number, s.department, l.lane AS in_lane, loc.location_access AS in_location', false)
            ->join('staff s', 's.id = sl.staff_id')
            ->join('lanes l', 'l.id = sl.lane_id', 'left')
            ->join('locations loc', 'loc.id = l.location_id', 'left')
            ->where('sl.action', 'denied')
            ->where('sl.scanned_at >=', $f['start'])
            ->where('sl.scanned_at <=', $f['end']);
        $this->scope($b, 's');
        $this->applyFilters($b, $f);

        [$col, $dir] = self::SORTS[$sort];
        $b->orderBy($col === 'time_in' ? 'sl.scanned_at' : $col, $dir)->orderBy('sl.id', 'DESC')->limit(self::MAX_ROWS + 1);

        $out = [];
        foreach ($b->get()->getResultArray() as $r) {
            $out[] = $this->rowBase($r) + [
                'location'      => $this->locationLabel($r['in_lane'], $r['in_location']),
                'exit_location' => '-',
                'time_in'       => $this->fmtDate($r['time_in'], 'd/m/Y h:i A'),
                'time_out'      => '-',
                'minutes'       => null,
                'duration'      => '-',
                'status'        => 'Rejected',
                'out_of_window' => false,
                'remark'        => (string) ($r['message'] ?? ''),
            ];
        }
        return $out;
    }

    private function counts(array $f): array
    {
        $gateIns = $this->visitsBase();
        $this->applyFilters($gateIns, $f);
        $this->inRange($gateIns, $f);
        $gateIns = (int) $gateIns->countAllResults();

        $unique = $this->visitsBase()->select('COUNT(DISTINCT sv.staff_id) AS c', false);
        $this->applyFilters($unique, $f);
        $this->inRange($unique, $f);
        $unique = (int) ($unique->get()->getRowArray()['c'] ?? 0);

        $checkedOut = $this->visitsBase()->where('sv.check_out_time IS NOT NULL', null, false);
        $this->applyFilters($checkedOut, $f);
        $this->inRange($checkedOut, $f);
        $checkedOut = (int) $checkedOut->countAllResults();

        $inPremise = $this->visitsBase()->where('sv.check_out_time IS NULL', null, false);
        $this->applyFilters($inPremise, $f);
        $inPremise = (int) $inPremise->countAllResults();

        $oow = $this->visitsBase();
        $this->applyFilters($oow, $f);
        $this->outOfWindow($oow, $f);
        $oow = (int) $oow->countAllResults();

        $rejected = $this->db()->table('staff_card_logs sl')->join('staff s', 's.id = sl.staff_id')
            ->where('sl.action', 'denied')->where('sl.scanned_at >=', $f['start'])->where('sl.scanned_at <=', $f['end']);
        $this->scope($rejected, 's');
        $this->applyFilters($rejected, $f);
        $rejected = (int) $rejected->countAllResults();

        return [
            'gate_ins'      => $gateIns,
            'unique_staff'  => $unique,
            'in_premise'    => $inPremise,
            'out_of_window' => $oow,
            'checked_out'   => $checkedOut,
            'rejected'      => $rejected,
        ];
    }

    private function rowBase(array $r): array
    {
        return [
            'staff_id'   => (int) $r['staff_id'],
            'full_name'  => (string) ($r['full_name'] ?? ''),
            'staff_no'   => (string) (($r['staff_no'] ?? '') !== '' ? $r['staff_no'] : '-'),
            'ic_masked'  => mask_ic_passport($r['ic_passport'] ?? '', '-'),
            'contact'    => (string) (($r['contact_number'] ?? '') !== '' ? $r['contact_number'] : '-'),
            'department' => (string) (($r['department'] ?? '') !== '' ? $r['department'] : '-'),
            'date'         => $this->fmtDate($r['time_in'] ?? null),
            'time_in_sort' => (string) ($r['time_in'] ?? ''),
            'detail_url' => base_url('staffs/card-info/' . (int) $r['staff_id']),
        ];
    }

    private function locationLabel(?string $lane, ?string $location): string
    {
        $lane     = trim((string) $lane);
        $location = trim((string) $location);
        if ($lane === '' && $location === '') {
            return '-';
        }
        if ($lane === '' || $location === '' || strcasecmp($lane, $location) === 0) {
            return $lane !== '' ? $lane : $location;
        }
        return $location . ' · ' . $lane;
    }

    private function departments(): array
    {
        try {
            $b = $this->db()->table('staff')->select('department')->distinct()
                ->where('department IS NOT NULL', null, false)->where('department !=', '');
            $this->scope($b);
            return array_values(array_filter(array_column($b->orderBy('department')->get()->getResultArray(), 'department')));
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function validDate($value): ?string
    {
        $value = trim((string) $value);
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }
        [$y, $m, $d] = array_map('intval', explode('-', $value));
        return checkdate($m, $d, $y) ? $value : null;
    }
}
