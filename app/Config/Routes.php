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

$routes->group('api/qr', function($routes) {
    $routes->get('scan', 'QRCode::scan');
    $routes->get('scan-lane', 'QRCode::scanLane');
    $routes->get('status', 'QRCode::status');
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
$routes->get('config', 'Config::index');
$routes->get('config/getLogs', 'Config::getLogs');
$routes->get('config/exportLogs', 'Config::exportLogs');
// Client Feature Flag Routes
$routes->get('config/getClientFeatures/(:num)', 'Config::getClientFeatures/$1');
$routes->post('config/saveClientFeatures/(:num)', 'Config::saveClientFeatures/$1');

// Notification Settings Routes
$routes->get('config/getNotificationSettings/(:num)', 'Config::getNotificationSettings/$1');
$routes->post('config/saveNotificationSettings/(:num)', 'Config::saveNotificationSettings/$1');
$routes->get('config/getMessagingCredentials/(:num)/(:alpha)', 'Config::getMessagingCredentials/$1/$2');
$routes->post('config/saveMessagingCredentials/(:num)/(:alpha)', 'Config::saveMessagingCredentials/$1/$2');
$routes->get('config/getWhatsappTemplates/(:num)', 'Config::getWhatsappTemplates/$1');
$routes->post('config/saveWhatsappTemplates/(:num)', 'Config::saveWhatsappTemplates/$1');

// Dynamic Form Field Routes
$routes->get('config/getClientFormFields/(:num)', 'Config::getClientFormFields/$1');
$routes->post('config/saveClientFormFields/(:num)', 'Config::saveClientFormFields/$1');

// Alert Priority Management Routes
$routes->get('config/getAlertPriorities', 'Config::getAlertPriorities');
$routes->post('config/updateAlertPriority/(:num)', 'Config::updateAlertPriority/$1');

// App Config Routes
$routes->get('config/getAppConfigs',          'AppConfig::getAll');
$routes->get('config/getAppConfig/(:num)',     'AppConfig::get/$1');
$routes->post('config/updateAppConfig/(:num)', 'AppConfig::update/$1');

// Role Management Routes
$routes->get('config/getRoles', 'Config::getRoles');
$routes->get('config/getRole/(:num)', 'Config::getRole/$1');
$routes->post('config/createRole', 'Config::createRole');
$routes->post('config/updateRole/(:num)', 'Config::updateRole/$1');
$routes->delete('config/deleteRole/(:num)', 'Config::deleteRole/$1');

// User Management Routes
$routes->get('config/getUsers', 'Config::getUsers');
$routes->get('config/getUser/(:num)', 'Config::getUser/$1');
$routes->post('config/createUser', 'Config::createUser');
$routes->post('config/updateUser/(:num)', 'Config::updateUser/$1');
$routes->delete('config/deleteUser/(:num)', 'Config::deleteUser/$1');
$routes->get('config/getAllRoles', 'Config::getAllRoles');

// Company Management Routes
$routes->get('config/getCompanies', 'Config::getCompanies');
$routes->get('config/getCompany/(:num)', 'Config::getCompany/$1');
$routes->post('config/createCompany', 'Config::createCompany');
$routes->post('config/updateCompany/(:num)', 'Config::updateCompany/$1');
$routes->delete('config/deleteCompany/(:num)', 'Config::deleteCompany/$1');
$routes->get('config/getAllCompanies', 'Config::getAllCompanies');

// Sub Company Management Routes
$routes->get('config/getSubCompanies', 'Config::getSubCompanies');
$routes->get('config/getSubCompany/(:num)', 'Config::getSubCompany/$1');
$routes->post('config/createSubCompany', 'Config::createSubCompany');
$routes->post('config/updateSubCompany/(:num)', 'Config::updateSubCompany/$1');
$routes->delete('config/deleteSubCompany/(:num)', 'Config::deleteSubCompany/$1');

// Country Management Routes
$routes->get('config/getCountries', 'Config::getCountries');
$routes->get('config/getCountry/(:num)', 'Config::getCountry/$1');
$routes->post('config/createCountry', 'Config::createCountry');
$routes->post('config/updateCountry/(:num)', 'Config::updateCountry/$1');
$routes->delete('config/deleteCountry/(:num)', 'Config::deleteCountry/$1');
$routes->get('config/getAllCountries', 'Config::getAllCountries');

// State Management Routes
$routes->get('config/getStates', 'Config::getStates');
$routes->get('config/getState/(:num)', 'Config::getState/$1');
$routes->post('config/createState', 'Config::createState');
$routes->put('config/updateState/(:num)', 'Config::updateState/$1');
$routes->delete('config/deleteState/(:num)', 'Config::deleteState/$1');
$routes->get('config/getAllStates', 'Config::getAllStates');

// City Management Routes
$routes->get('config/getCities', 'Config::getCities');
$routes->get('config/getCity/(:num)', 'Config::getCity/$1');
$routes->post('config/createCity', 'Config::createCity');
$routes->put('config/updateCity/(:num)', 'Config::updateCity/$1');
$routes->delete('config/deleteCity/(:num)', 'Config::deleteCity/$1');

// Department Management Routes
$routes->get('config/getDepartments', 'Config::getDepartments');
$routes->get('config/getDepartment/(:num)', 'Config::getDepartment/$1');
$routes->post('config/createDepartment', 'Config::createDepartment');
$routes->put('config/updateDepartment/(:num)', 'Config::updateDepartment/$1');
$routes->delete('config/deleteDepartment/(:num)', 'Config::deleteDepartment/$1');

// Designation Management Routes
$routes->get('config/getDesignations', 'Config::getDesignations');
$routes->get('config/getDesignation/(:num)', 'Config::getDesignation/$1');
$routes->post('config/createDesignation', 'Config::createDesignation');
$routes->put('config/updateDesignation/(:num)', 'Config::updateDesignation/$1');
$routes->delete('config/deleteDesignation/(:num)', 'Config::deleteDesignation/$1');

// Location Management Routes
$routes->get('config/getLocations', 'Config::getLocations');
$routes->get('config/getLocation/(:num)', 'Config::getLocation/$1');
$routes->post('config/createLocation', 'Config::createLocation');
$routes->put('config/updateLocation/(:num)', 'Config::updateLocation/$1');
$routes->delete('config/deleteLocation/(:num)', 'Config::deleteLocation/$1');

// Lane Management Routes
$routes->get('config/getLanes', 'Config::getLanes');
$routes->get('config/getLane/(:num)', 'Config::getLane/$1');
$routes->post('config/createLane', 'Config::createLane');
$routes->put('config/updateLane/(:num)', 'Config::updateLane/$1');
$routes->delete('config/deleteLane/(:num)', 'Config::deleteLane/$1');

// Reject Reason Management Routes
$routes->get('config/getRejectReasons', 'Config::getRejectReasons');
$routes->get('config/getRejectReason/(:num)', 'Config::getRejectReason/$1');
$routes->post('config/createRejectReason', 'Config::createRejectReason');
$routes->put('config/updateRejectReason/(:num)', 'Config::updateRejectReason/$1');
$routes->delete('config/deleteRejectReason/(:num)', 'Config::deleteRejectReason/$1');

// Staff Pass List Routes
$routes->get('staffs', 'StaffList::index');
$routes->get('staffs/staffpassrequest', 'StaffPassRequest::index');
$routes->post('staffs/staffpassrequest/store', 'StaffPassRequest::store');
//$routes->get('staff-pass-request', 'StaffList::downloadTemplate');
$routes->post('staff-pass/import', 'StaffController::import');
$routes->get('staffpassrequest/view/(:any)', 'StaffPassRequest::view/$1');



// Visitor Card Management Routes
$routes->get('config/getVisitorCards', 'Config::getVisitorCards');
$routes->get('config/getVisitorCard/(:num)', 'Config::getVisitorCard/$1');
$routes->post('config/createVisitorCard', 'Config::createVisitorCard');
$routes->put('config/updateVisitorCard/(:num)', 'Config::updateVisitorCard/$1');
$routes->delete('config/deleteVisitorCard/(:num)', 'Config::deleteVisitorCard/$1');

// Video Management Routes
$routes->get('config/getVideos', 'Config::getVideos');
$routes->get('config/getVideo/(:num)', 'Config::getVideo/$1');
$routes->post('config/createVideo', 'Config::createVideo');
$routes->post('config/updateVideo/(:num)', 'Config::updateVideo/$1');
$routes->delete('config/deleteVideo/(:num)', 'Config::deleteVideo/$1');

// Visit Reason Management Routes
$routes->get('config/getVisitReasons', 'Config::getVisitReasons');
$routes->post('config/createVisitReason', 'Config::createVisitReason');
$routes->post('config/updateVisitReason/(:num)', 'Config::updateVisitReason/$1');
$routes->delete('config/deleteVisitReason/(:num)', 'Config::deleteVisitReason/$1');
$routes->get('config/getLocationVisited', 'Config::getLocationVisited');
$routes->post('config/createLocationVisited', 'Config::createLocationVisited');
$routes->post('config/updateLocationVisited/(:num)', 'Config::updateLocationVisited/$1');
$routes->delete('config/deleteLocationVisited/(:num)', 'Config::deleteLocationVisited/$1');

$routes->get('config/getVisitorTypes', 'Config::getVisitorTypes');
$routes->get('config/getVisitorType/(:num)', 'Config::getVisitorType/$1');
$routes->post('config/createVisitorType', 'Config::createVisitorType');
$routes->post('config/updateVisitorType/(:num)', 'Config::updateVisitorType/$1');
$routes->delete('config/deleteVisitorType/(:num)', 'Config::deleteVisitorType/$1');
// Device Assignments Routes
$routes->get('config/getDeviceAssignments', 'Config::getDeviceAssignments');
$routes->get('config/getDeviceAssignment/(:num)', 'Config::getDeviceAssignment/$1');
$routes->post('config/checkDeviceStatus/(:num)', 'Config::checkDeviceStatus/$1');
$routes->post('config/createDeviceAssignment', 'Config::createDeviceAssignment');
$routes->post('config/updateDeviceAssignment/(:num)', 'Config::updateDeviceAssignment/$1');
$routes->post('config/deleteDeviceAssignment/(:num)', 'Config::deleteDeviceAssignment/$1');

// IP Range Settings
$routes->get('config/getIpRangeSettings', 'Config::getIpRangeSettings');
$routes->post('config/saveIpRangeSettings', 'Config::saveIpRangeSettings');
$routes->get('config/getLoginPageSettings', 'Config::getLoginPageSettings');
$routes->post('config/saveLoginPageSettings', 'Config::saveLoginPageSettings');
$routes->get('config/getEmailTemplateFormSettings', 'Config::getEmailTemplateFormSettings');
$routes->post('config/saveEmailTemplateFormSettings', 'Config::saveEmailTemplateFormSettings');
$routes->get('config/getInvitationEmailTemplateSettings', 'Config::getInvitationEmailTemplateSettings');
$routes->post('config/saveInvitationEmailTemplateSettings', 'Config::saveInvitationEmailTemplateSettings');
$routes->get('config/getEmailTemplates', 'Config::getEmailTemplates');
$routes->get('config/getEmailTemplate/(:num)', 'Config::getEmailTemplate/$1');
$routes->get('config/previewEmailTemplate/(:num)', 'Config::previewEmailTemplate/$1');
$routes->post('config/createEmailTemplate', 'Config::createEmailTemplate');
$routes->post('config/updateEmailTemplate/(:num)', 'Config::updateEmailTemplate/$1');
$routes->post('config/createEmailTemplateFormField', 'Config::createEmailTemplateFormField');
$routes->post('config/updateEmailTemplateFormField/(:num)', 'Config::updateEmailTemplateFormField/$1');
$routes->post('config/deleteEmailTemplateFormField/(:num)', 'Config::deleteEmailTemplateFormField/$1');
$routes->post('config/reorderEmailTemplateFormFields', 'Config::reorderEmailTemplateFormFields');
$routes->get('config/generateVisitorQr', 'Config::generateVisitorQr');


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
    $routes->post('visitors/batchUnbindCards', 'VisitorList::batchUnbindCards');
    $routes->get('visitors/generateQr/(:num)', 'VisitorList::generateQr/$1');
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
    $routes->get('closedlist/export', 'BlacklistClosedList::export');
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
    $routes->get('report/visitor/chronology-print/(:num)', 'VisitorChronology::chronologyPrint/$1');
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
    $routes->get('previewEmailTemplate/(:num)', 'Config::previewEmailTemplate/$1');
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
    
    // Inbound API token generation (Protected by superadmin)
    $routes->post('generateInboundToken', 'Api\InboundApi::generateToken');
});

