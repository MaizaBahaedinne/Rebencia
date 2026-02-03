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
        $routes->post('calculate', 'Commissions::calculate');
        $routes->post('approve/(:num)', 'Commissions::approve/$1');
        $routes->post('pay/(:num)', 'Commissions::markAsPaid/$1');
    });
    
    // Workflows
    $routes->group('workflows', function($routes) {
        $routes->get('/', 'Workflows::index');
        $routes->get('create', 'Workflows::create');
        $routes->post('store', 'Workflows::store');
        $routes->get('edit/(:num)', 'Workflows::edit/$1');
        $routes->post('update/(:num)', 'Workflows::update/$1');
        $routes->delete('delete/(:num)', 'Workflows::delete/$1');
    });
    
    // Settings
    $routes->group('settings', function($routes) {
        $routes->get('/', 'Settings::index');
        $routes->post('update', 'Settings::update');
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
    $routes->resource('properties');
    $routes->resource('clients');
    $routes->resource('transactions');
    $routes->post('estimation/calculate', 'Estimation::calculate');
});
