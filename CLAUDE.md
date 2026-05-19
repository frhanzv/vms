# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**SafeG VMS** — a Visitor Management System built on **CodeIgniter 4** (PHP 8.1+), served by Laragon (Apache + MySQL) at `http://localhost/vms`. The product name shown in the UI is "SafeG".

## Common Commands

```bash
# Install / update PHP dependencies
composer install

# Run all tests
vendor/bin/phpunit

# Run a single test file
vendor/bin/phpunit tests/unit/HealthTest.php

# Run database migrations
php spark migrate

# Rollback last migration batch
php spark migrate:rollback

# Generate encryption key
php -r "echo bin2hex(random_bytes(32));"
```

Logs are in `writable/logs/`. Set `CI_ENVIRONMENT = development` in `.env` to enable the debug toolbar and verbose errors.

## Architecture

### Request lifecycle

1. Apache rewrites all requests into `public/index.php` via `.htaccess`.
2. `Config\Filters` runs `AuthFilter` globally (session `isLoggedIn`), skipping public paths listed in the `except` array.
3. Route-level `['filter' => 'role:...']` arguments pass allowed role names to `RoleFilter`, which checks `session()->get('role')`.
4. `client_feature` route filter (`ClientFeatureFilter`) additionally gates routes by per-company feature toggles stored in `client_features` table.
5. Controllers extend `BaseController`, which provides `userCan()` for in-controller permission checks.

### Access-control layers (two orthogonal systems)

| Layer | Where defined | What it controls |
|---|---|---|
| **Role filter** (`role:...`) | `Routes.php` filter args | Which role names can reach the route |
| **Granular access** (`has_access()`) | `roles.access` JSON column | Fine-grained module/action toggles per role |

`has_access($module, $action)` (in `app/Helpers/access_helper.php`) loads the role's `access` JSON from the DB, caches it in session under `role_access_cache`, and exposes per-module/action booleans. Call it in views and controllers for UI-level guards.

`client_feature_enabled($key)` (in `app/Helpers/feature_helper.php`) checks whether an optional feature is enabled for the current user's company. `superadmin` always bypasses this check.

### Role hierarchy (defined in `Routes.php` aliases)

```
superadmin > clientsuperadmin > admin > officer > host
```

Routes use shorthand variables (`$plusAdmin`, `$plusOfficer`, etc.) that expand to comma-separated role strings.

### Multi-tenancy

Users belong to a `company_id`. Most data is company-scoped. `superadmin` is the global system role (not scoped to a company); `clientsuperadmin` is the top role within a company.

### Key models and traits

- **`OptimisticLockTrait`** — models that store a `version` INT column use `updateWithLock($id, $data, $version)` to prevent concurrent overwrites. A `ConcurrencyException` is thrown on version mismatch.
- **`SyncUidTrait`** — auto-generates a UUID `sync_uid` on insert (before-insert callback). Used for external sync and kiosk integration.
- Most models set `$returnType = 'array'` — results are plain PHP arrays, not objects.

### Visitor flow

`invitations` → `invitation_schedules` (date ranges) → `invitation_visitors` (per-visitor check-in/check-out).  
Approval status moves through: `Pending → Submitted → Approved / Rejected`.

### Hardware integrations

- **RFID** (`Controllers/RFID.php`, `Config/RFIDReader.php`) — Yanzeo SA810 UHF reader over TCP. Debounce prevents duplicate scans.
- **QR Code** (`Controllers/QRCode.php`) — QR-based check-in alternative to RFID.
- **Kiosk Mobile API** (`Controllers/Api/KioskApi.php`) — unauthenticated REST endpoints used by the MNR Android kiosk app. URL scheme mirrors Java/Spring (`/api/admin/*`, `/api/vendorpass/*`, `/api/user/*`).

### External APIs

- **Outbound API** (`Controllers/Api/ApiManagement.php`) — manages API keys for syncing data to an external Laravel-based system.
- **Inbound API** (`Controllers/Api/InboundApi.php`) — webhook receiver at `POST /api/v1/receive`, protected by Bearer token (`inbound_api_auth` filter).
- **LLM assistant** — OpenAI-compatible endpoint configured via `.env` (`LLM_ENABLED`, `LLM_BASE_URL`, `LLM_API_KEY`, `LLM_MODEL`). Drives the dashboard chat feature.

### Views

Views use CodeIgniter's native view system. The layout template is `app/Views/layouts/master.php` (renders `$this->renderSection('content')`). Sidebar is `app/Views/partials/sidebar.php`. Tailwind CSS is loaded from CDN.

### Feature flags

`ClientFeatureModel::allFeatures()` lists all toggleable features (`blacklist`, `invitations`, `workflows`, `staff_pass`, `visitor_card`, `security_alerts`, `device_management`, `company_visited`). Absence of a DB record means the feature is **enabled** (default-on).

## Environment Variables (`.env`)

Key non-obvious variables:

| Variable | Purpose |
|---|---|
| `LLM_ENABLED` | Toggles the dashboard AI assistant |
| `LLM_BASE_URL` | OpenAI-compatible base URL (can point to local Ollama) |
| `LLM_API_KEY` | API key — must be set in `.env`, never hardcoded |
| `LLM_MODEL` | Default: `gpt-4o` |
| `encryption.key` | CI4 encryption key — generate with `php -r "echo 'hex2bin:'.bin2hex(random_bytes(32));"` |

## Migrations

Migration files live in `app/Database/Migrations/` and are named with a timestamp prefix. When adding a new migration, always run `php spark migrate` to apply it. If a migration seeds reference data, document what it seeds in the filename (e.g. `*_SeedRealLocationData.php`).
