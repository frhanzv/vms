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
use App\Models\UserModel;
use App\Models\VisitReasonModel;
use App\Models\VisitorCardLogModel;
use App\Models\VisitorCardModel;
use App\Models\VisitorTypeModel;
use CodeIgniter\API\ResponseTrait;
use App\Models\MobileKioskSettingModel;
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
        $model = new MobileKioskSettingModel();
        $settings = $model->findAll();

        $config = [];
        foreach ($settings as $s) {
            $config[$s['setting_key']] = $s['setting_value'];
        }

        $defaultVisitorFields = [
            'contact_number'  => ['show' => true,  'required' => true],
            'company_name'    => ['show' => true,  'required' => true],
            'email'           => ['show' => true,  'required' => false],
            'vehicle_reg_no'  => ['show' => true,  'required' => false],
            'address'         => ['show' => true,  'required' => true],
            'date_of_birth'   => ['show' => false, 'required' => false],
            'postal_code'     => ['show' => false, 'required' => false],
            'state'           => ['show' => false, 'required' => false],
            'city'            => ['show' => false, 'required' => false],
            'cardholder_name' => ['show' => true,  'required' => true],
            'ic_number'       => ['show' => true,  'required' => true],
            'country'         => ['show' => true,  'required' => true],
        ];

        $visitorFieldsRaw = $config['kiosk_visitor_fields'] ?? null;
        $visitorFields = $visitorFieldsRaw
            ? json_decode($visitorFieldsRaw, true) ?? $defaultVisitorFields
            : $defaultVisitorFields;

        $walkIn      = filter_var($config['kiosk_walk_in']      ?? 'true', FILTER_VALIDATE_BOOLEAN);
        $invitation  = filter_var($config['kiosk_invitation']   ?? 'true', FILTER_VALIDATE_BOOLEAN);
        $collectCard = filter_var($config['kiosk_collect_card'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
        $vvip        = filter_var($config['kiosk_vvip']         ?? 'true', FILTER_VALIDATE_BOOLEAN);

        $kiosk = [
            // Legacy MNR fields (camelCase + enabled/disabled strings)
            'project'            => $config['project_name']        ?? 'VMS',
            'visitorPassPrint'   => $config['visitor_pass_print']  ?? 'enabled',
            'facialVerification' => $config['facial_verification'] ?? 'disabled',
            'securityBriefing'   => $config['security_briefing']  ?? 'disabled',
            'allowWalkIn'        => $walkIn ? 'enabled' : 'disabled',
            'vpOCR'              => true,
            'vpFacial'           => true,

            // Kiosk feature flags — camelCase (Android/Gson) and snake_case
            'walkIn'             => $walkIn,
            'walk_in'            => $walkIn,
            'invitation'         => $invitation,
            'collectCard'        => $collectCard,
            'collect_card'       => $collectCard,
            'vvip'               => $vvip,

            // Branding
            'welcomeText'        => $config['kiosk_welcome_text']  ?? 'Welcome',
            'welcome_text'       => $config['kiosk_welcome_text']  ?? 'Welcome',
            'primaryColor'       => $config['kiosk_primary_color'] ?? '#1A73E8',
            'primary_color'      => $config['kiosk_primary_color'] ?? '#1A73E8',
            'logoUrl'            => $config['kiosk_logo_url']      ?? '',
            'logo_url'           => $config['kiosk_logo_url']      ?? '',

            'visitorFields'      => $visitorFields,
            'visitor_fields'     => $visitorFields,
        ];

        return $this->respond(['status' => 'success', 'data' => $kiosk]);
    }

    /** GET /api/admin/campanies/all  (typo kept for mobile app compatibility) */
    public function getAllCompanies(): \CodeIgniter\HTTP\Response
    {
        $model     = new CompanyModel();
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
        $body = $this->request->getJSON(true) ?? $this->request->getPost();
        $icNo = trim($body['icNo'] ?? $body['ic_no'] ?? $body['icPassport'] ?? '');

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
     *   visitReason, visitorTypeId, invitedBy, resident, address,
     *   photo_base64 (optional — face photo from FaceDetectionActivity)
     *
     * Note: Android sends resident as "Local" or "Foreigner"
     *       DB stores as "Malaysian" or "Non-Malaysian"
     * Note: Android does NOT send country/state/city — auto-set Malaysia for local.
     * Note: country, state, city are sent as int IDs from Android — resolved to names here.
     */
    public function doVisitorPassReqMobile(): \CodeIgniter\HTTP\Response
    {
        $body = $this->request->getJSON(true) ?? $this->request->getPost();

        $fullName  = trim($body['visitorName']    ?? $body['full_name']             ?? '');
        $icNo      = trim($body['icNo']           ?? $body['ic_no']                ?? $body['ic_passport'] ?? '');
        $contact   = trim($body['phoneNo']        ?? $body['contact']              ?? '');
        $email     = trim($body['email']          ?? $body['visitorEmail']         ?? '');
        $company   = trim($body['companyName']    ?? $body['company']              ?? '');
        $vehicleNo = trim($body['vehicleNo']      ?? $body['vehicle_registration'] ?? '');
        $location  = trim($body['locationAccess'] ?? $body['location']             ?? '');
        $rawReason = trim($body['visitReason']    ?? $body['reason']               ?? '');
        $invitedBy = trim($body['invitedBy']      ?? $body['invited_by']           ?? '');

        if ($fullName === '' || $contact === '' || $rawReason === '') {
            return $this->failValidationErrors('visitorName, phoneNo, and visitReason are required');
        }

        // Resolve numeric reason ID to the actual reason text
        if (ctype_digit($rawReason)) {
            $reasonRow = (new VisitReasonModel())->find((int) $rawReason);
            $reason    = $reasonRow ? $reasonRow['reason'] : $rawReason;
        } else {
            $reason = $rawReason;
        }

        // Normalize resident field
        // Android sends "Local" or "Foreigner"
        // DB stores "Malaysian" or "Non-Malaysian"
        // Fallback: auto-detect from IC format if not provided
        $residentRaw        = trim($body['resident'] ?? '');
        $residentNormalized = match (strtolower($residentRaw)) {
            'local', 'malaysian'              => 'Malaysian',
            'foreigner', 'non-malaysian',
            'non malaysian'                   => 'Non-Malaysian',
            // Not provided — auto-detect from IC format
            // Malaysian IC = exactly 12 digits numeric
            default => preg_match('/^\d{12}$/', $icNo) ? 'Malaysian' : 'Non-Malaysian',
        };

        log_message('info', "KioskApi::doVisitorPassReqMobile resident raw='{$residentRaw}' normalized='{$residentNormalized}' ic='{$icNo}'");

        // Resolve country ID to name — Android sends int country ID
        // If local and no country sent — auto-set Malaysia
        // If foreigner and no country sent — leave null (Android never sends country)
        $countryRaw  = $body['country'] ?? null;
        $countryName = null;
        if ($countryRaw !== null && is_numeric($countryRaw) && (int) $countryRaw > 0) {
            // Numeric ID — look up country name from DB
            $countryModel = new CountryModel();
            $countryRow   = $countryModel->find((int) $countryRaw);
            $countryName  = $countryRow ? $countryRow['name'] : null;
        } elseif ($countryRaw !== null && !is_numeric($countryRaw) && trim($countryRaw) !== '') {
            // Already a string name — use as is
            $countryName = trim($countryRaw);
        } elseif ($residentNormalized === 'Malaysian') {
            // Local visitor — Android never sends country, auto-set Malaysia
            $countryModel = new CountryModel();
            $malaysia     = $countryModel->where('name', 'Malaysia')->first();
            $countryName  = $malaysia ? $malaysia['name'] : 'Malaysia';
            log_message('info', "KioskApi::doVisitorPassReqMobile auto-set country=Malaysia for local visitor");
        }
        // Foreigner — country stays null, Android never sends it

        // Resolve state ID to name — Android sends int state ID
        $stateRaw  = $body['state'] ?? null;
        $stateName = null;
        if ($stateRaw !== null && is_numeric($stateRaw) && (int) $stateRaw > 0) {
            // Numeric ID — look up state name from DB
            $stateModel = new StateModel();
            $stateRow   = $stateModel->find((int) $stateRaw);
            $stateName  = $stateRow ? $stateRow['name'] : null;
        } elseif ($stateRaw !== null && !is_numeric($stateRaw) && trim($stateRaw) !== '') {
            // Already a string name — use as is
            $stateName = trim($stateRaw);
        }

        // Resolve city ID to name — Android sends int city ID
        $cityRaw  = $body['city'] ?? null;
        $cityName = null;
        if ($cityRaw !== null && is_numeric($cityRaw) && (int) $cityRaw > 0) {
            // Numeric ID — look up city name from DB
            $cityModel = new CityModel();
            $cityRow   = $cityModel->find((int) $cityRaw);
            $cityName  = $cityRow ? $cityRow['name'] : null;
        } elseif ($cityRaw !== null && !is_numeric($cityRaw) && trim($cityRaw) !== '') {
            // Already a string name — use as is
            $cityName = trim($cityRaw);
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
            'host_contact'         => trim($body['hostContact']     ?? $body['host_contact']     ?? ''),
            'company_visited'      => trim($body['companyVisited']  ?? $body['company_visited']  ?? ''),
            'date_of_birth'        => $body['dateOfBirth'] ?? $body['date_of_birth'] ?? null,
            'sex'                  => $body['sex']         ?? null,
            // Use normalized resident — "Malaysian" or "Non-Malaysian"
            'resident'             => $residentNormalized,
            'address'              => $body['address']     ?? null,
            'postcode'             => $body['postcode']    ?? null,
            // Use resolved names instead of raw IDs
            'city'                 => $cityName,
            'state'                => $stateName,
            'country'              => $countryName,
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

        // Save face photo if provided in payload
        // Android ThankYouActivity sends photo_base64 inside doVisitorPassReqMobile body
        $photoBase64 = $body['photo_base64'] ?? $body['photo'] ?? null;
        if ($photoBase64) {
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photoBase64));
            if ($imageData !== false) {
                $savePath = WRITEPATH . '../public/uploads/visitor_photos/';
                if (!is_dir($savePath)) {
                    mkdir($savePath, 0775, true);
                }

                $newName      = 'kiosk_' . $id . '_' . time() . '.jpg';
                file_put_contents($savePath . $newName, $imageData);
                $relativePath = 'visitor_photos/' . $newName;

                // Update invitation with photo path + facial verification fields
                $model->skipValidation(true)->update($id, [
                    'profile_photo_path'        => $relativePath,
                    'facial_verification_image' => $relativePath,
                    'facial_verified_at'        => date('Y-m-d H:i:s'),
                    'updated_at'                => date('Y-m-d H:i:s'),
                ]);

                log_message('info', "KioskApi::doVisitorPassReqMobile photo saved: {$relativePath} for invitation_id={$id}");
            } else {
                log_message('warning', "KioskApi::doVisitorPassReqMobile invalid base64 photo for invitation_id={$id}");
            }
        }

        // Refresh invitation record (includes photo path and resolved names if saved)
        $invitation = $model->find($id);

        log_message('info', "KioskApi::doVisitorPassReqMobile created invitation_id={$id} name={$fullName} resident={$residentNormalized} country={$countryName} state={$stateName} city={$cityName}");

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
     *
     * Android sends: { "cardId": "...", "invitationId": 123 }
     * After success:
     *   - Card status → in_use
     *   - Invitation status → Approved + checked_in_at set
     *   - invitation_visitors record created/updated
     *   - VisitorCardLog entry created
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

        // Auto-approve invitation + set checked_in_at
        $invModel->skipValidation(true)->update($invitationId, [
            'status'        => 'Approved',
            'checked_in_at' => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        // Log the card check-in assignment
        $db = \Config\Database::connect();
        $db->table('visitor_card_logs')->insert([
            'visitor_card_id' => (int) $card['id'],
            'invitation_id'   => (int) $invitationId,
            'action'          => 'checkin',
            'scanned_at'      => date('Y-m-d H:i:s'),
            'created_at'      => date('Y-m-d H:i:s'),
        ]);

        // Insert into invitation_visitors so visitor appears in Visitors List
        $db       = \Config\Database::connect();
        $existing = $db->table('invitation_visitors')
            ->where('invitation_id', $invitationId)
            ->get()->getFirstRow('array');

        if (!$existing) {
            // Create new invitation_visitor record
            $db->table('invitation_visitors')->insert([
                'invitation_id'        => $invitationId,
                'full_name'            => $invitation['full_name'],
                'ic_passport'          => $invitation['ic_passport']          ?? 'PENDING',
                'contact'              => $invitation['contact']               ?? 'N/A',
                'company'              => $invitation['company']               ?? '',
                'vehicle_registration' => $invitation['vehicle_registration']  ?? '',
                'visitor_card_id'      => $card['id'],
                'check_in_time'        => date('Y-m-d H:i:s'),
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
                'version'              => 1,
            ]);
        } else {
            // Update existing record with card and check-in time
            $db->table('invitation_visitors')
                ->where('invitation_id', $invitationId)
                ->update([
                    'visitor_card_id' => $card['id'],
                    'check_in_time'   => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ]);
        }

        return $this->respond([
            'status'       => 'success',
            'message'      => 'Card assigned successfully',
            'cardId'       => $cardId,
            'invitationId' => $invitationId,
        ]);
    }

    /**
     * POST /api/vendorpass/uploadVendorPassPhotoMobile
     * Accepts a visitor face photo and stores it against the invitation.
     *
     * Android sends via addBodyParameter:
     *   invitationId — invitation ID from StaticData.invitationId
     *   photo_base64 — base64 encoded face image
     *
     * Also supports multipart file upload as fallback.
     */
    public function uploadVendorPassPhotoMobile(): \CodeIgniter\HTTP\Response
    {
        // Accept invitationId from POST body param (Android addBodyParameter)
        try {
            $body = $this->request->getJSON(true) ?? [];
        } catch (\Throwable $e) {
            $body = [];
        }
        $invitationId = (int) (
            $this->request->getPost('invitationId')
            ?? $this->request->getPost('invitation_id')
            ?? $body['invitationId']
            ?? $body['invitation_id']
            ?? 0
        );

        if ($invitationId === 0) {
            return $this->failValidationErrors('invitationId is required');
        }

        $invModel   = new InvitationModel();
        $invitation = $invModel->find($invitationId);
        if (!$invitation) {
            return $this->failNotFound('Invitation not found');
        }

        // Accept photo_base64 from POST body param (Android addBodyParameter)
        // Also accept 'photo' key as fallback
        $base64 = $this->request->getPost('photo_base64')
            ?? $this->request->getPost('photo')
            ?? $body['photo_base64']
            ?? $body['photo']
            ?? null;

        if ($base64) {
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
            if ($imageData === false) {
                return $this->failValidationErrors('Invalid base64 image data');
            }

            $savePath = WRITEPATH . '../public/uploads/visitor_photos/';
            if (!is_dir($savePath)) {
                mkdir($savePath, 0775, true);
            }

            $newName      = 'kiosk_' . $invitationId . '_' . time() . '.jpg';
            file_put_contents($savePath . $newName, $imageData);
            $relativePath = 'visitor_photos/' . $newName;

            // Save photo path + mark facial verification done
            $invModel->skipValidation(true)->update($invitationId, [
                'profile_photo_path'        => $relativePath,
                'facial_verification_image' => $relativePath,
                'facial_verified_at'        => date('Y-m-d H:i:s'),
                'updated_at'                => date('Y-m-d H:i:s'),
            ]);

            return $this->respond([
                'status'  => 'success',
                'message' => 'Photo uploaded successfully',
                'path'    => $relativePath,
            ]);
        }

        // Fallback: accept multipart file upload
        $file = $this->request->getFile('file') ?? $this->request->getFile('photo');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName  = 'kiosk_' . $invitationId . '_' . time() . '.' . $file->getExtension();
            $savePath = WRITEPATH . '../public/uploads/visitor_photos/';

            if (!is_dir($savePath)) {
                mkdir($savePath, 0775, true);
            }

            $file->move($savePath, $newName);
            $relativePath = 'visitor_photos/' . $newName;

            // Save photo path + mark facial verification done
            $invModel->skipValidation(true)->update($invitationId, [
                'profile_photo_path'        => $relativePath,
                'facial_verification_image' => $relativePath,
                'facial_verified_at'        => date('Y-m-d H:i:s'),
                'updated_at'                => date('Y-m-d H:i:s'),
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
        $json = $this->request->getJSON(true) ?? [];

        // Accept GET param OR POST body (Android sends POST with "username" key)
        $keyword = trim(
            $this->request->getGet('keyword')
                ?? $this->request->getGet('staffNo')
                ?? $this->request->getGet('username')
                ?? $this->request->getPost('username')
                ?? $this->request->getPost('keyword')
                ?? $this->request->getPost('staffNo')
                ?? $json['username']
                ?? $json['keyword']
                ?? $json['staffNo']
                ?? ''
        );

        $model = new StaffModel();
        $staffQuery = $model->groupStart()
            ->where('status', 'active')
            ->orWhere('status', 'Active')
            ->orWhere('status IS NULL', null, false)
            ->orWhere('status', '')
            ->groupEnd();
        if ($keyword !== '') {
            $staffQuery->groupStart()
                ->like('staff_no', $keyword)
                ->orLike('full_name', $keyword)
                ->orLike('name_on_staff_pass', $keyword)
                ->orLike('email', $keyword)
                ->groupEnd();
        }
        $staff = $staffQuery->orderBy('full_name', 'ASC')->findAll(30);

        $data = [];
        foreach ($staff as $s) {
            $row = $this->formatHostSearchRow(
                (int) $s['id'],
                'staff',
                (string) ($s['full_name'] ?? ''),
                (string) ($s['staff_no'] ?? ''),
                (string) ($s['contact_number'] ?? ''),
                (string) ($s['email'] ?? ''),
                (string) ($s['designation'] ?? ''),
                (string) ($s['department'] ?? ''),
                (string) ($s['profile_photo'] ?? $s['photo_path'] ?? '')
            );
            if ($row !== null) {
                $data[] = $row;
            }
        }

        $userModel = new UserModel();
        $userQuery = $userModel->select('id, username, email, full_name, staff_id, contact_no, role, is_active, profile_photo')
            ->where('is_active', 1);
        if ($keyword !== '') {
            $userQuery->groupStart()
                ->like('username', $keyword)
                ->orLike('full_name', $keyword)
                ->orLike('staff_id', $keyword)
                ->orLike('email', $keyword)
                ->groupEnd();
        }
        $users = $userQuery->orderBy('full_name', 'ASC')->findAll(30);

        foreach ($users as $user) {
            $staffNo = (string) ($user['staff_id'] ?: $user['username']);
            $dedupeKey = strtolower(trim($staffNo !== '' ? $staffNo : ($user['email'] ?? '')));
            $alreadyIncluded = false;
            foreach ($data as $row) {
                $existingKey = strtolower(trim((string) ($row['staffNo'] ?: $row['email'])));
                if ($dedupeKey !== '' && $existingKey === $dedupeKey) {
                    $alreadyIncluded = true;
                    break;
                }
            }
            if ($alreadyIncluded) {
                continue;
            }

            $row = $this->formatHostSearchRow(
                (int) $user['id'],
                'user',
                (string) ($user['full_name'] ?: $user['username']),
                $staffNo,
                (string) ($user['contact_no'] ?? ''),
                (string) ($user['email'] ?? ''),
                (string) ($user['role'] ?? ''),
                '',
                (string) ($user['profile_photo'] ?? '')
            );
            if ($row !== null) {
                $data[] = $row;
            }
        }

        usort($data, static fn($a, $b) => strcasecmp($a['name'] ?? '', $b['name'] ?? ''));

        return $this->respond([
            'status'        => 'success',
            'data'          => $data,
            'content'       => $data,
            'totalElements' => count($data),
        ]);
    }

    private function formatHostSearchRow(
        int $id,
        string $source,
        string $name,
        string $staffNo,
        string $mobileNo,
        string $email,
        string $designation = '',
        string $department = '',
        string $photo = ''
    ): ?array {
        $name = trim($name);
        $staffNo = trim($staffNo);
        $mobileNo = trim($mobileNo);
        $email = trim($email);

        if ($name === '' && $staffNo === '') {
            return null;
        }
        if ($staffNo === '') {
            $staffNo = $email !== '' ? $email : (string) $id;
        }
        if ($name === '') {
            $name = $staffNo;
        }

        return [
            'id'             => $id,
            'source'         => $source,
            'sourceId'       => $id,
            'staffNo'        => $staffNo,
            'staff_no'       => $staffNo,
            'staffId'        => $staffNo,
            'staff_id'       => $staffNo,
            'username'       => $staffNo,
            'fullName'       => $name,
            'full_name'      => $name,
            'staffName'      => $name,
            'staff_name'     => $name,
            'name'           => $name,
            'nameOnPass'     => $name,
            'name_on_pass'   => $name,
            'mobileNo'       => $mobileNo,
            'mobile_no'      => $mobileNo,
            'phoneNo'        => $mobileNo,
            'phone_no'       => $mobileNo,
            'phone'          => $mobileNo,
            'contactNo'      => $mobileNo,
            'contact_no'     => $mobileNo,
            'contactNumber'  => $mobileNo,
            'contact_number' => $mobileNo,
            'telNo'          => $mobileNo,
            'tel_no'         => $mobileNo,
            'email'          => $email,
            'department'     => $department,
            'designation'    => $designation,
            'role'           => $designation,
            'photo'          => $photo,
            'profilePhoto'   => $photo,
            'profile_photo'  => $photo,
            'icPassport'     => '',
            'icNo'           => '',
            'passportNo'     => '',
            'locationAccess' => '',
            'cardStatus'     => '',
            'cardExpiry'     => '',
            'visitorType'    => '',
            'status'         => 'active',
            'message'        => '',
        ];
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

        // Flow flag — sent by Android to indicate which flow called this
        // Values: "invitation", "collect_card", "walk_in"
        $flow = trim(
            $this->request->getGet('flow')
                ?? $this->request->getPost('flow')
                ?? ($this->request->getJSON(true)['flow'] ?? '')
        );

        if ($keyword === '') {
            return $this->respond(['status' => 'success', 'data' => []]);
        }

        $model = new InvitationModel();
        $query = $model->groupStart()
            ->like('ic_passport', $keyword)
            ->orLike('full_name', $keyword)
            ->groupEnd();

        // Invitation flow — only return web-created invitations (not kiosk walk-ins)
        if ($flow === 'invitation') {
            $query->where('registration_source', 'Invitation')
                ->whereIn('status', ['Submitted', 'Approved']);
        }

        $visitors = $query->orderBy('created_at', 'DESC')->findAll(20);

        // Invitation flow — no valid invitation found
        // Android will show error and redirect to SelectOptionActivity
        if ($flow === 'invitation' && empty($visitors)) {
            return $this->respond([
                'status'  => 'success',
                'data'    => [],
                'message' => 'no_invitation',
            ]);
        }

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
     * Android sends "data" key (not "ciphertext").
     * Compatible with the AES-256-CBC encryption used in the VMS QR workflow.
     * The secret key is read from the setting key "qr_encryption_key" or
     * falls back to the CI4 encryption key.
     *
     * Also handles plain text card QR codes (no encryption) — returns as-is.
     */
    public function decrypt(): \CodeIgniter\HTTP\Response
    {
        $body      = $this->request->getJSON(true) ?? $this->request->getPost();
        // Android sends "data" key — also accept "text", "encrypted", "ciphertext" as fallback
        $encrypted = trim($body['data'] ?? $body['ciphertext'] ?? $body['text'] ?? $body['encrypted'] ?? '');

        if ($encrypted === '') {
            return $this->failValidationErrors('data field is required');
        }

        try {
            $settingModel = new SettingModel();
            $keySetting   = $settingModel->where('setting_key', 'qr_encryption_key')->first();
            $key          = $keySetting ? $keySetting['setting_value'] : null;

            if ($key) {
                // AES-256-CBC with base64 payload
                $decoded = base64_decode($encrypted, true);
                if ($decoded !== false && strlen($decoded) > 16) {
                    $iv     = substr($decoded, 0, 16);
                    $cipher = substr($decoded, 16);
                    $plain  = openssl_decrypt($cipher, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

                    if ($plain !== false && $plain !== '') {
                        // Return "data" key — Android reads response.optString("data", "")
                        log_message('debug', 'KioskApi::decrypt encrypted=' . $encrypted . ' result=' . $plain);
                        return $this->respond(['status' => 'success', 'data' => $plain]);
                    }
                }
            }

            // No encryption key or decryption failed
            // Return original value as-is — handles plain text card QR (e.g. "VC-008")
            log_message('debug', 'KioskApi::decrypt no encryption — returning as-is: ' . $encrypted);
            return $this->respond(['status' => 'success', 'data' => $encrypted]);
        } catch (\Throwable $e) {
            log_message('error', 'KioskApi::decrypt error: ' . $e->getMessage());
            // Even on exception — return original value so card scan still works
            return $this->respond(['status' => 'success', 'data' => $encrypted]);
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
        // Detect foreigner
        // Malaysian IC = exactly 12 digits numeric (e.g. 550101010101)
        // Foreigner passport = anything else (letters, shorter, longer)
        $icPassport  = $inv['ic_passport'] ?? '';
        $isForeigner = in_array(
            strtolower($inv['resident'] ?? ''),
            ['non-malaysian', 'foreigner', 'non malaysian']
        ) || (
            // If resident not set — auto detect by IC format
            // Malaysian IC = exactly 12 digits only
            $icPassport !== '' && !preg_match('/^\d{12}$/', $icPassport)
        );
        $icNo       = $isForeigner ? '' : $icPassport;
        $passportNo = $isForeigner ? $icPassport : '';

        return [
            // Core fields Android app model expects
            'id'          => (int) $inv['id'],
            'username'    => $inv['ic_passport']        ?? '', // used as QR lookup key
            'name'        => $inv['full_name']           ?? '', // Android uses 'name'
            'fullName'    => $inv['full_name']           ?? '', // alias
            'mobileNo'    => $inv['contact']             ?? '', // Android uses 'mobileNo'
            'contactNo'   => $inv['contact']             ?? '', // alias — CardDetailsActivity reads 'contactNo'
            'contact'     => $inv['contact']             ?? '', // alias
            'phoneNo'     => $inv['contact']             ?? '', // alias
            'icNo'        => $icNo,                             // Malaysian IC
            'passportNo'  => $passportNo,                       // Foreigner passport
            'photo'       => $inv['profile_photo_path']  ?? '', // Android uses 'photo'
            'visitorType' => $inv['visitor_type_id']            // Android uses 'visitorType'
                ? $this->resolveVisitorType((int) $inv['visitor_type_id'])
                : '',
            'email'       => $inv['visitor_email']       ?? '',
            'status'      => $inv['status']              ?? '',
            'message'     => '',                                // placeholder for error msg

            // Extra fields for CardDetailsActivity auto-fill
            'companyName'  => $inv['company']               ?? '',
            'company'      => $inv['company']               ?? '',
            'vehicleNo'    => $inv['vehicle_registration']  ?? '',
            'regNum'       => $inv['vehicle_registration']  ?? '', // alias used by CardDetails
            'address'      => $inv['address']               ?? '',
            'postcode'     => $inv['postcode']              ?? '',
            'city'         => $inv['city']                  ?? '',
            'state'        => $inv['state']                 ?? '',
            'country'      => $inv['country']               ?? '', // needed for foreigner country box

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
