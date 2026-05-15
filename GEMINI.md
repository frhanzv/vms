# Visitor Management System (VMS) - Project Guide

## Overview
A comprehensive Visitor Management System built with CodeIgniter 4, designed to manage visitor registrations, invitations, and access control via RFID and QR codes. It includes a Kiosk API for Android integration and OCR capabilities for document processing.

## Tech Stack
- **Framework:** CodeIgniter 4.4.x
- **PHP:** 8.1+
- **Database:** MySQL
- **Frontend:** Vanilla CSS, JavaScript, CodeIgniter Views
- **Integrations:**
  - RFID Readers (TCP/IP listeners)
  - QR Code Scanners
  - Google Cloud Vision (OCR for MyKad)
  - Kiosk Mobile API (compatible with Java/Spring legacy endpoints)

## Key Directories
- `app/Controllers/`: Application logic, grouped by feature. `BaseController` contains role-based permission checks.
- `app/Models/`: Database interactions using CodeIgniter's Model class.
- `app/Views/`: UI templates.
- `app/Config/`: System configuration, including `Routes.php` and `Filters.php`.
- `app/Commands/`: Custom CLI commands (e.g., RFID/QR listeners, data sync).
- `public/`: Web root, contains `index.php` and assets.
- `docs/`: Technical documentation and API specifications.

## Development Workflow

### Role-Based Access Control (RBAC)
Roles are managed via filters in `app/Config/Routes.php` and a custom `userCan` method in `app/Controllers/BaseController.php`.
Common roles: `superadmin`, `clientsuperadmin`, `admin`, `officer`, `host`.

### API Integration
- **Inbound API:** Protected by `InboundApiAuthFilter` using Bearer tokens.
- **Kiosk API:** Public endpoints under `api/admin` and `api/vendorpass` to support legacy mobile apps.
- **RFID/QR Listeners:** Implemented as long-running CLI commands in `app/Commands/`.

### Configuration
- Environment variables are stored in `.env`.
- Feature flags are managed via the `ClientFeatureFilter` and `ClientFeatureModel`.

## Coding Standards & Conventions
- **Naming:** Follow PSR-12 coding standards. Controllers and Models use PascalCase.
- **Database:** Use migrations for schema changes. Avoid raw SQL where possible; prefer Query Builder or Models.
- **Views:** Keep logic in views to a minimum. Use helpers for common UI tasks.
- **Permissions:** Always check permissions in controllers using `$this->userCan('action')` for granular control.

## Testing
- Tests are located in the `tests/` directory.
- Run tests using `vendor/bin/phpunit`.
- Use `php spark` for CLI tasks and development server (`php spark serve`).

## Deployment
- The `writable` directory must be writable by the web server.
- The `public` folder should be the document root.
- Ensure `CI_ENVIRONMENT` is set to `production` in live environments.
