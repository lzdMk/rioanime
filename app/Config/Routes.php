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
    
    // Pagination API routes
    $routes->get('getAccounts', 'Admin::getAccounts');
    $routes->get('getAnimeList', 'Admin::getAnimeList');
    
    // Account management API
    $routes->get('getAccount/(:num)', 'Admin::getAccount/$1');
    $routes->post('createAccount', 'Admin::createAccount');
    $routes->post('updateAccount/(:num)', 'Admin::updateAccount/$1');
    $routes->delete('deleteAccount/(:num)', 'Admin::deleteAccount/$1');
    
    // Anime management API
    $routes->get('getAnime/(:num)', 'Admin::getAnime/$1');
    $routes->post('createAnime', 'Admin::createAnime');
    $routes->post('updateAnime/(:num)', 'Admin::updateAnime/$1');
    $routes->delete('deleteAnime/(:num)', 'Admin::deleteAnime/$1');
    $routes->post('importAnime', 'Admin::importAnime');
    
    // Notification management API
    $routes->post('send-notification', 'Admin::sendNotification');
    $routes->get('getUsers', 'Admin::getUsers');
    
    // Metrics API
    $routes->get('getMetricsData', 'Admin::getMetricsData');
    $routes->get('getDeviceAnalytics', 'Admin::getDeviceAnalytics');
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
$routes->get('account/notifications', 'Account::notifications');
$routes->post('account/check-email', 'Account::checkEmail');

// Account profile actions
$routes->post('account/updateProfile', 'Account::updateProfile');
$routes->post('account/changePassword', 'Account::changePassword');
$routes->post('account/uploadAvatar', 'Account::uploadAvatar');
$routes->get('api/user/profile-data', 'Account::getProfileData');

// Notification routes
$routes->get('api/notifications', 'Notification::getNotifications');
$routes->post('api/notifications/mark-read', 'Notification::markAsRead');
$routes->post('api/notifications/delete', 'Notification::deleteNotifications');
$routes->get('api/notifications/unread-count', 'Notification::getUnreadCount');

// Follow routes
$routes->get('api/follow/status/(:num)', 'Follow::checkStatus/$1');
$routes->post('api/follow/follow', 'Follow::follow');
$routes->post('api/follow/unfollow', 'Follow::unfollow');

// Watch list page
$routes->get('account/watch-list', 'Account::watchList');

// Avatar cleanup (development only)
$routes->get('avatar-cleanup', 'AvatarCleanup::index');

