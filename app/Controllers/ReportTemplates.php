<?php

namespace App\Controllers;

use App\Libraries\ReportBuilder;

/**
 * Report > Report Templates (as in the Kurita system): an admin saves a template of sections and columns,
 * previews it for a period, downloads it as Excel / PDF, emails it, or lets it go out on a schedule
 * (php spark reports:send, run every few minutes by the task scheduler).
 */
class ReportTemplates extends BaseController
{
    private function builder(): ReportBuilder
    {
        return new ReportBuilder();
    }

    /** 0 for the platform superadmin (templates over all clients), otherwise the user's client. */
    private function companyId(): int
    {
        helper(['role', 'feature']);

        return is_platform_superadmin() ? 0 : (int) current_client_id();
    }

    private function table()
    {
        return \Config\Database::connect()->table('report_templates');
    }

    private function find(int $id): ?array
    {
        $c = $this->companyId();
        $b = $this->table()->where('id', $id);
        $c > 0 ? $b->where('company_id', $c) : $b->where('company_id IS NULL', null, false);

        return $b->get()->getRowArray() ?: null;
    }

    private function period(): array
    {
        $ok = static fn($d) => is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) ? $d : null;

        return [$ok($this->request->getGetPost('date_from')), $ok($this->request->getGetPost('date_to'))];
    }

    public function index()
    {
        $c = $this->companyId();
        $b = $this->table()->orderBy('updated_at', 'DESC');
        $c > 0 ? $b->where('company_id', $c) : $b->where('company_id IS NULL', null, false);

        return view('report_templates/index', ['pageTitle' => 'Report Templates - SafeG', 'rows' => $b->get()->getResultArray(), 'rb' => $this->builder()]);
    }

    public function create()
    {
        return $this->form(['id' => 0, 'name' => '', 'description' => '', 'sections' => '{}', 'report_layout' => 'sectioned', 'frequency' => 'manual',
            'schedule_config' => '{}', 'format' => 'xlsx', 'recipients' => '[]', 'is_active' => 1]);
    }

    public function edit($id)
    {
        $tpl = $this->find((int) $id);

        return $tpl ? $this->form($tpl) : redirect()->to(base_url('report/templates'))->with('error', 'Template not found.');
    }

    private function form(array $tpl, array $errors = [])
    {
        return view('report_templates/form', ['pageTitle' => 'Report Template - SafeG', 'tpl' => $tpl, 'rb' => $this->builder(), 'errors' => $errors]);
    }

    public function save()
    {
        $id  = (int) $this->request->getPost('id');
        $old = $id ? $this->find($id) : null;
        if ($id && ! $old) {
            return redirect()->to(base_url('report/templates'))->with('error', 'Template not found.');
        }
        $p       = $this->request->getPost();
        $catalog = $this->builder()->catalog();
        $errors  = [];

        $sections = [];
        foreach ((array) ($p['sections'] ?? []) as $key => $s) {
            if (! isset($catalog[$key]) || empty($s['enabled'])) {
                continue;
            }
            $cols = array_values(array_intersect(array_keys($catalog[$key]['columns']), array_keys((array) ($s['columns'] ?? []))));
            if ($cols) {
                $sections[$key] = ['label' => $catalog[$key]['label'], 'columns' => $cols];
            }
        }
        $emails = array_values(array_unique(array_map('strtolower', preg_split('/[\s,;]+/', trim((string) ($p['recipient_text'] ?? '')), -1, PREG_SPLIT_NO_EMPTY) ?: [])));
        $bad    = array_filter($emails, static fn($e) => ! filter_var($e, FILTER_VALIDATE_EMAIL));
        $freq   = array_key_exists($p['frequency'] ?? '', ReportBuilder::FREQUENCIES) ? $p['frequency'] : 'manual';

        $name = trim((string) ($p['name'] ?? ''));
        if ($name === '') {
            $errors[] = 'Template Name is required.';
        }
        if (! $sections) {
            $errors[] = 'Choose at least one section with at least one column.';
        }
        if ($bad) {
            $errors[] = 'These emails are not valid: ' . implode(', ', $bad);
        }
        if ($freq !== 'manual' && ! $emails) {
            $errors[] = 'A scheduled report needs at least one email recipient.';
        }

        $time = preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', (string) ($p['schedule_time'] ?? '')) ? $p['schedule_time'] : '08:00';
        $cfg  = ['time' => $time];
        if (in_array($freq, ['weekly', 'monthly', 'yearly'], true)) {
            $cfg['weekday'] = in_array($p['schedule_weekday'] ?? '', ReportBuilder::WEEKDAYS, true) ? $p['schedule_weekday'] : 'monday';
        }
        if (in_array($freq, ['monthly', 'yearly'], true)) {
            $cfg['week'] = in_array($p['schedule_week'] ?? '', ReportBuilder::WEEKS, true) ? $p['schedule_week'] : 'first';
        }
        if ($freq === 'yearly') {
            $cfg['month'] = max(1, min(12, (int) ($p['schedule_month'] ?? 1)));
        }

        $data = [
            'name'            => mb_substr($name, 0, 150),
            'description'     => trim((string) ($p['description'] ?? '')) ?: null,
            'sections'        => json_encode($sections),
            'report_layout'   => ($p['report_layout'] ?? '') === 'combined' ? 'combined' : 'sectioned',
            'frequency'       => $freq,
            'schedule_config' => json_encode($cfg),
            'format'          => array_key_exists($p['format'] ?? '', ReportBuilder::FORMATS) ? $p['format'] : 'xlsx',
            'recipients'      => json_encode($emails),
            'is_active'       => empty($p['is_active']) ? 0 : 1,
            'updated_by'      => (int) session()->get('user_id') ?: null,
            'updated_at'      => date('Y-m-d H:i:s'),
        ];
        if ($errors) {
            return $this->form(['id' => $id] + $data + ($old ?? []), $errors);
        }
        if ($id) {
            $this->table()->where('id', $id)->update($data);
        } else {
            $c = $this->companyId();
            $this->table()->insert($data + ['company_id' => $c > 0 ? $c : null, 'created_by' => $data['updated_by'], 'created_at' => $data['updated_at']]);
        }

        return redirect()->to(base_url('report/templates'))->with('success', 'Report template saved.');
    }

    public function delete($id)
    {
        if ($this->find((int) $id)) {
            $this->table()->where('id', (int) $id)->delete();
        }

        return redirect()->to(base_url('report/templates'))->with('success', 'Report template deleted.');
    }

    /** Preview: every chosen section as a table for the chosen period. */
    public function show($id)
    {
        $tpl = $this->find((int) $id);
        if (! $tpl) {
            return redirect()->to(base_url('report/templates'))->with('error', 'Template not found.');
        }
        [$from, $to] = $this->period();
        $rb          = $this->builder();

        return view('report_templates/show', ['pageTitle' => $tpl['name'] . ' - SafeG', 'tpl' => $tpl, 'rb' => $rb, 'data' => $rb->build($tpl, $from, $to), 'from' => $from, 'to' => $to]);
    }

    public function export($id)
    {
        $tpl = $this->find((int) $id);
        if (! $tpl) {
            return redirect()->to(base_url('report/templates'))->with('error', 'Template not found.');
        }
        [$from, $to] = $this->period();
        $files       = $this->builder()->attachments($tpl, $from, $to);
        if (count($files) > 1) {
            $path = tempnam(sys_get_temp_dir(), 'rpt');
            $zip  = new \ZipArchive();
            $zip->open($path, \ZipArchive::OVERWRITE);
            foreach ($files as $f) {
                $zip->addFromString($f['filename'], $f['content']);
            }
            $zip->close();
            $files = [['filename' => preg_replace('/\.\w+$/', '.zip', $files[0]['filename']), 'content' => file_get_contents($path), 'mime' => 'application/zip']];
            unlink($path);
        }
        $this->table()->where('id', $tpl['id'])->update(['last_generated_at' => date('Y-m-d H:i:s')]);

        return $this->response->setHeader('Content-Type', $files[0]['mime'])
            ->setHeader('Content-Disposition', 'attachment; filename="' . $files[0]['filename'] . '"')->setBody($files[0]['content']);
    }

    public function send($id)
    {
        $tpl = $this->find((int) $id);
        if (! $tpl) {
            return redirect()->to(base_url('report/templates'))->with('error', 'Template not found.');
        }
        [$from, $to] = $this->period();
        $err         = $this->builder()->send($tpl, $from, $to);
        if ($err) {
            return redirect()->back()->with('error', $err);
        }
        $this->table()->where('id', $tpl['id'])->update(['last_generated_at' => date('Y-m-d H:i:s')]);

        return redirect()->back()->with('success', 'Report sent to ' . implode(', ', json_decode($tpl['recipients'], true)) . '.');
    }
}
