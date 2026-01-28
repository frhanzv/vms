# Google Cloud Vision API Setup Guide

This guide will help you set up Google Cloud Vision API for MyKad OCR.

## Prerequisites

- Google Cloud Platform account
- **Billing account enabled** (required even for free tier)
- Python 3.x installed
- pip package manager

## Step 1: Create a Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Click on the project dropdown at the top
3. Click "New Project"
4. Enter a project name (e.g., "VMS-MyKad-OCR")
5. Click "Create"

## Step 1.5: Enable Billing (CRITICAL STEP)

**⚠️ IMPORTANT**: Google Cloud Vision API requires billing to be enabled, even if you only use the free tier.

1. Go to [Billing](https://console.cloud.google.com/billing)
2. Click "Link a Billing Account" or "Create Account"
3. Follow the prompts to add a credit card (you won't be charged if you stay within the free tier)
4. Link the billing account to your project

**Free Tier**: You get 1,000 OCR requests per month for free. You'll only be charged if you exceed this limit.

## Step 2: Enable Vision API

1. In the Google Cloud Console, go to "APIs & Services" > "Library"
2. Search for "Cloud Vision API"
3. Click on it and then click "Enable"

## Step 3: Create Service Account Credentials

1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "Service Account"
3. Enter a name (e.g., "vms-ocr-service")
4. Click "Create and Continue"
5. For "Grant this service account access to project", select one of these roles:
   - **Option 1 (Recommended)**: Select "Basic" > "Editor" (broader access)
   - **Option 2 (More Restricted)**: Select "Project" > "Viewer" (the API access is usually sufficient)
   - **Option 3**: Skip this step (leave blank) - as long as the API is enabled, the service account from the same project can usually access it
6. Click "Continue" then "Done"

**Note**: If you need tighter security, you can also use "Service Usage Consumer" role, but "Editor" or just enabling the API typically works fine for development.

## Step 4: Generate and Download JSON Key

1. In the "Credentials" page, find your service account under "Service Accounts"
2. Click on the service account email
3. Go to the "Keys" tab
4. Click "Add Key" > "Create new key"
5. Choose "JSON" format
6. Click "Create"
7. The JSON key file will be downloaded to your computer

## Step 5: Set Up Environment Variable

### Windows (PowerShell)
```powershell
# Temporary (current session only)
$env:GOOGLE_APPLICATION_CREDENTIALS="C:\path\to\your\service-account-key.json"

# Permanent (add to system environment variables)
[System.Environment]::SetEnvironmentVariable('GOOGLE_APPLICATION_CREDENTIALS', 'C:\path\to\your\service-account-key.json', 'User')
```

### Windows (Command Prompt)
```cmd
# Temporary
set GOOGLE_APPLICATION_CREDENTIALS=C:\path\to\your\service-account-key.json

# Permanent
setx GOOGLE_APPLICATION_CREDENTIALS "C:\path\to\your\service-account-key.json"
```

### Linux/Mac
```bash
# Add to ~/.bashrc or ~/.zshrc for permanent setup
export GOOGLE_APPLICATION_CREDENTIALS="/path/to/your/service-account-key.json"
```

## Step 6: Recommended - Store Credentials in Project Directory

For easier deployment, you can store the credentials file in your project:

1. Create a `credentials` folder in your project root (add to .gitignore!)
2. Place the JSON key file there, e.g., `credentials/google-vision-key.json`
3. Set the environment variable to point to this location:
   ```powershell
   $env:GOOGLE_APPLICATION_CREDENTIALS="$PWD\credentials\google-vision-key.json"
   ```

## Step 7: Update .gitignore

**IMPORTANT:** Make sure to add the credentials to `.gitignore`:

```
# Google Cloud credentials
credentials/
*-key.json
google-vision-key.json
```

## Step 8: Test the Setup

Run the OCR script with a test MyKad image:

```bash
python ocr_mykad.py path/to/mykad-image.jpg
```

If successful, you should see JSON output with the extracted text.

## Pricing Information

Google Cloud Vision API offers:
- **Free Tier**: 1,000 units per month
- **Paid Tier**: $1.50 per 1,000 units (after free tier)

Each OCR request counts as 1 unit. For a typical VMS application with moderate usage, the free tier should be sufficient.

## Troubleshooting

### Error: "Could not automatically determine credentials"
- Make sure the `GOOGLE_APPLICATION_CREDENTIALS` environment variable is set correctly
- Verify the JSON key file exists at the specified path
- Restart your terminal/IDE after setting the environment variable

### Error: "API has not been enabled"
- Go back to Step 2 and ensure Cloud Vision API is enabled
- Wait a few minutes after enabling the API

### Error: "Permission denied"
- Verify your service account has appropriate roles (Editor or Viewer)
- Check if billing is enabled on your Google Cloud project (required even for free tier usage)
- Make sure the Cloud Vision API is enabled for your project

### Error: "This API method requires billing to be enabled"
- **This is the most common error!**
- Go to https://console.cloud.google.com/billing
- Enable billing for your project by linking a credit card
- Wait 5-10 minutes for the changes to propagate
- You won't be charged if you stay within the 1,000 free requests per month

### Low accuracy results
- Ensure the MyKad image is well-lit and in focus
- The image should be at least 640x480 pixels
- Avoid images with heavy glare or shadows

## Alternative: Using API Key (Less Secure)

If you prefer using an API key instead of a service account:

1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "API Key"
3. Copy the API key
4. Restrict the API key to only Cloud Vision API
5. Modify the script to use the API key:

```python
from google.cloud import vision_v1

client = vision_v1.ImageAnnotatorClient(
    client_options={"api_key": "YOUR_API_KEY"}
)
```

**Note:** Service Account (JSON key) is recommended for production use.

## Benefits of Google Cloud Vision over EasyOCR

1. **Higher Accuracy**: Google's ML models are trained on billions of images
2. **Better Language Support**: Excellent for mixed English/Malay text on MyKad
3. **Structured Output**: Provides word, paragraph, and block-level information
4. **Confidence Scores**: More accurate confidence metrics
5. **No GPU Required**: Cloud-based processing
6. **Regular Updates**: Google continuously improves the models

## Support

For issues with Google Cloud Vision API:
- [Official Documentation](https://cloud.google.com/vision/docs)
- [Python Client Library](https://googleapis.dev/python/vision/latest/)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/google-cloud-vision)
