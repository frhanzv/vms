<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * RFID Reader Configuration
 *
 * Configuration for UHF RFID Reader (Yanzeo SA810 compatible)
 */
class RFIDReader extends BaseConfig
{
    /**
     * Reader Connection Settings
     */

    // Reader IP address (for network-connected readers)
    public string $readerIP = '192.168.1.100';

    // Reader TCP port (default for SA810 is usually 49152)
    public int $readerPort = 49152;

    // Connection timeout in seconds
    public int $connectionTimeout = 5;

    // Reader identifier
    public string $readerID = 'VMS_RFID_001';

    /**
     * Protocol Settings
     *
     * Supported protocols:
     * - 'hex': Hexadecimal EPC format (default for Yanzeo SA810)
     */
    public string $protocol = 'hex';

    /**
     * Gate/Location Mapping
     *
     * If you have multiple readers at different gates,
     * map reader IDs to gate/location IDs
     */
    public array $readerGateMapping = [
        'VMS_RFID_001' => 1,  // Main gate
    ];

    /**
     * Default gate ID if reader is not mapped
     */
    public int $defaultGateID = 1;

    /**
     * Read Debounce
     *
     * Minimum seconds between reads for the same tag
     * to prevent duplicate entries
     */
    public int $readDebounceSeconds = 3;

    /**
     * Logging Settings
     */
    public bool $logAllReads = true;

    public bool $logUnregisteredCards = true;

    /**
     * Notification Settings
     */
    public bool $enableNotifications = false;

    public array $notificationEmails = [];

    /**
     * Antenna Settings
     *
     * SA810 typically has 4 antennas
     */
    public array $enabledAntennas = [1, 2, 3, 4];

    // RF power level (0-30 dBm, typically 20-26 for indoor use)
    public int $rfPower = 26;

    /**
     * Tag Filtering
     *
     * Filter tags based on EPC prefix
     */
    public bool $enableTagFiltering = false;

    public array $allowedTagPrefixes = [
        // 'DD',
        // 'E2',
    ];

    /**
     * Web Configuration URL
     */
    public ?string $webConfigURL = null;

    /**
     * Auto Check-out Settings
     *
     * Automatically check out visitors after certain hours
     */
    public bool $autoCheckout = true;

    // Hours after which to auto check-out
    public int $autoCheckoutHours = 8;
}
