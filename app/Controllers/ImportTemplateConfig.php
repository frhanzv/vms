<?php

namespace App\Controllers;

use App\Libraries\VendorImportTemplate;

/**
 * Config > Import Template: replace the Excel template vendors download for bulk import.
 * A client admin manages its own client's file; the platform admin manages the shared one.
 */
class ImportTemplateConfig extends BaseController
{
    private function scopeClient(): ?int
    {
        helper(['role', 'feature']);

        return is_platform_superadmin() ? null : (int) current_client_id();
    }

    private function json(bool $ok, string $msg, array $extra = [], int $code = 200)
    {
        return $this->response->setStatusCode($code)->setJSON(['success' => $ok, 'message' => $msg] + $extra);
    }

    public function index()
    {
        $mine   = VendorImportTemplate::customPath($this->scopeClient());
        $global = VendorImportTemplate::customPath(null);

        return $this->json(true, 'ok', [
            'custom'      => is_file($mine),
            'updated_at'  => is_file($mine) ? date('d M Y H:i', filemtime($mine)) : null,
            'size_kb'     => is_file($mine) ? (int) round(filesize($mine) / 1024) : null,
            'inherits'    => ! is_file($mine) && is_file($global),
            'platform'    => $this->scopeClient() === null,
        ]);
    }

    public function upload()
    {
        $file = $this->request->getFile('template');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return $this->json(false, 'Choose an Excel (.xlsx) file to upload.', [], 400);
        }
        if (strtolower($file->getExtension()) !== 'xlsx') {
            return $this->json(false, 'Only .xlsx files are allowed.', [], 400);
        }
        if ($file->getSize() > 5 * 1024 * 1024) {
            return $this->json(false, 'The file is too big (maximum 5 MB).', [], 400);
        }
        if ($err = VendorImportTemplate::validate($file->getTempName())) {
            return $this->json(false, $err, [], 400);
        }

        if (! is_dir(VendorImportTemplate::dir())) {
            mkdir(VendorImportTemplate::dir(), 0775, true);
        }
        copy($file->getTempName(), VendorImportTemplate::customPath($this->scopeClient()));

        return $this->json(true, 'New import template saved. Vendors download it from now on.');
    }

    /** Go back to the built-in (or platform) template. */
    public function reset()
    {
        $p = VendorImportTemplate::customPath($this->scopeClient());
        if (is_file($p)) {
            unlink($p);
        }

        return $this->json(true, 'Back to the default import template.');
    }

    /** Download for the config page: ?which=current (default) or builtin */
    public function download()
    {
        helper(['role', 'feature']);
        $builtin = $this->request->getGet('which') === 'builtin';
        $path    = $builtin ? VendorImportTemplate::builtIn() : VendorImportTemplate::resolve(is_platform_superadmin() ? null : (int) current_client_id());

        return $this->response->download($path, null)->setFileName('VendorPassTemplate.xlsx');
    }

    /** Vendor List > Import > Download. */
    public function vendorDownload()
    {
        helper(['role', 'feature']);
        $cid = is_platform_superadmin() ? null : (int) current_client_id();

        return $this->response->download(VendorImportTemplate::resolve($cid ?: null), null)->setFileName('VendorPassTemplate.xlsx');
    }
}
