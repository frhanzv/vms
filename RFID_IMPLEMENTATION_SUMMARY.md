# RFID Listener Implementation Summary

## What Was Implemented

I've successfully implemented a complete RFID listener system for the VMS project based on the workwise system. Here's what was created:

### 1. Configuration File
- **`app/Config/RFIDReader.php`** - RFID reader configuration
  - Reader IP, port, protocol settings
  - Debounce settings to prevent duplicate reads
  - Logging and notification options

### 2. Command Files (Listener Services)
- **`app/Commands/RfidListener.php`** - Single reader listener
  - Continuously listens to one RFID reader via TCP socket
  - Parses card EPC from raw data (supports DD, E2, 30 prefixes)
  - Calls API endpoint when card is detected
  - Auto-reconnects on connection loss
  - Graceful shutdown handling

- **`app/Commands/RfidListenerMulti.php`** - Multi-reader listener
  - Listens to multiple RFID readers simultaneously
  - Auto-detects lanes with RFID configuration
  - Handles per-lane reader connections
  - Dynamic connection management (adds/removes readers as lanes change)
  - More efficient for multiple gate setups

### 3. Controller
- **`app/Controllers/RFID.php`** - RFID API endpoints
  - `GET /api/rfid/scan` - Single reader endpoint
  - `GET /api/rfid/scan-lane` - Multi-reader with lane info
  - `GET /api/rfid/status` - Reader status check
  - `GET /api/rfid/test-connection` - Connection test
  - Handles check-in/check-out logic
  - Updates card status and invitation records
  - Logs all card scans

### 4. Model
- **`app/Models/VisitorCardLogModel.php`** - Card scan logging
  - Tracks all card scan activity
  - Provides statistics and reporting functions
  - Searchable logs with filters

### 5. Database Migration
- **`app/Database/Migrations/2026-01-29-041540_AddRfidFieldsToLanes.php`**
  - Added RFID fields to `lanes` table:
    - `rfid_reader_ip` - Reader IP address
    - `rfid_reader_port` - Reader port (default 49152)
    - `rfid_enabled` - Enable/disable RFID per lane
  - Created `visitor_card_logs` table for audit trail

### 6. Routes
- Updated **`app/Config/Routes.php`** to add RFID API routes (no authentication required)

### 7. Batch Files
- **`start_rfid_listener.bat`** - Easy start for multi-reader mode
- **`start_rfid_listener_single.bat`** - Easy start for single reader mode

### 8. Documentation
- **`RFID_SETUP.md`** - Complete setup and troubleshooting guide

## How It Works

### Card Scan Flow:

1. **RFID Reader** detects a card and sends data via TCP socket
2. **Listener Service** receives the raw data
3. **Parser** extracts the card EPC (24 hex characters)
4. **Debounce Check** prevents duplicate reads (3-second cooldown)
5. **API Call** to `/api/rfid/scan` or `/api/rfid/scan-lane`
6. **Controller** validates card and finds active invitation
7. **Logic Determination**:
   - If not checked in → **CHECK IN**
   - If already checked in → **CHECK OUT**
8. **Database Update** - Updates invitation_visitors and card status
9. **Logging** - Records scan in visitor_card_logs
10. **Response** - Returns success/failure to listener

### Supported Card Formats:
- **Yanzeo SA810 Protocol**: `CCFFFF...` format with EPC embedded
- **DD prefix**: `DD1234567890ABCDEF012345`
- **E2 prefix**: `E2 1234567890ABCDEF012345`
- **30 prefix**: `301234567890ABCDEF012345`

## Usage

### Start the Listener:

**For multiple readers (recommended):**
```bash
php spark rfid:listen-all
# Or double-click: start_rfid_listener.bat
```

**For single reader:**
```bash
php spark rfid:listen
# Or double-click: start_rfid_listener_single.bat
```

### Configure Reader:

1. Edit `app/Config/RFIDReader.php` - Set main reader IP/port
2. For multi-reader: Go to Config → Lanes Management
   - Set RFID Reader IP for each lane
   - Set RFID Reader Port (default: 49152)
   - Enable RFID for the lane
   - Set lane type (entry/exit/both)

### Test Connection:

```bash
curl http://localhost/vms/api/rfid/test-connection
```

## Key Features

✅ **Auto-reconnect** - Automatically reconnects if reader connection drops  
✅ **Duplicate prevention** - 3-second cooldown prevents duplicate scans  
✅ **Multi-reader support** - Handle multiple gates simultaneously  
✅ **Real-time monitoring** - Console output shows all activity  
✅ **Audit logging** - All scans logged to database  
✅ **Flexible parsing** - Supports multiple EPC formats  
✅ **Lane-based logic** - Entry/exit lanes determine check-in/check-out  
✅ **Card status tracking** - Updates card status (active/in_use)  
✅ **Duration calculation** - Shows visit duration on check-out  

## Next Steps

1. **Configure your RFID reader** IP address in `app/Config/RFIDReader.php`
2. **Run the migration** (already done): `php spark migrate`
3. **Test connection**: Visit `/api/rfid/test-connection`
4. **Start listener**: Run `start_rfid_listener.bat`
5. **Scan a card** and watch the console output
6. **Check logs** in the database: `visitor_card_logs` table

## Files Created/Modified

### New Files:
- `app/Config/RFIDReader.php`
- `app/Commands/RfidListener.php`
- `app/Commands/RfidListenerMulti.php`
- `app/Controllers/RFID.php`
- `app/Models/VisitorCardLogModel.php`
- `app/Database/Migrations/2026-01-29-041540_AddRfidFieldsToLanes.php`
- `start_rfid_listener.bat`
- `start_rfid_listener_single.bat`
- `RFID_SETUP.md`
- `RFID_IMPLEMENTATION_SUMMARY.md`

### Modified Files:
- `app/Config/Routes.php` - Added RFID API routes
- `app/Views/config/index.php` - Changed "Card ID" to "Card EPC", removed Serial No
- `app/Models/VisitorCardModel.php` - Updated validation messages

## Migration Completed Successfully ✅

The database migration has been run and the following were created:
- Added RFID fields to `lanes` table
- Created `visitor_card_logs` table with foreign keys

All RFID commands are now available:
- `php spark rfid:listen`
- `php spark rfid:listen-all`

The system is ready to use!
