# Settings Page - Quick Start Guide

## ✅ What's Been Created

A complete settings page with:
- ✨ Profile photo upload (drag & drop or click to select)
- 👤 Full name editor
- 🔒 Password change functionality
- 📱 Responsive design matching your app theme
- 🌙 Dark mode support

## 🚀 Next Steps

### 1. Run Database Migration
```bash
cd c:\laragon\www\vms
php spark migrate
```
This adds the `profile_photo` column to your users table.

### 2. Test the Settings Page
1. Login to your application
2. Click "Settings" in the sidebar (any page)
3. Try uploading a profile photo
4. Update your full name
5. Change your password

## 📁 Files Created
- `app/Controllers/Settings.php` - Main controller
- `app/Views/settings/index.php` - Settings page UI
- `app/Database/Migrations/2026-01-16-000001_AddProfilePhotoToUsers.php` - DB migration
- `writable/uploads/.htaccess` - Public access config

## 🔧 Files Updated
- `app/Models/UserModel.php` - Added profile_photo field
- `app/Controllers/Dashboard.php` - Added user photo support
- `app/Config/Routes.php` - Added 4 new routes
- All view files - Updated settings links (6 files)

## 🎨 Features

### Profile Photo Upload
- Drag & drop or click to browse
- Instant preview before save
- Auto-saves on upload
- Validates file type (JPG, PNG, GIF) and size (max 2MB)
- Shows in sidebar after upload

### Profile Info
- Update full name
- Username and email are read-only
- Changes reflect immediately

### Change Password
- Requires current password
- Minimum 6 characters
- Confirmation required
- Secure hashing

## 🔗 Routes Available
- `GET /settings` - Settings page
- `POST /settings/updateProfile` - Update name
- `POST /settings/updatePassword` - Change password
- `POST /settings/updatePhoto` - Upload photo (AJAX)

## 🛡️ Security
- CSRF protection on all forms
- Password verification before changes
- File upload validation
- Session updates on changes

## 💡 Tips
- Photos are stored in `writable/uploads/profiles/`
- Old photos are automatically deleted when new ones are uploaded
- All validation errors display inline
- Success messages appear at the top of the page

## 🎉 Ready to Use!
Your settings page is fully functional and follows your application's design theme.
