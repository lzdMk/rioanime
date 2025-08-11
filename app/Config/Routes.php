<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Search routes
$routes->get('search', 'Search::index');
$routes->post('search/ajax', 'Search::ajax');
$routes->get('search/ajax', 'Search::ajax');

// Watch routes
$routes->get('watch/(:segment)', 'Watch::index/$1');
$routes->get('watch/(:segment)/(:num)', 'Watch::episode/$1/$2');

// Embedded player routes
$routes->get('embeded/watch/(:num)/(:num)', 'Embedded::watch/$1/$2');

// Admin routes
$routes->group('admin', function ($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('metrics', 'Admin::metrics');
    $routes->get('accounts', 'Admin::accounts');
    $routes->get('anime-manage', 'Admin::animeManage');
});

// Random anime route
$routes->get('random', 'Home::randomAnime');

// Account routes
$routes->post('account/register', 'Account::register');
$routes->post('account/login', 'Account::login');
$routes->post('account/logout', 'Account::logout');
$routes->post('account/check-username', 'Account::checkUsername');

$routes->get('account/profile', 'Account::profile');
$routes->get('account/continue-watching', 'Account::continueWatching');
$routes->post('account/check-email', 'Account::checkEmail');

