# VMS Kiosk Mobile API — Developer Reference

> **System:** Visitor Management System (VMS) — CodeIgniter 4  
> **Audience:** MNR Kiosk Android app developer  
> **Base URL:** `http://<server-ip>:8080`  
> **Authentication:** None — all endpoints are public  
> **Content-Type:** `application/json` (unless noted otherwise)

---

## Table of Contents

1. [Setup & Configuration](#1-setup--configuration)
2. [Constants.kt Update Required](#2-constantskt-update-required)
3. [Response Format](#3-response-format)
4. [Error Format](#4-error-format)
5. [Visitor Walk-In Flow](#5-visitor-walk-in-flow)
6. [Endpoints — Admin Lookups](#6-endpoints--admin-lookups)
   - [GET Active Locations](#61-get-active-locations)
   - [GET Active Sub-Locations (Lanes)](#62-get-active-sub-locations-lanes)
   - [GET Active Countries](#63-get-active-countries)
   - [GET States by Country](#64-get-states-by-country)
   - [GET Cities by State](#65-get-cities-by-state)
   - [GET Vehicle Types](#66-get-vehicle-types)
   - [GET License Classes](#67-get-license-classes)
   - [GET Vaccine Types](#68-get-vaccine-types)
   - [GET Module Config](#69-get-module-config)
   - [GET All Companies](#610-get-all-companies)
7. [Endpoints — Vendor Pass](#7-endpoints--vendor-pass)
   - [GET Visit Reason List](#71-get-visit-reason-list)
   - [POST Check IC Exist](#72-post-check-ic-exist)
   - [POST Register Visitor (Walk-In)](#73-post-register-visitor-walk-in)
   - [POST Assign Visitor Card](#74-post-assign-visitor-card)
   - [POST Upload Visitor Photo](#75-post-upload-visitor-photo)
8. [Endpoints — User / Staff](#8-endpoints--user--staff)
   - [GET Staff Pass by Staff No or Name](#81-get-staff-pass-by-staff-no-or-name)
   - [GET Visitor Pass by IC or Name](#82-get-visitor-pass-by-ic-or-name)
9. [Endpoints — Laravel URL Routes](#9-endpoints--laravel-url-routes)
   - [POST Decrypt](#91-post-decrypt)
   - [GET Visitor Types](#92-get-visitor-types)
10. [Not Supported in VMS](#10-not-supported-in-vms)

---

## 1. Setup & Configuration

VMS runs as a single PHP (CodeIgniter 4) application. There is **no separate Java backend**. All endpoints are served by the same server on the same port.

| Item | Value |
|---|---|
| Server | `http://192.168.0.214:8080` (example) |
| Protocol | HTTP (change to HTTPS when deployed with SSL) |
| Port | `8080` (or as configured in Laragon/Apache) |
| Auth | None — no token or login required |
| Format | JSON — set `Content-Type: application/json` for POST requests |

---

## 2. Constants.kt Update Required

The original `Constants.kt` sets `laravel_url` to **port 80** (host without port). VMS now handles those routes too, so `laravel_url` must match `base_url`.

**Change this:**
```kotlin
laravel_url = "${url.protocol}://${url.host}"
// Result: http://192.168.0.214  ← port 80, OLD Laravel/MNR
```

**To this:**
```kotlin
laravel_url = base_url
// Result: http://192.168.0.214:8080  ← VMS handles everything
```

All other URL constants stay exactly the same. No other code changes needed.

---

## 3. Response Format

All successful responses return HTTP `200` (or `201` for resource creation) with this structure:

```json
{
  "status": "success",
  "data": [ ... ]
}
```

For **paginated** responses (visit reasons):

```json
{
  "status": "success",
  "content": [ ... ],
  "totalElements": 12,
  "totalPages": 1,
  "page": 0,
  "size": 20
}
```

For **single record** responses (check IC, register visitor):

```json
{
  "status": "success",
  "data": { ... }
}
```

---

## 4. Error Format

| HTTP Code | Meaning | When it happens |
|---|---|---|
| `400` | Validation error | Missing required fields |
| `403` | Forbidden | Card is not available (already in use, lost, etc.) |
| `404` | Not found | Invitation ID or card ID not found |
| `500` | Server error | Unexpected failure |

Error body:

```json
{
  "status": "error",
  "error": {
    "status": 400,
    "error": "Validation Failed",
    "messages": {
      "field": "Error description"
    }
  }
}
```

---

## 5. Visitor Walk-In Flow

The recommended sequence for a kiosk walk-in registration:

```
1.  GET  /api/admin/locationAccess/active         ← Load location list for dropdown
2.  GET  /api/admin/country/active                ← Load countries
3.  GET  /api/admin/state/country/{id}            ← Load states for selected country
4.  GET  /api/admin/city/state/{id}               ← Load cities for selected state
5.  GET  /api/admin/campanies/all                 ← Load company list
6.  GET  /api/vendorpass/getVisitReasonList       ← Load visit reasons
7.  GET  /vms/api/visitor-types                   ← Load visitor type options
8.  GET  /api/admin/moduleConfig/getByProject     ← Check feature flags (optional)

--- Visitor arrives at kiosk ---

9.  POST /api/vendorpass/checkICExist             ← Check if visitor was here before
10. POST /api/vendorpass/doVisitorPassReqMobile   ← Register the visitor (get invitation ID)
11. POST /api/vendorpass/uploadVendorPassPhotoMobile ← Upload photo (use invitation ID)
12. POST /api/vendorpass/insertVendorPassCard     ← Assign RFID card (use invitation ID)
```

Steps 1–8 are typically done on app startup and cached locally. Steps 9–12 happen per visitor.

---

## 6. Endpoints — Admin Lookups

### 6.1 GET Active Locations

Returns all active access locations (main gates / buildings).

```
GET /api/admin/locationAccess/active
```

**No parameters.**

**Success Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "locationAccess": "Main Gate",
      "branch": "HQ Building",
      "mobileApp": "enabled",
      "isHoldArea": "no",
      "visitorPassPrint": "enabled",
      "inOutBound": "both",
      "status": "active"
    },
    {
      "id": 2,
      "locationAccess": "Side Entrance",
      "branch": "HQ Building",
      "mobileApp": "enabled",
      "isHoldArea": "no",
      "visitorPassPrint": "disabled",
      "inOutBound": "inbound",
      "status": "active"
    }
  ]
}
```

**Field notes:**
- `mobileApp` — `enabled` or `disabled`. Filter to `enabled` for kiosk-accessible locations.
- `inOutBound` — `inbound`, `outbound`, or `both`.
- `isHoldArea` — `yes` or `no`. Hold areas are for detention/waiting.

---

### 6.2 GET Active Sub-Locations (Lanes)

Returns all active lanes (sub-locations / gates) with their parent location.

```
GET /api/admin/subLocationAccess/active
```

**No parameters.**

**Success Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "lane": "Lane A",
      "locationId": 1,
      "locationAccess": "Main Gate",
      "branch": "HQ Building",
      "kioskIp": "192.168.0.101",
      "inBound": "yes",
      "outBound": "yes",
      "status": "active"
    }
  ]
}
```

**Field notes:**
- `kioskIp` — IP address of the kiosk device assigned to this lane (may be empty).
- `inBound` / `outBound` — `yes` or `no`.

---

### 6.3 GET Active Countries

Returns all active countries for address/registration forms.

```
GET /api/admin/country/active
```

**No parameters.**

**Success Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Malaysia",
      "code": "MY",
      "status": "Active"
    },
    {
      "id": 2,
      "name": "Singapore",
      "code": "SG",
      "status": "Active"
    }
  ]
}
```

---

### 6.4 GET States by Country

Returns all active states/regions for a given country ID.

```
GET /api/admin/state/country/{countryId}
```

**Path parameter:**

| Parameter | Type | Required | Description |
|---|---|---|---|
| `countryId` | integer | Yes | ID from the countries list |

**Example:**
```
GET /api/admin/state/country/1
```

**Success Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 5,
      "name": "Selangor",
      "code": "SGR",
      "countryId": 1,
      "status": "active"
    },
    {
      "id": 6,
      "name": "Kuala Lumpur",
      "code": "KL",
      "countryId": 1,
      "status": "active"
    }
  ]
}
```

---

### 6.5 GET Cities by State

Returns all active cities for a given state ID.

```
GET /api/admin/city/state/{stateId}
```

**Path parameter:**

| Parameter | Type | Required | Description |
|---|---|---|---|
| `stateId` | integer | Yes | ID from the states list |

**Example:**
```
GET /api/admin/city/state/5
```

**Success Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 12,
      "name": "Shah Alam",
      "code": "SHA",
      "stateId": 5,
      "status": "active"
    },
    {
      "id": 13,
      "name": "Petaling Jaya",
      "code": "PJ",
      "stateId": 5,
      "status": "active"
    }
  ]
}
```

---

### 6.6 GET Vehicle Types

> **Note:** Vehicle types are not managed in VMS. This endpoint always returns an empty list. Handle this gracefully in the UI (hide the vehicle type dropdown if `data` is empty).

```
GET /api/admin/vinType/active/all
```

**Success Response:**
```json
{
  "status": "success",
  "data": []
}
```

---

### 6.7 GET License Classes

> **Note:** License classes are not managed in VMS. This endpoint always returns an empty list.

```
GET /api/admin/licenseClass/active
```

**Success Response:**
```json
{
  "status": "success",
  "data": []
}
```

---

### 6.8 GET Vaccine Types

> **Note:** Vaccine types are not managed in VMS. This endpoint always returns an empty list.

```
GET /api/admin/vaccineType/active
```

**Success Response:**
```json
{
  "status": "success",
  "data": []
}
```

---

### 6.9 GET Module Config

Returns VMS feature flags for the kiosk to check which features are enabled.

```
GET /api/admin/moduleConfig/getByProject
```

**No parameters.**

**Success Response:**
```json
{
  "status": "success",
  "data": {
    "project": "VMS",
    "visitorPassPrint": "enabled",
    "facialVerification": "disabled",
    "securityBriefing": "disabled",
    "allowWalkIn": "enabled"
  }
}
```

**Field notes:**
- `visitorPassPrint` — Whether to print a visitor pass slip after registration.
- `facialVerification` — Whether facial recognition is required.
- `securityBriefing` — Whether visitor must watch a security video.
- `allowWalkIn` — Whether kiosk walk-in registration is permitted.

---

### 6.10 GET All Companies

Returns all active companies for the visitor registration form company dropdown.

```
GET /api/admin/campanies/all
```

> Note: The path uses `campanies` (original MNR typo) — this is intentional for backward compatibility.

**No parameters.**

**Success Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "ABC Sdn Bhd",
      "registrationNo": "202001012345",
      "address": "No 1, Jalan Industri, Shah Alam",
      "contactNo": "03-55512345",
      "email": "info@abc.com",
      "status": "active"
    }
  ]
}
```

---

## 7. Endpoints — Vendor Pass

### 7.1 GET Visit Reason List

Returns a paginated list of visit reasons (Business Meeting, Delivery, etc.).

```
GET /api/vendorpass/getVisitReasonList?page=0&pageSize=20
```

**Query parameters:**

| Parameter | Type | Required | Default | Description |
|---|---|---|---|---|
| `page` | integer | No | `0` | Zero-based page index |
| `pageSize` | integer | No | `20` | Records per page |

**Example:**
```
GET /api/vendorpass/getVisitReasonList?page=0&pageSize=20
```

**Success Response:**
```json
{
  "status": "success",
  "content": [
    { "id": 1, "reason": "Business Meeting" },
    { "id": 2, "reason": "Client Visit" },
    { "id": 3, "reason": "Consultation" },
    { "id": 4, "reason": "Delivery" },
    { "id": 5, "reason": "Equipment Installation" },
    { "id": 6, "reason": "Interview" },
    { "id": 7, "reason": "Maintenance" },
    { "id": 8, "reason": "Personal Visit" },
    { "id": 9, "reason": "Project Discussion" },
    { "id": 10, "reason": "Site Inspection" },
    { "id": 11, "reason": "Training" },
    { "id": 12, "reason": "Vendor Meeting" }
  ],
  "totalElements": 12,
  "totalPages": 1,
  "page": 0,
  "size": 20
}
```

---

### 7.2 POST Check IC Exist

Checks whether a visitor with the given IC/passport number already has a record in VMS. Use this before registration to pre-fill the form with their previous details.

```
POST /api/vendorpass/checkICExist
Content-Type: application/json
```

**Request Body:**

| Field | Type | Required | Aliases accepted | Description |
|---|---|---|---|---|
| `icNo` | string | Yes | `ic_no`, `icPassport` | IC or passport number to check |

**Example Request:**
```json
{
  "icNo": "880101145678"
}
```

**Response — IC found (returning visitor):**
```json
{
  "status": "success",
  "exists": true,
  "data": {
    "id": 42,
    "visitorName": "Ahmad bin Hassan",
    "fullName": "Ahmad bin Hassan",
    "icPassport": "880101145678",
    "icNo": "880101145678",
    "phoneNo": "0123456789",
    "contact": "0123456789",
    "email": "ahmad@email.com",
    "companyName": "ABC Sdn Bhd",
    "company": "ABC Sdn Bhd",
    "vehicleNo": "WXY 1234",
    "vehicleRegistration": "WXY 1234",
    "location": "Main Gate",
    "locationAccess": "Main Gate",
    "visitReason": "Business Meeting",
    "reason": "Business Meeting",
    "invitedBy": "John Doe",
    "visitorTypeId": 1,
    "dateOfBirth": "1988-01-01",
    "sex": "Male",
    "resident": "Resident",
    "address": "No 5, Jalan Bahagia, Shah Alam",
    "postcode": "40150",
    "city": "Shah Alam",
    "state": "Selangor",
    "country": "Malaysia",
    "profilePhotoPath": "visitor_photos/kiosk_42_1714980000.jpg",
    "registrationSource": "kiosk",
    "status": "Approved",
    "checkedInAt": "",
    "createdAt": "2026-03-15 09:30:00"
  }
}
```

**Response — IC not found (new visitor):**
```json
{
  "status": "success",
  "exists": false,
  "data": null,
  "message": "IC not found"
}
```

---

### 7.3 POST Register Visitor (Walk-In)

Creates a new walk-in visitor record in VMS. Returns the full invitation record including the `id` needed for subsequent card and photo upload calls.

```
POST /api/vendorpass/doVisitorPassReqMobile
Content-Type: application/json
```

> This endpoint handles both `visitorPassRegistration` and `visitorPassRegistrationLite` (same URL in Constants.kt).

**Request Body:**

| Field | Type | Required | Aliases accepted | Description |
|---|---|---|---|---|
| `visitorName` | string | **Yes** | `full_name` | Full name of the visitor |
| `phoneNo` | string | **Yes** | `contact` | Mobile / phone number |
| `visitReason` | string | **Yes** | `reason` | Reason for visit (free text or from reasons list) |
| `icNo` | string | No | `ic_no`, `ic_passport` | Malaysian IC or passport number |
| `email` | string | No | `visitorEmail` | Visitor email address |
| `companyName` | string | No | `company` | Visitor's company name |
| `vehicleNo` | string | No | `vehicle_registration` | Vehicle plate number |
| `locationAccess` | string | No | `location` | Access location (from locations list) |
| `invitedBy` | string | No | `invited_by` | Name of the host/person being visited |
| `hostContact` | string | No | `host_contact` | Host phone number |
| `companyVisited` | string | No | `company_visited` | Company being visited (internal dept) |
| `visitorTypeId` | integer | No | `visitor_type_id` | ID from visitor types list |
| `dateOfBirth` | string | No | `date_of_birth` | Format: `yyyy-MM-dd` |
| `sex` | string | No | — | `Male` or `Female` |
| `resident` | string | No | — | `Resident` or `Non-Resident` |
| `address` | string | No | — | Home/company address |
| `postcode` | string | No | — | Postcode |
| `city` | string | No | — | City |
| `state` | string | No | — | State |
| `country` | string | No | — | Country |

**Minimal Example Request (required fields only):**
```json
{
  "visitorName": "Siti Aishah binti Roslan",
  "phoneNo": "0197654321",
  "visitReason": "Business Meeting"
}
```

**Full Example Request:**
```json
{
  "visitorName": "Siti Aishah binti Roslan",
  "icNo": "920515106789",
  "phoneNo": "0197654321",
  "email": "siti@example.com",
  "companyName": "XYZ Technologies Sdn Bhd",
  "vehicleNo": "BCD 5678",
  "locationAccess": "Main Gate",
  "visitReason": "Business Meeting",
  "invitedBy": "Encik Razif",
  "hostContact": "0123456789",
  "companyVisited": "IT Department",
  "visitorTypeId": 1,
  "dateOfBirth": "1992-05-15",
  "sex": "Female",
  "resident": "Resident",
  "address": "No 12, Jalan Kenanga, Petaling Jaya",
  "postcode": "47500",
  "city": "Petaling Jaya",
  "state": "Selangor",
  "country": "Malaysia"
}
```

**Success Response (HTTP 201):**
```json
{
  "status": "success",
  "message": "Visitor pass created successfully",
  "data": {
    "id": 105,
    "visitorName": "Siti Aishah binti Roslan",
    "fullName": "Siti Aishah binti Roslan",
    "icPassport": "920515106789",
    "icNo": "920515106789",
    "phoneNo": "0197654321",
    "contact": "0197654321",
    "email": "siti@example.com",
    "companyName": "XYZ Technologies Sdn Bhd",
    "company": "XYZ Technologies Sdn Bhd",
    "vehicleNo": "BCD 5678",
    "vehicleRegistration": "BCD 5678",
    "location": "Main Gate",
    "locationAccess": "Main Gate",
    "visitReason": "Business Meeting",
    "reason": "Business Meeting",
    "invitedBy": "Encik Razif",
    "visitorTypeId": 1,
    "dateOfBirth": "1992-05-15",
    "sex": "Female",
    "resident": "Resident",
    "address": "No 12, Jalan Kenanga, Petaling Jaya",
    "postcode": "47500",
    "city": "Petaling Jaya",
    "state": "Selangor",
    "country": "Malaysia",
    "profilePhotoPath": "",
    "registrationSource": "kiosk",
    "status": "Approved",
    "checkedInAt": "",
    "createdAt": "2026-05-07 14:32:00"
  }
}
```

> **Important:** Save the `data.id` value. You will need it for the photo upload and card assignment steps.

**Validation Error Response (HTTP 400):**
```json
{
  "status": "error",
  "error": {
    "status": 400,
    "error": "Validation Failed",
    "messages": {
      "0": "visitorName, phoneNo, and visitReason are required"
    }
  }
}
```

---

### 7.4 POST Assign Visitor Card

Assigns an RFID visitor card to a registered visitor. The card must exist in VMS and have status `active`. After assignment, the card status changes to `in_use` and a check-in log entry is created.

```
POST /api/vendorpass/insertVendorPassCard
Content-Type: application/json
```

**Request Body:**

| Field | Type | Required | Aliases accepted | Description |
|---|---|---|---|---|
| `cardId` | string | **Yes** | `card_id` | RFID card EPC/ID string |
| `invitationId` | integer | **Yes** | `invitation_id` | ID returned from registration step |

**Example Request:**
```json
{
  "cardId": "E2000017921302180460678D",
  "invitationId": 105
}
```

**Success Response:**
```json
{
  "status": "success",
  "message": "Card assigned successfully",
  "cardId": "E2000017921302180460678D",
  "invitationId": 105
}
```

**Error — Card not found (HTTP 404):**
```json
{
  "status": "error",
  "error": {
    "status": 404,
    "error": "Not Found",
    "messages": {
      "0": "Card 'E2000017921302180460678D' not found in VMS"
    }
  }
}
```

**Error — Card not available (HTTP 403):**
```json
{
  "status": "error",
  "error": {
    "status": 403,
    "error": "Forbidden",
    "messages": {
      "0": "Card 'E2000017921302180460678D' is not available (status: in_use)"
    }
  }
}
```

**Possible card statuses:**
| Status | Meaning |
|---|---|
| `active` | Available — can be assigned |
| `in_use` | Already assigned to another visitor |
| `lost` | Reported lost — cannot be assigned |
| `inactive` | Deactivated — cannot be assigned |

---

### 7.5 POST Upload Visitor Photo

Uploads a photo for a registered visitor. Supports two methods: multipart file upload or base64-encoded image.

```
POST /api/vendorpass/uploadVendorPassPhotoMobile
```

---

#### Method A — Multipart File Upload (recommended)

```
Content-Type: multipart/form-data
```

**Form fields:**

| Field | Type | Required | Description |
|---|---|---|---|
| `invitationId` | integer | **Yes** | ID from registration |
| `file` | file | **Yes** | Image file (jpg/png) |

**Android Retrofit example:**
```kotlin
val file = File(photoPath)
val requestBody = file.asRequestBody("image/jpeg".toMediaType())
val photoPart = MultipartBody.Part.createFormData("file", file.name, requestBody)
val idPart = invitationId.toString().toRequestBody("text/plain".toMediaType())

val call = apiService.uploadPhoto(photoPart, idPart)
```

---

#### Method B — Base64 JSON Upload

```
Content-Type: application/json
```

**Request Body:**

| Field | Type | Required | Description |
|---|---|---|---|
| `invitationId` | integer | **Yes** | ID from registration |
| `photo_base64` | string | **Yes** | Base64-encoded image (with or without data URI prefix) |

**Example Request:**
```json
{
  "invitationId": 105,
  "photo_base64": "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQ..."
}
```

Or without the data URI prefix:
```json
{
  "invitationId": 105,
  "photo_base64": "/9j/4AAQSkZJRgABAQ..."
}
```

---

**Success Response (both methods):**
```json
{
  "status": "success",
  "message": "Photo uploaded successfully",
  "path": "visitor_photos/kiosk_105_1714980000.jpg"
}
```

**Error — No photo provided (HTTP 400):**
```json
{
  "status": "error",
  "error": {
    "status": 400,
    "error": "Validation Failed",
    "messages": {
      "0": "No valid photo file or base64 data provided"
    }
  }
}
```

---

## 8. Endpoints — User / Staff

### 8.1 GET Staff Pass by Staff No or Name

Searches for staff members by staff number or name. Returns up to 20 results.

```
GET /api/user/getStaffPassByStaffNoOrName?keyword=<search>
```

**Query parameters:**

| Parameter | Type | Required | Aliases accepted | Description |
|---|---|---|---|---|
| `keyword` | string | Yes | `staffNo` | Staff number or partial name to search |

**Example:**
```
GET /api/user/getStaffPassByStaffNoOrName?keyword=ALI001
GET /api/user/getStaffPassByStaffNoOrName?keyword=Ahmad
```

**Success Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 7,
      "staffNo": "ALI001",
      "fullName": "Ali bin Abu",
      "nameOnPass": "ALI BIN ABU",
      "icPassport": "850303145678",
      "department": "Security",
      "designation": "Guard",
      "locationAccess": "Main Gate",
      "cardStatus": "active",
      "cardExpiry": "2027-12-31",
      "status": "active"
    }
  ]
}
```

**Empty result (keyword not found):**
```json
{
  "status": "success",
  "data": []
}
```

---

### 8.2 GET Visitor Pass by IC or Name

Searches previous visitor records by IC/passport number or name. Returns up to 20 most recent results. Useful for returning visitor lookup.

```
GET /api/user/getVisitorPassByStaffNoOrName?keyword=<search>
```

**Query parameters:**

| Parameter | Type | Required | Aliases accepted | Description |
|---|---|---|---|---|
| `keyword` | string | Yes | `staffNo` | IC number or partial visitor name |

**Example:**
```
GET /api/user/getVisitorPassByStaffNoOrName?keyword=880101145678
GET /api/user/getVisitorPassByStaffNoOrName?keyword=Ahmad
```

**Success Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 42,
      "visitorName": "Ahmad bin Hassan",
      "fullName": "Ahmad bin Hassan",
      "icPassport": "880101145678",
      "icNo": "880101145678",
      "phoneNo": "0123456789",
      "contact": "0123456789",
      "email": "ahmad@email.com",
      "companyName": "ABC Sdn Bhd",
      "company": "ABC Sdn Bhd",
      "vehicleNo": "WXY 1234",
      "vehicleRegistration": "WXY 1234",
      "location": "Main Gate",
      "locationAccess": "Main Gate",
      "visitReason": "Business Meeting",
      "reason": "Business Meeting",
      "invitedBy": "John Doe",
      "visitorTypeId": 1,
      "dateOfBirth": "1988-01-01",
      "sex": "Male",
      "resident": "Resident",
      "address": "No 5, Jalan Bahagia, Shah Alam",
      "postcode": "40150",
      "city": "Shah Alam",
      "state": "Selangor",
      "country": "Malaysia",
      "profilePhotoPath": "visitor_photos/kiosk_42_1714980000.jpg",
      "registrationSource": "kiosk",
      "status": "Approved",
      "checkedInAt": "",
      "createdAt": "2026-03-15 09:30:00"
    }
  ]
}
```

---

## 9. Endpoints — Laravel URL Routes

These two endpoints use `laravel_url` in Constants.kt. After the [Constants.kt update](#2-constantskt-update-required), they are served by VMS on the same port as all other endpoints.

### 9.1 POST Decrypt

Decrypts an AES-256-CBC encrypted string. Used by the kiosk to decode QR code payloads generated by VMS.

```
POST /decrypt
Content-Type: application/json
```

> The decryption key is configured in VMS settings under key `qr_encryption_key`. Contact the VMS administrator if QR codes are not decrypting correctly.

**Request Body:**

| Field | Type | Required | Aliases accepted | Description |
|---|---|---|---|---|
| `data` | string | **Yes** | `text`, `encrypted` | Base64-encoded AES-256-CBC encrypted string |

**Example Request:**
```json
{
  "data": "SGVsbG9Xb3JsZExhcmF2ZWxBUElFbmNyeXB0ZWQ="
}
```

**Success Response:**
```json
{
  "status": "success",
  "data": "880101145678|42|2026-05-07"
}
```

**Error — Empty input (HTTP 400):**
```json
{
  "status": "error",
  "error": {
    "status": 400,
    "error": "Validation Failed",
    "messages": {
      "0": "data field is required"
    }
  }
}
```

---

### 9.2 GET Visitor Types

Returns all visitor type categories (e.g. Contractor, Vendor, Guest).

```
GET /vms/api/visitor-types
```

**No parameters.**

**Success Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Contractor",
      "path": ""
    },
    {
      "id": 2,
      "name": "Guest",
      "path": ""
    },
    {
      "id": 3,
      "name": "Vendor",
      "path": ""
    }
  ]
}
```

**Field notes:**
- `path` — Optional sub-path/category. Usually empty. Safe to ignore.

---

## 10. Not Supported in VMS

The following features from the old MNR Java backend are **not available** in VMS. The endpoints exist and return empty arrays so the app does not crash, but no data will be returned.

| Feature | Endpoint | VMS Status |
|---|---|---|
| Vehicle types | `GET /api/admin/vinType/active/all` | Returns `[]` — not managed in VMS |
| License classes | `GET /api/admin/licenseClass/active` | Returns `[]` — not managed in VMS |
| Vaccine types | `GET /api/admin/vaccineType/active` | Returns `[]` — not managed in VMS |

If these are required for the kiosk workflow, inform the VMS backend developer to add the corresponding lookup tables and data.

---

## Quick Reference — All Endpoints

| # | Method | Path | Description |
|---|---|---|---|
| 1 | GET | `/api/admin/locationAccess/active` | Active locations |
| 2 | GET | `/api/admin/subLocationAccess/active` | Active lanes |
| 3 | GET | `/api/admin/country/active` | Active countries |
| 4 | GET | `/api/admin/state/country/{id}` | States by country |
| 5 | GET | `/api/admin/city/state/{id}` | Cities by state |
| 6 | GET | `/api/admin/vinType/active/all` | Vehicle types (empty) |
| 7 | GET | `/api/admin/licenseClass/active` | License classes (empty) |
| 8 | GET | `/api/admin/vaccineType/active` | Vaccine types (empty) |
| 9 | GET | `/api/admin/moduleConfig/getByProject` | Feature flags / config |
| 10 | GET | `/api/admin/campanies/all` | All companies |
| 11 | GET | `/api/vendorpass/getVisitReasonList` | Visit reasons (paginated) |
| 12 | POST | `/api/vendorpass/checkICExist` | Check if IC exists |
| 13 | POST | `/api/vendorpass/doVisitorPassReqMobile` | Register walk-in visitor |
| 14 | POST | `/api/vendorpass/insertVendorPassCard` | Assign RFID card |
| 15 | POST | `/api/vendorpass/uploadVendorPassPhotoMobile` | Upload visitor photo |
| 16 | GET | `/api/user/getStaffPassByStaffNoOrName` | Search staff |
| 17 | GET | `/api/user/getVisitorPassByStaffNoOrName` | Search previous visitors |
| 18 | POST | `/decrypt` | Decrypt QR/AES data |
| 19 | GET | `/vms/api/visitor-types` | Visitor type categories |
