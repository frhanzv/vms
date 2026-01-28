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
        $folder = basename($folder);
        $filename = basename($filename);

        $filePath = WRITEPATH . 'uploads/' . $folder . '/' . $filename;

        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404, 'File not found');
        }

        // Get file mime type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        // Set headers
        $this->response->setHeader('Content-Type', $mimeType);
        $this->response->setHeader('Content-Length', filesize($filePath));
        
        // For images, allow inline display
        if (strpos($mimeType, 'image/') === 0) {
            $this->response->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"');
        } else {
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }

        // Output file
        $this->response->setBody(file_get_contents($filePath));
        return $this->response;
    }
}
