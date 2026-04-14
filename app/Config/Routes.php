<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Authentication Routes
$routes->get('login', 'Auth::login');
$routes->post('auth/attemptLogin', 'Auth::attemptLogin');
$routes->get('auth/logout', 'Auth::logout');

// RFID API routes (no auth filter - for RFID reader webhook)
$routes->group('api/rfid', function($routes) {
    $routes->get('scan', 'RFID::scan');                  // Single reader endpoint
    $routes->get('scan-lane', 'RFID::scanLane');         // Multi-reader endpoint with lane info
    $routes->get('status', 'RFID::status');              // Reader status
    $routes->get('test-connection', 'RFID::testConnection'); // Test connection
});

// Protected Routes
$routes->get('/', 'Dashboard::index');
$routes->get('dashboard', 'Dashboard::index');
$routes->post('dashboard/acknowledgeAlert', 'Dashboard::acknowledgeAlert');
$routes->get('dashboard/trafficData', 'Dashboard::trafficData');
$routes->get('invitations', 'InvitationList::index');
$routes->get('invitations/create', 'InvitationList::create');
$routes->post('invitations/store', 'InvitationList::store');
$routes->post('invitations/resend/(:num)', 'InvitationList::resend/$1');
$routes->get('requests', 'RequestList::index');
$routes->post('requests/approve', 'RequestList::approve');
$routes->post('requests/reject', 'RequestList::reject');
$routes->post('requests/pastVisits', 'RequestList::pastVisits');
$routes->get('visitors', 'VisitorList::index');
$routes->post('visitors/update', 'VisitorList::updateVisitor');
$routes->post('visitors/bindCard', 'VisitorList::bindCard');
$routes->post('visitors/unbindCard', 'VisitorList::unbindCard');
$routes->get('visitor-pass-request', 'VisitorPassRequest::index');
$routes->post('visitor-pass-request/store', 'VisitorPassRequest::store');
$routes->get('logbook', 'VisitorLogbook::index');
$routes->get('workflow', 'VisitorWorkflow::index');
$routes->get('workflow/create', 'VisitorWorkflow::create');

// Visitor Registration Routes (Public - no menu)
$routes->get('visitor-registration', 'VisitorRegistration::index');
$routes->post('visitor-registration/submit', 'VisitorRegistration::submit');
$routes->post('visitor-registration/processMyKad', 'VisitorRegistration::processMyKad');
$routes->post('visitor-registration/updateEmail', 'VisitorRegistration::updateEmail');

// File serving route (for uploaded files)
$routes->get('uploads/(:segment)/(:segment)', 'FileServer::serve/$1/$2');

// Security Briefing Routes (Public - accessed via email link)
$routes->get('security/briefing', 'SecurityBriefing::index');
$routes->post('security/validateCompletion', 'SecurityBriefing::validateCompletion');
$routes->get('security/facial-verification', 'SecurityBriefing::facialVerification');
$routes->post('security/facialComplete', 'SecurityBriefing::facialComplete');
$routes->get('security/completed', 'SecurityBriefing::completed');
$routes->get('security/checkin', 'SecurityBriefing::checkin');
$routes->post('security/confirmCheckin', 'SecurityBriefing::confirmCheckin');
$routes->get('config', 'Config::index');
$routes->get('config/getLogs', 'Config::getLogs');
$routes->get('config/exportLogs', 'Config::exportLogs');

// App Config Routes
$routes->get('config/getAppConfigs', 'Config::getAppConfigs');
$routes->post('config/updateAppConfig/(:num)', 'Config::updateAppConfig/$1');

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

// Additional Location Routes
$routes->get('config/getAllLocations', 'Config::getAllLocations');

$routes->get('settings', 'Settings::index');
$routes->post('settings/updateProfile', 'Settings::updateProfile');
$routes->post('settings/updatePassword', 'Settings::updatePassword');
$routes->post('settings/updatePhoto', 'Settings::updatePhoto');
$routes->post('settings/removePhoto', 'Settings::removePhoto');

// Report Routes
$routes->get('report/access', 'AccessReport::index');
$routes->post('report/access/generate', 'AccessReport::generate');
$routes->post('report/access/movementHistory', 'AccessReport::movementHistory');
$routes->get('report/visitor', 'VisitorReport::index');
$routes->post('report/visitor/generate', 'VisitorReport::generate');
$routes->get('report/chronology', 'VisitorChronology::index');
$routes->post('report/chronology/generate', 'VisitorChronology::generate');
$routes->get('report/bydoor', 'VisitorInfoByDoor::index');
$routes->post('report/bydoor/generate', 'VisitorInfoByDoor::generate');

// Blacklist Routes
$routes->get('blacklist/blacklistrequest', 'BlacklistRequest::requestList');
$routes->get('blacklist/entry', 'BlacklistEntry::index');
$routes->get('blacklist/closedlist', 'BlacklistClosedList::index');