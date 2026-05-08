<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * QR Code Reader Listener
 *
 * Reads QR codes from a USB HID barcode/QR scanner attached to the kiosk.
 * Most USB scanners operate in HID-keyboard mode: they "type" the scanned
 * value followed by a newline (Enter key).  Running this command in a
 * focused terminal window captures that input via STDIN.
 *
 * Usage:
 *   php spark qr:listen                        # single reader (no lane)
 *   php spark qr:listen --lane=1 --type=entry  # dedicated entry lane
 *   php spark qr:listen --lane=2 --type=exit   # dedicated exit lane
 *   php spark qr:listen --base-url=http://localhost:8080  # custom VMS URL
 */
class QrListener extends BaseCommand
{
    protected $group       = 'QR';
    protected $name        = 'qr:listen';
    protected $description = 'Listen for USB HID QR reader input and call the VMS check-in/check-out API.';

    protected $options = [
        '--lane'     => 'Lane ID to associate scans with (optional)',
        '--type'     => 'Lane type: entry, exit, or both (default: both)',
        '--base-url' => 'VMS base URL (default: http://localhost:8080)',
    ];

    // Minimum seconds between repeated scans of the same code
    private const DEBOUNCE_SECONDS = 3;

    public function run(array $params)
    {
        $laneId  = CLI::getOption('lane');
        $type    = CLI::getOption('type')     ?? 'both';
        $baseUrl = CLI::getOption('base-url') ?? 'http://localhost:8080';

        CLI::write('[QR Listener] Starting...', 'green');
        CLI::write('[QR Listener] Base URL : ' . $baseUrl, 'yellow');

        if ($laneId) {
            CLI::write('[QR Listener] Lane ID  : ' . $laneId, 'yellow');
            CLI::write('[QR Listener] Lane Type: ' . $type, 'yellow');
        } else {
            CLI::write('[QR Listener] Mode     : single reader (no lane)', 'yellow');
        }

        CLI::write('[QR Listener] Waiting for QR codes... (Ctrl+C to stop)', 'green');
        CLI::newLine();

        $lastCode = '';
        $lastTime = 0;

        // Read lines from STDIN (USB HID scanner "types" code + Enter)
        while (($line = fgets(STDIN)) !== false) {
            $code = trim($line);

            if ($code === '') {
                continue;
            }

            // Debounce: skip if same code scanned within DEBOUNCE_SECONDS
            $now = time();
            if ($code === $lastCode && ($now - $lastTime) < self::DEBOUNCE_SECONDS) {
                continue;
            }

            $lastCode = $code;
            $lastTime = $now;

            CLI::write('[' . date('Y-m-d H:i:s') . '] QR scanned: ' . $code, 'cyan');

            $result = $this->callScanApi($baseUrl, $code, $laneId, $type);
            $this->printResult($result, $code);
        }

        CLI::write('[QR Listener] STDIN closed. Exiting.', 'red');
    }

    private function callScanApi(string $baseUrl, string $code, ?string $laneId, string $type): array
    {
        $baseUrl = rtrim($baseUrl, '/');

        if ($laneId) {
            $url = $baseUrl . '/api/qr/scan-lane?' . http_build_query([
                'qr_code'   => $code,
                'lane_id'   => $laneId,
                'lane_type' => $type,
            ]);
        } else {
            $url = $baseUrl . '/api/qr/scan?' . http_build_query(['qr_code' => $code]);
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        $response = curl_exec($ch);
        $errno    = curl_errno($ch);
        curl_close($ch);

        if ($errno || $response === false) {
            return ['success' => false, 'message' => 'Connection error (curl errno ' . $errno . ')'];
        }

        $decoded = json_decode($response, true);

        return is_array($decoded) ? $decoded : ['success' => false, 'message' => 'Invalid response'];
    }

    private function printResult(array $result, string $code): void
    {
        if (!($result['success'] ?? false)) {
            CLI::write('  ✗ FAILED: ' . ($result['message'] ?? 'Unknown error'), 'red');
            return;
        }

        $action  = strtoupper($result['action'] ?? 'unknown');
        $name    = $result['visitor']['name']    ?? 'Unknown';
        $company = $result['visitor']['company'] ?? '';
        $time    = $result['time']               ?? '';
        $color   = ($result['action'] ?? '') === 'checkin' ? 'green' : 'yellow';

        CLI::write('  ✓ ' . $action . ': ' . $name . ($company ? ' (' . $company . ')' : ''), $color);
        CLI::write('    Time: ' . $time, 'white');

        if (!empty($result['duration'])) {
            CLI::write('    Duration: ' . $result['duration'], 'white');
        }

        CLI::newLine();
    }
}
