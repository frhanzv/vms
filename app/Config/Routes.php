<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Role filter string aliases for readability
$superadmins      = 'role:superadmin,clientsuperadmin';
$plusAdmin        = 'role:superadmin,clientsuperadmin,admin';
$plusOfficer      = 'role:superadmin,clientsuperadmin,officer';
$plusAdminOfficer = 'role:superadmin,clientsuperadmin,admin,officer';
$plusHost         = 'role:superadmin,clientsuperadmin,officer,host';
$plusAdminHost    = 'role:superadmin,clientsuperadmin,admin,host';

// ===========================
// Public Routes (no auth)
// ===========================

$routes->get('login', 'Auth::login');
$routes->post('auth/attemptLogin', 'Auth::attemptLogin');
$routes->get('auth/logout', 'Auth::logout');

$routes->group('api/rfid', function($routes) {
    $routes->get('scan', 'RFID::scan');
    $routes->get('scan-lane', 'RFID::scanLane');
    $routes->get('status', 'RFID::status');
    $routes->get('test-connection', 'RFID::testConnection');
});

$routes->get('visitor-registration', 'VisitorRegistration::index');
$routes->post('visitor-registration/submit', 'VisitorRegistration::submit');
$routes->post('visitor-registration/processMyKad', 'VisitorRegistration::processMyKad');
$routes->post('visitor-registration/updateEmail', 'VisitorRegistration::updateEmail');
$routes->post('visitor-registration/inviteAdditionalGuests', 'VisitorRegistration::inviteAdditionalGuests');

$routes->get('uploads/(:segment)/(:segment)', 'FileServer::serve/$1/$2');

$routes->get('security/briefing', 'SecurityBriefing::index');
$routes->post('security/validateCompletion', 'SecurityBriefing::validateCompletion');
$routes->get('security/facial-verification', 'SecurityBriefing::facialVerification');
$routes->post('security/facial-complete', 'SecurityBriefing::facialComplete');
$routes->get('security/completed', 'SecurityBriefing::completed');
$routes->get('security/checkin', 'SecurityBriefing::checkin');
$routes->post('security/confirmCheckin', 'SecurityBriefing::confirmCheckin');

$routes->get('config/generateVisitorQr', 'Config::generateVisitorQr');

// ===========================
// Protected Routes — All Roles
// ===========================

$routes->get('/', 'Dashboard::index');
$routes->get('dashboard', 'Dashboard::index');
$routes->post('dashboard/acknowledgeAlert', 'Dashboard::acknowledgeAlert');
$routes->get('dashboard/trafficData', 'Dashboard::trafficData');
$routes->get('dashboard/accessDeniedData', 'Dashboard::accessDeniedData');
$routes->get('dashboard/overstayData', 'Dashboard::overstayData');
$routes->get('dashboard/alertDetailData/(:num)', 'Dashboard::alertDetailData/$1');
$routes->get('dashboard/onSiteData', 'Dashboard::onSiteData');
$routes->get('dashboard/expectedTodayData', 'Dashboard::expectedTodayData');
$routes->get('dashboard/checkedOutData', 'Dashboard::checkedOutData');
$routes->get('dashboard/activeAlertsData', 'Dashboard::activeAlertsData');
$routes->get('dashboard/widgetSnapshot', 'Dashboard::widgetSnapshot');
$routes->get('dashboard/recentActivityData', 'Dashboard::recentActivityData');
$routes->post('dashboard/quickCheckIn', 'Dashboard::quickCheckIn');

$routes->get('settings', 'Settings::index');
$routes->post('settings/updateProfile', 'Settings::updateProfile');
$routes->post('settings/updatePassword', 'Settings::updatePassword');
$routes->post('settings/updatePhoto', 'Settings::updatePhoto');
$routes->post('settings/removePhoto', 'Settings::removePhoto');

// ===========================
// Invitations + Visitors
// superadmin, clientsuperadmin, officer, host
// ===========================

