# MyKad OCR Setup Instructions

The MyKad reading functionality has been implemented but requires an OCR (Optical Character Recognition) library to work.

## Current Status
✅ Frontend upload button and UI implemented
✅ Backend endpoint created
✅ File upload handling ready
⚠️ OCR processing needs configuration

## Option 1: Tesseract OCR (Recommended - Free & Open Source)

### Installation Steps:

1. **Install Tesseract OCR on your server:**
   - Windows: Download from https://github.com/UB-Mannheim/tesseract/wiki
   - Add Tesseract to your system PATH

2. **Install PHP Tesseract wrapper:**
   ```bash
   composer require thiagoalessio/tesseract_ocr
   ```

3. **Uncomment the OCR code in VisitorRegistration.php:**
   - Find the `extractMyKadData()` method
   - Uncomment the Tesseract implementation section
   - Comment out the mock return statement

## Option 2: Cloud OCR Services (Paid but more accurate)

### Google Cloud Vision API
1. Enable Cloud Vision API in Google Cloud Console
2. Install: `composer require google/cloud-vision`
3. Configure API credentials

### Azure Computer Vision
1. Create Azure Computer Vision resource
2. Install: `composer require microsoft/azure-storage-blob`
3. Configure API keys

## Testing Without OCR
The current implementation returns empty fields, allowing you to test the upload functionality without actual OCR processing.

## Security Notes
- Uploaded images are stored in `writable/uploads/temp/`
- Images are automatically deleted after processing
- Ensure proper file size limits in php.ini (upload_max_filesize, post_max_size)

## MyKad Data Extraction
The system attempts to extract:
- IC Number (12 digits)
- Full Name
- Date of Birth (from IC number)
- Sex (from IC number last digit)
- Address
- Postcode
- City
- State

## Implementation Status
The `parseMyKadText()` method contains regex patterns to parse Malaysian MyKad format. Adjust patterns based on your OCR output quality.
