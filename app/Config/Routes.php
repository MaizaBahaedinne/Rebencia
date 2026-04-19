<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 *
 * REBENCIA – Table de routage complète
 */

// ============================================================
// Désactiver l'auto-routing (sécurité)
// ============================================================
$routes->setAutoRoute(false);

// --------------------------------------------------------
// AUTH (public)
// --------------------------------------------------------
$routes->get('/',      'Auth\LoginController::index');
$routes->get('login',  'Auth\LoginController::index');
$routes->post('login', 'Auth\LoginController::authenticate');
$routes->get('logout', 'Auth\LoginController::logout');

// --------------------------------------------------------
// ADMIN – protégé par filtre auth
// --------------------------------------------------------
$routes->group('admin', ['filter' => 'auth'], function ($routes) {

    // Dashboard
    $routes->get('/',           'Admin\DashboardController::index');
    $routes->get('dashboard',   'Admin\DashboardController::index');

    // Profil personnel
    $routes->get('profile',             'Admin\UsersController::profile');
    $routes->post('profile/update',     'Admin\UsersController::updateProfile');
    $routes->post('profile/password',   'Admin\UsersController::changePassword');

    // Gestion Utilisateurs
    $routes->get('users',                       'Admin\UsersController::index');
    $routes->get('users/create',                'Admin\UsersController::create');
    $routes->post('users/store',                'Admin\UsersController::store');
    $routes->get('users/(:num)',                'Admin\UsersController::show/$1');
    $routes->get('users/(:num)/edit',           'Admin\UsersController::edit/$1');
    $routes->post('users/(:num)/update',        'Admin\UsersController::update/$1');
    $routes->post('users/(:num)/toggle-status', 'Admin\UsersController::toggleStatus/$1');
    $routes->post('users/(:num)/delete',        'Admin\UsersController::delete/$1');

    // Matrice des Rôles
    $routes->get('roles',                           'Admin\RolesController::index');
    $routes->post('roles/(:num)/permissions',       'Admin\RolesController::updatePermissions/$1');
    $routes->get('roles/matrix',                    'Admin\RolesController::matrix');

    // Biens Immobiliers
    $routes->get('properties',                          'Admin\PropertiesController::index');
    $routes->get('properties/create',                   'Admin\PropertiesController::create');
    $routes->post('properties/store',                   'Admin\PropertiesController::store');
    $routes->get('properties/(:num)',                   'Admin\PropertiesController::show/$1');
    $routes->get('properties/(:num)/edit',              'Admin\PropertiesController::edit/$1');
    $routes->post('properties/(:num)/update',           'Admin\PropertiesController::update/$1');
    $routes->post('properties/(:num)/publish',          'Admin\PropertiesController::publish/$1');
    $routes->post('properties/(:num)/delete',           'Admin\PropertiesController::delete/$1');
    $routes->post('properties/(:num)/image',            'Admin\PropertiesController::uploadImage/$1');
    $routes->post('properties/images/(:num)/delete',    'Admin\PropertiesController::deleteImage/$1');

    // Leads / CRM
    $routes->get('leads',                       'Admin\LeadsController::index');
    $routes->get('leads/create',                'Admin\LeadsController::create');
    $routes->post('leads/store',                'Admin\LeadsController::store');
    $routes->get('leads/(:num)',                'Admin\LeadsController::show/$1');
    $routes->get('leads/(:num)/edit',           'Admin\LeadsController::edit/$1');
    $routes->post('leads/(:num)/update',        'Admin\LeadsController::update/$1');
    $routes->post('leads/(:num)/status',        'Admin\LeadsController::updateStatus/$1');
    $routes->post('leads/(:num)/assign',        'Admin\LeadsController::assign/$1');
    $routes->post('leads/(:num)/note',          'Admin\LeadsController::addNote/$1');
    $routes->post('leads/(:num)/delete',        'Admin\LeadsController::delete/$1');

    // System – Logs
    $routes->get('system/logs',         'Admin\SystemController::logs');
    $routes->get('system/logs/export',  'Admin\SystemController::exportLogs');

    // System – Déploiement Git
    $routes->get('system/deploy',       'Admin\SystemController::deploy');
    $routes->post('system/deploy/pull', 'Admin\SystemController::gitPull');
});

// --------------------------------------------------------
// API JSON interne (AJAX – protégé par auth)
// --------------------------------------------------------
$routes->group('api', ['filter' => 'auth'], function ($routes) {
    $routes->get('stats/summary',   'Api\StatsController::summary');
    $routes->get('leads/pipeline',  'Api\LeadsController::pipeline');
});
