<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', static function() {
    return redirect()->to('/login');
});

$routes->get('/dashboard', 'DashboardController::index', ['filter' => 'session']);

service('auth')->routes($routes);
