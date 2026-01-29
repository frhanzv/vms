<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\LaneModel;
use Exception;

/**
 * Multi-Reader RFID Listener Service
 *
 * Listens to multiple RFID readers simultaneously (one per gate/lane)
 * Automatically detects new gates and connects to their readers
 *
 * Usage: php spark rfid:listen-all
 */
class RfidListenerMulti extends BaseCommand
{
    protected $group       = 'RFID';
    protected $name        = 'rfid:listen-all';
    protected $description = 'Start RFID listener for all gate readers';

    protected $readers = []; // [lane_id => ['socket' => resource, 'lane' => array, 'buffer' => string]]
    protected $isRunning = false;
    protected $laneModel;
    protected $lastLaneCheck = 0;
    protected $laneCheckInterval = 5; // Check for new lanes/config changes every 5 seconds
    protected $lastCardId = [];
    protected $cardCooldown = 3; // seconds to prevent duplicate reads

    public function run(array $params)
    {
        $this->laneModel = new LaneModel();

        CLI::write('======================================', 'green');
        CLI::write('   Multi-Reader RFID Listener', 'green');
        CLI::write('======================================', 'green');
        CLI::newLine();

        CLI::write('Config check interval: ' . $this->laneCheckInterval . ' seconds', 'cyan');
        CLI::write('Scanning for gate readers...', 'yellow');
        CLI::write('Press Ctrl+C to stop the service', 'cyan');
        CLI::newLine();

        // Register signal handlers for graceful shutdown
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGTERM, [$this, 'shutdown']);
            pcntl_signal(SIGINT, [$this, 'shutdown']);
        }

        $this->isRunning = true;

        // Main loop
        while ($this->isRunning) {
            try {
                // Check for new lanes periodically
                $timeSinceCheck = time() - $this->lastLaneCheck;
                if ($timeSinceCheck > $this->laneCheckInterval) {
                    $this->updateReaderConnections();
                    $this->lastLaneCheck = time();
                }

                // Listen to all connected readers
                $this->listenToAllReaders();

                // Allow signal handlers to run
                if (function_exists('pcntl_signal_dispatch')) {
                    pcntl_signal_dispatch();
                }

                usleep(10000); // 10ms sleep to prevent CPU overload while maintaining fast response

            } catch (Exception $e) {
                CLI::error("Error: " . $e->getMessage());
                sleep(5);
            }
        }

        $this->cleanup();
    }

    /**
     * Update reader connections based on lanes
     */
    protected function updateReaderConnections()
    {
        $startTime = microtime(true);

        // Get all active lanes with RFID readers configured
        $lanes = $this->laneModel
            ->where('status', 'active')
            ->where('rfid_reader_ip IS NOT NULL')
            ->where('rfid_reader_ip !=', '')
            ->findAll();

        // Track all lane IDs that should be connected
        $activeLaneIds = [];

        // Connect to each lane's reader
        foreach ($lanes as $lane) {
            $laneId = $lane['id'];
            $activeLaneIds[] = $laneId;

            // Check if lane already connected
            if (isset($this->readers[$laneId])) {
                $existing = $this->readers[$laneId]['lane'];
                // Check if config changed (IP or port)
                $ipChanged = ($existing['rfid_reader_ip'] ?? '') !== ($lane['rfid_reader_ip'] ?? '');
                $portChanged = ($existing['rfid_reader_port'] ?? 49152) !== ($lane['rfid_reader_port'] ?? 49152);

                if ($ipChanged || $portChanged) {
                    // Config changed, reconnect
                    $this->disconnectReader($laneId);
                    $this->connectReader($lane);
                }
            } else {
                // New lane, connect
                $this->connectReader($lane);
            }
        }

        // Remove disconnected lanes
        $currentLaneIds = array_keys($this->readers);
        $removedLanes = array_diff($currentLaneIds, $activeLaneIds);
        foreach ($removedLanes as $laneId) {
            $this->disconnectReader($laneId);
        }

        $elapsed = round((microtime(true) - $startTime) * 1000, 2);
        $intervalUsed = time() - $this->lastLaneCheck;
        CLI::write('[' . date('H:i:s') . '] Active readers: ' . count($this->readers) . ' (checked ' . $intervalUsed . 's ago, took ' . $elapsed . 'ms)', 'dark_gray');
    }

    /**
     * Connect to a reader
     */
    protected function connectReader($lane)
    {
        $laneId = $lane['id'];
        $ip = $lane['rfid_reader_ip'];
        $port = $lane['rfid_reader_port'] ?: 49152;

        try {
            // Quick check if IP is reachable before attempting socket connection
            $pingTest = @fsockopen($ip, $port, $errno, $errstr, 0.3);
            if (!$pingTest) {
                // Skip connection attempt if not reachable (will retry in 5 seconds)
                return;
            }
            fclose($pingTest);

            $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

            if ($socket === false) {
                throw new Exception('Failed to create socket: ' . socket_strerror(socket_last_error()));
            }

            // Set socket options
            socket_set_option($socket, SOL_SOCKET, SO_KEEPALIVE, 1);
            socket_set_option($socket, SOL_TCP, TCP_NODELAY, 1);

            $result = @socket_connect($socket, $ip, $port);

            if ($result === false) {
                $error = socket_last_error($socket);
                // EINPROGRESS is expected for non-blocking connects
                if ($error !== SOCKET_EINPROGRESS && $error !== SOCKET_EWOULDBLOCK && $error !== 10035) {
                    socket_close($socket);
                    throw new Exception("Failed to connect: " . socket_strerror($error));
                }

                // Wait for connection to complete
                $write = [$socket];
                $read = $except = null;
                if (@socket_select($read, $write, $except, 2) === false) {
                    socket_close($socket);
                    throw new Exception("Connection timeout");
                }
            }

            // Set to non-blocking after connection
            socket_set_nonblock($socket);

            $laneName = $lane['name'] ?? 'Lane ' . $laneId;
            $laneType = $lane['lane_type'] ?? 'entry';

            $this->readers[$laneId] = [
                'socket' => $socket,
                'lane' => $lane,
                'buffer' => '',
                'last_activity' => time()
            ];

            CLI::write("✓ Connected to {$laneName} ({$ip}:{$port}) [Type: {$laneType}]", 'green');

        } catch (Exception $e) {
            $errLaneName = $lane['name'] ?? 'Lane';
            CLI::error("✗ Failed to connect to {$errLaneName} ({$ip}:{$port}): " . $e->getMessage());
        }
    }

    /**
     * Disconnect from a reader
     */
    protected function disconnectReader($laneId)
    {
        if (isset($this->readers[$laneId])) {
            $reader = $this->readers[$laneId];
            if ($reader['socket']) {
                @socket_close($reader['socket']);
            }
            $laneName = $reader['lane']['name'] ?? 'Lane';
            CLI::write("✗ Disconnected from {$laneName}", 'yellow');
            unset($this->readers[$laneId]);
        }
    }

    /**
     * Listen to all connected readers
     */
    protected function listenToAllReaders()
    {
        if (empty($this->readers)) {
            return;
        }

        // Prepare socket arrays for select
        $read = [];
        $socketToLane = [];

        foreach ($this->readers as $laneId => $reader) {
            $read[] = $reader['socket'];
            $key = array_search($reader['socket'], $read, true);
            $socketToLane[$key] = $laneId;
        }

        $write = null;
        $except = null;
        $timeout = 0;

        $changed = @socket_select($read, $write, $except, $timeout, 100000); // 100ms timeout

        if ($changed === false) {
            // Error in select
            return;
        }

        if ($changed === 0) {
            // No data available
            return;
        }

        // Read from sockets that have data
        foreach ($read as $key => $socket) {
            $laneId = $socketToLane[$key];

            $chunk = @socket_read($socket, 1024, PHP_BINARY_READ);

            if ($chunk === false) {
                $error = socket_last_error($socket);
                if ($error !== SOCKET_EAGAIN && $error !== SOCKET_EWOULDBLOCK && $error !== 0) {
                    // Connection error, disconnect and immediately try to reconnect
                    $lane = $this->readers[$laneId]['lane'];
                    $this->disconnectReader($laneId);
                    // Immediate reconnection attempt
                    $this->connectReader($lane);
                }
                continue;
            }

            if ($chunk === '') {
                // Connection closed, disconnect and immediately try to reconnect
                $lane = $this->readers[$laneId]['lane'];
                $this->disconnectReader($laneId);
                // Immediate reconnection attempt
                $this->connectReader($lane);
                continue;
            }

            // Append to buffer
            $this->readers[$laneId]['buffer'] .= $chunk;
            $this->readers[$laneId]['last_activity'] = time();

            // Process buffer
            if ($this->processBuffer($laneId)) {
                $this->readers[$laneId]['buffer'] = ''; // Clear buffer after processing
            }
        }
    }

    /**
     * Process buffer for a specific reader
     */
    protected function processBuffer($laneId): bool
    {
        $buffer = $this->readers[$laneId]['buffer'];
        $lane = $this->readers[$laneId]['lane'];

        // Parse card EPC
        $cardEpc = $this->parseCardEpc($buffer);

        if (!$cardEpc) {
            // Clear buffer if too large
            if (strlen($buffer) > 512) {
                return true;
            }
            return false;
        }

        return $this->processCardEpc($cardEpc, $lane);
    }

    /**
     * Process a detected card EPC
     */
    protected function processCardEpc($cardEpc, $lane): bool
    {
        // Prevent duplicate reads within cooldown period
        $currentTime = time();
        $key = $lane['id'] . '_' . $cardEpc;

        if (isset($this->lastCardId[$key]) &&
            ($currentTime - $this->lastCardId[$key]) < $this->cardCooldown) {
            return true; // Ignore duplicate but clear buffer
        }

        $this->lastCardId[$key] = $currentTime;

        $laneName = $lane['name'] ?? 'Lane';

        // Process card scan using lane's type
        $result = $this->processCardScan($cardEpc, $lane);

        // Display card with visitor name if available
        $visitorInfo = isset($result['visitor']['name']) ? ' (' . $result['visitor']['name'] . ')' : '';
        CLI::write('[' . date('Y-m-d H:i:s') . '] Card: ' . $cardEpc . $visitorInfo . ' @ ' . $laneName, 'green');

        if ($result['success']) {
            $action = $result['action'] === 'checkin' ? 'CHECK IN' : 'CHECK OUT';
            CLI::write("  ✓ {$action}: {$result['visitor']['name']}", 'light_green');
            if (isset($result['duration'])) {
                CLI::write("  Duration: {$result['duration']}", 'cyan');
            }
        } else {
            CLI::error("  ✗ " . $result['message']);
        }

        CLI::newLine();
        return true;
    }

    /**
     * Parse card EPC from raw data
     */
    protected function parseCardEpc($data): ?string
    {
        $hex = bin2hex($data);
        $hex = strtoupper($hex);

        // Method 1: Yanzeo SA810 specific protocol
        if (preg_match('/CCFFFF[0-9A-F]{8}[0-9A-F]{4}((?:DD|E2|30)[0-9A-F]{22})/', $hex, $matches)) {
            return $matches[1];
        }

        // Method 2: Look for DD/E2/30 prefixed tags
        if (preg_match('/(DD[0-9A-F]{22})/', $hex, $matches)) {
            return $matches[1];
        }

        if (preg_match('/(E2[0-9A-F]{22})/', $hex, $matches)) {
            return $matches[1];
        }

        if (preg_match('/(30[0-9A-F]{22})/', $hex, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Process card scan via local API
     */
    protected function processCardScan($cardEpc, $lane): array
    {
        $ch = curl_init();

        // Determine lane type based on in_bound and out_bound settings
        $laneType = 'entry'; // default
        if ($lane['in_bound'] === 'yes' && $lane['out_bound'] === 'yes') {
            $laneType = 'both';
        } elseif ($lane['out_bound'] === 'yes') {
            $laneType = 'exit';
        } elseif ($lane['in_bound'] === 'yes') {
            $laneType = 'entry';
        }

        // Pass card_epc, lane_id, and lane_type to the API
        $url = base_url("api/rfid/scan-lane?card_epc=" . urlencode($cardEpc) .
                       "&lane_id=" . urlencode($lane['id']) .
                       "&lane_type=" . urlencode($laneType));

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $decoded = json_decode($response, true);
            if (!$decoded) {
                // Log the actual response for debugging
                CLI::write("  ✗ Failed to decode JSON. Response: " . substr($response, 0, 200), 'red');
                return ['success' => false, 'message' => 'Invalid JSON response'];
            }
            return $decoded;
        }

        CLI::write("  ✗ HTTP Error: {$httpCode}", 'red');
        return [
            'success' => false,
            'message' => "HTTP Error: {$httpCode}"
        ];
    }

    /**
     * Shutdown handler
     */
    public function shutdown()
    {
        CLI::newLine();
        CLI::write('Shutting down multi-reader listener...', 'yellow');
        $this->isRunning = false;
    }

    /**
     * Cleanup resources
     */
    protected function cleanup()
    {
        foreach ($this->readers as $laneId => $reader) {
            $this->disconnectReader($laneId);
        }
        CLI::write('Service stopped.', 'green');
    }
}
