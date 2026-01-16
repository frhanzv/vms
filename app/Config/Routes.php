<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Authentication Routes
$routes->get('login', 'Auth::login');
$routes->post('auth/attemptLogin', 'Auth::attemptLogin');
$routes->get('auth/logout', 'Auth::logout');

// Protected Routes
$routes->get('/', 'Dashboard::index');
$routes->get('dashboard', 'Dashboard::index');
$routes->get('invitations', 'InvitationList::index');
$routes->get('invitations/create', 'InvitationList::create');
$routes->post('invitations/store', 'InvitationList::store');
$routes->get('requests', 'RequestList::index');
$routes->get('visitors', 'VisitorList::index');
$routes->get('logbook', 'VisitorLogbook::index');

// Security Briefing Routes (Public - accessed via email link)
$routes->get('security/briefing', 'SecurityBriefing::index');
$routes->post('security/validateCompletion', 'SecurityBriefing::validateCompletion');
$routes->get('security/facial-verification', 'SecurityBriefing::facialVerification');
$routes->get('security/checkin', 'SecurityBriefing::checkin');
$routes->post('security/confirmCheckin', 'SecurityBriefing::confirmCheckin');