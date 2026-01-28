@echo off
REM Test script for Google Cloud Vision OCR

set GOOGLE_APPLICATION_CREDENTIALS=C:\laragon\www\vms\credentials\vms-mykad-ocr-13799932dbd4.json
set PYTHON_EXE=C:\laragon\www\vms\.venv\Scripts\python.exe
set OCR_SCRIPT=C:\laragon\www\vms\ocr_mykad.py

echo Testing Google Cloud Vision OCR Setup...
echo.

if not exist "%GOOGLE_APPLICATION_CREDENTIALS%" (
    echo ERROR: Credentials file not found!
    echo Looking for: %GOOGLE_APPLICATION_CREDENTIALS%
    exit /b 1
)

if not exist "%PYTHON_EXE%" (
    echo ERROR: Python not found!
    echo Looking for: %PYTHON_EXE%
    exit /b 1
)

if not exist "%OCR_SCRIPT%" (
    echo ERROR: OCR script not found!
    echo Looking for: %OCR_SCRIPT%
    exit /b 1
)

echo All files found. Testing API connection...
echo.

"%PYTHON_EXE%" -c "from google.cloud import vision; client = vision.ImageAnnotatorClient(); print('Google Cloud Vision API: Connected successfully!')"

echo.
echo Ready to test with MyKad image!
echo.
echo Usage: test_ocr.bat path\to\mykad-image.jpg
echo.

if "%~1"=="" (
    echo No image provided. Connection test only.
    exit /b 0
)

if not exist "%~1" (
    echo ERROR: Image file not found: %~1
    exit /b 1
)

echo Processing: %~1
echo.

"%PYTHON_EXE%" "%OCR_SCRIPT%" "%~1"
