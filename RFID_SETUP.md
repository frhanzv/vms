# RFID Listener Setup Guide

This guide explains how to set up and use the RFID listener system for the Visitor Management System (VMS).

## Overview

The RFID listener system allows the VMS to automatically detect visitor cards when they are scanned by RFID readers (e.g., Yanzeo SA810 UHF RFID Reader). The system supports:

- Single reader mode (one RFID reader)
- Multi-reader mode (multiple RFID readers at different gates/lanes)
- Automatic check-in and check-out based on card scans
- Real-time visitor tracking

## Prerequisites

1. **RFID Reader Hardware**
   - Yanzeo SA810 UHF RFID Reader (or compatible)
   - Network-connected (TCP/IP)
   - Properly configured and connected to your network

2. **PHP Requirements**
   - PHP 8.0 or higher
   - `sockets` extension enabled
   - `pcntl` extension enabled (optional, for graceful shutdown)

3. **Database Setup**
   - Run migrations to create required tables:
     ```bash
     php spark migrate
     ```

## Configuration

### 1. RFID Reader Configuration

Edit `app/Config/RFIDReader.php` to configure your RFID reader settings:

```php
public string $readerIP = '192.168.1.100';  // Your RFID reader IP address
public int $readerPort = 49152;              // Default port for Yanzeo SA810
public int $readDebounceSeconds = 3;         // Prevent duplicate reads
```

### 2. Lane Configuration (for Multi-Reader Mode)

If you have multiple RFID readers at different gates:

1. Go to **Config** → **Lanes Management**
2. Add or edit lanes
3. Configure RFID settings for each lane:
   - **RFID Reader IP**: IP address of the reader (e.g., `192.168.1.100`)
   - **RFID Reader Port**: Port number (default: `49152`)
   - **RFID Enabled**: Enable/disable RFID for this lane
   - **Lane Type**: Set to `entry`, `exit`, or `both`

## Usage

### Single Reader Mode

For a single RFID reader setup:

```bash
php spark rfid:listen
```

Or use the batch file:
```bash
start_rfid_listener_single.bat
```

### Multi-Reader Mode (Recommended)

For multiple RFID readers (automatically detects all configured lanes):

```bash
php spark rfid:listen-all
```

Or use the batch file:
```bash
start_rfid_listener.bat
```

### What Happens When a Card is Scanned?

1. **Card Detection**: The listener detects the card's EPC (Electronic Product Code)
2. **Validation**: Checks if the card is registered in the system
3. **Status Check**: Verifies the card status (active/in_use)
4. **Invitation Lookup**: Finds the active invitation for today
5. **Action Determination**:
   - **Check-in**: If visitor hasn't checked in yet
   - **Check-out**: If visitor is already checked in
6. **Database Update**: Updates invitation and card status
7. **Logging**: Records the scan in `visitor_card_logs` table

## API Endpoints

The RFID system exposes the following API endpoints:

### 1. Single Reader Scan
```
GET /api/rfid/scan?card_epc=DD1234567890ABCDEF012345
```

### 2. Multi-Reader Scan (with Lane Info)
```
GET /api/rfid/scan-lane?card_epc=DD1234567890ABCDEF012345&lane_id=1&lane_type=entry
```

### 3. Reader Status
```
GET /api/rfid/status
```

### 4. Test Connection
```
GET /api/rfid/test-connection
```

## Running as a Service

### Windows Service (using NSSM)

1. Download NSSM (Non-Sucking Service Manager) from https://nssm.cc/
2. Install the service:
   ```cmd
   nssm install VMS_RFID_Listener "C:\path\to\php.exe" "C:\laragon\www\vms\spark rfid:listen-all"
   ```
3. Configure service settings:
   ```cmd
   nssm set VMS_RFID_Listener AppDirectory "C:\laragon\www\vms"
   nssm set VMS_RFID_Listener AppExit Default Restart
   nssm set VMS_RFID_Listener AppStdout "C:\laragon\www\vms\writable\logs\rfid-stdout.log"
   nssm set VMS_RFID_Listener AppStderr "C:\laragon\www\vms\writable\logs\rfid-stderr.log"
   ```
4. Start the service:
   ```cmd
   nssm start VMS_RFID_Listener
   ```

### Linux Service (systemd)

Create a service file at `/etc/systemd/system/vms-rfid-listener.service`:

```ini
[Unit]
Description=VMS RFID Listener Service
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/vms
ExecStart=/usr/bin/php /var/www/vms/spark rfid:listen-all
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

Enable and start the service:
```bash
sudo systemctl enable vms-rfid-listener
sudo systemctl start vms-rfid-listener
sudo systemctl status vms-rfid-listener
```

## Troubleshooting

### Connection Issues

1. **Cannot connect to RFID reader**
   - Verify the IP address and port in configuration
   - Check network connectivity: `ping <reader-ip>`
   - Ensure firewall allows connection on the specified port
   - Test connection via API: `GET /api/rfid/test-connection`

2. **Socket errors**
   - Ensure `sockets` extension is enabled in PHP
   - Check: `php -m | grep sockets`
   - Enable in `php.ini`: `extension=sockets`

### Card Detection Issues

1. **Card not detected**
   - Check if card EPC is registered in Visitor Cards Management
   - Verify card status is `active` or `in_use`
   - Check RFID reader antenna power level
   - Ensure card is within read range

2. **Duplicate reads**
   - Adjust `readDebounceSeconds` in configuration (default: 3 seconds)
   - Check if multiple antennas are detecting the same card

### Invitation Issues

1. **No active invitation found**
   - Verify invitation exists for today's date
   - Check invitation status is `approved`
   - Ensure visitor card is assigned to the invitation

## Monitoring

### View Real-time Activity

The listener outputs real-time activity to the console:

```
✓ Connected to Main Gate (192.168.1.100:49152) [Type: entry]
✓ Connected to Exit Gate (192.168.1.101:49152) [Type: exit]
[2026-01-29 12:15:30] Active readers: 2

[2026-01-29 12:16:45] Card: DD1234567890ABCDEF012345 (John Doe) @ Main Gate
  ✓ CHECK IN: John Doe
  Time: 2026-01-29 12:16:45

[2026-01-29 16:30:22] Card: DD1234567890ABCDEF012345 (John Doe) @ Exit Gate
  ✓ CHECK OUT: John Doe
  Duration: 4 hours 13 minutes
```

### View Logs in Database

Query the `visitor_card_logs` table to see all card scan history:

```sql
SELECT 
    vcl.*,
    vc.card_id as card_epc,
    i.visitor_name,
    l.name as lane_name
FROM visitor_card_logs vcl
LEFT JOIN visitor_cards vc ON vc.id = vcl.visitor_card_id
LEFT JOIN invitations i ON i.id = vcl.invitation_id
LEFT JOIN lanes l ON l.id = vcl.lane_id
ORDER BY vcl.scanned_at DESC
LIMIT 100;
```

## Card EPC Format

The system supports standard EPC-96 format tags with the following prefixes:
- `DD` - Most common for Yanzeo SA810
- `E2` - Standard EPC Gen2
- `30` - Alternative format

Example EPC: `DD1234567890ABCDEF012345` (24 hex characters)

## Security Notes

1. The RFID API endpoints (`/api/rfid/*`) are intentionally **not protected** by authentication to allow the listener service to call them
2. Ensure these endpoints are only accessible from trusted networks
3. Consider implementing IP whitelisting in your firewall or web server configuration
4. All card scans are logged for audit purposes

## Support

For issues or questions:
1. Check the logs in `writable/logs/`
2. Review this documentation
3. Contact system administrator

---

**Last Updated**: January 29, 2026