$routes->group('', ['filter' => $plusHost], function($routes) {
    $routes->get('invitations', 'InvitationList::index');
    $routes->get('invitations/export', 'InvitationList::export');
    $routes->get('invitations/create', 'InvitationList::create');
    $routes->post('invitations/store', 'InvitationList::store');
    $routes->post('invitations/resend/(:num)', 'InvitationList::resend/$1');
    $routes->get('invitations/history-rows', 'InvitationList::historyRows');
    $routes->get('invitations/history-for-form/(:num)', 'InvitationList::historyForForm/$1');
    $routes->get('visitors', 'VisitorList::index');
    $routes->get('visitors/export', 'VisitorList::export');
    $routes->post('visitors/update', 'VisitorList::updateVisitor');
    $routes->post('visitors/bindCard', 'VisitorList::bindCard');
    $routes->post('visitors/unbindCard', 'VisitorList::unbindCard');
});

// ===========================
// Request List — View
// superadmin, clientsuperadmin, admin, host
// ===========================

$routes->group('', ['filter' => $plusAdminHost], function($routes) {
    $routes->get('requests', 'RequestList::index');
    $routes->post('requests/pastVisits', 'RequestList::pastVisits');
});

// ===========================
// Request List — Approve/Reject Actions
// superadmin, clientsuperadmin, admin (not host)
// ===========================

$routes->group('', ['filter' => $plusAdmin], function($routes) {
    $routes->post('requests/approve', 'RequestList::approve');
    $routes->post('requests/batchApprove', 'RequestList::batchApprove');
    $routes->post('requests/reject', 'RequestList::reject');
});

// ===========================
// Staff Pass
// superadmin, clientsuperadmin, admin, officer
// ===========================

$routes->group('', ['filter' => $plusAdminOfficer], function($routes) {
    $routes->get('staffs', 'StaffList::index');
    $routes->get('staffs/staffpassrequest', 'StaffPassRequest::index');
    $routes->post('staffs/staffpassrequest/store', 'StaffPassRequest::store');
    $routes->post('staff-pass/import', 'StaffController::import');
    $routes->get('staffpassrequest/view/(:any)', 'StaffPassRequest::view/$1');
});

// ===========================
// Visitor Workflow + Visitor Pass Request
// superadmin, clientsuperadmin, officer
// ===========================

$routes->group('', ['filter' => $plusOfficer], function($routes) {
    $routes->get('visitor-pass-request', 'VisitorPassRequest::index');
    $routes->post('visitor-pass-request/store', 'VisitorPassRequest::store');
    $routes->get('workflow', 'VisitorWorkflow::index');
    $routes->get('workflow/create', 'VisitorWorkflow::create');
    $routes->post('workflow/save', 'VisitorWorkflow::save');
});

// ===========================
// Blacklist — View
// superadmin, clientsuperadmin, officer
// ===========================

$routes->group('blacklist', ['filter' => $plusOfficer], function($routes) {
    $routes->get('blacklistrequest', 'BlacklistRequest::index');
    $routes->get('entry', 'BlacklistEntry::index');
    $routes->get('entry/search', 'BlacklistEntry::search');
    $routes->get('closedlist', 'BlacklistClosedList::index');
    $routes->get('closedlist/view/(:num)', 'BlacklistClosedList::view/$1');
});

// ===========================
// Blacklist — Write
// superadmin, clientsuperadmin only
// ===========================

$routes->group('blacklist', ['filter' => $superadmins], function($routes) {
    $routes->get('entry/proceed', 'BlacklistEntry::proceed');
    $routes->post('entry/store', 'BlacklistEntry::store');
    $routes->post('closedlist/release/(:num)', 'BlacklistClosedList::release/$1');
});

// ===========================
// Reports
// superadmin, clientsuperadmin, admin, officer
// ===========================

$routes->group('', ['filter' => $plusAdminOfficer], function($routes) {
    $routes->get('report/access', 'AccessReport::index');
    $routes->post('report/access/generate', 'AccessReport::generate');
    $routes->post('report/access/movementHistory', 'AccessReport::movementHistory');
    $routes->get('report/visitor', 'VisitorReport::index');
    $routes->post('report/visitor/generate', 'VisitorReport::generate');
    $routes->get('report/chronology', 'VisitorChronology::index');
    $routes->post('report/chronology/generate', 'VisitorChronology::generate');
    $routes->get('report/bydoor', 'VisitorInfoByDoor::index');
    $routes->post('report/bydoor/generate', 'VisitorInfoByDoor::generate');
    $routes->get('report/visitor/details/(:num)', 'VisitorChronology::details/$1');
    $routes->post('report/visitor/movement', 'VisitorChronology::movementTimeline');
});

