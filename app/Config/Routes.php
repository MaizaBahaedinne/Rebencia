<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default routes
$routes->get('/', 'Home::index');

// ==========================================
// ADMIN ROUTES
// ==========================================
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function($routes) {
    
    // Dashboard (route principale admin)
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');
    
    // Authentication
    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::attemptLogin');
    $routes->get('logout', 'Auth::logout');
    
    // Users Management
    $routes->group('users', function($routes) {
        $routes->get('/', 'Users::index');
        $routes->get('create', 'Users::create');
        $routes->post('store', 'Users::store');
        $routes->get('edit/(:num)', 'Users::edit/$1');
        $routes->post('update/(:num)', 'Users::update/$1');
        $routes->delete('delete/(:num)', 'Users::delete/$1');
    });
    
    // Roles & Permissions
    $routes->group('roles', function($routes) {
        $routes->get('/', 'Roles::index');
        $routes->get('create', 'Roles::create');
        $routes->post('store', 'Roles::store');
        $routes->get('edit/(:num)', 'Roles::edit/$1');
        $routes->post('update/(:num)', 'Roles::update/$1');
        $routes->delete('delete/(:num)', 'Roles::delete/$1');
    });
    
    // Agencies
    $routes->group('agencies', function($routes) {
        $routes->get('/', 'Agencies::index');
        $routes->get('create', 'Agencies::create');
        $routes->post('store', 'Agencies::store');
        $routes->get('edit/(:num)', 'Agencies::edit/$1');
        $routes->post('update/(:num)', 'Agencies::update/$1');
        $routes->delete('delete/(:num)', 'Agencies::delete/$1');
    });
    
    // Properties
    $routes->group('properties', function($routes) {
        $routes->get('/', 'Properties::index');
        $routes->get('create', 'Properties::create');
        $routes->post('store', 'Properties::store');
        $routes->get('edit/(:num)', 'Properties::edit/$1');
        $routes->post('update/(:num)', 'Properties::update/$1');
        $routes->delete('delete/(:num)', 'Properties::delete/$1');
        $routes->get('view/(:num)', 'Properties::view/$1');
        $routes->post('deleteImage/(:num)', 'Properties::deleteImage/$1');
        $routes->get('assignments', 'Properties::assignments');
        $routes->post('reassign', 'Properties::reassign');
    });
    
    // Clients & CRM
    $routes->group('clients', function($routes) {
        $routes->get('/', 'Clients::index');
        $routes->get('create', 'Clients::create');
        $routes->post('store', 'Clients::store');
        $routes->get('edit/(:num)', 'Clients::edit/$1');
        $routes->post('update/(:num)', 'Clients::update/$1');
        $routes->delete('delete/(:num)', 'Clients::delete/$1');
        $routes->get('view/(:num)', 'Clients::view/$1');
    });
    
    // Transactions
    $routes->group('transactions', function($routes) {
        $routes->get('/', 'Transactions::index');
        $routes->get('create', 'Transactions::create');
        $routes->post('store', 'Transactions::store');
        $routes->get('edit/(:num)', 'Transactions::edit/$1');
        $routes->post('update/(:num)', 'Transactions::update/$1');
        $routes->delete('delete/(:num)', 'Transactions::delete/$1');
    });
    
    // Commissions
    $routes->group('commissions', function($routes) {
        $routes->get('/', 'Commissions::index');
        $routes->get('approve/(:num)', 'Commissions::approve/$1');
        $routes->get('mark-as-paid/(:num)', 'Commissions::markAsPaid/$1');
        $routes->post('bulk-approve', 'Commissions::bulkApprove');
        $routes->post('bulk-pay', 'Commissions::bulkPay');
        $routes->get('agent-report/(:num)', 'Commissions::agentReport/$1');
    });
    
    // Notifications
    $routes->group('notifications', function($routes) {
        $routes->get('/', 'Notifications::index');
        $routes->post('mark-as-read/(:num)', 'Notifications::markAsRead/$1');
        $routes->post('mark-all-as-read', 'Notifications::markAllAsRead');
        $routes->get('unread-count', 'Notifications::getUnreadCount');
    });
    
    // Reports & Export
    $routes->group('reports', function($routes) {
        $routes->get('/', 'Reports::index');
        $routes->get('export-properties', 'Reports::exportProperties');
        $routes->get('export-clients', 'Reports::exportClients');
        $routes->get('export-transactions', 'Reports::exportTransactions');
        $routes->get('export-commissions', 'Reports::exportCommissions');
    });
    
    // Workflows
    $routes->group('workflows', function($routes) {
        $routes->get('/', 'Workflows::index');
        $routes->get('create', 'Workflows::create');
        $routes->post('store', 'Workflows::store');
        $routes->get('edit/(:num)', 'Workflows::edit/$1');
        $routes->post('update/(:num)', 'Workflows::update/$1');
        $routes->delete('delete/(:num)', 'Workflows::delete/$1');
        $routes->get('pipeline/(:alpha)', 'Workflows::pipeline/$1');
        $routes->post('move-stage', 'Workflows::moveStage');
    });
    
    // Documents
    $routes->group('documents', function($routes) {
        $routes->get('(:num)', 'Documents::index/$1');
        $routes->post('upload/(:num)', 'Documents::upload/$1');
        $routes->get('download/(:num)', 'Documents::download/$1');
        $routes->post('delete/(:num)', 'Documents::delete/$1');
        $routes->get('generate-contract/(:num)', 'Documents::generateContract/$1');
    });
    
    // Settings
    $routes->group('settings', function($routes) {
        $routes->get('/', 'Settings::index');
        $routes->post('update', 'Settings::update');
    });
    
    // Analytics
    $routes->group('analytics', function($routes) {
        $routes->get('/', 'Analytics::index');
        $routes->get('agent/(:num)', 'Analytics::agentReport/$1');
        $routes->get('commission-evolution', 'Analytics::getCommissionEvolution');
    });
    
    // Appointments
    $routes->group('appointments', function($routes) {
        $routes->get('/', 'Appointments::index');
        $routes->get('list', 'Appointments::list');
        $routes->get('create', 'Appointments::create');
        $routes->post('store', 'Appointments::store');
        $routes->get('edit/(:num)', 'Appointments::edit/$1');
        $routes->post('update/(:num)', 'Appointments::update/$1');
        $routes->delete('delete/(:num)', 'Appointments::delete/$1');
        $routes->get('getEvents', 'Appointments::getEvents');
        $routes->post('updateStatus', 'Appointments::updateStatus');
        $routes->get('sendReminders', 'Appointments::sendReminders');
    });
    
    // Tasks
    $routes->group('tasks', function($routes) {
        $routes->get('/', 'Tasks::index');
        $routes->get('create', 'Tasks::create');
        $routes->post('store', 'Tasks::store');
        $routes->post('updateStatus', 'Tasks::updateStatus');
        $routes->delete('delete/(:num)', 'Tasks::delete/$1');
        $routes->get('my-tasks', 'Tasks::myTasks');
    });
    
    // System & Backup
    $routes->group('system', function($routes) {
        $routes->get('/', 'System::index');
        $routes->get('createBackup', 'System::createBackup');
        $routes->get('downloadBackup/(:segment)', 'System::downloadBackup/$1');
        $routes->delete('deleteBackup/(:segment)', 'System::deleteBackup/$1');
        $routes->get('audit-logs', 'System::auditLogs');
        $routes->get('info', 'System::info');
    });
    
    // Signatures
    $routes->group('signatures', function($routes) {
        $routes->get('sign/(:num)', 'Signatures::sign/$1');
        $routes->post('saveSignature', 'Signatures::saveSignature');
        $routes->get('view/(:num)', 'Signatures::view/$1');
        $routes->get('getSignatures/(:num)', 'Signatures::getSignatures/$1');
        $routes->post('requestSignature', 'Signatures::requestSignature');
    });
    
    // Chat
    $routes->group('chat', function($routes) {
        $routes->get('/', 'Chat::index');
        $routes->get('getMessages', 'Chat::getMessages');
        $routes->post('sendMessage', 'Chat::sendMessage');
        $routes->get('getUnreadCount', 'Chat::getUnreadCount');
    });
    
    // Objectives
    $routes->group('objectives', function($routes) {
        $routes->get('/', 'Objectives::index');
        $routes->get('set', 'Objectives::setObjectives');
        $routes->post('save', 'Objectives::save');
    });
    
    // CMS
    $routes->group('pages', function($routes) {
        $routes->get('/', 'Pages::index');
        $routes->get('create', 'Pages::create');
        $routes->post('store', 'Pages::store');
        $routes->get('edit/(:num)', 'Pages::edit/$1');
        $routes->post('update/(:num)', 'Pages::update/$1');
        $routes->delete('delete/(:num)', 'Pages::delete/$1');
    });
});

// ==========================================
// PUBLIC ROUTES
// ==========================================
$routes->get('properties', 'Properties::index');
$routes->get('properties/(:segment)', 'Properties::view/$1');
$routes->get('search', 'Properties::search');

// Contact
$routes->get('contact', 'Contact::index');
$routes->post('contact/send', 'Contact::send');

// Pages dynamiques
$routes->get('page/(:segment)', 'Pages::view/$1');

// API Routes (REST)
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    // Auth
    $routes->post('auth/login', 'Auth::login');
    $routes->post('auth/me', 'Auth::me');
    $routes->post('auth/refresh', 'Auth::refresh');
    
    // Resources
    $routes->resource('properties', ['controller' => 'Properties']);
    $routes->resource('clients', ['controller' => 'Clients']);
    $routes->resource('transactions', ['controller' => 'Transactions']);
});
