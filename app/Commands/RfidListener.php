<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Exception;

/**
 * RFID Reader Listener Service
 *
 * Continuously listens to the RFID reader's TCP port
 * and processes card scans automatically
 *
 * Usage: php spark rfid:listen
 */
class RfidListener extends BaseCommand
{
    protected $group       = 'RFID';
    protected $name        = 'rfid:listen';
    protected $description = 'Start RFID reader listener service';

    protected $socket;
    protected $isRunning = false;
    protected $config;
    protected $reconnectDelay = 5; // seconds
    protected $lastCardId = '';
    protected $lastCardTime = 0;
    protected $cardCooldown = 3; // seconds to prevent duplicate reads

    public function run(array $params)
    {
        $this->config = config('RFIDReader');

        CLI::write('======================================', 'green');
        CLI::write('   VMS RFID Reader Listener', 'green');
        CLI::write('======================================', 'green');
        CLI::newLine();

        CLI::write("Reader IP: {$this->config->readerIP}", 'yellow');
        CLI::write("Reader Port: {$this->config->readerPort}", 'yellow');
        CLI::write("Protocol: {$this->config->protocol}", 'yellow');
        CLI::newLine();

        CLI::write('Press Ctrl+C to stop the service', 'cyan');
        CLI::newLine();

        // Register signal handlers for graceful shutdown
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGTERM, [$this, 'shutdown']);
            pcntl_signal(SIGINT, [$this, 'shutdown']);
        }

        $this->isRunning = true;

        while ($this->isRunning) {
            try {
                $this->connectAndListen();
            } catch (Exception $e) {
                CLI::error("Connection error: " . $e->getMessage());
                CLI::write("Reconnecting in {$this->reconnectDelay} seconds...", 'yellow');
                sleep($this->reconnectDelay);
            }
        }

        $this->cleanup();
    }

    /**
     * Connect to reader and listen for card data
     */
    protected function connectAndListen()
    {
        CLI::write('Connecting to RFID reader...', 'yellow');

        $this->socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if ($this->socket === false) {
            throw new Exception('Failed to create socket: ' . socket_strerror(socket_last_error()));
        }

        // Set socket options - non-blocking mode for better control
        socket_set_option($this->socket, SOL_SOCKET, SO_KEEPALIVE, 1);
        socket_set_option($this->socket, SOL_TCP, TCP_NODELAY, 1);

        $result = @socket_connect($this->socket, $this->config->readerIP, $this->config->readerPort);

        if ($result === false) {
            $error = socket_last_error($this->socket);
            // EINPROGRESS (10036/115) is expected for non-blocking connects on Windows/Linux
            if ($error !== SOCKET_EINPROGRESS && $error !== SOCKET_EWOULDBLOCK && $error !== 10035) {
                $errorMsg = socket_strerror($error);
                socket_close($this->socket);
                throw new Exception("Failed to connect: {$errorMsg}");
            }

            // Wait for connection to complete
            $write = [$this->socket];
            $read = $except = null;
            if (@socket_select($read, $write, $except, 10) === false) {
                socket_close($this->socket);
                throw new Exception("Connection timeout");
            }
        }

        // Set to non-blocking after connection is established
        socket_set_nonblock($this->socket);

        CLI::write('✓ Connected to RFID reader!', 'green');
        CLI::write('Listening for card scans...', 'cyan');
        CLI::newLine();

        // Main listening loop
        $buffer = '';
        while ($this->isRunning) {
            // Use socket_select to wait for data with timeout
            $read = [$this->socket];
            $write = null;
            $except = null;
            $timeout = 1; // 1 second timeout

            $changed = @socket_select($read, $write, $except, $timeout);

            if ($changed === false) {
                $error = socket_last_error($this->socket);
                throw new Exception('Socket select error: ' . socket_strerror($error));
            }

            if ($changed === 0) {
                // Timeout - no data, continue
                if (function_exists('pcntl_signal_dispatch')) {
                    pcntl_signal_dispatch();
                }
                continue;
            }

            // Data is available to read
            $chunk = @socket_read($this->socket, 1024, PHP_BINARY_READ);

            if ($chunk === false) {
                $error = socket_last_error($this->socket);
                if ($error !== SOCKET_EAGAIN && $error !== SOCKET_EWOULDBLOCK && $error !== 0) {
                    throw new Exception('Socket read error: ' . socket_strerror($error));
                }
                continue;
            }

            if ($chunk === '') {
                // Connection closed by reader - this is normal for some readers
                CLI::write('Reader closed connection (normal behavior)', 'yellow');
                throw new Exception('Connection closed - will reconnect');
            }

            // Append to buffer
            $buffer .= $chunk;

            // Process complete messages (look for end markers or fixed length)
            // Try to extract card IDs from buffer
            if ($this->processBuffer($buffer)) {
                $buffer = ''; // Clear buffer after successful processing
            }

            // Allow signal handlers to run
            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }
        }
    }

    /**
     * Process buffer and extract card data
     */
    protected function processBuffer(&$buffer): bool
    {
        // Parse card EPC from the buffer
        $cardEpc = $this->parseCardEpc($buffer);

        if (!$cardEpc) {
            // If buffer is too large without finding a card, show debug info and clear
            if (strlen($buffer) > 512) {
                CLI::write('Received data but no valid card EPC found', 'yellow');
                CLI::write('Raw data (hex): ' . bin2hex(substr($buffer, 0, 100)) . '...', 'dark_gray');
                return true; // Clear buffer
            }
            return false; // Keep accumulating
        }

        return $this->processCardEpc($cardEpc);
    }

    /**
     * Process a detected card EPC
     */
    protected function processCardEpc($cardEpc): bool
    {
        // Prevent duplicate reads within cooldown period
        $currentTime = time();
        if ($cardEpc === $this->lastCardId && ($currentTime - $this->lastCardTime) < $this->cardCooldown) {
            return true; // Ignore duplicate read but clear buffer
        }

        $this->lastCardId = $cardEpc;
        $this->lastCardTime = $currentTime;

        // Process visitor card scan via API
        $result = $this->processCardScan($cardEpc);

        // Display card with visitor name if available
        $visitorInfo = isset($result['visitor']['name']) ? ' (' . $result['visitor']['name'] . ')' : '';
        CLI::write('[' . date('Y-m-d H:i:s') . '] Card detected: ' . $cardEpc . $visitorInfo, 'green');

        if ($result['success']) {
            $action = $result['action'] === 'checkin' ? 'CHECK IN' : 'CHECK OUT';
            CLI::write("  ✓ {$action}: {$result['visitor']['name']}", 'light_green');
            if (isset($result['gate'])) {
                CLI::write("  Gate: {$result['gate']['name']}", 'cyan');
            }
            CLI::write("  Time: {$result['time']}", 'cyan');
            if (isset($result['duration'])) {
                CLI::write("  Duration: {$result['duration']}", 'cyan');
            }
        } else {
            CLI::error("  ✗ " . $result['message']);
        }

        CLI::newLine();
        return true; // Clear buffer after processing
    }

    /**
     * Parse card EPC from raw data
     */
    protected function parseCardEpc($data): ?string
    {
        // Convert to hex
        $hex = bin2hex($data);
        $hex = strtoupper($hex);

        // Method 1: For Yanzeo SA810 specific protocol (most specific - try first)
        // Pattern: CCFFFF [cmd] [length] [flags] 00 [prefix] DD/E2/30... [EPC 24 chars] [checksum]
        if (preg_match('/CCFFFF[0-9A-F]{8}[0-9A-F]{4}((?:DD|E2|30)[0-9A-F]{22})/', $hex, $matches)) {
            return $matches[1]; // Extract the 24-char EPC
        }

        // Method 2: More flexible - look for DD/E2/30 followed by 22 more hex chars
        if (preg_match('/(DD[0-9A-F]{22})/', $hex, $matches)) {
            return $matches[1];
        }

        if (preg_match('/(E2[0-9A-F]{22})/', $hex, $matches)) {
            return $matches[1];
        }

        if (preg_match('/(30[0-9A-F]{22})/', $hex, $matches)) {
            return $matches[1];
        }

        // Method 3: Generic EPC96 tag - skip common prefixes
        $cleanHex = preg_replace('/^[0-9A-F]*?(DD|E2|30)/', '$1', $hex);
        if (preg_match('/^([0-9A-F]{24})/', $cleanHex, $matches)) {
            $possibleEpc = $matches[1];
            // Validate it's not all zeros or all Fs
            if ($possibleEpc !== str_repeat('0', 24) && $possibleEpc !== str_repeat('F', 24)) {
                $uniqueChars = array_unique(str_split($possibleEpc));
                if (count($uniqueChars) > 3) {
                    return $possibleEpc;
                }
            }
        }

        return null;
    }

    /**
     * Process card scan via local API
     */
    protected function processCardScan($cardEpc): array
    {
        $ch = curl_init();

        $url = base_url("api/rfid/scan?card_epc=" . urlencode($cardEpc));

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
            return json_decode($response, true) ?: ['success' => false, 'message' => 'Invalid response'];
        }

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
        CLI::write('Shutting down RFID listener service...', 'yellow');
        $this->isRunning = false;
    }

    /**
     * Cleanup resources
     */
    protected function cleanup()
    {
        if ($this->socket) {
            @socket_close($this->socket);
        }
        CLI::write('Service stopped.', 'green');
    }
}
