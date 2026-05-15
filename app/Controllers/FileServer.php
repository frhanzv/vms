<?php

namespace App\Controllers;

class FileServer extends BaseController
{
    public function serve($folder = null, $filename = null)
    {
        if (!$folder || !$filename) {
            return $this->response->setStatusCode(404);
        }

        // Sanitize inputs to prevent directory traversal
        $folder   = basename($folder);
        $filename = basename($filename);

        // Writable uploads (registration, facial, gov docs, etc.) and public/uploads
        // (e.g. kiosk API stores under FCPATH . 'uploads/visitor_photos/').
        $candidates = [
            WRITEPATH . 'uploads/' . $folder . '/' . $filename,
            FCPATH . 'uploads/' . $folder . '/' . $filename,
        ];

        $filePath = null;
        foreach ($candidates as $candidate) {
            if (is_file($candidate) && is_readable($candidate)) {
                $filePath = $candidate;
                break;
            }
        }

        if ($filePath === null) {
            return $this->response->setStatusCode(404, 'File not found');
        }

        // Get file mime type
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        $fileSize = filesize($filePath);

        // Bypass CI4 response pipeline — send headers and stream directly
        if (!headers_sent()) {
            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . $fileSize);

            if (strpos($mimeType, 'image/') === 0 || $mimeType === 'application/pdf') {
                header('Content-Disposition: inline; filename="' . $filename . '"');
            } else {
                header('Content-Disposition: attachment; filename="' . $filename . '"');
            }

            header('Cache-Control: public, max-age=3600');
            header('Pragma: public');
        }

        // Clean any output buffer then stream the file
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        readfile($filePath);
        exit;
    }
}