// ===========================
// Config — User Management
// superadmin, clientsuperadmin, admin
// (controller enforces admin can only manage host-role users)
// ===========================

$routes->group('config', ['filter' => $plusAdmin], function($routes) {
    $routes->get('getUsers', 'Config::getUsers');
    $routes->get('getUser/(:num)', 'Config::getUser/$1');
    $routes->post('createUser', 'Config::createUser');
    $routes->post('updateUser/(:num)', 'Config::updateUser/$1');
    $routes->delete('deleteUser/(:num)', 'Config::deleteUser/$1');
    $routes->get('getAllRoles', 'Config::getAllRoles');
});

// ===========================
// Config — Client Level
// superadmin, clientsuperadmin
// ===========================

$routes->group('config', ['filter' => $superadmins], function($routes) {
    $routes->get('/', 'Config::index');
    $routes->get('getLogs', 'Config::getLogs');
    $routes->get('exportLogs', 'Config::exportLogs');
    $routes->get('getAlertPriorities', 'Config::getAlertPriorities');
    $routes->post('updateAlertPriority/(:num)', 'Config::updateAlertPriority/$1');

    // Roles
    $routes->get('getRoles', 'Config::getRoles');
    $routes->get('getRole/(:num)', 'Config::getRole/$1');
    $routes->post('createRole', 'Config::createRole');
    $routes->post('updateRole/(:num)', 'Config::updateRole/$1');
    $routes->delete('deleteRole/(:num)', 'Config::deleteRole/$1');

    // Companies
    $routes->get('getCompanies', 'Config::getCompanies');
    $routes->get('getCompany/(:num)', 'Config::getCompany/$1');
    $routes->post('createCompany', 'Config::createCompany');
    $routes->post('updateCompany/(:num)', 'Config::updateCompany/$1');
    $routes->delete('deleteCompany/(:num)', 'Config::deleteCompany/$1');
    $routes->get('getAllCompanies', 'Config::getAllCompanies');

    // Sub Companies
    $routes->get('getSubCompanies', 'Config::getSubCompanies');
    $routes->get('getSubCompany/(:num)', 'Config::getSubCompany/$1');
    $routes->post('createSubCompany', 'Config::createSubCompany');
    $routes->post('updateSubCompany/(:num)', 'Config::updateSubCompany/$1');
    $routes->delete('deleteSubCompany/(:num)', 'Config::deleteSubCompany/$1');

    // Countries
    $routes->get('getCountries', 'Config::getCountries');
    $routes->get('getCountry/(:num)', 'Config::getCountry/$1');
    $routes->post('createCountry', 'Config::createCountry');
    $routes->post('updateCountry/(:num)', 'Config::updateCountry/$1');
    $routes->delete('deleteCountry/(:num)', 'Config::deleteCountry/$1');
    $routes->get('getAllCountries', 'Config::getAllCountries');

    // States
    $routes->get('getStates', 'Config::getStates');
    $routes->get('getState/(:num)', 'Config::getState/$1');
    $routes->post('createState', 'Config::createState');
    $routes->put('updateState/(:num)', 'Config::updateState/$1');
    $routes->delete('deleteState/(:num)', 'Config::deleteState/$1');
    $routes->get('getAllStates', 'Config::getAllStates');

    // Cities
    $routes->get('getCities', 'Config::getCities');
    $routes->get('getCity/(:num)', 'Config::getCity/$1');
    $routes->post('createCity', 'Config::createCity');
    $routes->put('updateCity/(:num)', 'Config::updateCity/$1');
    $routes->delete('deleteCity/(:num)', 'Config::deleteCity/$1');

    // Departments
    $routes->get('getDepartments', 'Config::getDepartments');
    $routes->get('getDepartment/(:num)', 'Config::getDepartment/$1');
    $routes->post('createDepartment', 'Config::createDepartment');
    $routes->put('updateDepartment/(:num)', 'Config::updateDepartment/$1');
    $routes->delete('deleteDepartment/(:num)', 'Config::deleteDepartment/$1');

    // Designations
    $routes->get('getDesignations', 'Config::getDesignations');
    $routes->get('getDesignation/(:num)', 'Config::getDesignation/$1');
    $routes->post('createDesignation', 'Config::createDesignation');
    $routes->put('updateDesignation/(:num)', 'Config::updateDesignation/$1');
    $routes->delete('deleteDesignation/(:num)', 'Config::deleteDesignation/$1');

    // Locations
    $routes->get('getLocations', 'Config::getLocations');
    $routes->get('getLocation/(:num)', 'Config::getLocation/$1');
    $routes->post('createLocation', 'Config::createLocation');
    $routes->put('updateLocation/(:num)', 'Config::updateLocation/$1');
    $routes->delete('deleteLocation/(:num)', 'Config::deleteLocation/$1');

    // Lanes
    $routes->get('getLanes', 'Config::getLanes');
    $routes->get('getLane/(:num)', 'Config::getLane/$1');
    $routes->post('createLane', 'Config::createLane');
    $routes->put('updateLane/(:num)', 'Config::updateLane/$1');
    $routes->delete('deleteLane/(:num)', 'Config::deleteLane/$1');

    // Reject Reasons
    $routes->get('getRejectReasons', 'Config::getRejectReasons');
    $routes->get('getRejectReason/(:num)', 'Config::getRejectReason/$1');
    $routes->post('createRejectReason', 'Config::createRejectReason');
    $routes->put('updateRejectReason/(:num)', 'Config::updateRejectReason/$1');
    $routes->delete('deleteRejectReason/(:num)', 'Config::deleteRejectReason/$1');

    // Visitor Cards
    $routes->get('getVisitorCards', 'Config::getVisitorCards');
    $routes->get('getVisitorCard/(:num)', 'Config::getVisitorCard/$1');
    $routes->post('createVisitorCard', 'Config::createVisitorCard');
    $routes->put('updateVisitorCard/(:num)', 'Config::updateVisitorCard/$1');
    $routes->delete('deleteVisitorCard/(:num)', 'Config::deleteVisitorCard/$1');

    // Videos
    $routes->get('getVideos', 'Config::getVideos');
    $routes->get('getVideo/(:num)', 'Config::getVideo/$1');
    $routes->post('createVideo', 'Config::createVideo');
    $routes->post('updateVideo/(:num)', 'Config::updateVideo/$1');
    $routes->delete('deleteVideo/(:num)', 'Config::deleteVideo/$1');

    // Visit Reasons
    $routes->get('getVisitReasons', 'Config::getVisitReasons');
    $routes->post('createVisitReason', 'Config::createVisitReason');
    $routes->post('updateVisitReason/(:num)', 'Config::updateVisitReason/$1');
    $routes->delete('deleteVisitReason/(:num)', 'Config::deleteVisitReason/$1');

    // Location Visited
    $routes->get('getLocationVisited', 'Config::getLocationVisited');
    $routes->post('createLocationVisited', 'Config::createLocationVisited');
    $routes->post('updateLocationVisited/(:num)', 'Config::updateLocationVisited/$1');
    $routes->delete('deleteLocationVisited/(:num)', 'Config::deleteLocationVisited/$1');

    // Visitor Types
    $routes->get('getVisitorTypes', 'Config::getVisitorTypes');
    $routes->get('getVisitorType/(:num)', 'Config::getVisitorType/$1');
    $routes->post('createVisitorType', 'Config::createVisitorType');
    $routes->post('updateVisitorType/(:num)', 'Config::updateVisitorType/$1');
    $routes->delete('deleteVisitorType/(:num)', 'Config::deleteVisitorType/$1');

    // Login Page Settings
    $routes->get('getLoginPageSettings', 'Config::getLoginPageSettings');
    $routes->post('saveLoginPageSettings', 'Config::saveLoginPageSettings');

    // Email Templates
    $routes->get('getEmailTemplateFormSettings', 'Config::getEmailTemplateFormSettings');
    $routes->post('saveEmailTemplateFormSettings', 'Config::saveEmailTemplateFormSettings');
    $routes->get('getInvitationEmailTemplateSettings', 'Config::getInvitationEmailTemplateSettings');
    $routes->post('saveInvitationEmailTemplateSettings', 'Config::saveInvitationEmailTemplateSettings');
    $routes->get('getEmailTemplates', 'Config::getEmailTemplates');
    $routes->get('getEmailTemplate/(:num)', 'Config::getEmailTemplate/$1');
    $routes->post('createEmailTemplate', 'Config::createEmailTemplate');
    $routes->post('updateEmailTemplate/(:num)', 'Config::updateEmailTemplate/$1');
    $routes->post('createEmailTemplateFormField', 'Config::createEmailTemplateFormField');
    $routes->post('updateEmailTemplateFormField/(:num)', 'Config::updateEmailTemplateFormField/$1');
    $routes->post('deleteEmailTemplateFormField/(:num)', 'Config::deleteEmailTemplateFormField/$1');
    $routes->post('reorderEmailTemplateFormFields', 'Config::reorderEmailTemplateFormFields');

    // Pathways
    $routes->get('getPathways', 'Config::getPathways');
    $routes->get('getPathway/(:num)', 'Config::getPathway/$1');
    $routes->post('createPathway', 'Config::createPathway');
    $routes->post('updatePathway/(:num)', 'Config::updatePathway/$1');
    $routes->delete('deletePathway/(:num)', 'Config::deletePathway/$1');
    $routes->get('getAllLanes', 'Config::getAllLanes');

    // Workflows
    $routes->get('getWorkflows', 'Config::getWorkflows');
    $routes->post('updateWorkflows', 'Config::updateWorkflows');
    $routes->get('getAllLocations', 'Config::getAllLocations');

    // Registration Types
    $routes->get('getRegTypes', 'Config::getRegTypes');
    $routes->get('getRegType/(:num)', 'Config::getRegType/$1');
    $routes->post('createRegType', 'Config::createRegType');
    $routes->post('updateRegType/(:num)', 'Config::updateRegType/$1');
    $routes->post('deleteRegType/(:num)', 'Config::deleteRegType/$1');

    // Blacklist Config
    $routes->post('saveBlacklistReason', 'BlacklistReasonCreate::save');
    $routes->get('getBlacklistReason/(:num)', 'Config::getBlacklistReason/$1');

    // Business Type
    $routes->post('updateBusinessType', 'Config::updateBusinessType');
});

