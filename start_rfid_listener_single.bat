@echo off
REM VMS RFID Listener Service (Single Reader Mode)
REM This script starts the RFID listener for a single reader

echo ======================================
echo    VMS RFID Listener Service
echo    (Single Reader Mode)
echo ======================================
echo.

REM Check if PHP is available
where php >nul 2>nul
if %errorlevel% neq 0 (
    echo ERROR: PHP is not found in PATH
    echo Please make sure PHP is installed and added to system PATH
    pause
    exit /b 1
)

echo Starting RFID listener...
echo Press Ctrl+C to stop the service
echo.

REM Start the listener
php spark rfid:listen

pause
