<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Search routes
$routes->get('search', 'Search::index');

// Watch routes
$routes->get('watch/(:segment)', 'Watch::index/$1');
$routes->get('watch/(:segment)/(:num)', 'Watch::episode/$1/$2');
$routes->post('api/episode-url', 'Watch::getEpisodeUrl');
