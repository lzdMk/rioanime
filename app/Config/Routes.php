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

