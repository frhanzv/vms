<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BlacklistRequestModel;

class BlacklistRequest extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new BlacklistRequestModel();
    }

    public function index()
    {
        $filters = [
            'search' => $this->request->getGet('search'),
            'status' => $this->request->getGet('status'),
            'type'   => $this->request->getGet('type'),
        ];

        return view('blacklist/blacklistrequest', [
            'pageTitle' => 'Blacklist Request',
            'blacklist' => $this->model->getAll($filters),
        ]);
    }

    public function view($id = null)
    {
        $entry = $this->model->getById($id);

        if (!$entry) {
            return redirect()->to('/blacklistrequest')
                ->with('error', 'Record not found.');
        }

        return view('blacklist/blacklistrequest_view', [
            'pageTitle' => 'View Record',
            'entry'     => $entry,
        ]);
    }

    public function edit($id = null)
    {
        $entry = $this->model->getById($id);

        if (!$entry) {
            return redirect()->to('/blacklistrequest')
                ->with('error', 'Record not found.');
        }

        return view('blacklist/blacklistrequest_edit', [
            'pageTitle' => 'Edit Record',
            'entry'     => $entry,
        ]);
    }

    public function delete($id = null)
    {
        $this->model->delete($id);

        return redirect()->to('/blacklistrequest')
            ->with('success', 'Deleted successfully.');
    }
}
