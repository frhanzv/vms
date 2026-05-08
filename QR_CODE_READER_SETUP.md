# QR Code Reader Setup Guide

This guide explains how to set up and use the QR Code reader system for the Visitor Management System (VMS) as an alternative to RFID card scanning.

## Overview

The QR Code reader system allows the VMS to perform visitor check-in and check-out by scanning a QR code printed on the visitor's badge or invitation. It supports the same check-in/check-out logic as the RFID system and can be configured per lane.

Key differences from RFID:

| Feature | RFID | QR Code |
|---|---|---|
| Hardware | Network UHF reader (TCP) | USB HID barcode/QR scanner |
| Visitor badge | Physical RFID card | Printed QR code |
| Card assignment | Required | Not required |
| Listener mode | TCP socket | STDIN (keyboard emulation) |
| API endpoint | `/api/rfid/scan` | `/api/qr/scan` |

---

## Prerequisites

### Hardware

- Any USB HID barcode/QR scanner (2D scanners recommended, e.g., Zebra, Honeywell, Datalogic)
- The scanner must operate in **HID-keyboard mode** (most USB scanners do by default) — it "types" the scanned value followed by Enter

### Software

- PHP 8.0 or higher with `curl` extension enabled
- VMS application with migrations up to date (`php spark migrate`)
- Visitor badges must include a QR code containing `INV-{invitation_id}` (see [Badge QR Format](#badge-qr-format))

---

## Configuration

### 1. Set Scan Type per Lane

Each gate lane can independently use RFID or QR Code scanning.

1. Go to **Config → Lanes Management**
2. Click **Edit** on the lane connected to your QR scanner
3. Under **Scan Reader Settings**, set **Scan Type** to `QR Code Reader`
4. Save — no IP or port configuration is needed for QR mode

> **Tip:** You can mix scan types across lanes — for example, one lane uses RFID and another uses QR Code.

### 2. Global Default Scan Type (optional)

A global `scan_type` setting is stored in the `settings` table. It is seeded with `rfid` by default. To change it:

```sql
UPDATE settings SET setting_value = 'qr_code' WHERE setting_key = 'scan_type';
```

Or query it via the status endpoint:

```
GET /api/qr/status
```

---

## Badge QR Format

The QR code printed on a visitor's badge must encode one of:

| Format | Example | Notes |
|---|---|---|
| `INV-{id}` | `INV-142` | Recommended — clearly prefixed |
| `{id}` | `142` | Plain integer invitation ID |

Generate the QR code using the invitation ID from the `invitations` table. Any QR code generation library or online tool can produce the required image.

Example using the `endroid/qr-code` library (already a VMS dependency):

```php
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$qr     = QrCode::create('INV-' . $invitationId)->setSize(200);
$writer = new PngWriter();
$result = $writer->write($qr);
// $result->getString() contains the PNG binary
```

---

## Running the QR Listener

The QR listener is a CLI command that reads scanned codes from STDIN. Because USB HID scanners behave like keyboards, the terminal window running this command must have focus when a badge is scanned.

### Basic usage (single reader, no lane association)

```bash
php spark qr:listen
```

### With lane and direction

```bash
# Entry lane — check-in only
php spark qr:listen --lane=1 --type=entry

# Exit lane — check-out only
php spark qr:listen --lane=2 --type=exit

# Single gate for both check-in and check-out
php spark qr:listen --lane=3 --type=both
```

### Custom VMS base URL

```bash
php spark qr:listen --lane=1 --type=entry --base-url=http://192.168.1.50:8080
```

### Available options

| Option | Default | Description |
|---|---|---|
| `--lane` | _(none)_ | Lane ID to associate scans with |
| `--type` | `both` | `entry`, `exit`, or `both` |
| `--base-url` | `http://localhost:8080` | Base URL of the VMS server |

### Sample console output

```
[QR Listener] Starting...
[QR Listener] Base URL : http://localhost:8080
[QR Listener] Lane ID  : 1
[QR Listener] Lane Type: entry
[QR Listener] Waiting for QR codes... (Ctrl+C to stop)

[2026-05-08 09:03:12] QR scanned: INV-142
  ✓ CHECKIN: John Doe (Acme Corp)
    Time: 2026-05-08 09:03:12

[2026-05-08 17:45:01] QR scanned: INV-142
  ✓ CHECKOUT: John Doe (Acme Corp)
    Time: 2026-05-08 17:45:01
    Duration: 8 hours 41 minutes
```

---

## API Endpoints

All QR endpoints are **public** (no authentication required) so the listener service can call them freely.

### 1. Single reader scan

```
GET /api/qr/scan?qr_code=INV-142
```

**Response (check-in):**
```json
{
  "success": true,
  "action": "checkin",
  "visitor": { "name": "John Doe", "company": "Acme Corp" },
  "time": "2026-05-08 09:03:12",
  "duration": null,
  "qr_code": "INV-142"
}
```

**Response (check-out):**
```json
{
  "success": true,
  "action": "checkout",
  "visitor": { "name": "John Doe", "company": "Acme Corp" },
  "time": "2026-05-08 17:45:01",
  "duration": "8 hours 41 minutes",
  "qr_code": "INV-142"
}
```

### 2. Lane scan (with direction)

```
GET /api/qr/scan-lane?qr_code=INV-142&lane_id=1&lane_type=entry
```

Response includes an additional `lane` object:
```json
{
  "success": true,
  "action": "checkin",
  "visitor": { "name": "John Doe", "company": "Acme Corp" },
  "lane": { "id": "1", "type": "entry" },
  "time": "2026-05-08 09:03:12",
  "duration": null,
  "qr_code": "INV-142"
}
```

### 3. Status

```
GET /api/qr/status
```

```json
{
  "success": true,
  "scan_type": "qr_code",
  "reader": { "type": "qr_code", "mode": "usb_hid", "status": "configured" }
}
```

---

## Check-in / Check-out Logic

The QR scan follows the same rules as RFID:

1. **Parse QR code** → extract invitation ID (`INV-142` → `142`)
2. **Look up invitation** — must exist with status `Approved`
3. **Find or create `invitation_visitors` record** — auto-created if none exists (no physical card assignment needed)
4. **Determine action:**
   - Lane type `entry` → always check in
   - Lane type `exit` → always check out
   - Lane type `both` (default) → check in if not yet checked in, check out otherwise
5. **Update `invitation_visitors`** atomically (optimistic locking via `version` column)
6. **Log** the scan to `visitor_card_logs` with `scan_source = 'qr_code'` and `visitor_card_id = NULL`

---

## Running as a Service

### Windows — NSSM

1. Download [NSSM](https://nssm.cc/) and place it on your PATH
2. Open a command prompt as Administrator and run:

```cmd
nssm install VMS_QR_Listener "C:\laragon\bin\php\php8.x\php.exe" "C:\laragon\www\vms\spark qr:listen --lane=1 --type=entry"
nssm set VMS_QR_Listener AppDirectory "C:\laragon\www\vms"
nssm set VMS_QR_Listener AppExit Default Restart
nssm set VMS_QR_Listener AppStdout "C:\laragon\www\vms\writable\logs\qr-listener-stdout.log"
nssm set VMS_QR_Listener AppStderr "C:\laragon\www\vms\writable\logs\qr-listener-stderr.log"
nssm start VMS_QR_Listener
```

> **Note for USB HID mode:** When running as a Windows service, the process has no attached console and cannot receive keyboard input directly from the USB scanner. For kiosk installations, it is better to run the listener in a visible terminal window on the kiosk PC that has focus, or use the web-based kiosk approach below.

### Windows — Startup Batch File

Create `start_qr_listener.bat` in the VMS root:

```bat
@echo off
title VMS QR Code Listener - Lane 1 (Entry)
cd /d C:\laragon\www\vms
php spark qr:listen --lane=1 --type=entry
pause
```

Add a shortcut to this file in the Windows Startup folder (`shell:startup`) so it launches automatically on login.

### Linux — systemd

Create `/etc/systemd/system/vms-qr-listener.service`:

```ini
[Unit]
Description=VMS QR Code Listener (Lane 1, Entry)
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/vms
ExecStart=/usr/bin/php /var/www/vms/spark qr:listen --lane=1 --type=entry
StandardInput=tty
TTYPath=/dev/ttyUSB0
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

> Set `TTYPath` to the device file of your USB QR scanner (find it with `dmesg | tail` after plugging in the scanner).

```bash
sudo systemctl enable vms-qr-listener
sudo systemctl start vms-qr-listener
sudo systemctl status vms-qr-listener
```

---

## Database Changes

The QR Code feature introduced two migrations:

### `2026-05-08-000001_AddScanTypeToLanes`

- Added `scan_type` ENUM(`rfid`, `qr_code`) DEFAULT `rfid` to the `lanes` table
- Seeded `settings` table with `scan_type = rfid`

### `2026-05-08-000002_AddQrScanSupport`

- Made `visitor_card_id` nullable in `visitor_card_logs` (QR scans have no physical card)
- Added `scan_source` ENUM(`rfid`, `qr_code`) DEFAULT `rfid` to `visitor_card_logs`

### Querying scan history

```sql
SELECT
    vcl.scanned_at,
    vcl.action,
    vcl.scan_source,
    vc.card_id        AS card_epc,
    i.full_name       AS visitor_name,
    i.company,
    l.lane            AS lane_name
FROM visitor_card_logs vcl
LEFT JOIN visitor_cards vc ON vc.id = vcl.visitor_card_id
LEFT JOIN invitations   i  ON i.id  = vcl.invitation_id
LEFT JOIN lanes         l  ON l.id  = vcl.lane_id
ORDER BY vcl.scanned_at DESC
LIMIT 100;
```

Filter for QR-only scans:
```sql
WHERE vcl.scan_source = 'qr_code'
```

---

## Troubleshooting

### Scanner input not being received by the listener

- Make sure the terminal window running `php spark qr:listen` has **keyboard focus** when scanning
- Test by typing a code manually (e.g., `INV-1` then Enter) — the listener should process it
- Verify the scanner is in HID-keyboard mode (consult the scanner manual; most have a programming barcode for this)

### "Invalid QR code format" response

- Ensure the QR code encodes only `INV-{id}` or a plain integer — no extra characters or whitespace
- Some scanners add a prefix or suffix; use the scanner's programming barcodes to strip them

### "No approved invitation found"

- Verify the invitation ID exists in the `invitations` table
- Check the invitation `status` is `Approved`
- The invitation does not need an RFID card assigned for QR check-in

### "Visitor has already checked out"

- Each invitation can only be checked out once per day
- If the visitor needs to re-enter, an admin must clear `check_out_time` in `invitation_visitors` manually

### curl errors in the listener

- Confirm the VMS server is running and reachable at the `--base-url` address
- Test with: `curl "http://localhost:8080/api/qr/scan?qr_code=INV-1"`
- Ensure `curl` extension is enabled in PHP: `php -m | findstr curl`

---

## Security Notes

- The `/api/qr/*` endpoints are **intentionally unauthenticated** so the CLI listener can call them without a session
- Restrict access to these endpoints at the network/firewall level — they should only be reachable from the kiosk/gate machines
- All scans are recorded in `visitor_card_logs` with `scan_source = 'qr_code'` for audit purposes
- QR codes on badges contain only the invitation ID — no sensitive personal data is embedded

---

## Comparison: RFID vs QR Code

| | RFID | QR Code |
|---|---|---|
| **Hardware cost** | Higher (networked UHF reader) | Lower (USB HID scanner) |
| **Badge cost** | RFID chip card | Printed paper |
| **Read distance** | Up to several metres | ~5–30 cm (scanner must point at code) |
| **Speed** | Passive (hands-free) | Active (scan gesture required) |
| **Setup complexity** | Network configuration needed | Plug-and-play USB |
| **Card assignment** | Required in VMS | Not required |
| **Best for** | High-traffic automatic gates | Manual desk check-in |

---

**Last Updated:** May 8, 2026
