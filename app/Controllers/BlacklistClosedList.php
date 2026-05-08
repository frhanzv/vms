<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BlacklistRequestModel;

class BlacklistClosedList extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new BlacklistRequestModel();
    }

    /**
     * Show all closed blacklist records
     */
    public function index()
    {
        $closed = $this->model
            ->where('status', 'closed')
            ->orderBy('blacklist_date', 'DESC')
            ->findAll();

        return view('blacklist/closedlist', [
            'pageTitle'       => 'Blacklist Closed List',
            'closed_blacklist' => $closed,
        ]);
    }

    /**
     * View a single closed blacklist record (returns JSON for modal)
     */
    public function view($id = null)
    {
        $entry = $this->model->getById((int) $id);

        if (!$entry) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Record not found.',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $entry,
        ]);
    }

    public function export()
    {
        $rows = $this->model
            ->where('status', 'closed')
            ->orderBy('blacklist_date', 'DESC')
            ->findAll();

        $handle = fopen('php://temp', 'w+');
        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, [
            'No',
            'Created Date',
            'Blacklist Date',
            'IC / Passport No',
            'Staff ID',
            'Name',
            'Type',
            'Reason',
            'Released Date',
        ]);

        foreach ($rows as $index => $row) {
            fputcsv($handle, [
                $index + 1,
                $row['created_date'] ?? '',
                $row['blacklist_date'] ?? '',
                $row['ic_passport_no'] ?? '',
                $row['staff_id'] ?? '',
                $row['name'] ?? '',
                $row['type'] ?? '',
                $row['reason'] ?? '',
                $row['released_date'] ?? '',
            ]);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="blacklist-closed-' . date('Y-m-d-His') . '.csv"')
            ->setBody((string) $csvContent);
    }

    /**
     * Release a blacklisted individual
     */
    public function release($id = null)
    {
        $entry = $this->model->getById((int) $id);

        if (!$entry) {
            return redirect()->to(base_url('blacklist/closedlist'))
                ->with('error', 'Record not found.');
        }

        $released = $this->model->release((int) $id);

        if (!$released) {
            return redirect()->to(base_url('blacklist/closedlist'))
                ->with('error', 'Failed to release. Please try again.');
        }

        return redirect()->to(base_url('blacklist/closedlist'))
            ->with('success', esc($entry['name']) . ' has been successfully released.');
    }
}