// ===========================
// Kiosk Mobile API (Public — no auth)
// Mirrors the Java/Spring URL scheme used by the MNR Android kiosk app.
// ===========================

$routes->group('api/admin', function($routes) {
    $routes->get('locationAccess/active',        'Api\KioskApi::getActiveLocations');
    $routes->get('subLocationAccess/active',     'Api\KioskApi::getActiveSubLocations');
    $routes->get('country/active',               'Api\KioskApi::getActiveCountries');
    $routes->get('state/country/(:num)',         'Api\KioskApi::getStatesByCountry/$1');
    $routes->get('city/state/(:num)',            'Api\KioskApi::getCitiesByState/$1');
    $routes->get('vinType/active/all',           'Api\KioskApi::getActiveVehicleTypes');
    $routes->get('licenseClass/active',          'Api\KioskApi::getActiveLicenseClasses');
    $routes->get('vaccineType/active',           'Api\KioskApi::getActiveVaccineTypes');
    $routes->get('moduleConfig/getByProject',   'Api\KioskApi::getModuleConfig');
    $routes->get('campanies/all',                'Api\KioskApi::getAllCompanies');
});

$routes->group('api/vendorpass', function($routes) {
    $routes->get('getVisitReasonList',           'Api\KioskApi::getVisitReasonList');
    $routes->post('checkICExist',                'Api\KioskApi::checkICExist');
    $routes->post('doVisitorPassReqMobile',      'Api\KioskApi::doVisitorPassReqMobile');
    $routes->post('insertVendorPassCard',        'Api\KioskApi::insertVendorPassCard');
    $routes->post('uploadVendorPassPhotoMobile', 'Api\KioskApi::uploadVendorPassPhotoMobile');
});

$routes->group('api/user', function($routes) {
    $routes->get('getStaffPassByStaffNoOrName',   'Api\KioskApi::getStaffPassByStaffNoOrName');
    $routes->get('getVisitorPassByStaffNoOrName', 'Api\KioskApi::getVisitorPassByStaffNoOrName');
});

// laravel_url routes (served here at any port VMS is running on)
$routes->post('decrypt',             'Api\KioskApi::decrypt');
$routes->get('vms/api/visitor-types', 'Api\KioskApi::getVisitorTypes');

// ===========================
// Inbound API (Webhooks)
// Protected by Bearer Token Filter
// ===========================

$routes->group('api/v1', ['filter' => 'inbound_api_auth'], function($routes) {
    $routes->post('receive', 'Api\InboundApi::receive');
});
