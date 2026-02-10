<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default routes
$routes->get('/', 'Home::index');

// API routes (public)
$routes->get('api/cities', 'Home::getCities');
$routes->get('api/zones/cities', 'Api\Zones::cities');
$routes->get('api/zones/governorates', 'Api\Zones::governorates');
$routes->get('api/zones/cities-by-governorate/(:num)', 'Api\Zones::citiesByGovernorate/$1');

// Public pages
$routes->get('about', 'Pages::about');
$routes->get('contact', 'Pages::contact');
$routes->post('contact/send', 'Pages::sendContact');

// Search routes
$routes->get('search', 'Search::index');

// Properties routes (public)
$routes->get('properties', 'Properties::index');
$routes->post('properties/submit-request', 'Properties::submitRequest');
$routes->get('properties/(:any)', 'Properties::view/$1');

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
    
    // Profile
    $routes->get('profile', 'Users::profile');
    $routes->post('profile/update', 'Users::updateProfile');
    $routes->post('profile/change-password', 'Users::changePassword');
    
    // Users Management
    $routes->group('users', function($routes) {
        $routes->get('/', 'Users::index');
        $routes->get('create', 'Users::create');
        $routes->post('store', 'Users::store');
        $routes->get('view/(:num)', 'Users::view/$1');
        $routes->get('edit/(:num)', 'Users::edit/$1');
        $routes->post('update/(:num)', 'Users::update/$1');
        $routes->delete('delete/(:num)', 'Users::delete/$1');
        $routes->get('manage-roles/(:num)', 'Users::manageRoles/$1');
        $routes->post('assign-role/(:num)', 'Users::assignRole/$1');
        $routes->post('set-default-role/(:num)', 'Users::setDefaultRole/$1');
        $routes->get('remove-role/(:num)/(:num)', 'Users::removeRole/$1/$2');
        $routes->get('login-as/(:num)', 'Users::loginAs/$1');
        $routes->get('bulk-manage', 'Users::bulkManage');
        $routes->post('bulk-action', 'Users::bulkAction');
    });
    
    // Stop impersonation
    $routes->get('stop-impersonation', 'Users::stopImpersonation');

    // Switch Role
    $routes->post('switch-role', 'Users::switchRole');
    
    // Menus Management (routes directes pour éviter problème de cache)
    $routes->get('menus', 'Menus::index');
    $routes->get('menus/create', 'Menus::create');
    $routes->post('menus/store', 'Menus::store');
    $routes->get('menus/edit/(:num)', 'Menus::edit/$1');
    $routes->post('menus/update/(:num)', 'Menus::update/$1');
    $routes->get('menus/delete/(:num)', 'Menus::delete/$1');
    $routes->get('menus/role-menus', 'Menus::roleMenus');
    $routes->get('menus/role-menus/(:num)', 'Menus::roleMenus/$1');
    $routes->post('menus/update-role-menus', 'Menus::updateRoleMenus');
    
    // Roles & Permissions
    $routes->group('roles', function($routes) {
        $routes->get('/', 'Roles::index');
        $routes->get('create', 'Roles::create');
        $routes->post('store', 'Roles::store');
        $routes->get('edit/(:num)', 'Roles::edit/$1');
        $routes->post('update/(:num)', 'Roles::update/$1');
        $routes->get('delete/(:num)', 'Roles::delete/$1');
        $routes->get('matrix', 'Roles::matrix');
        $routes->post('sync-permissions', 'Roles::syncPermissions');
    });
    
    // Agencies
    $routes->group('agencies', function($routes) {
        $routes->get('/', 'Agencies::index');
        $routes->get('create', 'Agencies::create');
        $routes->post('store', 'Agencies::store');
        $routes->get('edit/(:num)', 'Agencies::edit/$1');
        $routes->post('update/(:num)', 'Agencies::update/$1');
        $routes->get('delete/(:num)', 'Agencies::delete/$1');
        $routes->get('view/(:num)', 'Agencies::view/$1');
    });
    
    // Zones
    $routes->group('zones', function($routes) {
        $routes->get('/', 'Zones::index');
        $routes->get('create', 'Zones::create');
        $routes->post('store', 'Zones::store');
        $routes->get('edit/(:num)', 'Zones::edit/$1');
        $routes->post('update/(:num)', 'Zones::update/$1');
        $routes->get('delete/(:num)', 'Zones::delete/$1');
    });
    
    // Properties
    $routes->group('properties', function($routes) {
        $routes->get('/', 'Properties::index');
        $routes->get('create', 'Properties::create');
        $routes->post('store', 'Properties::store');
        $routes->post('save-step', 'Properties::saveStep');
        $routes->get('edit/(:num)', 'Properties::edit/$1');
        $routes->post('update/(:num)', 'Properties::update/$1');
        $routes->put('update/(:num)', 'Properties::update/$1');
        $routes->delete('delete/(:num)', 'Properties::delete/$1');
        $routes->get('view/(:num)', 'Properties::view/$1');
        $routes->post('deleteImage/(:num)', 'Properties::deleteImage/$1');
        $routes->post('deleteDocument/(:num)', 'Properties::deleteDocument/$1');
        $routes->get('bulk-manage', 'Properties::bulkManage');
        $routes->post('bulk-action', 'Properties::bulkAction');
        
        // Owner management
        $routes->get('search-owners', 'Properties::searchOwners');
        $routes->post('update-owner/(:num)', 'Properties::updateOwner/$1');
        
        // Property Extended Data Routes
        $routes->post('(:num)/rooms/save', 'PropertyExtendedController::saveRooms/$1');
        $routes->post('(:num)/options/save', 'PropertyExtendedController::saveOptions/$1');
        $routes->post('(:num)/location/save', 'PropertyExtendedController::saveLocationScoring/$1');
        $routes->post('(:num)/financial/save', 'PropertyExtendedController::saveFinancialData/$1');
        $routes->post('(:num)/costs/save', 'PropertyExtendedController::saveEstimatedCosts/$1');
        $routes->post('(:num)/orientation/save', 'PropertyExtendedController::saveOrientation/$1');
        $routes->post('(:num)/media/upload', 'PropertyExtendedController::saveMediaExtension/$1');
        
        $routes->delete('rooms/(:num)', 'PropertyExtendedController::deleteRoom/$1');
        $routes->delete('media/(:num)', 'PropertyExtendedController::deleteMediaFile/$1');
    });
    
    // Property Configuration (Admin)
    $routes->group('properties/config', function($routes) {
        $routes->get('/', 'PropertyConfigController::index');
        $routes->get('(:alpha)', 'PropertyConfigController::edit/$1');
        $routes->post('(:alpha)', 'PropertyConfigController::update/$1');
        $routes->post('(:alpha)/toggle/(:alpha)', 'PropertyConfigController::toggleFeature/$1/$2');
        $routes->get('(:alpha)/sections', 'PropertyConfigController::getSections/$1');
        $routes->post('validate/(:num)', 'PropertyConfigController::validateProperty/$1');
        $routes->post('(:alpha)/reset', 'PropertyConfigController::reset/$1');
    });
    
    // Property Analysis & Reports
    $routes->group('properties', function($routes) {
        $routes->get('(:num)/analysis', 'PropertyAnalysisController::dashboard/$1');
        $routes->get('(:num)/financial-report', 'PropertyAnalysisController::financialReport/$1');
        $routes->get('compare/(:num)/(:num)', 'PropertyAnalysisController::comparison/$1/$2');
        $routes->get('portfolio', 'PropertyAnalysisController::portfolio');
        $routes->post('(:num)/export-report', 'PropertyAnalysisController::exportReport/$1');
        $routes->get('(:num)/metrics', 'PropertyAnalysisController::getMetrics/$1');
        $routes->get('(:num)/projection/(:num)', 'PropertyAnalysisController::getProjection/$1/$2');
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
        
        // Commission management
        $routes->get('view-commission/(:num)', 'Transactions::viewCommission/$1');
        $routes->get('mark-commission-paid/(:num)', 'Transactions::markCommissionPaid/$1');
        $routes->get('recalculate-commission/(:num)', 'Transactions::recalculateCommission/$1');
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
    
    // Commission Settings (Configuration & Rules)
    $routes->group('commission-settings', function($routes) {
        // Rules Management
        $routes->get('/', 'CommissionSettings::index');
        $routes->get('rules', 'CommissionSettings::rules');
        $routes->get('create-rule', 'CommissionSettings::createRule');
        $routes->post('store-rule', 'CommissionSettings::storeRule');
        $routes->get('edit-rule/(:num)', 'CommissionSettings::editRule/$1');
        $routes->post('update-rule/(:num)', 'CommissionSettings::updateRule/$1');
        $routes->delete('delete-rule/(:num)', 'CommissionSettings::deleteRule/$1');
        $routes->post('set-default-rule/(:num)', 'CommissionSettings::setDefaultRule/$1');
        
        // Overrides Management
        $routes->get('overrides', 'CommissionSettings::overrides');
        $routes->get('create-override', 'CommissionSettings::createOverride');
        $routes->post('store-override', 'CommissionSettings::storeOverride');
        $routes->get('edit-override/(:num)', 'CommissionSettings::editOverride/$1');
        $routes->post('update-override/(:num)', 'CommissionSettings::updateOverride/$1');
        $routes->delete('delete-override/(:num)', 'CommissionSettings::deleteOverride/$1');
        
        // Simulation & Logs
        $routes->get('simulate', 'CommissionSettings::simulate');
        $routes->post('process-simulation', 'CommissionSettings::processSimulation');
        $routes->get('logs', 'CommissionSettings::logs');
    });
    
    // Notifications
    $routes->group('notifications', function($routes) {
        $routes->get('/', 'Notifications::index');
        $routes->post('mark-as-read/(:num)', 'Notifications::markAsRead/$1');
        $routes->post('mark-all-as-read', 'Notifications::markAllAsRead');
        $routes->get('unread-count', 'Notifications::getUnreadCount');
    });
    
    // Hierarchy Management
    $routes->group('hierarchy', function($routes) {
        $routes->get('/', 'Hierarchy::index');
        $routes->get('assign-manager', 'Hierarchy::assignManager');
        $routes->get('assign-manager/(:num)', 'Hierarchy::assignManager/$1');
        $routes->post('assign-manager', 'Hierarchy::assignManager');
        $routes->post('assign-manager/(:num)', 'Hierarchy::assignManager/$1');
        $routes->get('view-user/(:num)', 'Hierarchy::viewUser/$1');
        $routes->post('update-role/(:num)', 'Hierarchy::updateRole/$1');
        $routes->post('update-agency/(:num)', 'Hierarchy::updateAgency/$1');
        $routes->post('update-manager/(:num)', 'Hierarchy::updateManager/$1');
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
        $routes->get('general', 'Settings::general');
        $routes->get('email', 'Settings::email');
        $routes->get('sms', 'Settings::sms');
        $routes->get('payment', 'Settings::payment');
        $routes->get('notifications', 'Settings::notifications');
        $routes->get('footer', 'Settings::footer');
        $routes->post('update', 'Settings::update');
        $routes->post('updateFooter', 'Settings::updateFooter');
    });
    
    // Sliders Management
    $routes->group('sliders', function($routes) {
        $routes->get('/', 'Sliders::index');
        $routes->get('create', 'Sliders::create');
        $routes->post('store', 'Sliders::store');
        $routes->get('edit/(:num)', 'Sliders::edit/$1');
        $routes->post('update/(:num)', 'Sliders::update/$1');
        $routes->post('delete/(:num)', 'Sliders::delete/$1');
        $routes->post('toggle-status/(:num)', 'Sliders::toggleStatus/$1');
    });
    
    // Theme Management
    $routes->group('theme', function($routes) {
        $routes->get('/', 'Theme::index');
        $routes->post('update', 'Theme::update');
        $routes->get('reset', 'Theme::reset');
        $routes->post('preview', 'Theme::preview');
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