// ===========================
// Config — System Level
// superadmin only
// ===========================

$routes->group('config', ['filter' => 'role:superadmin'], function($routes) {
    // Client Feature Flags
    $routes->get('getClientFeatures/(:num)', 'Config::getClientFeatures/$1');
    $routes->post('saveClientFeatures/(:num)', 'Config::saveClientFeatures/$1');

    // Dynamic VMS Form
    $routes->get('getClientFormFields/(:num)', 'Config::getClientFormFields/$1');
    $routes->post('saveClientFormFields/(:num)', 'Config::saveClientFormFields/$1');

    // App Config
    $routes->get('getAppConfigs', 'AppConfig::getAll');
    $routes->get('getAppConfig/(:num)', 'AppConfig::get/$1');
    $routes->post('updateAppConfig/(:num)', 'AppConfig::update/$1');

    // Device Assignments
    $routes->get('getDeviceAssignments', 'Config::getDeviceAssignments');
    $routes->get('getDeviceAssignment/(:num)', 'Config::getDeviceAssignment/$1');
    $routes->post('checkDeviceStatus/(:num)', 'Config::checkDeviceStatus/$1');
    $routes->post('createDeviceAssignment', 'Config::createDeviceAssignment');
    $routes->post('updateDeviceAssignment/(:num)', 'Config::updateDeviceAssignment/$1');
    $routes->post('deleteDeviceAssignment/(:num)', 'Config::deleteDeviceAssignment/$1');

    // IP Range Settings
    $routes->get('getIpRangeSettings', 'Config::getIpRangeSettings');
    $routes->post('saveIpRangeSettings', 'Config::saveIpRangeSettings');

    // API Key Management
    $routes->get('getApiKeys', 'Api\ApiManagement::getApiKeys');
    $routes->get('getApiKey/(:num)', 'Api\ApiManagement::getApiKey/$1');
    $routes->post('createApiKey', 'Api\ApiManagement::createApiKey');
    $routes->post('updateApiKey/(:num)', 'Api\ApiManagement::updateApiKey/$1');
    $routes->delete('deleteApiKey/(:num)', 'Api\ApiManagement::deleteApiKey/$1');
    $routes->post('syncApiKeys', 'Api\ApiManagement::syncApiKeys');
    $routes->post('callExternalApi', 'Api\ApiManagement::callExternalApi');
    $routes->post('saveLaravelBaseUrl', 'Api\ApiManagement::saveLaravelBaseUrl');
    $routes->get('getLaravelBaseUrl', 'Api\ApiManagement::getLaravelBaseUrl');
});
