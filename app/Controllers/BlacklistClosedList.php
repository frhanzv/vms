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