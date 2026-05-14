<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CityModel;
use App\Models\CompanyModel;
use App\Models\CountryModel;
use App\Models\InvitationModel;
use App\Models\LaneModel;
use App\Models\LocationModel;
use App\Models\SettingModel;
use App\Models\StaffModel;
use App\Models\StateModel;
use App\Models\VisitReasonModel;
use App\Models\VisitorCardLogModel;
use App\Models\VisitorCardModel;
use App\Models\VisitorTypeModel;
use CodeIgniter\API\ResponseTrait;

/**
 * KioskApi — public API endpoints consumed by the MNR kiosk mobile app.
 *
 * All routes are unauthenticated. They mirror the Java/Spring URL scheme so the
 * Android app requires no code changes:
 *
 *  base_url  → http://<host>:8080   (VMS port)
 *  laravel_url → http://<host>       (port 80 — also served here for convenience)
 */
class KioskApi extends BaseController
{
    use ResponseTrait;

    // -------------------------------------------------------------------------
    // Admin — lookups
    // -------------------------------------------------------------------------

    /** GET /api/admin/locationAccess/active */
    public function getActiveLocations(): \CodeIgniter\HTTP\Response
    {
        $model = new LocationModel();
        $locations = $model->where('status', 'active')
            ->orderBy('location_access', 'ASC')
            ->findAll();

        $data = array_map(fn($l) => [
            'id'              => (int) $l['id'],
            'locationAccess'  => $l['location_access'],
            'branch'          => $l['branch'],
            'mobileApp'       => $l['mobile_app'],
            'isHoldArea'      => $l['is_hold_area'],
            'visitorPassPrint' => $l['visitor_pass_print'],
            'inOutBound'      => $l['in_out_bound'],
            'status'          => $l['status'],
        ], $locations);

        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    /** GET /api/admin/subLocationAccess/active */
    public function getActiveSubLocations(): \CodeIgniter\HTTP\Response
    {
        $db = \Config\Database::connect();
        $lanes = $db->table('lanes')
            ->select('lanes.id, lanes.lane, lanes.location_id, lanes.kiosk_ip,
                      lanes.in_bound, lanes.out_bound, lanes.status,
                      locations.location_access, locations.branch')
            ->join('locations', 'locations.id = lanes.location_id', 'left')
            ->where('lanes.status', 'active')
            ->orderBy('locations.location_access', 'ASC')
            ->orderBy('lanes.lane', 'ASC')
            ->get()->getResultArray();

        $data = array_map(fn($l) => [
            'id'             => (int) $l['id'],
            'lane'           => $l['lane'],
            'locationId'     => (int) $l['location_id'],
            'locationAccess' => $l['location_access'],
            'branch'         => $l['branch'],
            'kioskIp'        => $l['kiosk_ip'],
            'inBound'        => $l['in_bound'],
            'outBound'       => $l['out_bound'],
            'status'         => $l['status'],
        ], $lanes);

        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    /** GET /api/admin/country/active */
    public function getActiveCountries(): \CodeIgniter\HTTP\Response
    {
        $model = new CountryModel();
        $countries = $model->where('status', 'Active')
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = array_map(fn($c) => [
            'id'     => (int) $c['id'],
            'name'   => $c['name'],
            'code'   => $c['code'],
            'status' => $c['status'],
        ], $countries);

        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    /** GET /api/admin/state/country/:countryId */
    public function getStatesByCountry(int $countryId): \CodeIgniter\HTTP\Response
    {
        $model = new StateModel();
        $states = $model->where('country_id', $countryId)
            ->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = array_map(fn($s) => [
            'id'        => (int) $s['id'],
            'name'      => $s['name'],
            'code'      => $s['code'],
            'countryId' => (int) $s['country_id'],
            'status'    => $s['status'],
        ], $states);

        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    /** GET /api/admin/city/state/:stateId */
    public function getCitiesByState(int $stateId): \CodeIgniter\HTTP\Response
    {
        $model = new CityModel();
        $cities = $model->where('state_id', $stateId)
            ->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = array_map(fn($c) => [
            'id'      => (int) $c['id'],
            'name'    => $c['name'],
            'code'    => $c['code'] ?? '',
            'stateId' => (int) $c['state_id'],
            'status'  => $c['status'],
        ], $cities);

        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    /**
     * GET /api/admin/vinType/active/all
     * Vehicle types are not tracked in VMS — return empty list.
     */
    public function getActiveVehicleTypes(): \CodeIgniter\HTTP\Response
    {
        return $this->respond(['status' => 'success', 'data' => []]);
    }

    /**
     * GET /api/admin/licenseClass/active
     * License classes are not tracked in VMS — return empty list.
     */
    public function getActiveLicenseClasses(): \CodeIgniter\HTTP\Response
    {
        return $this->respond(['status' => 'success', 'data' => []]);
    }

    /**
     * GET /api/admin/vaccineType/active
     * Vaccine types are not tracked in VMS — return empty list.
     */
    public function getActiveVaccineTypes(): \CodeIgniter\HTTP\Response
    {
        return $this->respond(['status' => 'success', 'data' => []]);
    }

    /** GET /api/admin/moduleConfig/getByProject */
    public function getModuleConfig(): \CodeIgniter\HTTP\Response
    {
        $model = new SettingModel();
        $settings = $model->findAll();

        $config = [];
        foreach ($settings as $s) {
            $config[$s['setting_key']] = $s['setting_value'];
        }

        // Expose only non-sensitive keys relevant to the kiosk
        $kiosk = [
            'project'            => $config['project_name']          ?? 'VMS',
            'visitorPassPrint'   => $config['visitor_pass_print']    ?? 'enabled',
            'facialVerification' => $config['facial_verification']   ?? 'disabled',
            'securityBriefing'   => $config['security_briefing']     ?? 'disabled',
            'allowWalkIn'        => $config['allow_walk_in']         ?? 'enabled',
        ];

        return $this->respond(['status' => 'success', 'data' => $kiosk]);
    }

    /** GET /api/admin/campanies/all  (typo kept for mobile app compatibility) */
    public function getAllCompanies(): \CodeIgniter\HTTP\Response
    {
        $model = new CompanyModel();
        $companies = $model->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = array_map(fn($c) => [
            'id'             => (int) $c['id'],
            'name'           => $c['name'],
            'registrationNo' => $c['registration_no'] ?? '',
            'address'        => $c['address']         ?? '',
            'contactNo'      => $c['contact_no']      ?? '',
            'email'          => $c['email']            ?? '',
            'status'         => $c['status'],
        ], $companies);

        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    // -------------------------------------------------------------------------
    // Vendor pass — visit reasons
    // -------------------------------------------------------------------------

    /** GET /api/vendorpass/getVisitReasonList?page=0&pageSize=20 */
    public function getVisitReasonList(): \CodeIgniter\HTTP\Response
    {
        $page     = max(0, (int) $this->request->getGet('page'));
        $pageSize = max(1, (int) ($this->request->getGet('pageSize') ?? 20));

        $model  = new VisitReasonModel();
        $total  = $model->countAll();
        $offset = $page * $pageSize;

        $reasons = (new VisitReasonModel())
            ->orderBy('reason', 'ASC')
            ->limit($pageSize, $offset)
            ->findAll();

        $data = array_map(fn($r) => [
            'id'     => (int) $r['id'],
            'reason' => $r['reason'],
        ], $reasons);

        return $this->respond([
            'status'        => 'success',
            'content'       => $data,
            'totalElements' => (int) $total,
            'totalPages'    => (int) ceil($total / $pageSize),
            'page'          => $page,
            'size'          => $pageSize,
        ]);
    }

    // -------------------------------------------------------------------------
    // Vendor pass — IC check & registration
    // -------------------------------------------------------------------------

    /**
     * POST /api/vendorpass/checkICExist
     * Body: { "icNo": "...", "passportNo": "..." }
     */
    public function checkICExist(): \CodeIgniter\HTTP\Response
    {
        $body   = $this->request->getJSON(true) ?? $this->request->getPost();
        $icNo   = trim($body['icNo'] ?? $body['ic_no'] ?? $body['icPassport'] ?? '');

        if ($icNo === '') {
            return $this->failValidationErrors('icNo is required');
        }

        $model      = new InvitationModel();
        $invitation = $model->where('ic_passport', $icNo)
            ->orderBy('created_at', 'DESC')
            ->first();

        if (!$invitation) {
            return $this->respond([
                'status'  => 'success',
                'exists'  => false,
                'data'    => null,
                'message' => 'IC not found',
            ]);
        }

        return $this->respond([
            'status' => 'success',
            'exists' => true,
            'data'   => $this->formatInvitation($invitation),
        ]);
    }

    /**
     * POST /api/vendorpass/doVisitorPassReqMobile
     * Registers a walk-in visitor and creates an invitation record.
     *
     * Expected body fields (camelCase from mobile):
     *   visitorName, icNo, phoneNo, email, companyName, vehicleNo,
     *   locationId, visitReason, visitorTypeId, invitedBy, hostContact,
     *   dateOfBirth, sex, resident, address, postcode, city, state, country
     */
    public function doVisitorPassReqMobile(): \CodeIgniter\HTTP\Response
    {
        $body = $this->request->getJSON(true) ?? $this->request->getPost();

        $fullName  = trim($body['visitorName'] ?? $body['full_name']    ?? '');
        $icNo      = trim($body['icNo']        ?? $body['ic_no']        ?? $body['ic_passport'] ?? '');
        $contact   = trim($body['phoneNo']     ?? $body['contact']      ?? '');
        $email     = trim($body['email']       ?? $body['visitorEmail'] ?? '');
        $company   = trim($body['companyName'] ?? $body['company']      ?? '');
        $vehicleNo = trim($body['vehicleNo']   ?? $body['vehicle_registration'] ?? '');
        $location  = trim($body['locationAccess'] ?? $body['location']  ?? '');
        $rawReason = trim($body['visitReason'] ?? $body['reason']       ?? '');
        $invitedBy = trim($body['invitedBy']   ?? $body['invited_by']   ?? '');

        if ($fullName === '' || $contact === '' || $rawReason === '') {
            return $this->failValidationErrors('visitorName, phoneNo, and visitReason are required');
        }

        // Resolve numeric reason ID to the actual reason text
        if (ctype_digit($rawReason)) {
            $reasonRow = (new VisitReasonModel())->find((int) $rawReason);
            $reason = $reasonRow ? $reasonRow['reason'] : $rawReason;
        } else {
            $reason = $rawReason;
        }

        $data = [
            'full_name'            => $fullName,
            'ic_passport'          => $icNo,
            'contact'              => $contact,
            'visitor_email'        => $email,
            'company'              => $company,
            'vehicle_registration' => $vehicleNo,
            'location'             => $location,
            'reason'               => $reason,
            'invited_by'           => $invitedBy,
            'visitor_type_id'      => (int) ($body['visitorTypeId'] ?? $body['visitor_type_id'] ?? 0) ?: null,
            'host_contact'         => trim($body['hostContact']    ?? $body['host_contact']    ?? ''),
            'company_visited'      => trim($body['companyVisited'] ?? $body['company_visited'] ?? ''),
            'date_of_birth'        => $body['dateOfBirth']  ?? $body['date_of_birth']  ?? null,
            'sex'                  => $body['sex']          ?? null,
            'resident'             => $body['resident']     ?? null,
            'address'              => $body['address']      ?? null,
            'postcode'             => $body['postcode']     ?? null,
            'city'                 => $body['city']         ?? null,
            'state'                => $body['state']        ?? null,
            'country'              => $body['country']      ?? null,
            'registration_source'  => 'kiosk',
            'status'               => 'Submitted',
        ];

        // Remove null fields so model validation stays clean
        $data = array_filter($data, fn($v) => $v !== null && $v !== '');

        $model = new InvitationModel();
        $model->skipValidation(true);
        $id = $model->insert($data, true);

        if (!$id) {
            return $this->failServerError('Failed to create visitor pass');
        }

        $invitation = $model->find($id);

        return $this->respondCreated([
            'status'  => 'success',
            'message' => 'Visitor pass created successfully',
            'data'    => $this->formatInvitation($invitation),
        ]);
    }

    // -------------------------------------------------------------------------
    // Vendor pass — card assignment & photo upload
    // -------------------------------------------------------------------------

    /**
     * POST /api/vendorpass/insertVendorPassCard
     * Binds a visitor card to an invitation.
     * Body: { "cardId": "...", "invitationId": 123 }
     */
    public function insertVendorPassCard(): \CodeIgniter\HTTP\Response
    {
        $body         = $this->request->getJSON(true) ?? $this->request->getPost();
        $cardId       = trim($body['cardId']       ?? $body['card_id']       ?? '');
        $invitationId = (int) ($body['invitationId'] ?? $body['invitation_id'] ?? 0);

        if ($cardId === '' || $invitationId === 0) {
            return $this->failValidationErrors('cardId and invitationId are required');
        }

        $invModel   = new InvitationModel();
        $invitation = $invModel->find($invitationId);
        if (!$invitation) {
            return $this->failNotFound('Invitation not found');
        }

        $cardModel = new VisitorCardModel();
        $card      = $cardModel->where('card_id', $cardId)->first();

        if (!$card) {
            return $this->failNotFound("Card '{$cardId}' not found in VMS");
        }

        if ($card['status'] !== 'active') {
            return $this->failForbidden("Card '{$cardId}' is not available (status: {$card['status']})");
        }

        // Mark card as in_use
        $cardModel->update($card['id'], ['status' => 'in_use']);

        // Log the card check-in assignment
        $logModel = new VisitorCardLogModel();
        $logModel->insert([
            'visitor_card_id' => $card['id'],
            'invitation_id'   => $invitationId,
            'action'          => 'checkin',
            'scanned_at'      => date('Y-m-d H:i:s'),
        ]);

        return $this->respond([
            'status'       => 'success',
            'message'      => 'Card assigned successfully',
            'cardId'       => $cardId,
            'invitationId' => $invitationId,
        ]);
    }

    /**
     * POST /api/vendorpass/uploadVendorPassPhotoMobile
     * Accepts a visitor photo (multipart or base64) and stores it.
     * Form fields: file (image), invitationId
     */
    public function uploadVendorPassPhotoMobile(): \CodeIgniter\HTTP\Response
    {
        $invitationId = (int) ($this->request->getPost('invitationId') ?? $this->request->getPost('invitation_id') ?? 0);

        if ($invitationId === 0) {
            return $this->failValidationErrors('invitationId is required');
        }

        $invModel   = new InvitationModel();
        $invitation = $invModel->find($invitationId);
        if (!$invitation) {
            return $this->failNotFound('Invitation not found');
        }

        $file = $this->request->getFile('file') ?? $this->request->getFile('photo');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName  = 'kiosk_' . $invitationId . '_' . time() . '.' . $file->getExtension();
            $savePath = WRITEPATH . '../public/uploads/visitor_photos/';

            if (!is_dir($savePath)) {
                mkdir($savePath, 0775, true);
            }

            $file->move($savePath, $newName);
            $relativePath = 'visitor_photos/' . $newName;

            $invModel->skipValidation(true)->update($invitationId, [
                'profile_photo_path' => $relativePath,
            ]);

            return $this->respond([
                'status'  => 'success',
                'message' => 'Photo uploaded successfully',
                'path'    => $relativePath,
            ]);
        }

        // Fallback: accept base64-encoded image
        $base64 = $this->request->getPost('photo_base64') ?? ($this->request->getJSON(true)['photo_base64'] ?? null);
        if ($base64) {
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
            if ($imageData === false) {
                return $this->failValidationErrors('Invalid base64 image data');
            }

            $savePath = WRITEPATH . '../public/uploads/visitor_photos/';
            if (!is_dir($savePath)) {
                mkdir($savePath, 0775, true);
            }

            $newName = 'kiosk_' . $invitationId . '_' . time() . '.jpg';
            file_put_contents($savePath . $newName, $imageData);
            $relativePath = 'visitor_photos/' . $newName;

            $invModel->skipValidation(true)->update($invitationId, [
                'profile_photo_path' => $relativePath,
            ]);

            return $this->respond([
                'status'  => 'success',
                'message' => 'Photo uploaded successfully',
                'path'    => $relativePath,
            ]);
        }

        return $this->failValidationErrors('No valid photo file or base64 data provided');
    }

    // -------------------------------------------------------------------------
    // User — staff & visitor pass lookup
    // -------------------------------------------------------------------------

    /**
     * GET /api/user/getStaffPassByStaffNoOrName?keyword=...
     * Also accepts POST body { "username": "..." } for Android app compatibility.
     */
    public function getStaffPassByStaffNoOrName(): \CodeIgniter\HTTP\Response
    {
        // Accept GET param OR POST body (Android sends POST with "username" key)
        $keyword = trim(
            $this->request->getGet('keyword')
                ?? $this->request->getGet('staffNo')
                ?? $this->request->getPost('username')
                ?? ($this->request->getJSON(true)['username'] ?? '')
        );

        if ($keyword === '') {
            return $this->respond(['status' => 'success', 'data' => []]);
        }

        $model = new StaffModel();
        $staff = $model->groupStart()
            ->like('staff_no', $keyword)
            ->orLike('full_name', $keyword)
            ->orLike('name_on_staff_pass', $keyword)
            ->groupEnd()
            ->where('status', 'active')
            ->orderBy('full_name', 'ASC')
            ->findAll(20);

        $data = array_map(fn($s) => [
            'id'             => (int) $s['id'],
            'staffNo'        => $s['staff_no'],
            'username'       => $s['staff_no'],        // Android uses 'username' field
            'fullName'       => $s['full_name'],
            'name'           => $s['full_name'],        // Android uses 'name' field
            'nameOnPass'     => $s['name_on_staff_pass'] ?? $s['full_name'],
            'icPassport'     => $s['ic_passport'] ?? '',
            'icNo'           => $s['ic_passport'] ?? '', // Android uses 'icNo' field
            'passportNo'     => '',                      // Staff won't have passport
            'department'     => $s['department']  ?? '',
            'designation'    => $s['designation'] ?? '',
            'locationAccess' => $s['location_access'] ?? '',
            'cardStatus'     => $s['card_status'] ?? '',
            'cardExpiry'     => $s['card_expiry'] ?? '',
            'mobileNo'       => $s['contact_number'] ?? '', // Android uses 'mobileNo' field
            'email'          => $s['email'] ?? '',
            'photo'          => $s['photo_path'] ?? '',     // Android uses 'photo' field
            'visitorType'    => '',                          // Staff don't have visitorType
            'status'         => $s['status'],
            'message'        => '',
        ], $staff);

        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    /**
     * GET /api/user/getVisitorPassByStaffNoOrName?keyword=...
     * Also accepts POST body { "username": "..." } for Android app compatibility.
     * Used by PortraitCaptureActivity to look up invitation by QR scan data.
     */
    public function getVisitorPassByStaffNoOrName(): \CodeIgniter\HTTP\Response
    {
        // Accept GET param OR POST body (Android sends POST with "username" key)
        $keyword = trim(
            $this->request->getGet('keyword')
                ?? $this->request->getGet('staffNo')
                ?? $this->request->getPost('username')
                ?? ($this->request->getJSON(true)['username'] ?? '')
        );

        if ($keyword === '') {
            return $this->respond(['status' => 'success', 'data' => []]);
        }

        $model    = new InvitationModel();
        $visitors = $model->groupStart()
            ->like('ic_passport', $keyword)
            ->orLike('full_name', $keyword)
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->findAll(20);

        $data = array_map(fn($v) => $this->formatInvitation($v), $visitors);

        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    // -------------------------------------------------------------------------
    // Visitor types — served at laravel_url (/vms/api/...)
    // -------------------------------------------------------------------------

    /** GET /vms/api/visitor-types */
    public function getVisitorTypes(): \CodeIgniter\HTTP\Response
    {
        $model = new VisitorTypeModel();
        $types = $model->orderBy('name', 'ASC')->findAll();

        $data = array_map(fn($t) => [
            'id'   => (int) $t['id'],
            'name' => $t['name'],
            'path' => $t['path'] ?? '',
        ], $types);

        return $this->respond(['status' => 'success', 'data' => $data]);
    }

    // -------------------------------------------------------------------------
    // Decrypt — served at laravel_url (/decrypt)
    // -------------------------------------------------------------------------

    /**
     * POST /decrypt
     * Body: { "data": "<encrypted_string>" }
     *
     * Compatible with the AES-256-CBC encryption used in the VMS QR workflow.
     * The secret key is read from the setting key "qr_encryption_key" or
     * falls back to the CI4 encryption key.
     */
    public function decrypt(): \CodeIgniter\HTTP\Response
    {
        $body      = $this->request->getJSON(true) ?? $this->request->getPost();
        $encrypted = trim($body['data'] ?? $body['text'] ?? $body['encrypted'] ?? '');

        if ($encrypted === '') {
            return $this->failValidationErrors('data field is required');
        }

        try {
            $settingModel = new SettingModel();
            $keySetting   = $settingModel->where('setting_key', 'qr_encryption_key')->first();
            $key          = $keySetting ? $keySetting['setting_value'] : null;

            if ($key) {
                // AES-256-CBC with base64 payload
                $decoded = base64_decode($encrypted);
                $iv      = substr($decoded, 0, 16);
                $cipher  = substr($decoded, 16);
                $plain   = openssl_decrypt($cipher, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

                if ($plain !== false) {
                    return $this->respond(['status' => 'success', 'data' => $plain]);
                }
            }

            // Fallback: treat as plain JSON / token
            $decoded = base64_decode($encrypted, true);
            if ($decoded !== false) {
                return $this->respond(['status' => 'success', 'data' => $decoded]);
            }

            return $this->respond(['status' => 'success', 'data' => $encrypted]);
        } catch (\Throwable $e) {
            log_message('error', 'KioskApi::decrypt error: ' . $e->getMessage());
            return $this->failServerError('Decryption failed');
        }
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------

    /**
     * Format an invitation row into the response shape expected by the Android app.
     *
     * Android model: GetStaffPassByStaffNoOrNameResponseItem
     * Required fields: username, name, mobileNo, icNo, passportNo, photo, visitorType,
     *                  email, status, message
     *
     * Extra fields used by CardDetailsActivity auto-fill:
     *   companyName, vehicleNo/regNum, address, postcode, city, state, country,
     *   dateOfBirth, visitReason/reason
     */
    private function formatInvitation(array $inv): array
    {
        // Split ic_passport into icNo vs passportNo based on resident field
        // Non-Malaysian / Foreigner → passportNo, Malaysian → icNo
        $icPassport  = $inv['ic_passport'] ?? '';
        $isForeigner = in_array(
            strtolower($inv['resident'] ?? ''),
            ['non-malaysian', 'foreigner', 'non malaysian']
        );
        $icNo       = $isForeigner ? '' : $icPassport;
        $passportNo = $isForeigner ? $icPassport : '';

        return [
            // Core fields Android app model expects
            'id'          => (int) $inv['id'],
            'username'    => $inv['ic_passport']   ?? '',   // used as QR lookup key
            'name'        => $inv['full_name']      ?? '',   // Android uses 'name'
            'fullName'    => $inv['full_name']      ?? '',   // alias
            'mobileNo'    => $inv['contact']        ?? '',   // Android uses 'mobileNo'
            'contactNo'   => $inv['contact']        ?? '',   // alias for mobileNo
            'icNo'        => $icNo,                          // Malaysian IC
            'passportNo'  => $passportNo,                    // Foreigner passport
            'photo'       => $inv['profile_photo_path'] ?? '', // Android uses 'photo'
            'visitorType' => $inv['visitor_type_id']         // Android uses 'visitorType'
                ? $this->resolveVisitorType((int) $inv['visitor_type_id'])
                : '',
            'email'       => $inv['visitor_email']  ?? '',
            'status'      => $inv['status']         ?? '',
            'message'     => '',                             // placeholder for error msg

            // Extra fields for CardDetailsActivity auto-fill
            'companyName'  => $inv['company']              ?? '',
            'company'      => $inv['company']              ?? '',
            'vehicleNo'    => $inv['vehicle_registration'] ?? '',
            'regNum'       => $inv['vehicle_registration'] ?? '', // alias used by CardDetails
            'address'      => $inv['address']              ?? '',
            'postcode'     => $inv['postcode']             ?? '',
            'city'         => $inv['city']                 ?? '',
            'state'        => $inv['state']                ?? '',
            'country'      => $inv['country']              ?? '', // needed for foreigner country box

            // Additional fields
            'dateOfBirth'        => $inv['date_of_birth']        ?? '',
            'visitReason'        => $inv['reason']               ?? '',
            'reason'             => $inv['reason']               ?? '',
            'location'           => $inv['location']             ?? '',
            'invitedBy'          => $inv['invited_by']           ?? '',
            'visitorTypeId'      => (int) ($inv['visitor_type_id'] ?? 0),
            'resident'           => $inv['resident']             ?? '',
            'registrationSource' => $inv['registration_source']  ?? '',
            'createdAt'          => $inv['created_at']           ?? '',
        ];
    }

    /**
     * Resolve visitor type name from visitor_type_id.
     * Used by formatInvitation() to populate 'visitorType' field.
     */
    private function resolveVisitorType(int $id): string
    {
        $model = new VisitorTypeModel();
        $type  = $model->find($id);
        return $type ? ($type['name'] ?? '') : '';
    }
}
