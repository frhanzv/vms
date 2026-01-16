# Settings Page - Implementation Guide

## Overview
A comprehensive settings page has been created for the SafeG VMS application with the following features:

### Features Implemented

#### 1. Profile Photo Upload
- **Drag & Drop Support**: Users can drag and drop their profile photo into the designated area
- **File Browser**: Click to browse and select a photo from their device
- **Instant Preview**: Photo preview updates immediately after selection
- **File Validation**: 
  - Accepts JPG, JPEG, PNG, GIF formats
  - Maximum file size: 2MB
  - Server-side validation included
- **AJAX Upload**: Non-blocking upload with progress indicator
- **Persistent Storage**: Photos saved to `writable/uploads/profiles/`
- **Sidebar Integration**: Updated photo reflects immediately in the sidebar after page reload

#### 2. Profile Information
- **Full Name Update**: Editable full name field
- **Read-Only Fields**: Username and email are displayed but not editable
- **Session Update**: Full name in session updates automatically after save
- **Validation**: Server-side validation with error display

#### 3. Password Change
- **Secure Process**: Requires current password verification
- **Password Confirmation**: New password must be confirmed
- **Minimum Requirements**: 6 characters minimum
- **Validation**: 
  - Current password verification
  - New password and confirmation must match
  - Clear error messages for validation failures

### Files Created/Modified

#### Created Files:
1. `app/Controllers/Settings.php` - Settings controller with all logic
2. `app/Views/settings/index.php` - Settings page view
3. `app/Database/Migrations/2026-01-16-000001_AddProfilePhotoToUsers.php` - Migration for profile_photo column
4. `writable/uploads/.htaccess` - Allow public access to uploaded photos

#### Modified Files:
1. `app/Models/UserModel.php` - Added 'profile_photo' to allowedFields
2. `app/Config/Routes.php` - Added settings routes
3. `app/Views/dashboard.php` - Updated settings link
4. `app/Views/invitations/list.php` - Updated settings link
5. `app/Views/invitations/create.php` - Updated settings link
6. `app/Views/visitors/list.php` - Updated settings link
7. `app/Views/visitors/logbook.php` - Updated settings link
8. `app/Views/requests/list.php` - Updated settings link

### Routes Added:
- `GET /settings` - Display settings page
- `POST /settings/updateProfile` - Update full name
- `POST /settings/updatePassword` - Change password
- `POST /settings/updatePhoto` - Upload profile photo (AJAX)

### Installation Steps:

1. **Run Database Migration**:
   ```bash
   php spark migrate
   ```
   This will add the `profile_photo` column to the `users` table.

2. **Create Uploads Directory** (if it doesn't exist):
   The controller will automatically create `writable/uploads/profiles/` directory when needed.

3. **Verify Permissions**:
   Ensure the `writable/uploads/` directory is writable by the web server.

### Usage:

1. Navigate to any page and click "Settings" in the sidebar
2. The settings page has three sections:
   - **Profile Photo**: Drag/drop or click to upload
   - **Profile Information**: Update full name
   - **Change Password**: Secure password change

### Security Features:
- CSRF protection on all forms
- Password hashing using PHP's password_hash()
- Current password verification before allowing password change
- File type and size validation for photo uploads
- Session updates after profile changes

### Design:
- Follows the existing SafeG theme
- Responsive layout
- Dark mode support
- Material Symbols icons
- Smooth transitions and hover effects
- Success/error message notifications

### Technical Details:

**Photo Upload Flow**:
1. User selects or drops photo
2. JavaScript validates file type and size
3. Preview shown immediately
4. AJAX request to server
5. Server validates and saves file
6. Old photo deleted if exists
7. Database updated with new filename
8. Success message shown
9. Page reloads to update sidebar

**Password Change Flow**:
1. User enters current, new, and confirmation passwords
2. Form submitted with CSRF token
3. Server validates all fields
4. Current password verified against database
5. New passwords match verified
6. Password hashed and saved
7. Success message shown

### Error Handling:
- Form validation errors displayed inline
- Upload errors shown in upload section
- Password errors shown in password section
- Flash messages for success/error states

### Browser Compatibility:
- Modern browsers with drag & drop API support
- Fallback to file input for older browsers
- Responsive design for mobile devices
