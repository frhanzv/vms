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
     * Show closed blacklist records (server-paginated).
     */
    public function index()
    {
        $searchTerm = trim((string) ($this->request->getGet('search') ?? ''));
        $typeFilter = trim((string) ($this->request->getGet('type') ?? ''));
        $sortBy     = (string) ($this->request->getGet('sort') ?? 'date_desc');
        $page       = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage    = (int) ($this->request->getGet('per_page') ?? 10);

        if (! in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        $allowedSorts = ['name_asc', 'date_desc'];
        if (! in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'date_desc';
        }

        if ($typeFilter !== '' && ! in_array($typeFilter, ['Staff', 'Visitor'], true)) {
            $typeFilter = '';
        }

        $builder = $this->buildClosedListQuery($searchTerm, $typeFilter);
        $total   = (int) $builder->countAllResults(false);
        $lastPage = max(1, (int) ceil($total / $perPage));

        if ($page > $lastPage) {
            $page = $lastPage;
        }

        $this->applyClosedListSort($builder, $sortBy);

        $closed = $builder
            ->limit($perPage, ($page - 1) * $perPage)
            ->findAll();

        $rowOffset = ($page - 1) * $perPage;

        return view('blacklist/closedlist', [
            'pageTitle'        => 'Blacklist Closed List',
            'closed_blacklist' => $closed,
            'searchTerm'       => $searchTerm,
            'typeFilter'       => $typeFilter,
            'sortBy'           => $sortBy,
            'pagination'       => [
                'current_page' => $page,
                'last_page'    => $lastPage,
                'total'        => $total,
                'per_page'     => $perPage,
            ],
            'rowOffset'        => $rowOffset,
        ]);
    }

    /**
     * @return \CodeIgniter\Model
     */
    private function buildClosedListQuery(string $searchTerm, string $typeFilter)
    {
        $builder = $this->model->where('status', 'closed');

        if ($searchTerm !== '') {
            $builder->groupStart()
                ->like('name', $searchTerm)
                ->orLike('ic_passport_no', $searchTerm)
                ->orLike('staff_id', $searchTerm)
                ->groupEnd();
        }

        if ($typeFilter !== '') {
            $builder->where('type', $typeFilter);
        }

        return $builder;
    }

    private function applyClosedListSort($builder, string $sortBy): void
    {
        if ($sortBy === 'name_asc') {
            $builder->orderBy('name', 'ASC');
            return;
        }

        $builder->orderBy('blacklist_date', 'DESC');
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
                "\t" . ($row['ic_passport_no'] ?? ''),
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
            ->with('success', esc((string) ($entry['name'] ?? '')) . ' has been successfully released.');
    }
}
